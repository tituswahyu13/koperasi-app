<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman indeks laporan.
     */
    public function index()
    {
        return view('laporan.index');
    }

    /**
     * Menampilkan laporan simpanan (belum diimplementasikan secara rinci).
     */
    public function simpanan()
    {
        // Data placeholder atau implementasi awal
        $simpanans = Simpanan::with('anggota')->latest()->paginate(10);
        return view('laporan.simpanan', compact('simpanans'));
    }

    /**
     * Menampilkan laporan pinjaman (belum diimplementasikan secara rinci).
     */
    public function pinjaman()
    {
        // Data placeholder atau implementasi awal
        $pinjamans = Pinjaman::with('anggota')->latest()->paginate(10);
        return view('laporan.pinjaman', compact('pinjamans'));
    }

    /**
     * Menampilkan Laporan Arus Kas.
     */
    public function arusKas(Request $request)
    {
        // 1. Ambil filter tanggal
        $start_date = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $end_date = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        // 2. Kumpulkan Transaksi PEMASUKAN
        $pemasukan = [];

        // 2A. Pemasukan dari Simpanan (Pokok, Wajib, Wajib Khusus, Manasuka, Mandiri, Jasa Anggota, Voucher Income)
        $simpananIn = Simpanan::whereBetween('tanggal_simpanan', [$start_date, $end_date])
                              ->get();
        
        foreach ($simpananIn as $transaksi) {
            $kategori = match($transaksi->jenis_simpanan) {
                'pokok', 'wajib', 'wajib_khusus', 'manasuka' => 'Penerimaan Simpanan Wajib & Pokok',
                'mandiri' => 'Penerimaan Simpanan Mandiri',
                'jasa_anggota' => 'Penerimaan Simpanan Jasa Anggota',
                'voucher_income' => 'Pemasukan dari Voucher',
                default => 'Simpanan Lain',
            };

            $pemasukan[] = [
                'tanggal' => $transaksi->tanggal_simpanan,
                'jenis' => 'Pemasukan',
                'kategori' => $kategori,
                'deskripsi' => "Simpanan {$transaksi->jenis_simpanan} dari {$transaksi->anggota->nama_lengkap ?? 'Anggota Dihapus'}",
                'jumlah' => $transaksi->jumlah_simpanan,
            ];
        }

        // 2B. Pemasukan dari Angsuran Pinjaman (Pokok + Bunga)
        $paymentIn = Payment::whereBetween('tanggal_bayar', [$start_date, $end_date])
                             ->get();
                             
        foreach ($paymentIn as $payment) {
            $pinjaman = Pinjaman::find($payment->pinjaman_id);
            $anggotaNama = $pinjaman->anggota->nama_lengkap ?? 'Anggota Dihapus';

            // Pemasukan Pokok Angsuran
            $pemasukan[] = [
                'tanggal' => $payment->tanggal_bayar,
                'jenis' => 'Pemasukan',
                'kategori' => 'Penerimaan Angsuran Pokok',
                'deskripsi' => "Angsuran Pokok Pinjaman #{$payment->pinjaman_id} ({$anggotaNama})",
                'jumlah' => $payment->pokok,
            ];
            
            // Pemasukan Bunga Pinjaman (Ini adalah pendapatan/pendapatan bunga)
            $pemasukan[] = [
                'tanggal' => $payment->tanggal_bayar,
                'jenis' => 'Pemasukan',
                'kategori' => 'Penerimaan Bunga Pinjaman',
                'deskripsi' => "Bunga Angsuran Pinjaman #{$payment->pinjaman_id} ({$anggotaNama})",
                'jumlah' => $payment->bunga,
            ];
        }

        // 3. Kumpulkan Transaksi PENGELUARAN (Untuk saat ini, hanya Penarikan Simpanan Mandiri/Manasuka)
        $pengeluaran = [];
        
        // 3A. Pengeluaran: Penarikan Simpanan (Jika ada fungsionalitas Penarikan/Penarikan Sukarela)
        // Saat ini, belum ada transaksi penarikan yang dicatat di SimpananController, 
        // tapi jika ada, logikanya akan ditempatkan di sini. 
        // Untuk saat ini, kita akan asumsikan pengeluaran adalah Biaya Admin Pinjaman yang diserahkan/dicatat.

        // 3B. Pengeluaran: Potongan Biaya Admin Pinjaman (dianggap sebagai uang yang dikeluarkan/diproses)
        $pinjamanAdmin = Pinjaman::where('status', 'approved')
                                 ->whereBetween('tanggal_pengajuan', [$start_date, $end_date])
                                 ->where('biaya_admin', '>', 0)
                                 ->get();

        foreach ($pinjamanAdmin as $pinjaman) {
             $pengeluaran[] = [
                'tanggal' => $pinjaman->tanggal_pengajuan,
                'jenis' => 'Pengeluaran',
                'kategori' => 'Biaya Administrasi Pinjaman',
                'deskripsi' => "Pencatatan Biaya Admin Pinjaman #{$pinjaman->id}",
                'jumlah' => $pinjaman->biaya_admin,
            ];
        }


        // 4. Gabungkan dan Urutkan Transaksi
        $allTransactions = array_merge($pemasukan, $pengeluaran);
        
        // Urutkan berdasarkan tanggal
        usort($allTransactions, function($a, $b) {
            return Carbon::parse($a['tanggal']) <=> Carbon::parse($b['tanggal']);
        });

        // 5. Kirim data ke view
        return view('laporan.arus-kas', compact('allTransactions', 'start_date', 'end_date'));
    }
}