<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // WAJIB DI-IMPORT
use Carbon\Carbon;

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
        
        // 1. Ambil data sisa hutang pinjaman aktif
        $activeLoans = collect();
        $totalActiveLoan = 0;

        if (Auth::check() && Auth::user()->anggota) {
            $anggotaId = Auth::user()->anggota->id;
            
            // Ambil pinjaman yang statusnya approved dan belum lunas
            $activeLoans = Pinjaman::where('anggota_id', $anggotaId)
                                    ->whereIn('status', ['approved'])
                                    ->where('sisa_pinjaman', '>', 0)
                                    ->get();
            
            // Hitung total sisa tagihan dari semua pinjaman aktif
            $totalActiveLoan = $activeLoans->sum(function ($loan) {
                // UNTUK SIMULASI: Kita ambil sisa_pinjaman sebagai total yang harus dilunasi.
                return $loan->sisa_pinjaman; 
            });
        }

        return view('simulasi.index', compact('loanTypes', 'results', 'totalActiveLoan', 'activeLoans'));
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

        // --- 2. PERHITUNGAN SIMULASI ---

        $bungaTotal = $jumlahPinjaman * $config['bunga'] * $tenor;
        $biayaAdmin = $jumlahPinjaman * $config['admin'];
        $totalTagihan = $jumlahPinjaman + $bungaTotal;
        $angsuranPerBulan = round($totalTagihan / $tenor, 2);
        
        $potonganWajibPinjam = 0;
        if ($validatedData['loan_type'] === 'uang_jk_panjang') {
            $potonganWajibPinjam = $jumlahPinjaman * 0.01;
        }

        // --- 3. AMBIL DATA HUTANG AKTIF ---
        $activeLoans = collect();
        $totalActiveLoan = 0;

        if (Auth::check() && Auth::user()->anggota) {
            $anggotaId = Auth::user()->anggota->id;
            
            $activeLoans = Pinjaman::where('anggota_id', $anggotaId)
                                    ->whereIn('status', ['approved'])
                                    ->where('sisa_pinjaman', '>', 0)
                                    ->get();
            
            $totalActiveLoan = $activeLoans->sum('sisa_pinjaman');
        }

        // --- 4. PERBAIKAN KRITIS: HITUNG UANG BERSIH YANG DITERIMA ---
        // Uang Bersih = Pokok Pinjaman - Biaya Admin - Potongan Wajib Pinjam - TOTAL SISA HUTANG LAMA
        $uangDiterima = $jumlahPinjaman - $biayaAdmin - $potonganWajibPinjam - $totalActiveLoan;
        
        // --- 5. SIAPKAN HASIL SIMULASI ---

        $results = [
            'input' => $validatedData,
            'bunga_total' => $bungaTotal,
            'biaya_admin' => $biayaAdmin,
            'potongan_wajib_pinjam' => $potonganWajibPinjam,
            'total_tagihan' => $totalTagihan,
            'angsuran_per_bulan' => $angsuranPerBulan,
            'uang_diterima' => $uangDiterima, // Nilai sudah dikurangi hutang lama
            'config_label' => $config['label'],
        ];

        $loanTypes = $this->loanTypes;
        return view('simulasi.index', compact('loanTypes', 'results', 'totalActiveLoan', 'activeLoans'));
    }
}