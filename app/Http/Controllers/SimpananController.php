<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Collection; // WAJIB DI-IMPORT JIKA MENGGUNAKAN COLLECTION/ARRAY

class SimpananController extends Controller
{
    /**
     * Helper function untuk mendapatkan kolom saldo berdasarkan jenis simpanan.
     */
    private function getSaldoColumn($jenis)
    {
        return match ($jenis) {
            'pokok' => 'saldo_pokok',
            'wajib' => 'saldo_wajib',
            'wajib_khusus' => 'saldo_wajib_khusus',
            'wajib_pinjam' => 'saldo_wajib_pinjam',
            'manasuka', 'penarikan_manasuka', 'penarikan_manasuka_massal' => 'saldo_manasuka',
            'jasa_anggota', 'penarikan_jasa_anggota' => 'saldo_jasa_anggota',
            'mandiri', 'penarikan_mandiri' => 'saldo_mandiri',
            'cicilan' => 'saldo_wajib_pinjam',
            default => null,
        };
    }

    public function index()
    {
        $query = Simpanan::with('anggota')->latest();

        if (!auth()->user()->isAdmin()) {
            $anggotaId = auth()->user()->anggota->id ?? null;
            if (!$anggotaId) {
                return view('simpanan.index', ['simpanans' => collect([])]);
            }
            $query->where('anggota_id', $anggotaId);
        }

        $simpanans = $query->paginate(10);
        return view('simpanan.index', compact('simpanans'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.create', compact('anggotas'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // ... (Metode store tetap sama) ...
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_simpanan' => 'required|numeric|min:1', 
            'jenis_simpanan' => 'required|string|in:mandiri,jasa_anggota', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            Simpanan::create($validatedData);

            $anggota = Anggota::find($validatedData['anggota_id']);
            $kolom_saldo = $this->getSaldoColumn($validatedData['jenis_simpanan']);

            if ($anggota && $kolom_saldo) {
                $anggota->increment($kolom_saldo, $validatedData['jumlah_simpanan']);
            } else {
                 throw new \Exception("Anggota tidak ditemukan atau jenis simpanan tidak valid.");
            }

            DB::commit();
            return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mencatat setoran: ' . $e->getMessage());
        }
    }
    
    // --- FITUR PENARIKAN SATU ANGGOTA (WITHDRAW) ---

    public function withdraw()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // PERBAIKAN: Mengambil data saldo yang dibutuhkan oleh JavaScript
        $anggotas = Anggota::orderBy('nama_lengkap')->get([
            'id', 
            'nama_lengkap', 
            'saldo_manasuka', 
            'saldo_mandiri', 
            'saldo_jasa_anggota',
            'user_id' // Untuk menampilkan username jika diperlukan
        ]);

        // Muat user relationship untuk nama/username yang lebih detail
        $anggotas->load('user');
        
        // Kirim data anggota dan saldonya ke view
        return view('simpanan.withdraw', compact('anggotas'));
    }
    
