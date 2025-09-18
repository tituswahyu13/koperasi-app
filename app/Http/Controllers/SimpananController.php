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
            'jenis_simpanan' => 'required|string|in:wajib,manasuka,mandiri',
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        Simpanan::create($validatedData);

        $anggota = Anggota::find($validatedData['anggota_id']);
        if ($anggota) {
            switch ($validatedData['jenis_simpanan']) {
                case 'wajib':
                    $anggota->increment('saldo_wajib', $validatedData['jumlah_simpanan']);
                    break;
                case 'manasuka':
                    $anggota->increment('saldo_manasuka', $validatedData['jumlah_simpanan']);
                    break;
                case 'mandiri':
                    $anggota->increment('saldo_mandiri', $validatedData['jumlah_simpanan']);
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
            'jenis_simpanan' => 'required|string|in:wajib,manasuka,mandiri',
            'deskripsi' => 'nullable|string',
            'tanggal_simpanan' => 'required|date',
        ]);

        $anggotaLama = Anggota::find($simpanan->anggota_id);
        $jumlahLama = $simpanan->jumlah_simpanan;
        $jenisLama = $simpanan->jenis_simpanan;

        $simpanan->update($validatedData);

        $anggotaBaru = Anggota::find($validatedData['anggota_id']);
        $jumlahBaru = $validatedData['jumlah_simpanan'];
        $jenisBaru = $validatedData['jenis_simpanan'];

        // Kurangi saldo lama
        if ($anggotaLama) {
            switch ($jenisLama) {
                case 'wajib':
                    $anggotaLama->decrement('saldo_wajib', $jumlahLama);
                    break;
                case 'manasuka':
                    $anggotaLama->decrement('saldo_manasuka', $jumlahLama);
                    break;
                case 'mandiri':
                    $anggotaLama->decrement('saldo_mandiri', $jumlahLama);
                    break;
            }
        }

        // Tambahkan saldo baru
        if ($anggotaBaru) {
            switch ($jenisBaru) {
                case 'wajib':
                    $anggotaBaru->increment('saldo_wajib', $jumlahBaru);
                    break;
                case 'manasuka':
                    $anggotaBaru->increment('saldo_manasuka', $jumlahBaru);
                    break;
                case 'mandiri':
                    $anggotaBaru->increment('saldo_mandiri', $jumlahBaru);
                    break;
            }
        }

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil diperbarui!');
    }

    public function destroy(Simpanan $simpanan)
    {
        $anggota = Anggota::find($simpanan->anggota_id);
        if ($anggota) {
            switch ($simpanan->jenis_simpanan) {
                case 'wajib':
                    $anggota->decrement('saldo_wajib', $simpanan->jumlah_simpanan);
                    break;
                case 'manasuka':
                    $anggota->decrement('saldo_manasuka', $simpanan->jumlah_simpanan);
                    break;
                case 'mandiri':
                    $anggota->decrement('saldo_mandiri', $simpanan->jumlah_simpanan);
                    break;
            }
        }

        $simpanan->delete();

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dihapus!');
    }
}