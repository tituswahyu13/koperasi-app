<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi simpanan.
     */
    public function index()
    {
        $simpanans = Simpanan::with('anggota')->latest()->paginate(10);
        return view('simpanan.index', compact('simpanans'));
    }

    /**
     * Menampilkan form untuk mencatat simpanan baru.
     */
    public function create()
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        return view('simpanan.create', compact('anggotas'));
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
            'jenis_simpanan' => 'required|string|in:harian,bulanan',
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        // Hitung selisih jumlah simpanan
        $selisih = $validatedData['jumlah_simpanan'] - $simpanan->jumlah_simpanan;

        // Perbarui data transaksi
        $simpanan->update($validatedData);

        // Perbarui saldo_simpanan di tabel anggota
        $anggota = Anggota::find($validatedData['anggota_id']);
        $anggota->increment('saldo_simpanan', $selisih);

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil diperbarui!');
    }

    public function destroy(Simpanan $simpanan)
    {
        // Kurangi saldo anggota sebelum menghapus transaksi
        $anggota = Anggota::find($simpanan->anggota_id);
        $anggota->decrement('saldo_simpanan', $simpanan->jumlah_simpanan);

        // Hapus transaksi simpanan
        $simpanan->delete();

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dihapus!');
    }
    /**
     * Menyimpan transaksi simpanan baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah_simpanan' => 'required|numeric|min:0',
            'jenis_simpanan' => 'required|string|in:harian,bulanan',
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        // Catat transaksi simpanan
        Simpanan::create($validatedData);

        // Perbarui saldo_simpanan di tabel anggota
        $anggota = Anggota::find($validatedData['anggota_id']);
        $anggota->increment('saldo_simpanan', $validatedData['jumlah_simpanan']);

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dicatat!');
    }

    // Tambahkan metode show, edit, update, dan destroy nanti
}
