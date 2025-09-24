<?php

namespace App\Http\Controllers;

use App\Models\Simpanan;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan menu laporan.
     */
    public function index()
    {
        return view('laporan.index');
    }

    /**
     * Menampilkan laporan simpanan.
     */
    public function simpanan()
    {
        $simpanans = Simpanan::with('anggota')
            ->latest('tanggal_simpanan')
            ->get();

        return view('laporan.simpanan', compact('simpanans'));
    }

    /**
     * Menampilkan laporan pinjaman.
     */
    public function pinjaman()
    {
        $pinjamans = Pinjaman::with('anggota')
            ->latest('tanggal_pengajuan')
            ->get();

        return view('laporan.pinjaman', compact('pinjamans'));
    }

    /**
     * Menampilkan laporan neraca uang masuk dan keluar.
     */
    public function arusKas()
    {
        // Pemasukan: semua transaksi simpanan, termasuk cicilan pinjaman
        $pemasukan = Simpanan::with('anggota')->latest()->get();
        $totalPemasukan = $pemasukan->sum('jumlah_simpanan');

        // Pengeluaran: semua pinjaman yang disetujui
        $pengeluaran = Pinjaman::with('anggota')
            ->where('status', 'approved')
            ->latest('tanggal_pengajuan')
            ->get();
        $totalPengeluaran = $pengeluaran->sum('jumlah_pinjaman');

        return view('laporan.arus-kas', compact('pemasukan', 'pengeluaran', 'totalPemasukan', 'totalPengeluaran'));
    }
}
