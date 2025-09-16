<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Pastikan variabel-variabel ini didefinisikan dengan benar
        $totalAnggota = Anggota::count();
        $totalSimpananWajib = Anggota::sum('saldo_wajib');
        $totalSimpananManasuka = Anggota::sum('saldo_manasuka');
        $totalSimpanan = $totalSimpananWajib + $totalSimpananManasuka;

        // Perbaiki di sini: ubah 'pinjamen' menjadi 'pinjamans'
        $totalPinjaman = Pinjaman::where('status', 'approved')->sum('jumlah_pinjaman');

        // Perbaiki di sini: ubah 'pinjamen' menjadi 'pinjamans'
        $pinjamanJatuhTempo = Pinjaman::where('status', 'approved')
            ->where('sisa_pinjaman', '>', 0)
            ->whereDate('tanggal_jatuh_tempo', '<=', Carbon::now()->addDays(7))
            ->count();

        $simpananBulanan = Simpanan::selectRaw('MONTH(tanggal_simpanan) as bulan, SUM(jumlah_simpanan) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Pastikan semua variabel di-compact di sini
        return view('dashboard', compact(
            'totalAnggota',
            'totalSimpanan',
            'totalPinjaman',
            'pinjamanJatuhTempo',
            'simpananBulanan'
        ));
    }
}
