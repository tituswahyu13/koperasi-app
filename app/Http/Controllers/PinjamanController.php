<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added for transaction safety
use Carbon\Carbon;

class PinjamanController extends Controller
{
    // Konfigurasi Jenis Pinjaman dan Bunga/Admin
    protected $loanTypes = [
        'uang_jk_panjang' => ['label' => 'Pinjaman Uang JK Panjang', 'bunga' => 0.01, 'admin' => 0.015],
        'sebrak' => ['label' => 'Pinjaman Sebrak', 'bunga' => 0.02, 'admin' => 0.015],
        'piutang_barang' => ['label' => 'Piutang Barang', 'bunga' => 0.015, 'admin' => 0.015],
    ];

    /**
     * Menampilkan daftar semua pengajuan pinjaman.
     */
    public function index()
    {
        $pinjamans = Pinjaman::with('anggota')->latest()->paginate(10);
        return view('pinjaman.index', compact('pinjamans'));
    }

    /**
     * Menampilkan form untuk pengajuan pinjaman.
     */
    public function create()
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        $loanTypes = $this->loanTypes; // Kirim konfigurasi ke view
        return view('pinjaman.create', compact('anggotas', 'loanTypes'));
    }

    /**
     * Menyimpan pengajuan pinjaman baru.
     */
    public function store(Request $request)
    {
        // Mendapatkan kunci jenis pinjaman yang valid
        $validLoanKeys = array_keys($this->loanTypes);
        
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_pinjaman' => 'required|numeric|min:100000',
            'tenor' => 'required|numeric|min:1',
            'loan_type' => 'required|string|in:' . implode(',', $validLoanKeys), // Jenis Pinjaman
            'payment_date_type' => 'required|string|in:tgl_1,tgl_15,manual', // Metode Pembayaran
            'tanggal_pinjaman' => 'required|date',
            'deskripsi' => 'nullable|string',
        ]);

        Pinjaman::create([
            'anggota_id' => $validatedData['anggota_id'],
            'jumlah_pinjaman' => $validatedData['jumlah_pinjaman'],
            // Simpan jenis pinjaman
            'loan_type' => $validatedData['loan_type'], 
            'tenor' => $validatedData['tenor'],
            'payment_date_type' => $validatedData['payment_date_type'],
            'deskripsi' => $validatedData['deskripsi'] ?? null,
            // Nilai Awal untuk status pending
            'jumlah_bayar' => 0,
            'bunga' => 0,
            'biaya_admin' => 0, // Inisialisasi biaya admin
            'sisa_pinjaman' => 0,
            'status' => 'pending',
            'tanggal_pengajuan' => $validatedData['tanggal_pinjaman'],
        ]);

        return redirect()->route('pinjaman.index')->with('success', 'Pengajuan pinjaman berhasil dibuat!');
    }

    /**
     * Menampilkan form persetujuan pinjaman (edit view).
     */
    public function edit(Pinjaman $pinjaman)
    {
        // Memuat data anggota agar bisa ditampilkan di view
        $pinjaman->load('anggota'); 
        
        // Kita langsung tampilkan view persetujuan (edit)
        return view('pinjaman.edit', compact('pinjaman'));
    }

    /**
     * Memperbarui status pinjaman (menyetujui atau menolak).
     */
    public function update(Request $request, Pinjaman $pinjaman)
    {
        $validatedData = $request->validate([
            'status' => 'required|string|in:approved,rejected',
        ]);

        if ($pinjaman->status != 'pending') {
            return redirect()->route('pinjaman.index')->with('error', 'Pinjaman ini sudah diproses.');
        }
        
        // Pastikan pinjaman memiliki loan_type yang valid sebelum menghitung
        if (!isset($this->loanTypes[$pinjaman->loan_type])) {
            return redirect()->route('pinjaman.index')->with('error', 'Jenis pinjaman tidak valid untuk perhitungan.');
        }
        
        $config = $this->loanTypes[$pinjaman->loan_type];

        if ($validatedData['status'] == 'approved') {
            $jumlahPinjaman = $pinjaman->jumlah_pinjaman;
            $tenor = $pinjaman->tenor;

            // Hitung Bunga Total = Jumlah Pinjaman * Bunga Rate per bulan * Tenor
            $bungaRate = $config['bunga'];
            $bungaTotal = $jumlahPinjaman * $bungaRate * $tenor;
            
            // Hitung Biaya Admin = Jumlah Pinjaman * Admin Rate
            $adminRate = $config['admin'];
            $biayaAdmin = $jumlahPinjaman * $adminRate;

            // Total Pinjaman yang harus dibayar = Jumlah Pinjaman + Bunga Total
            $totalPinjamanBersih = $jumlahPinjaman + $bungaTotal;

            // Perhitungan Jatuh Tempo Angsuran Pertama (Sederhana)
            $tanggalJatuhTempo = null;
            $tglPinjaman = Carbon::parse($pinjaman->tanggal_pengajuan);
            
            // Logika penentuan tanggal jatuh tempo
            if ($pinjaman->payment_date_type === 'tgl_1') {
                // Tentukan tanggal 1 bulan berikutnya
                $tanggalJatuhTempo = $tglPinjaman->copy()->addMonth()->day(1);
            } elseif ($pinjaman->payment_date_type === 'tgl_15') {
                 // Tentukan tanggal 15 bulan berikutnya
                $tanggalJatuhTempo = $tglPinjaman->copy()->addMonth()->day(15);
            }
            // Jika 'manual', tanggal jatuh tempo akan null/diabaikan di sini

            $pinjaman->update([
                'status' => 'approved',
                'bunga' => $bungaTotal,
                'biaya_admin' => $biayaAdmin,
                'sisa_pinjaman' => $totalPinjamanBersih,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            ]);
            
            return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil disetujui!');
        } else {
            $pinjaman->update(['status' => 'rejected']);
            return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil ditolak.');
        }
    }

    /**
     * Menampilkan detail pinjaman dan riwayat pembayaran cicilan.
     */
    public function show(Pinjaman $pinjaman)
    {
        // Muat data anggota yang terkait dengan pinjaman
        $pinjaman->load('anggota');
        
        // Mendapatkan konfigurasi pinjaman
        $config = $this->loanTypes[$pinjaman->loan_type] ?? null;

        // Perhitungan Angsuran Bulanan (Total Tagihan / Tenor)
        $angsuranPerBulan = 0;
        // Total Tagihan = Pokok Pinjaman + Bunga Total
        $totalTagihan = $pinjaman->jumlah_pinjaman + $pinjaman->bunga;

        if ($pinjaman->status == 'approved' && $pinjaman->tenor > 0) {
            // Gunakan presisi 2 desimal untuk angsuran
            $angsuranPerBulan = round($totalTagihan / $pinjaman->tenor, 2);
        }
        
        // Simpan data perhitungan ke instance pinjaman untuk dilempar ke view
        $pinjaman->total_tagihan = $totalTagihan;
        $pinjaman->angsuran_per_bulan = $angsuranPerBulan;
        $pinjaman->config = $config;

        // Cari semua transaksi cicilan yang terkait dengan pinjaman ini
        // kita mencari transaksi simpanan yang berjenis 'cicilan'
        $cicilans = Simpanan::where('anggota_id', $pinjaman->anggota_id)
            ->where('jenis_simpanan', 'cicilan')
            // Mencari Pinjaman ID di dalam kolom deskripsi
            ->where('deskripsi', 'like', '%' . $pinjaman->id . '%') 
            ->orderBy('tanggal_simpanan', 'asc')
            ->get();

        return view('pinjaman.show', compact('pinjaman', 'cicilans'));
    }
}