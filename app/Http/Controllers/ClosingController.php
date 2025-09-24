<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClosingController extends Controller
{
    public function index()
    {
        return view('closing.index');
    }

    public function closeMonth()
    {
        // $lastClosing = Simpanan::whereIn('jenis_simpanan', ['wajib', 'manasuka'])
        //                        ->whereMonth('tanggal_simpanan', Carbon::now()->month)
        //                        ->whereYear('tanggal_simpanan', Carbon::now()->year)
        //                        ->first();

        // if ($lastClosing) {
        //     return redirect()->route('closing.index')->with('error', 'Tutup bulan gagal: Proses sudah dijalankan pada bulan ini.');
        // }

        DB::beginTransaction();

        try {
            // Logika untuk memproses simpanan wajib dan manasuka
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
                    $anggota->increment('saldo_manasuka', $anggota->simpanan_manasuka);
                }
            }

            // Logika baru untuk memproses cicilan pinjaman
            $pinjamans = Pinjaman::where('status', 'approved')
                                 ->where('sisa_pinjaman', '>', 0)
                                 ->get();

            foreach ($pinjamans as $pinjaman) {
                $cicilanPerBulan = $pinjaman->sisa_pinjaman / $pinjaman->tenor;
                $deskripsiCicilan = 'Cicilan pinjaman bulan ' . Carbon::now()->format('F Y');

                // Catat transaksi sebagai simpanan (pengembalian pinjaman)
                Simpanan::create([
                    'anggota_id' => $pinjaman->anggota_id,
                    'jumlah_simpanan' => $cicilanPerBulan,
                    'jenis_simpanan' => 'cicilan',
                    'deskripsi' => $deskripsiCicilan,
                    'tanggal_simpanan' => Carbon::now(),
                ]);

                // Perbarui saldo pinjaman
                $pinjaman->decrement('sisa_pinjaman', $cicilanPerBulan);
                $pinjaman->increment('jumlah_bayar', $cicilanPerBulan);

                // Perbarui status pinjaman jika sudah lunas
                if ($pinjaman->sisa_pinjaman <= 0) {
                    $pinjaman->update(['status' => 'paid']);
                }
            }

            DB::commit();

            return redirect()->route('closing.index')->with('success', 'Tutup bulan berhasil! Simpanan dan cicilan otomatis telah dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('closing.index')->with('error', 'Tutup bulan gagal: ' . $e->getMessage());
        }
    }
}