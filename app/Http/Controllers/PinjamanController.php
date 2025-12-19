<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\Payment; // Import model Payment
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PinjamanController extends Controller
{
    // Konfigurasi Jenis Pinjaman dan Bunga/Admin
    protected $loanTypes = [
        'uang_jk_panjang' => ['label' => 'Pinjaman Uang JK Panjang', 'bunga' => 0.01, 'admin' => 0.015],
        'sebrak' => ['label' => 'Pinjaman Sebrak', 'bunga' => 0.02, 'admin' => 0.015],
        'piutang_barang' => ['label' => 'Piutang Barang', 'bunga' => 0.015, 'admin' => 0.015],
    ];

    // ... (index, create, store methods remain the same) ...

    /**
     * Menampilkan daftar semua pengajuan pinjaman.
     */
    public function index()
    {
        $query = Pinjaman::with('anggota')->latest();

        if (!auth()->user()->isAdmin()) {
            $anggotaId = auth()->user()->anggota->id ?? null;
            if (!$anggotaId) {
                return view('pinjaman.index', ['pinjamans' => collect([])]);
            }
            $query->where('anggota_id', $anggotaId);
        }

        $pinjamans = $query->paginate(10);
        return view('pinjaman.index', compact('pinjamans'));
    }

    /**
     * Menampilkan form untuk pengajuan pinjaman.
     */
    public function create()
    {
        // Untuk anggota, mereka hanya bisa mendaftar atas nama sendiri
        $anggotas = auth()->user()->isAdmin() 
            ? Anggota::orderBy('nama_lengkap')->get() 
            : collect([auth()->user()->anggota]);

        $loanTypes = $this->loanTypes;
        return view('pinjaman.create', compact('anggotas', 'loanTypes'));
    }

    /**
     * Menyimpan pengajuan pinjaman baru.
     */
    public function store(Request $request)
    {
        // 1. Force anggota_id if not admin
        if (!auth()->user()->isAdmin()) {
            $request->merge(['anggota_id' => auth()->user()->anggota->id ?? null]);
        }

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
            'loan_type' => $validatedData['loan_type'], 
            'tenor' => $validatedData['tenor'],
            'payment_date_type' => $validatedData['payment_date_type'],
            'deskripsi' => $validatedData['deskripsi'] ?? null,
            'jumlah_bayar' => 0,
            'bunga' => 0,
            'biaya_admin' => 0, 
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
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $pinjaman->load('anggota'); 
        return view('pinjaman.edit', compact('pinjaman'));
    }

    /**
     * Memperbarui status pinjaman (menyetujui atau menolak).
     */
    public function update(Request $request, Pinjaman $pinjaman)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $validatedData = $request->validate([
            'status' => 'required|string|in:approved,rejected',
        ]);

        if ($pinjaman->status != 'pending') {
            return redirect()->route('pinjaman.index')->with('error', 'Pinjaman ini sudah diproses.');
        }
        
        if (!isset($this->loanTypes[$pinjaman->loan_type])) {
            return redirect()->route('pinjaman.index')->with('error', 'Jenis pinjaman tidak valid untuk perhitungan.');
        }
        
        $config = $this->loanTypes[$pinjaman->loan_type];

        if ($validatedData['status'] == 'approved') {
            
            DB::beginTransaction();
            try {
                $jumlahPinjaman = $pinjaman->jumlah_pinjaman;
                $tenor = $pinjaman->tenor;
                $anggota = $pinjaman->anggota;

                // --- 1. HITUNG BIAYA DAN BUNGA ---
                
                $bungaRate = $config['bunga'];
                $bungaTotal = $jumlahPinjaman * $bungaRate * $tenor;
                
                $adminRate = $config['admin'];
                $biayaAdmin = $jumlahPinjaman * $adminRate;

                // Hitung Potongan Wajib Pinjam (1% dari Pokok Pinjaman)
                $potonganRateWajibPinjam = 0.01;
                $potonganWajibPinjam = 0;

                if ($pinjaman->loan_type === 'uang_jk_panjang') {
                    $potonganWajibPinjam = $jumlahPinjaman * $potonganRateWajibPinjam;
                }
                
                $totalPinjamanBersih = $jumlahPinjaman + $bungaTotal;

                // --- 2. PERHITUNGAN JATUH TEMPO ---

                $tanggalJatuhTempo = null;
                $tglPinjaman = Carbon::parse($pinjaman->tanggal_pengajuan);
                
                if ($pinjaman->payment_date_type === 'tgl_1') {
                    $tanggalJatuhTempo = $tglPinjaman->copy()->addMonth()->day(1);
                } elseif ($pinjaman->payment_date_type === 'tgl_15') {
                    $tanggalJatuhTempo = $tglPinjaman->copy()->addMonth()->day(15);
                }
                
                // --- 3. UPDATE SALDO ANGGOTA (Potongan Wajib Pinjam) ---
                
                if ($potonganWajibPinjam > 0) {
                    $anggota->increment('saldo_wajib_pinjam', $potonganWajibPinjam);

                    Simpanan::create([
                        'anggota_id' => $anggota->id,
                        'jumlah_simpanan' => $potonganWajibPinjam,
                        'jenis_simpanan' => 'wajib_pinjam',
                        'deskripsi' => "Potongan 1% wajib pinjam untuk Pinjaman ID #{$pinjaman->id} (Jenis: {$pinjaman->loan_type})",
                        'tanggal_simpanan' => now(),
                    ]);
                }

                // --- 4. UPDATE STATUS PINJAMAN ---
                
                $pinjaman->update([
                    'status' => 'approved',
                    'bunga' => $bungaTotal,
                    'biaya_admin' => $biayaAdmin,
                    'sisa_pinjaman' => $totalPinjamanBersih,
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                ]);
                
                DB::commit();
                return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil disetujui! Saldo Wajib Pinjam telah ditambahkan.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error('Pinjaman approval failed: ' . $e->getMessage()); 
                return redirect()->route('pinjaman.index')->with('error', 'Gagal menyetujui pinjaman. Transaksi dibatalkan: ' . $e->getMessage());
            }
        } else {
            // Status Rejected
            $pinjaman->update(['status' => 'rejected']);
            return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil ditolak.');
        }
    }

    /**
     * Menampilkan detail pinjaman dan riwayat pembayaran cicilan.
     */
    public function show(Pinjaman $pinjaman)
    {
        if (!auth()->user()->isAdmin()) {
            $anggotaId = auth()->user()->anggota->id ?? null;
            if ($pinjaman->anggota_id !== $anggotaId) {
                abort(403, 'Unauthorized action.');
            }
        }
        $pinjaman->load('anggota');
        
        $config = $this->loanTypes[$pinjaman->loan_type] ?? null;

        $angsuranPerBulan = 0;
        $totalTagihan = $pinjaman->jumlah_pinjaman + $pinjaman->bunga;

        if ($pinjaman->status == 'approved' && $pinjaman->tenor > 0) {
            $angsuranPerBulan = round($totalTagihan / $pinjaman->tenor, 2);
        }
        
        // Tambahkan perhitungan potongan wajib pinjam untuk ditampilkan di view
        $potonganWajibPinjam = 0;
        if ($pinjaman->loan_type === 'uang_jk_panjang') {
             $potonganWajibPinjam = $pinjaman->jumlah_pinjaman * 0.01;
        }
        $pinjaman->potongan_wajib_pinjam = $potonganWajibPinjam;
        
        // Ambil riwayat pembayaran dari tabel 'payments' yang baru
        $payments = Payment::where('pinjaman_id', $pinjaman->id)->orderBy('tanggal_bayar', 'asc')->get();
        
        // Hitung total pembayaran yang sudah dilakukan
        $totalDibayar = $payments->sum('total_bayar');

        // Update sisa pinjaman berdasarkan total pembayaran
        $pinjaman->sisa_pinjaman_bersih = $pinjaman->sisa_pinjaman - $totalDibayar;
        if ($pinjaman->sisa_pinjaman_bersih < 0) {
            $pinjaman->sisa_pinjaman_bersih = 0;
        }
        
        // Simpan data perhitungan ke instance pinjaman untuk dilempar ke view
        $pinjaman->total_tagihan = $totalTagihan;
        $pinjaman->angsuran_per_bulan = $angsuranPerBulan;
        $pinjaman->config = $config;

        return view('pinjaman.show', compact('pinjaman', 'payments')); // Menggunakan $payments
    }
    
    /**
     * Menampilkan form untuk pembayaran cicilan manual.
     */
    public function pay(Pinjaman $pinjaman)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        if ($pinjaman->status !== 'approved' || $pinjaman->sisa_pinjaman <= 0) {
            return redirect()->route('pinjaman.show', $pinjaman->id)->with('error', 'Pinjaman tidak valid untuk pembayaran.');
        }
        
        // Hitung total pembayaran yang sudah dilakukan
        $totalDibayar = Payment::where('pinjaman_id', $pinjaman->id)->sum('total_bayar');
        $sisaPokok = $pinjaman->jumlah_pinjaman - Payment::where('pinjaman_id', $pinjaman->id)->sum('pokok');
        $sisaBunga = $pinjaman->bunga - Payment::where('pinjaman_id', $pinjaman->id)->sum('bunga');
        
        // Hitung angsuran ideal (pokok + bunga) per bulan
        $totalTagihan = $pinjaman->jumlah_pinjaman + $pinjaman->bunga;
        $angsuranPerBulan = round($totalTagihan / $pinjaman->tenor, 2);

        return view('pinjaman.pay', compact('pinjaman', 'totalDibayar', 'sisaPokok', 'sisaBunga', 'angsuranPerBulan'));
    }
    
    /**
     * Memproses pembayaran cicilan manual.
     */
    public function processPayment(Request $request, Pinjaman $pinjaman)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // 1. Validasi Input
        $validatedData = $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'deskripsi' => 'nullable|string',
        ]);
        
        // Pastikan pinjaman masih memiliki sisa tagihan
        $totalDibayarSaatIni = Payment::where('pinjaman_id', $pinjaman->id)->sum('total_bayar');
        $sisaTagihan = $pinjaman->jumlah_pinjaman + $pinjaman->bunga - $totalDibayarSaatIni;

        $jumlahBayar = $validatedData['jumlah_bayar'];

        if ($jumlahBayar > $sisaTagihan) {
            return back()->withInput()->with('error', 'Jumlah pembayaran melebihi sisa tagihan pinjaman (Rp ' . number_format($sisaTagihan, 2, ',', '.') . ')');
        }

        DB::beginTransaction();

        try {
            // 2. Tentukan komponen Pokok dan Bunga yang dibayar
            // Metode: Flat Rate (Bunga dihitung proporsional dari total bayar)
            
            $pokokTotal = $pinjaman->jumlah_pinjaman;
            $bungaTotal = $pinjaman->bunga;
            $totalTagihan = $pokokTotal + $bungaTotal;

            // Hitung rasio pembayaran yang dialokasikan ke Pokok dan Bunga
            $rasioPokok = $pokokTotal / $totalTagihan;
            $rasioBunga = $bungaTotal / $totalTagihan;

            $pokokDibayar = round($jumlahBayar * $rasioPokok, 2);
            $bungaDibayar = round($jumlahBayar * $rasioBunga, 2);
            
            // Periksa jika ada pembulatan, pastikan totalnya sama
            if (($pokokDibayar + $bungaDibayar) != $jumlahBayar) {
                // Sesuaikan bunga agar totalnya tepat
                $bungaDibayar = $jumlahBayar - $pokokDibayar;
            }
            
            // 3. Catat Transaksi Pembayaran
            Payment::create([
                'pinjaman_id' => $pinjaman->id,
                'anggota_id' => $pinjaman->anggota_id,
                'pokok' => $pokokDibayar,
                'bunga' => $bungaDibayar,
                'total_bayar' => $jumlahBayar,
                'tanggal_bayar' => $validatedData['tanggal_bayar'],
                'sumber_pembayaran' => 'Manual (Tunai/Non-Otomatis)',
                'deskripsi' => $validatedData['deskripsi'] ?? 'Pembayaran cicilan manual.',
            ]);

            // 4. Update Pinjaman (jumlah_bayar dan status lunas)
            $pinjaman->increment('jumlah_bayar', $jumlahBayar);
            
            // Hitung sisa pinjaman bersih (saldo pinjaman - total bayar)
            $sisaBersih = $sisaTagihan - $jumlahBayar;

            // Perbarui status jika lunas
            if ($sisaBersih <= 0) {
                $pinjaman->status = 'lunas';
                $pinjaman->sisa_pinjaman = 0; // Pastikan sisa pinjaman menjadi nol
            } else {
                 // Perbarui sisa pinjaman (kolom sisa_pinjaman di DB mencatat total bersih)
                 $pinjaman->sisa_pinjaman = $pinjaman->sisa_pinjaman - $jumlahBayar;
            }
            $pinjaman->save(); 
            
            DB::commit();

            if ($sisaBersih <= 0) {
                return redirect()->route('pinjaman.show', $pinjaman->id)->with('success', 'Cicilan lunas! Pinjaman berhasil ditutup.');
            } else {
                return redirect()->route('pinjaman.show', $pinjaman->id)->with('success', 'Pembayaran cicilan Rp ' . number_format($jumlahBayar, 2, ',', '.') . ' berhasil dicatat.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Manual payment failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}