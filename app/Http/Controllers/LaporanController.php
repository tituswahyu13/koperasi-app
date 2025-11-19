<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Payment;
use App\Models\GeneralTransaction; // IMPORT MODEL BARU
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // Konfigurasi Jenis Pinjaman dan Bunga/Admin (Disinkronkan dari PinjamanController)
    protected $loanTypes = [
        'uang_jk_panjang' => ['label' => 'Pinjaman Uang JK Panjang', 'bunga' => 0.01, 'admin' => 0.015],
        'sebrak' => ['label' => 'Pinjaman Sebrak', 'bunga' => 0.02, 'admin' => 0.015],
        'piutang_barang' => ['label' => 'Piutang Barang', 'bunga' => 0.015, 'admin' => 0.015],
    ];

    /**
     * Menampilkan halaman indeks laporan.
     */
    public function index()
    {
        return view('laporan.index');
    }
    
    /**
     * Menampilkan Laporan Neraca.
     */
    public function neraca()
    {
        // --- 1. HITUNG LIABILITAS (KEWAJIBAN KOPERASI KEPADA ANGGOTA) ---
        $totalSimpananAnggota = Anggota::sum('saldo_pokok') + 
                                  Anggota::sum('saldo_wajib') + 
                                  Anggota::sum('saldo_wajib_khusus') + 
                                  Anggota::sum('saldo_manasuka') + 
                                  Anggota::sum('saldo_mandiri') + 
                                  Anggota::sum('saldo_jasa_anggota');

        $totalVoucher = Anggota::sum('voucher');
        
        // --- 2. HITUNG ASET (PIUTANG PINJAMAN) ---
        $pinjamans = Pinjaman::whereIn('status', ['approved'])
                             ->with('payments')
                             ->get();
        
        $totalPiutangPinjaman = $pinjamans->sum(function ($pinjaman) {
            $totalTagihanBersih = $pinjaman->jumlah_pinjaman + $pinjaman->bunga;
            $totalDibayar = $pinjaman->payments->sum('total_bayar');
            // Hanya menghitung sisa tagihan yang masih harus dibayar
            return max(0, $totalTagihanBersih - $totalDibayar);
        });

        // --- 3. HITUNG SALDO KAS (ASET LANCAR) ---
        
        // Pemasukan: Simpanan Anggota + Angsuran Pokok + Bunga + Pemasukan Umum
        $totalPemasukanSimpanan = Simpanan::sum('jumlah_simpanan'); // Termasuk Pokok, Wajib, Voucher Income
        $totalPemasukanPinjaman = Payment::sum('total_bayar'); // Total Angsuran Diterima
        $totalPemasukanUmum = GeneralTransaction::where('type', 'in')->sum('amount'); // Transaksi Operasional
        
        $totalKasMasuk = $totalPemasukanSimpanan + $totalPemasukanPinjaman + $totalPemasukanUmum;

        // Pengeluaran: Biaya Admin Pinjaman (dianggap dikeluarkan/dipotong dari kas saat pinjaman disetujui) + Pengeluaran Umum
        // Catatan: Simpanan Pokok yang dipinjamkan anggota (jumlah_pinjaman) tidak dihitung sebagai pengeluaran kas, karena itu adalah aset piutang.
        $totalPengeluaranAdmin = Pinjaman::sum('biaya_admin');
        $totalPengeluaranUmum = GeneralTransaction::where('type', 'out')->sum('amount'); // Transaksi Operasional
        
        $totalKasKeluar = $totalPengeluaranAdmin + $totalPengeluaranUmum;
        
        $saldoKas = $totalKasMasuk - $totalKasKeluar;

        // --- 4. HITUNG EKUITAS (SHU) ---
        // SHU = (Pendapatan Bunga + Pendapatan Voucher + Pendapatan Umum) - Biaya Operasional
        $totalBungaDiterima = Payment::sum('bunga');
        $totalPendapatanVoucher = Simpanan::where('jenis_simpanan', 'voucher_income')->sum('jumlah_simpanan');
        $totalPendapatanUmum = GeneralTransaction::where('type', 'in')->sum('amount');
        
        $totalBiayaOperasional = GeneralTransaction::where('type', 'out')->sum('amount');
        
        $totalPendapatanKotor = $totalBungaDiterima + $totalPendapatanVoucher + $totalPendapatanUmum;
        
        // SHU Akrual (Laba Bersih Sederhana)
        $shu = $totalPendapatanKotor - $totalBiayaOperasional;
        
        // --- 5. FINALISASI NERACA ---

        $totalAset = $totalPiutangPinjaman + $saldoKas;
        $totalLiabilitasEkuitas = $totalSimpananAnggota + $totalVoucher + $shu; 
        
        // Catatan Penting: Saldo Kas dan SHU dihitung secara terpisah. Neraca harus seimbang (Total Aset = Total Liabilitas + Ekuitas).
        // Perbedaan $totalAset dan $totalLiabilitasEkuitas yang idealnya nol menunjukkan konsistensi data.

        $neracaData = [
            'aset' => [
                'Saldo Kas & Bank' => $saldoKas, // Aset Fisik Uang
                'Piutang Pinjaman' => $totalPiutangPinjaman,
            ],
            'liabilitas_ekuitas' => [
                'Simpanan Anggota' => $totalSimpananAnggota,
                'Voucher Modal' => $totalVoucher,
                'Sisa Hasil Usaha (SHU)' => $shu,
            ],
            'total_aset' => $totalAset,
            'total_liabilitas_ekuitas' => $totalLiabilitasEkuitas,
            'balance_check' => $totalAset - $totalLiabilitasEkuitas, // Idealnya harus 0
        ];

        return view('laporan.neraca', compact('neracaData'));
    }

    /**
     * Menampilkan Laporan Saldo Simpanan per Anggota.
     */
    public function simpanan()
    {
        $anggotas = Anggota::with('user')->get();

        // Siapkan data laporan
        $laporanSimpanan = $anggotas->map(function ($anggota) {
            // Hitung Total Simpanan
            $totalSimpanan = $anggota->saldo_pokok + 
                             $anggota->saldo_wajib + 
                             $anggota->saldo_wajib_khusus + 
                             $anggota->saldo_manasuka + 
                             $anggota->saldo_mandiri +
                             $anggota->saldo_jasa_anggota; 
            
            return [
                'nama_lengkap' => $anggota->nama_lengkap,
                'status_aktif' => $anggota->status_aktif,
                'saldo_pokok' => $anggota->saldo_pokok,
                'saldo_wajib' => $anggota->saldo_wajib,
                'saldo_wajib_khusus' => $anggota->saldo_wajib_khusus,
                'saldo_manasuka' => $anggota->saldo_manasuka,
                'saldo_mandiri' => $anggota->saldo_mandiri,
                'saldo_jasa_anggota' => $anggota->saldo_jasa_anggota,
                'voucher' => $anggota->voucher,
                'total_saldo' => $totalSimpanan,
            ];
        });

        // Hitung Total Keseluruhan (Grand Total)
        $grandTotals = [
            'saldo_pokok' => $laporanSimpanan->sum('saldo_pokok'),
            'saldo_wajib' => $laporanSimpanan->sum('saldo_wajib'),
            'saldo_wajib_khusus' => $laporanSimpanan->sum('saldo_wajib_khusus'),
            'saldo_manasuka' => $laporanSimpanan->sum('saldo_manasuka'),
            'saldo_mandiri' => $laporanSimpanan->sum('saldo_mandiri'),
            'saldo_jasa_anggota' => $laporanSimpanan->sum('saldo_jasa_anggota'),
            'voucher' => $laporanSimpanan->sum('voucher'),
            'total_saldo' => $laporanSimpanan->sum('total_saldo'),
        ];
        
        return view('laporan.simpanan', compact('laporanSimpanan', 'grandTotals'));
    }
    
    public function pinjaman()
    {
        // Ambil semua pinjaman yang berstatus 'approved' atau 'pending'
        $pinjamans = Pinjaman::whereIn('status', ['approved'])
                             ->with('anggota', 'payments')
                             ->get();
        
        $laporanPinjaman = $pinjamans->map(function ($pinjaman) {
            $totalTagihanBersih = $pinjaman->jumlah_pinjaman + $pinjaman->bunga;
            
            // Hitung Total yang sudah dibayar (Pokok + Bunga)
            $totalDibayar = $pinjaman->payments->sum('total_bayar');
            $totalPokokDibayar = $pinjaman->payments->sum('pokok');
            $totalBungaDibayar = $pinjaman->payments->sum('bunga');
            
            // Hitung Sisa
            $sisaTagihan = $totalTagihanBersih - $totalDibayar;
            $sisaPokok = $pinjaman->jumlah_pinjaman - $totalPokokDibayar;
            $sisaBunga = $pinjaman->bunga - $totalBungaDibayar;
            
            // Tentukan Angsuran Bulanan (untuk referensi)
            $angsuranPerBulan = $pinjaman->tenor > 0 ? round($totalTagihanBersih / $pinjaman->tenor, 2) : 0;
            
            // Tentukan Tanggal Jatuh Tempo Berikutnya
            $nextDueDate = null;
            if ($pinjaman->tanggal_jatuh_tempo && $sisaTagihan > 0) {
                // Sederhana: Hitung jumlah bulan yang sudah dibayar (minimal)
                $monthsPaid = floor($totalDibayar / $angsuranPerBulan);
                // Hitung tanggal jatuh tempo berikutnya
                $nextDueDate = Carbon::parse($pinjaman->tanggal_jatuh_tempo)->addMonths($monthsPaid);
            }

            return [
                'id' => $pinjaman->id,
                'anggota_nama' => $pinjaman->anggota->nama_lengkap ?? 'N/A',
                'loan_type' => ucwords(str_replace('_', ' ', $pinjaman->loan_type)),
                'tenor' => $pinjaman->tenor,
                'pokok_pinjaman' => $pinjaman->jumlah_pinjaman,
                'bunga_total' => $pinjaman->bunga,
                'total_tagihan_bersih' => $totalTagihanBersih,
                'angsuran_per_bulan' => $angsuranPerBulan,
                
                'total_dibayar' => $totalDibayar,
                'sisa_tagihan' => max(0, $sisaTagihan), // Pastikan tidak minus
                'sisa_pokok' => max(0, $sisaPokok),
                'sisa_bunga' => max(0, $sisaBunga),

                'next_due_date' => $sisaTagihan > 0 ? $nextDueDate : null,
                'status' => $sisaTagihan <= 0 ? 'Lunas' : $pinjaman->status,
            ];
        })->filter(function ($pinjaman) {
            // Hanya tampilkan yang SISA TAGIHAN > 0
            return $pinjaman['sisa_tagihan'] > 0;
        });

        // Hitung Grand Total Sisa Pinjaman
        $grandTotalSisa = $laporanPinjaman->sum('sisa_tagihan');

        return view('laporan.pinjaman', compact('laporanPinjaman', 'grandTotalSisa'));
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
                              ->with('anggota') 
                              ->get();
        
        foreach ($simpananIn as $transaksi) {
            $kategori = match($transaksi->jenis_simpanan) {
                'pokok', 'wajib', 'wajib_khusus', 'manasuka' => 'Penerimaan Simpanan Wajib & Pokok',
                'mandiri' => 'Penerimaan Simpanan Mandiri',
                'jasa_anggota' => 'Penerimaan Simpanan Jasa Anggota',
                'voucher_income' => 'Pemasukan dari Voucher',
                default => 'Simpanan Lain',
            };

            $anggotaNamaSimpanan = $transaksi->anggota?->nama_lengkap ?? 'Anggota Dihapus';
            
            $pemasukan[] = [
                'tanggal' => $transaksi->tanggal_simpanan,
                'jenis' => 'Pemasukan',
                'kategori' => $kategori,
                'deskripsi' => "Simpanan {$transaksi->jenis_simpanan} dari {$anggotaNamaSimpanan}",
                'jumlah' => $transaksi->jumlah_simpanan,
            ];
        }

        // 2B. Pemasukan dari Angsuran Pinjaman (Pokok + Bunga) 
        $paymentIn = Payment::whereBetween('tanggal_bayar', [$start_date, $end_date])
                             ->get();
                             
        foreach ($paymentIn as $payment) {
            $pinjaman = Pinjaman::with('anggota')->find($payment->pinjaman_id);
            $anggotaNamaPinjaman = $pinjaman?->anggota?->nama_lengkap ?? 'Anggota Dihapus';

            // Pemasukan Pokok Angsuran
            $pemasukan[] = [
                'tanggal' => $payment->tanggal_bayar,
                'jenis' => 'Pemasukan',
                'kategori' => 'Penerimaan Angsuran Pokok',
                'deskripsi' => "Angsuran Pokok Pinjaman #{$payment->pinjaman_id} ({$anggotaNamaPinjaman})",
                'jumlah' => $payment->pokok,
            ];
            
            // Pemasukan Bunga Pinjaman (Ini adalah pendapatan/pendapatan bunga)
            $pemasukan[] = [
                'tanggal' => $payment->tanggal_bayar,
                'jenis' => 'Pemasukan',
                'kategori' => 'Penerimaan Bunga Pinjaman',
                'deskripsi' => "Bunga Angsuran Pinjaman #{$payment->pinjaman_id} ({$anggotaNamaPinjaman})",
                'jumlah' => $payment->bunga,
            ];
        }

        // 2C. Pemasukan dari Transaksi Operasional Umum (BARU)
        $generalIn = GeneralTransaction::where('type', 'in')
                                       ->whereBetween('transaction_date', [$start_date, $end_date])
                                       ->get();
        
        foreach ($generalIn as $transaksi) {
            $pemasukan[] = [
                'tanggal' => $transaksi->transaction_date,
                'jenis' => 'Pemasukan',
                'kategori' => "Pemasukan Umum ({$transaksi->category})",
                'deskripsi' => $transaksi->description,
                'jumlah' => $transaksi->amount,
            ];
        }

        // 3. Kumpulkan Transaksi PENGELUARAN
        $pengeluaran = [];
        
        // 3A. Pengeluaran: Potongan Biaya Admin Pinjaman 
        $pinjamanAdmin = Pinjaman::where('status', 'approved')
                                 ->whereBetween('tanggal_pengajuan', [$start_date, $end_date])
                                 ->where('biaya_admin', '>', 0)
                                 ->with('anggota') 
                                 ->get();

        foreach ($pinjamanAdmin as $pinjaman) {
            $anggotaNamaAdmin = $pinjaman->anggota?->nama_lengkap ?? 'Anggota Dihapus';

             $pengeluaran[] = [
                'tanggal' => $pinjaman->tanggal_pengajuan,
                'jenis' => 'Pengeluaran',
                'kategori' => 'Biaya Administrasi Pinjaman',
                'deskripsi' => "Pencatatan Biaya Admin Pinjaman #{$pinjaman->id} ({$anggotaNamaAdmin})",
                'jumlah' => $pinjaman->biaya_admin,
            ];
        }

        // 3B. Pengeluaran dari Transaksi Operasional Umum (BARU)
        $generalOut = GeneralTransaction::where('type', 'out')
                                        ->whereBetween('transaction_date', [$start_date, $end_date])
                                        ->get();

        foreach ($generalOut as $transaksi) {
            $pengeluaran[] = [
                'tanggal' => $transaksi->transaction_date,
                'jenis' => 'Pengeluaran',
                'kategori' => "Pengeluaran Umum ({$transaksi->category})",
                'deskripsi' => $transaksi->description,
                'jumlah' => $transaksi->amount,
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