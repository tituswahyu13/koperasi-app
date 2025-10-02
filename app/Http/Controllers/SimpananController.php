<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $simpanans = Simpanan::with('anggota')->latest()->paginate(10);
        return view('simpanan.index', compact('simpanans'));
    }

    public function create()
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.create', compact('anggotas'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_simpanan' => 'required|numeric|min:0',
            // Tambahkan jenis simpanan baru untuk validasi
            'jenis_simpanan' => 'required|string|in:wajib,manasuka,mandiri,pokok,wajib_khusus,wajib_pinjam,voucher,cicilan', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        Simpanan::create($validatedData);

        $anggota = Anggota::find($validatedData['anggota_id']);
        if ($anggota) {
            // Tentukan kolom saldo mana yang akan di-increment
            switch ($validatedData['jenis_simpanan']) {
                case 'pokok':
                    $anggota->increment('saldo_pokok', $validatedData['jumlah_simpanan']);
                    break;
                case 'wajib':
                    $anggota->increment('saldo_wajib', $validatedData['jumlah_simpanan']);
                    break;
                case 'wajib_khusus':
                    $anggota->increment('saldo_wajib_khusus', $validatedData['jumlah_simpanan']);
                    break;
                case 'wajib_pinjam':
                    $anggota->increment('saldo_wajib_pinjam', $validatedData['jumlah_simpanan']);
                    break;
                case 'manasuka':
                    $anggota->increment('saldo_manasuka', $validatedData['jumlah_simpanan']);
                    break;
                case 'mandiri':
                    $anggota->increment('saldo_mandiri', $validatedData['jumlah_simpanan']);
                    break;
                case 'cicilan':
                    // Cicilan pinjaman menambah saldo wajib pinjam/pokok pinjaman (sesuai kebijakan)
                    // Untuk kesederhanaan, kita arahkan ke saldo wajib pinjam sebagai pembayaran hutang
                    $anggota->increment('saldo_wajib_pinjam', $validatedData['jumlah_simpanan']);
                    break;
                case 'voucher':
                    // Voucher tidak menambah saldo, hanya dicatat sebagai transaksi
                    break;
            }
        }

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dicatat!');
    }

    public function show(Simpanan $simpanan)
    {
        $simpanan->load('anggota');
        return view('simpanan.show', compact('simpanan'));
    }

    public function edit(Simpanan $simpanan)
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.edit', compact('simpanan', 'anggotas'));
    }

    public function update(Request $request, Simpanan $simpanan)
    {
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_simpanan' => 'required|numeric|min:0',
            // Perbarui validasi
            'jenis_simpanan' => 'required|string|in:wajib,manasuka,mandiri,pokok,wajib_khusus,wajib_pinjam,voucher,cicilan', 
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        $anggotaLama = Anggota::find($simpanan->anggota_id);
        $jumlahLama = $simpanan->jumlah_simpanan;
        $jenisLama = $simpanan->jenis_simpanan;

        // 1. Perbarui data transaksi
        $simpanan->update($validatedData);

        $anggotaBaru = Anggota::find($validatedData['anggota_id']);
        $jumlahBaru = $validatedData['jumlah_simpanan'];
        $jenisBaru = $validatedData['jenis_simpanan'];

        // Fungsi helper untuk mendapatkan nama kolom saldo
        $getSaldoColumn = function ($jenis) {
            return match ($jenis) {
                'pokok' => 'saldo_pokok',
                'wajib' => 'saldo_wajib',
                'wajib_khusus' => 'saldo_wajib_khusus',
                'wajib_pinjam', 'cicilan' => 'saldo_wajib_pinjam',
                'manasuka' => 'saldo_manasuka',
                'mandiri' => 'saldo_mandiri',
                default => null,
            };
        };

        // 2. Kurangi saldo lama
        $kolomLama = $getSaldoColumn($jenisLama);
        if ($anggotaLama && $kolomLama) {
            $anggotaLama->decrement($kolomLama, $jumlahLama);
        }

        // 3. Tambahkan saldo baru
        $kolomBaru = $getSaldoColumn($jenisBaru);
        if ($anggotaBaru && $kolomBaru) {
            $anggotaBaru->increment($kolomBaru, $jumlahBaru);
        }

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil diperbarui!');
    }

    public function destroy(Simpanan $simpanan)
    {
        $anggota = Anggota::find($simpanan->anggota_id);

        $getSaldoColumn = function ($jenis) {
            return match ($jenis) {
                'pokok' => 'saldo_pokok',
                'wajib' => 'saldo_wajib',
                'wajib_khusus' => 'saldo_wajib_khusus',
                'wajib_pinjam', 'cicilan' => 'saldo_wajib_pinjam',
                'manasuka' => 'saldo_manasuka',
                'mandiri' => 'saldo_mandiri',
                default => null,
            };
        };
        
        $kolomLama = $getSaldoColumn($simpanan->jenis_simpanan);

        if ($anggota && $kolomLama) {
            $anggota->decrement($kolomLama, $simpanan->jumlah_simpanan);
        }

        $simpanan->delete();

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dihapus!');
    }
}