    public function processWithdrawal(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // ... (Metode processWithdrawal tetap sama) ...
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_penarikan' => 'required|numeric|min:1', 
            'jenis_simpanan' => 'required|string|in:manasuka,mandiri,jasa_anggota', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $anggota = Anggota::find($validatedData['anggota_id']);
            $jumlahPenarikan = $validatedData['jumlah_penarikan'];
            $jenisSimpanan = $validatedData['jenis_simpanan'];

            $kolom_saldo = $this->getSaldoColumn($jenisSimpanan);

            if (!$anggota || !$kolom_saldo) {
                 throw new \Exception("Anggota atau jenis simpanan tidak valid.");
            }
            
            if ($anggota->$kolom_saldo < $jumlahPenarikan) {
                DB::rollBack();
                return back()->withInput()->with('error', "Penarikan GAGAL. Saldo {$kolom_saldo} anggota tidak mencukupi (Saldo saat ini: Rp " . number_format($anggota->$kolom_saldo, 2, ',', '.') . ").");
            }

            $jenisTransaksi = 'penarikan_' . $jenisSimpanan;
            
            Simpanan::create([
                'anggota_id' => $anggota->id,
                'jumlah_simpanan' => -$jumlahPenarikan, 
                'jenis_simpanan' => $jenisTransaksi,
                'deskripsi' => $validatedData['deskripsi'] ?? "Penarikan {$jenisSimpanan}",
                'tanggal_simpanan' => $validatedData['tanggal_simpanan'],
            ]);

            $anggota->decrement($kolom_saldo, $jumlahPenarikan);

            DB::commit();
            return redirect()->route('simpanan.index')->with('success', 'Penarikan simpanan berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses penarikan: ' . $e->getMessage());
        }
    }
    
    // --- FITUR PENARIKAN SIMPANAN MASSAL (MANASUKA - TARIK PENUH) ---
    
    public function massWithdraw()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $totalManasuka = Anggota::where('status_aktif', 1)->sum('saldo_manasuka');
        return view('simpanan.mass_withdraw', compact('totalManasuka'));
    }
    
    public function processMassWithdrawal(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
                // VALIDASI DIPERBAIKI: Hanya butuh tanggal dan deskripsi
        $validatedData = $request->validate([
            'tanggal_penarikan' => 'required|date',
            'deskripsi' => 'nullable|string',
        ]);
        
        $tanggalPenarikan = $validatedData['tanggal_penarikan'];
        $deskripsi = $validatedData['deskripsi'] ?? "Penarikan Seluruh Saldo Manasuka Massal Hari Raya.";
        
        // Ambil semua anggota aktif yang memiliki saldo manasuka > 0
        $anggotas = Anggota::where('status_aktif', 1)
                            ->where('saldo_manasuka', '>', 0) 
                            ->get();

        if ($anggotas->isEmpty()) {
            return back()->with('error', 'Tidak ada anggota aktif yang memiliki saldo Manasuka untuk ditarik.');
        }

        $totalAnggotaDiproses = 0;
        $totalAmountWithdrawn = 0;
        
        DB::beginTransaction();
        try {
            foreach ($anggotas as $anggota) {
                $amountToWithdraw = $anggota->saldo_manasuka; 
                
                $kolom_saldo = 'saldo_manasuka';
                $jenisTransaksi = 'penarikan_manasuka_massal';
                
                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jumlah_simpanan' => -$amountToWithdraw, 
                    'jenis_simpanan' => $jenisTransaksi,
                    'deskripsi' => $deskripsi,
                    'tanggal_simpanan' => $tanggalPenarikan,
                ]);

                $anggota->decrement($kolom_saldo, $amountToWithdraw); 

                $totalAnggotaDiproses++;
                $totalAmountWithdrawn += $amountToWithdraw;
            }

            DB::commit();
            return redirect()->route('simpanan.index')->with('success', 
                "Penarikan SELURUH Saldo Manasuka berhasil dicatat untuk {$totalAnggotaDiproses} anggota. Total: Rp " . number_format($totalAmountWithdrawn, 2, ',', '.')
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses penarikan massal: ' . $e->getMessage());
        }
    }


    public function show(Simpanan $simpanan)
    {
        if (!auth()->user()->isAdmin()) {
            $anggotaId = auth()->user()->anggota->id ?? null;
            if ($simpanan->anggota_id !== $anggotaId) {
                abort(403, 'Unauthorized action.');
            }
        }
        $simpanan->load('anggota');
        return view('simpanan.show', compact('simpanan'));
    }

    public function edit(Simpanan $simpanan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.edit', compact('simpanan', 'anggotas'));
    }

    public function update(Request $request, Simpanan $simpanan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // Validasi harus mencakup semua jenis transaksi (setoran dan penarikan yang dicatat)
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_simpanan' => 'required|numeric', // Tidak boleh min:0 atau min:1 karena bisa negatif
            'jenis_simpanan' => 'required|string|in:pokok,wajib,wajib_khusus,wajib_pinjam,manasuka,jasa_anggota,mandiri,cicilan,penarikan_manasuka,penarikan_mandiri,penarikan_jasa_anggota', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $anggotaLama = Anggota::find($simpanan->anggota_id);
            $jumlahLama = $simpanan->jumlah_simpanan;
            $jenisLama = $simpanan->jenis_simpanan;

            $simpanan->update($validatedData);

            $anggotaBaru = Anggota::find($validatedData['anggota_id']);
            $jumlahBaru = $validatedData['jumlah_simpanan'];
            $jenisBaru = $validatedData['jenis_simpanan'];

            $kolomLama = $this->getSaldoColumn($jenisLama);
            if ($anggotaLama && $kolomLama) {
                $anggotaLama->decrement($kolomLama, $jumlahLama);
            }

            $kolomBaru = $this->getSaldoColumn($jenisBaru);
            if ($anggotaBaru && $kolomBaru) {
                $anggotaBaru->increment($kolomBaru, $jumlahBaru);
            }

            DB::commit();
            return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui simpanan: ' . $e->getMessage());
        }
    }

    public function destroy(Simpanan $simpanan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        DB::beginTransaction();

        try {
            $anggota = Anggota::find($simpanan->anggota_id);
            $jenisSimpanan = $simpanan->jenis_simpanan;
            $kolom_saldo = $this->getSaldoColumn($jenisSimpanan);
            
            $jumlahTransaksi = $simpanan->jumlah_simpanan; 
            
            if (str_starts_with($jenisSimpanan, 'penarikan_') || in_array($jenisSimpanan, ['mandiri', 'jasa_anggota', 'manasuka'])) {
                
                if ($anggota && $kolom_saldo) {
                    $anggota->decrement($kolom_saldo, $jumlahTransaksi);
                }

            } else {
                throw new \Exception("Jenis simpanan ini tidak dapat dihapus secara manual.");
            }

            $simpanan->delete();

            DB::commit();
            return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus simpanan: ' . $e->getMessage());
        }
    }
}