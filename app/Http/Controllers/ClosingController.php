<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClosingController extends Controller
{
    /**
     * Tampilkan halaman tutup bulan dengan tombol proses.
     */
    public function index()
    {
        return view('closing.index');
    }

    /**
     * Proses tutup bulan dan catat simpanan otomatis.
     */
    public function closeMonth()
    {
        // 1. Cek apakah proses tutup bulan sudah pernah dijalankan di bulan ini
        $lastClosing = Simpanan::whereIn('jenis_simpanan', ['wajib', 'manasuka'])
                               ->whereMonth('tanggal_simpanan', Carbon::now()->month)
                               ->whereYear('tanggal_simpanan', Carbon::now()->year)
                               ->first();

        if ($lastClosing) {
            return redirect()->route('closing.index')->with('error', 'Tutup bulan gagal: Proses sudah dijalankan pada bulan ini.');
        }

        // 2. Gunakan transaction untuk memastikan semua data tersimpan atau tidak sama sekali
        DB::beginTransaction();

        try {
            $anggotas = Anggota::where('simpanan_wajib', '>', 0)
                                ->orWhere('simpanan_manasuka', '>', 0)
                                ->get();

            foreach ($anggotas as $anggota) {
                // Proses Simpanan Wajib
                if ($anggota->simpanan_wajib > 0) {
                    Simpanan::create([
                        'anggota_id' => $anggota->id,
                        'jumlah_simpanan' => $anggota->simpanan_wajib,
                        'jenis_simpanan' => 'wajib',
                        'deskripsi' => 'Simpanan Wajib otomatis bulan ' . Carbon::now()->format('F Y'),
                        'tanggal_simpanan' => Carbon::now(),
                    ]);
                    // Perbarui saldo wajib anggota
                    $anggota->increment('saldo_wajib', $anggota->simpanan_wajib);
                }

                // Proses Simpanan Manasuka
                if ($anggota->simpanan_manasuka > 0) {
                    Simpanan::create([
                        'anggota_id' => $anggota->id,
                        'jumlah_simpanan' => $anggota->simpanan_manasuka,
                        'jenis_simpanan' => 'manasuka',
                        'deskripsi' => 'Simpanan Manasuka otomatis bulan ' . Carbon::now()->format('F Y'),
                        'tanggal_simpanan' => Carbon::now(),
                    ]);
                    // Perbarui saldo manasuka anggota
                    $anggota->increment('saldo_manasuka', $anggota->simpanan_manasuka);
                }
            }

            DB::commit();

            return redirect()->route('closing.index')->with('success', 'Tutup bulan berhasil! Simpanan otomatis telah dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('closing.index')->with('error', 'Tutup bulan gagal: ' . $e->getMessage());
        }
    }
}