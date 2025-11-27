<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulasiController extends Controller
{
    // Konfigurasi Jenis Pinjaman dan Bunga/Admin (Sama seperti PinjamanController)
    protected $loanTypes = [
        'uang_jk_panjang' => ['label' => 'Pinjaman Uang JK Panjang', 'bunga' => 0.01, 'admin' => 0.015],
        'sebrak' => ['label' => 'Pinjaman Sebrak', 'bunga' => 0.02, 'admin' => 0.015],
        'piutang_barang' => ['label' => 'Piutang Barang', 'bunga' => 0.015, 'admin' => 0.015],
    ];

    /**
     * Menampilkan form simulasi pinjaman.
     */
    public function index()
    {
        $loanTypes = $this->loanTypes;
        $results = null;
        return view('simulasi.index', compact('loanTypes', 'results'));
    }

    /**
     * Memproses perhitungan simulasi pinjaman.
     */
    public function calculate(Request $request)
    {
        // 1. Validasi Input
        $validLoanKeys = array_keys($this->loanTypes);

        $validatedData = $request->validate([
            'jumlah_pinjaman' => 'required|numeric|min:100000',
            'tenor' => 'required|numeric|min:1|max:120',
            'loan_type' => 'required|string|in:' . implode(',', $validLoanKeys),
        ]);
        
        $jumlahPinjaman = $validatedData['jumlah_pinjaman'];
        $tenor = $validatedData['tenor'];
        $config = $this->loanTypes[$validatedData['loan_type']];

        // --- 2. PERHITUNGAN ---

        // Bunga Total = Pokok Pinjaman * Bunga Rate per bulan * Tenor
        $bungaTotal = $jumlahPinjaman * $config['bunga'] * $tenor;
        
        // Biaya Administrasi = Pokok Pinjaman * Admin Rate
        $biayaAdmin = $jumlahPinjaman * $config['admin'];
        
        // Total Tagihan (Pokok + Bunga)
        $totalTagihan = $jumlahPinjaman + $bungaTotal;
        
        // Angsuran Bulanan = Total Tagihan / Tenor
        $angsuranPerBulan = round($totalTagihan / $tenor, 2);
        
        // Potongan Wajib Pinjam (1% dari Pokok Pinjaman)
        $potonganWajibPinjam = 0;
        if ($validatedData['loan_type'] === 'uang_jk_panjang') {
            $potonganWajibPinjam = $jumlahPinjaman * 0.01;
        }

        // Uang Bersih yang Diterima Anggota = Pokok Pinjaman - Biaya Admin - Potongan Wajib Pinjam
        $uangDiterima = $jumlahPinjaman - $biayaAdmin - $potonganWajibPinjam;
        
        // --- 3. SIAPKAN HASIL ---

        $results = [
            'input' => $validatedData,
            'bunga_total' => $bungaTotal,
            'biaya_admin' => $biayaAdmin,
            'potongan_wajib_pinjam' => $potonganWajibPinjam,
            'total_tagihan' => $totalTagihan,
            'angsuran_per_bulan' => $angsuranPerBulan,
            'uang_diterima' => $uangDiterima,
            'config_label' => $config['label'],
        ];

        $loanTypes = $this->loanTypes;
        return view('simulasi.index', compact('loanTypes', 'results'));
    }
}