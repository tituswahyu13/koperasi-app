<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Diperlukan untuk mengenkripsi password
use Illuminate\Support\Facades\Log; // Untuk logging


class AnggotaController extends Controller
{
    /**
     * Menampilkan daftar semua anggota.
     */
    public function index()
    {
        $anggotas = Anggota::with('user')->latest()->paginate(10);
        return view('anggota.index', compact('anggotas'));
    }

    /**
     * Menampilkan form untuk membuat anggota baru.
     */
    public function create()
    {
        return view('anggota.create');
    }

    /**
     * Menyimpan data anggota baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'simpanan_wajib' => 'required|numeric|min:0',
            'simpanan_manasuka' => 'required|numeric|min:0',
        ]);

        // 2. Buat Akun User (untuk login)
        $user = User::create([
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // 3. Buat Data Anggota (profil) dan hubungkan dengan User
        Anggota::create([
            'user_id' => $user->id,
            'nama_lengkap' => $validatedData['nama_lengkap'],
            'alamat' => $validatedData['alamat'],
            'no_hp' => $validatedData['no_hp'],
            'simpanan_wajib' => $validatedData['simpanan_wajib'],
            'simpanan_manasuka' => $validatedData['simpanan_manasuka'],
        ]);

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail anggota.
     */
    public function show(Anggota $anggota)
    {
        // Logika untuk menampilkan detail
    }

    /**
     * Menampilkan form untuk mengedit anggota.
     */
    public function edit($id)
    {
        // Debug: Log parameter yang diterima
        Log::info('Edit method called with ID:', ['id' => $id]);

        // Ambil data anggota berdasarkan ID
        $anggota = Anggota::with('user')->findOrFail($id);

        // Debug: Log data anggota
        Log::info('Edit Anggota Data:', [
            'id' => $anggota->id,
            'nama_lengkap' => $anggota->nama_lengkap,
            'alamat' => $anggota->alamat,
            'no_hp' => $anggota->no_hp,
            'user_id' => $anggota->user_id,
            'user_loaded' => $anggota->user ? 'yes' : 'no',
            'username' => $anggota->user ? $anggota->user->username : 'null'
        ]);

        return view('anggota.edit', compact('anggota'));
    }

    /**
     * Memperbarui data anggota di database.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'simpanan_wajib' => 'required|numeric|min:0',
            'simpanan_manasuka' => 'required|numeric|min:0',
        ]);

        // Ambil data anggota berdasarkan ID
        $anggota = Anggota::findOrFail($id);

        // Perbarui data di tabel 'anggotas'
        $anggota->update($validatedData);

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui!');
    }

    /**
     * Menghapus data anggota dari database.
     */
    public function destroy($id)
    {
        // Ambil data anggota berdasarkan ID
        $anggota = Anggota::with('user')->findOrFail($id);

        // Hapus data anggota
        $anggota->delete();

        // Hapus juga akun user yang terhubung
        if ($anggota->user) {
            $anggota->user->delete();
        }

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus!');
    }
}
