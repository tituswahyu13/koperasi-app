<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

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
        $simpanans = Simpanan::with('anggota')->latest()->paginate(10);
        return view('simpanan.index', compact('simpanans'));
    }

    public function create()
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.create', compact('anggotas'));
    }

    public function store(Request $request)
    {
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
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.withdraw', compact('anggotas'));
    }
    
    public function processWithdrawal(Request $request)
    {
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
    
    // --- FITUR BARU: PENARIKAN SIMPANAN MASSAL (MANASUKA) ---
    
    /**
     * Menampilkan form untuk penarikan massal (Manasuka).
     */
    public function massWithdraw()
    {
        // Hitung total saldo manasuka anggota yang aktif
        $totalManasuka = Anggota::where('status_aktif', 1)->sum('saldo_manasuka');
        return view('simpanan.mass_withdraw', compact('totalManasuka'));
    }
    
    /**
     * Memproses penarikan massal seluruh saldo Manasuka.
     */
    public function processMassWithdrawal(Request $request)
    {
        // VALIDASI DIPERBAIKI: Hanya butuh tanggal dan deskripsi
        $validatedData = $request->validate([
            'tanggal_penarikan' => 'required|date',
            'deskripsi' => 'nullable|string',
        ]);
        
        $tanggalPenarikan = $validatedData['tanggal_penarikan'];
        $deskripsi = $validatedData['deskripsi'] ?? "Penarikan Seluruh Saldo Manasuka Massal.";
        
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
                $amountToWithdraw = $anggota->saldo_manasuka; // Tarik SELURUH saldo
                
                $kolom_saldo = 'saldo_manasuka';
                $jenisTransaksi = 'penarikan_manasuka_massal';
                
                // 1. Catat Transaksi Penarikan (Kas Keluar)
                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jumlah_simpanan' => -$amountToWithdraw, // Dicatat sebagai nilai NEGATIF
                    'jenis_simpanan' => $jenisTransaksi,
                    'deskripsi' => $deskripsi,
                    'tanggal_simpanan' => $tanggalPenarikan,
                ]);

                // 2. Kurangi Saldo Anggota dan SETEL KE NOL
                $anggota->decrement($kolom_saldo, $amountToWithdraw); 
                $anggota->save(); // Penting untuk menyimpan perubahan saldo ke 0

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


    // ... (Metode update dan destroy tetap sama) ...
    public function update(Request $request, Simpanan $simpanan)
    {
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

            // 1. Kurangi saldo lama (kembalikan saldo ke kondisi sebelum transaksi lama)
            $kolomLama = $this->getSaldoColumn($jenisLama);
            if ($anggotaLama && $kolomLama) {
                $anggotaLama->decrement($kolomLama, $jumlahLama);
            }

            // 2. Tambahkan saldo baru (terapkan saldo dari transaksi baru)
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
        DB::beginTransaction();

        try {
            $anggota = Anggota::find($simpanan->anggota_id);
            $jenisSimpanan = $simpanan->jenis_simpanan;
            $kolom_saldo = $this->getSaldoColumn($jenisSimpanan);
            
            $jumlahTransaksi = $simpanan->jumlah_simpanan; 
            
            // HANYA izinkan penghapusan untuk transaksi yang dicatat secara manual
            if (str_starts_with($jenisSimpanan, 'penarikan_') || in_array($jenisSimpanan, ['mandiri', 'jasa_anggota', 'manasuka'])) {
                
                if ($anggota && $kolom_saldo) {
                    // Logika undo: Jika transaksi adalah setoran (+), lakukan decrement (-). Jika penarikan (-), lakukan increment (+).
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