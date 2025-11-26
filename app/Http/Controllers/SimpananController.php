<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Ditambahkan untuk transaksi

class SimpananController extends Controller
{
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
        // Validasi: hanya Mandiri dan Jasa Anggota yang diizinkan dicatat secara manual
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_simpanan' => 'required|numeric|min:1', // Minimal 1 agar tidak mencatat transaksi nol
            'jenis_simpanan' => 'required|string|in:mandiri,jasa_anggota', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat catatan Simpanan
            Simpanan::create($validatedData);

            // 2. Perbarui saldo Anggota
            $anggota = Anggota::find($validatedData['anggota_id']);
            if ($anggota) {
                $kolom_saldo = match($validatedData['jenis_simpanan']) {
                    'mandiri' => 'saldo_mandiri',
                    'jasa_anggota' => 'saldo_jasa_anggota',
                    default => null,
                };
                
                if ($kolom_saldo) {
                    // Gunakan increment() untuk menambahkan saldo
                    $anggota->increment($kolom_saldo, $validatedData['jumlah_simpanan']);
                }
            } else {
                 throw new \Exception("Anggota tidak ditemukan. Transaksi dibatalkan.");
            }

            DB::commit();
            return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Gagal mencatat simpanan: ' . $e->getMessage()); // Tambahkan logging jika diperlukan
            return back()->withInput()->with('error', 'Gagal mencatat simpanan: ' . $e->getMessage());
        }
    }
    
    // --- FITUR BARU: PENARIKAN SIMPANAN (HANYA MANASUKA DAN JASA ANGGOTA) ---

    /**
     * Menampilkan form untuk penarikan simpanan.
     */
    public function withdraw()
    {
        // Penarikan manasuka (bisa dilakukan kapan saja) dan jasa anggota
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.withdraw', compact('anggotas'));
    }
    
    /**
     * Memproses penarikan simpanan.
     */
    public function processWithdrawal(Request $request)
    {
        // Validasi: hanya penarikan Mandiri dan Jasa Anggota yang diizinkan
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_penarikan' => 'required|numeric|min:1', 
            // Penarikan hanya diizinkan untuk saldo yang non-wajib
            'jenis_simpanan' => 'required|string|in:manasuka,mandiri,jasa_anggota', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $anggota = Anggota::find($validatedData['anggota_id']);
            $jumlahPenarikan = $validatedData['jumlah_penarikan'];
            $jenisSimpanan = $validatedData['jenis_simpanan'];

            $kolom_saldo = match($jenisSimpanan) {
                'manasuka' => 'saldo_manasuka',
                'mandiri' => 'saldo_mandiri',
                'jasa_anggota' => 'saldo_jasa_anggota',
                default => null,
            };

            if (!$anggota || !$kolom_saldo) {
                 throw new \Exception("Anggota atau jenis simpanan tidak valid.");
            }
            
            // 1. Cek Saldo Mencukupi
            if ($anggota->$kolom_saldo < $jumlahPenarikan) {
                DB::rollBack();
                return back()->withInput()->with('error', "Penarikan GAGAL. Saldo {$kolom_saldo} anggota tidak mencukupi (Saldo saat ini: Rp " . number_format($anggota->$kolom_saldo, 2, ',', '.') . ").");
            }

            // 2. Catat Transaksi Penarikan 
            // Kita menggunakan nilai negatif untuk membedakan di tabel simpanans
            $jenisTransaksi = 'penarikan_' . $jenisSimpanan;
            
            Simpanan::create([
                'anggota_id' => $anggota->id,
                'jumlah_simpanan' => -$jumlahPenarikan, // Dicatat sebagai nilai negatif (Kas Keluar)
                'jenis_simpanan' => $jenisTransaksi,
                'deskripsi' => $validatedData['deskripsi'] ?? "Penarikan {$jenisSimpanan}",
                'tanggal_simpanan' => $validatedData['tanggal_simpanan'],
            ]);

            // 3. Kurangi Saldo Anggota
            $anggota->decrement($kolom_saldo, $jumlahPenarikan);

            DB::commit();
            return redirect()->route('simpanan.index')->with('success', 'Penarikan simpanan berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses penarikan: ' . $e->getMessage());
        }
    }


    // ... (show, edit, update, destroy methods remains the same) ...

    public function show(Simpanan $simpanan)
    {
        $simpanan->load('anggota');
        return view('simpanan.show', compact('simpanan'));
    }

    public function edit(Simpanan $simpanan)
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.edit', compact('simpanan', 'anggotas'));
    }

    public function update(Request $request, Simpanan $simpanan)
    {
        // Validasi: hanya Mandiri dan Jasa Anggota
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_simpanan' => 'required|numeric|min:1',
            'jenis_simpanan' => 'required|string|in:mandiri,jasa_anggota', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $anggotaLama = Anggota::find($simpanan->anggota_id);
            $jumlahLama = $simpanan->jumlah_simpanan;
            $jenisLama = $simpanan->jenis_simpanan;

            // 1. Update catatan Simpanan
            $simpanan->update($validatedData);

            $anggotaBaru = Anggota::find($validatedData['anggota_id']);
            $jumlahBaru = $validatedData['jumlah_simpanan'];
            $jenisBaru = $validatedData['jenis_simpanan'];

            // 2. Logika Update Saldo
            
            // a. Hitung Ulang Saldo Lama: Kurangi saldo dari anggota lama
            if ($anggotaLama) {
                $kolom_lama = match($jenisLama) {
                    'mandiri' => 'saldo_mandiri',
                    'jasa_anggota' => 'saldo_jasa_anggota',
                    default => null,
                };
                
                if ($kolom_lama) {
                    // Periksa apakah saldo cukup sebelum decrement
                    if ($anggotaLama->$kolom_lama < $jumlahLama) {
                        throw new \Exception("Saldo anggota lama tidak mencukupi untuk membatalkan transaksi.");
                    }
                    $anggotaLama->decrement($kolom_lama, $jumlahLama);
                }
            }

            // b. Terapkan Saldo Baru: Tambahkan saldo ke anggota baru
            if ($anggotaBaru) {
                $kolom_baru = match($jenisBaru) {
                    'mandiri' => 'saldo_mandiri',
                    'jasa_anggota' => 'saldo_jasa_anggota',
                    default => null,
                };
                
                if ($kolom_baru) {
                    $anggotaBaru->increment($kolom_baru, $jumlahBaru);
                }
            } else {
                throw new \Exception("Anggota baru tidak ditemukan. Transaksi dibatalkan.");
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
            
            $kolom_saldo = match($simpanan->jenis_simpanan) {
                'mandiri', 'penarikan_mandiri' => 'saldo_mandiri',
                'jasa_anggota', 'penarikan_jasa_anggota' => 'saldo_jasa_anggota',
                default => null,
            };
            
            $jumlahTransaksi = abs($simpanan->jumlah_simpanan); // Gunakan nilai absolut

            if ($anggota && $kolom_saldo) {
                
                // Jika ini adalah setoran (jumlah_simpanan > 0), kembalikan saldo dengan decrement
                if ($simpanan->jumlah_simpanan > 0) {
                    if ($anggota->$kolom_saldo < $jumlahTransaksi) {
                        throw new \Exception("Penghapusan setoran ini akan menyebabkan saldo minus.");
                    }
                    $anggota->decrement($kolom_saldo, $jumlahTransaksi);
                } 
                // Jika ini adalah penarikan (jumlah_simpanan < 0), kembalikan saldo dengan increment
                elseif ($simpanan->jumlah_simpanan < 0) {
                    $anggota->increment($kolom_saldo, $jumlahTransaksi);
                }
            } else {
                // Ini untuk mencegah penghapusan transaksi wajib dan pokok
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