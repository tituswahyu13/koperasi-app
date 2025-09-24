<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class PinjamanController extends Controller
{
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
        return view('pinjaman.create', compact('anggotas'));
    }

    /**
     * Menyimpan pengajuan pinjaman baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_pinjaman' => 'required|numeric|min:100000',
            'jenis_pinjaman' => 'required|string|in:uang,barang', // Tambahkan ini
            'tenor' => 'required|numeric|min:1', // Tambahkan ini
        ]);

        Pinjaman::create([
            'anggota_id' => $validatedData['anggota_id'],
            'jumlah_pinjaman' => $validatedData['jumlah_pinjaman'],
            'jenis_pinjaman' => $validatedData['jenis_pinjaman'],
            'tenor' => $validatedData['tenor'],
            'jumlah_bayar' => 0,
            'bunga' => 0,
            'sisa_pinjaman' => 0,
            'status' => 'pending',
            'tanggal_pengajuan' => now(),
        ]);

        return redirect()->route('pinjaman.index')->with('success', 'Pengajuan pinjaman berhasil dibuat!');
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

        if ($validatedData['status'] == 'approved') {
            $bungaRate = $pinjaman->jenis_pinjaman === 'uang' ? 0.01 : 0.015;
            $bunga = $pinjaman->jumlah_pinjaman * $bungaRate * $pinjaman->tenor;
            $totalPinjaman = $pinjaman->jumlah_pinjaman + $bunga;

            $pinjaman->update([
                'status' => 'approved',
                'bunga' => $bunga,
                'sisa_pinjaman' => $totalPinjaman,
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

        // Cari semua transaksi cicilan yang terkait dengan pinjaman ini
        // kita mencari transaksi simpanan yang berjenis 'cicilan'
        $cicilans = Simpanan::where('anggota_id', $pinjaman->anggota_id)
            ->where('jenis_simpanan', 'cicilan')
            ->where('deskripsi', 'like', '%' . $pinjaman->id . '%')
            ->orderBy('tanggal_simpanan', 'asc')
            ->get();

        return view('pinjaman.show', compact('pinjaman', 'cicilans'));
    }
}
