<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\Payment; // Import Model Payment
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Untuk debugging

class ClosingController extends Controller
{
    /**
     * Menampilkan form atau informasi untuk menjalankan Tutup Bulan.
     */
    public function index()
    {
        // Mendapatkan bulan dan tahun saat ini sebagai referensi default
        $today = Carbon::now();
        // Atur tanggal default ke tanggal 1 (atau hari ini jika itu tanggal 1 atau 15)
        $defaultDate = $today->day(1);
        if ($today->day === 1 || $today->day === 15) {
            $defaultDate = $today;
        }

        $nextMonth = $defaultDate->copy()->format('F Y');
        
        return view('closing.index', compact('defaultDate', 'nextMonth'));
    }

    /**
     * Menjalankan proses Tutup Bulan.
     */
    public function process(Request $request)
    {
        // Validasi bulan dan tahun yang akan diproses
        $request->validate([
            'process_date' => 'required|date_format:Y-m-d',
        ]);

        $processDate = Carbon::parse($request->input('process_date'));
        $month = $processDate->month;
        $year = $processDate->year;
        $processDay = $processDate->day;

        // PENCEGAHAN DOUBLE CLOSING
        // Cek apakah Simpanan Wajib (hanya yang dicatat di tgl 1) sudah ada di tanggal yang sama
        $checkExisting = Simpanan::where('jenis_simpanan', 'wajib')
                                 ->whereDate('tanggal_simpanan', $processDate->format('Y-m-d'))
                                 ->exists();

        if ($checkExisting) {
            return redirect()->route('closing.index')->with('error', "Proses Tutup Bulan untuk tanggal {$processDate->format('d-m-Y')} sudah pernah dilakukan. Batalkan proses jika Anda ingin mengulang.");
        }
        
        // Logika Tutup Bulan
        DB::beginTransaction();

        try {
            // Ambil semua anggota aktif
            $anggotas = Anggota::where('status_aktif', 1)->get();
            $totalSimpananProcessed = 0;
            $totalPinjamanProcessed = 0;
            $totalVoucherIncome = 0;
            $logs = [];

            // 1. PROSES SIMPANAN WAJIB, WAJIB KHUSUS, MANASUKA, VOUCHER
            foreach ($anggotas as $anggota) {
                $logs[] = "--- Memproses Anggota: {$anggota->nama_lengkap} (ID: {$anggota->id}) ---";

                // Tanggal transaksi harus sesuai dengan tanggal proses yang dipilih
                $dateTgl1 = $processDate->copy()->day(1); 
                $dateTgl15 = $processDate->copy()->day(15); 
                
                // Cek apakah hari proses sama dengan hari jatuh tempo Simpanan Wajib (Tgl 1)
                if ($processDay === 1) {
                    // --- Simpanan Wajib (Tgl 1) ---
                    if ($anggota->simpanan_wajib > 0) {
                        $anggota->increment('saldo_wajib', $anggota->simpanan_wajib);
                        $this->recordSimpanan($anggota, $anggota->simpanan_wajib, 'wajib', 'Iuran Wajib Bulanan', $dateTgl1);
                        $totalSimpananProcessed += $anggota->simpanan_wajib;
                        $logs[] = "  + Saldo Wajib: + Rp " . number_format($anggota->simpanan_wajib);
                    }
                    
                    // --- Simpanan Manasuka (Tgl 1) ---
                    if ($anggota->simpanan_manasuka > 0) {
                        $anggota->increment('saldo_manasuka', $anggota->simpanan_manasuka);
                        $this->recordSimpanan($anggota, $anggota->simpanan_manasuka, 'manasuka', 'Iuran Manasuka Bulanan', $dateTgl1);
                        $totalSimpananProcessed += $anggota->simpanan_manasuka;
                        $logs[] = "  + Saldo Manasuka: + Rp " . number_format($anggota->simpanan_manasuka);
                    }

                    // --- Voucher (Tgl 1) - DICATAT SEBAGAI PEMASUKAN KOPERASI (INCOME) ---
                    if ($anggota->voucher > 0) {
                        $this->recordSimpanan($anggota, $anggota->voucher, 'voucher_income', 'Pemasukan dari Voucher Anggota', $dateTgl1);
                        $totalVoucherIncome += $anggota->voucher;
                        $logs[] = "  + Pemasukan Voucher (Income): Rp " . number_format($anggota->voucher);
                    }
                }
                
                // Cek apakah hari proses sama dengan hari jatuh tempo Simpanan Wajib Khusus (Tgl 15)
                if ($processDay === 15) {
                    // --- Simpanan Wajib Khusus (Tgl 15) ---
                    if ($anggota->simpanan_wajib_khusus > 0) {
                        $anggota->increment('saldo_wajib_khusus', $anggota->simpanan_wajib_khusus);
                        $this->recordSimpanan($anggota, $anggota->simpanan_wajib_khusus, 'wajib_khusus', 'Iuran Wajib Khusus Bulanan', $dateTgl15);
                        $totalSimpananProcessed += $anggota->simpanan_wajib_khusus;
                        $logs[] = "  + Saldo Wajib Khusus: + Rp " . number_format($anggota->simpanan_wajib_khusus);
                    }
                }
            }
            
            // 2. PROSES ANGSURAN PINJAMAN OTOMATIS
            
            // Ambil semua pinjaman yang approved, tidak lunas, dan tidak manual
            $pinjamans = Pinjaman::where('status', 'approved')
                                    ->where('sisa_pinjaman', '>', 0) // Pastikan sisa pinjaman > 0
                                    ->whereIn('payment_date_type', ['tgl_1', 'tgl_15'])
                                    ->get();

            foreach ($pinjamans as $pinjaman) {
                
                $totalDibayarSaatIni = Payment::where('pinjaman_id', $pinjaman->id)->sum('total_bayar');
                $totalTagihanBersih = $pinjaman->jumlah_pinjaman + $pinjaman->bunga;
                $sisaTagihan = $totalTagihanBersih - $totalDibayarSaatIni;

                if ($sisaTagihan <= 0) continue; 

                $tenor = $pinjaman->tenor;
                $angsuranPerBulan = round($totalTagihanBersih / $tenor, 2);
                
                $pokokTotal = $pinjaman->jumlah_pinjaman;
                $bungaTotal = $pinjaman->bunga;

                // Tentukan Tanggal Bayar berdasarkan payment_date_type
                $tanggalBayar = null;
                if ($pinjaman->payment_date_type === 'tgl_1') {
                    $tanggalBayar = $processDate->copy()->day(1);
                } elseif ($pinjaman->payment_date_type === 'tgl_15') {
                    $tanggalBayar = $processDate->copy()->day(15);
                }

                // Cek apakah hari proses sama dengan hari jatuh tempo angsuran pinjaman
                if ($tanggalBayar && $tanggalBayar->day === $processDay) { 
                    
                    // Alokasi Pembayaran (Potongan proporsional dari total tagihan)
                    $angsuranAkanDibayar = min($angsuranPerBulan, $sisaTagihan);

                    $rasioPokok = $pokokTotal / $totalTagihanBersih;
                    $pokokDibayar = round($angsuranAkanDibayar * $rasioPokok, 2);
                    $bungaDibayar = $angsuranAkanDibayar - $pokokDibayar;

                    // --- 2A. CATAT TRANSAKSI PAYMENT BARU ---
                    Payment::create([
                        'pinjaman_id' => $pinjaman->id,
                        'anggota_id' => $pinjaman->anggota_id,
                        'pokok' => $pokokDibayar,
                        'bunga' => $bungaDibayar,
                        'total_bayar' => $angsuranAkanDibayar,
                        'tanggal_bayar' => $tanggalBayar,
                        'sumber_pembayaran' => 'Tutup Bulan Otomatis',
                        'deskripsi' => "Angsuran Otomatis Bulan {$month}/{$year} untuk Pinjaman ID #{$pinjaman->id}",
                    ]);

                    // --- 2B. UPDATE STATUS LUNAS JIKA SUDAH LUNAS ---
                    $sisaSetelahBayar = $sisaTagihan - $angsuranAkanDibayar;
                    if ($sisaSetelahBayar <= 0) {
                        $pinjaman->status = 'lunas';
                        $pinjaman->sisa_pinjaman = 0; 
                    }

                    $pinjaman->save();
                    
                    $totalPinjamanProcessed += $angsuranAkanDibayar;
                    $logs[] = "  - Pinjaman #{$pinjaman->id}: Bayar Rp " . number_format($angsuranAkanDibayar) . " (Sisa Tagihan: Rp " . number_format($sisaSetelahBayar) . ")";
                }
            }


            DB::commit();
            
            // Simpan log ke session
            $logSummary = "Proses Tutup Bulan {$month}/{$year} Selesai.";
            $logSummary .= "\nTotal Simpanan Wajib & Manasuka Diterima: Rp " . number_format($totalSimpananProcessed);
            $logSummary .= "\nTotal Pemasukan Voucher Dicatat: Rp " . number_format($totalVoucherIncome);
            $logSummary .= "\nTotal Angsuran Pinjaman Otomatis Diterima: Rp " . number_format($totalPinjamanProcessed);
            
            Log::info("Closing Process Log ({$month}/{$year})", $logs); // Log detail ke log file

            return redirect()->route('closing.index')->with('success', $logSummary);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Closing process failed: ' . $e->getMessage());
            return redirect()->route('closing.index')->with('error', 'Proses Tutup Bulan GAGAL. Pesan Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper function untuk mencatat transaksi simpanan
     */
    private function recordSimpanan(Anggota $anggota, $jumlah, $jenis, $deskripsi, $tanggal)
    {
        Simpanan::create([
            'anggota_id' => $anggota->id,
            'jumlah_simpanan' => $jumlah,
            'jenis_simpanan' => $jenis,
            'deskripsi' => $deskripsi,
            'tanggal_simpanan' => $tanggal,
        ]);
    }
}