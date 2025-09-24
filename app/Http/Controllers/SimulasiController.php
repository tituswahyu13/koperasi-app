<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class SimulasiController extends Controller
{
    /**
     * Menampilkan form simulasi pinjaman.
     */
    public function index()
    {
        return view('simulasi.index');
    }

    /**
     * Menghitung dan menampilkan tabel angsuran.
     */
    /**
     * Menghitung dan menampilkan tabel angsuran.
     */
    public function calculate(Request $request)
    {
        $validatedData = $request->validate([
            'jumlah_pinjaman' => 'required|numeric|min:100000',
            'jenis_pinjaman' => 'required|string|in:uang,barang',
            'tenor' => 'required|numeric|min:1',
        ]);

        $jumlahPinjaman = $validatedData['jumlah_pinjaman'];
        $tenor = $validatedData['tenor'];
        $jenisPinjaman = $validatedData['jenis_pinjaman'];

        // Menentukan bunga berdasarkan jenis pinjaman
        $bungaRate = $jenisPinjaman === 'uang' ? 0.01 : 0.015;
        $bungaPerBulan = $jumlahPinjaman * $bungaRate;
        $totalBunga = $bungaPerBulan * $tenor;
        $totalPinjaman = $jumlahPinjaman + $totalBunga;
        $cicilanTotal = $totalPinjaman / $tenor;
        $cicilanPokok = $jumlahPinjaman / $tenor;

        $jadwalAngsuran = [];
        $sisaPokok = $jumlahPinjaman;

        for ($i = 1; $i <= $tenor; $i++) {
            $tanggalJatuhTempo = Carbon::now()->addMonths($i);
            $sisaPokok -= $cicilanPokok;
            $jadwalAngsuran[] = [
                'bulan' => $i,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                'cicilan_pokok' => $cicilanPokok,
                'bunga_angsuran' => $bungaPerBulan,
                'cicilan_total' => $cicilanTotal,
                'sisa_pokok' => $sisaPokok > 0 ? $sisaPokok : 0,
            ];
        }

        return view('simulasi.index', compact(
            'jumlahPinjaman',
            'tenor',
            'jenisPinjaman',
            'bungaRate',
            'totalBunga',
            'totalPinjaman',
            'cicilanPokok',
            'cicilanTotal',
            'jadwalAngsuran'
        ));
    }
}
