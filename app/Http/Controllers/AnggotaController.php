<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Diperlukan untuk mengenkripsi password
use Illuminate\Support\Facades\Log; // Untuk logging
use Illuminate\Support\Facades\DB;

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
            // Inputan simpanan wajib per bulan (Simpanan Wajib)
            'simpanan_wajib' => 'required|numeric|min:0',
            // Inputan saldo awal yang akan masuk ke saldo_pokok
            'simpanan_pokok' => 'required|numeric|min:0', 
            // Inputan saldo awal yang akan masuk ke voucher
            'voucher' => 'required|numeric|min:0',
            // Inputan saldo awal yang akan masuk ke simpanan_wajib_khusus
            'simpanan_wajib_khusus' => 'required|numeric|min:0',
            // Inputan saldo awal yang akan masuk ke simpanan_manasuka
            'simpanan_manasuka' => 'required|numeric|min:0',
        ]);

        // Gunakan transaksi untuk memastikan User dan Anggota tercipta bersamaan
        DB::beginTransaction();

        try {
            // 2. Buat Akun User (untuk login)
            $user = User::create([
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // 3. Buat Data Anggota (profil) dan hubungkan dengan User
            Anggota::create([
                'user_id' => $user->id,
                'status_aktif' => 1, // SET DEFAULT VALUE '1'
                'nama_lengkap' => $validatedData['nama_lengkap'],
                'alamat' => $validatedData['alamat'],
                'no_hp' => $validatedData['no_hp'],
                
                // Simpan nilai iuran wajib per bulan
                'simpanan_wajib' => $validatedData['simpanan_wajib'],
                'simpanan_wajib_khusus' => $validatedData['simpanan_wajib_khusus'], // Simpan nilai iuran wajib khusus per bulan
                'simpanan_manasuka' => $validatedData['simpanan_manasuka'], // Simpan nilai iuran manasuka per bulan

                // Inisialisasi Saldo Awal (berdasarkan inputan)
                'saldo_pokok' => $validatedData['simpanan_pokok'], // Simpan Simpanan Pokok ke saldo_pokok
                'voucher' => $validatedData['voucher'], // Simpan Voucher ke kolom voucher
                
                // Saldo lain diinisialisasi 0 (sesuai default kolom)
                // 'saldo_wajib' => 0, // Ditinggalkan, karena ini adalah akumulasi iuran bulanan
                // 'saldo_manasuka' => 0,
                // 'saldo_mandiri' => 0,
                // 'saldo_wajib_pinjam' => 0,
                // 'saldo_jasa_anggota' => 0,
            ]);

            DB::commit();

            return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Anggota: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan anggota: ' . $e->getMessage());
        }
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
    // public function update(Request $request, $id)
    // {
    //     $validatedData = $request->validate([
    //         'password' => 'nullable|string|min:8',
    //         'status_aktif' => 'required|in:0,1',
    //         'nama_lengkap' => 'required|string|max:255',
    //         'alamat' => 'nullable|string',
    //         'no_hp' => 'nullable|string|max:15',
    //         'simpanan_wajib' => 'required|numeric|min:0',
    //         'simpanan_wajib_khusus' => 'required|numeric|min:0',
    //         'simpanan_manasuka' => 'required|numeric|min:0',
    //         'voucher' => 'required|numeric|min:0',
    //     ]);

    //     // Ambil data anggota berdasarkan ID
    //     $anggota = Anggota::findOrFail($id);

    //     // Perbarui data di tabel 'anggotas'
    //     $anggota->update($validatedData);

    //     return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui!');
    // }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            // User Data - Password nullable/opsional saat update
            'password' => 'nullable|string|min:8', 

            // Anggota Data
            'status_aktif' => 'required|in:0,1',
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            
            // Iuran/Saldo yang diizinkan diubah melalui form edit
            'simpanan_wajib' => 'required|numeric|min:0',
            'simpanan_wajib_khusus' => 'required|numeric|min:0',
            'simpanan_manasuka' => 'required|numeric|min:0',
            'voucher' => 'required|numeric|min:0', // Voucher sekarang divalidasi dan diizinkan diubah
        ]);

        // Ambil data anggota dan user terkait
        $anggota = Anggota::with('user')->findOrFail($id);

        DB::beginTransaction();
        try {
            // 2. Update Akun User (Hanya jika password diisi)
            if (!empty($validatedData['password'])) {
                $anggota->user->update([
                    'password' => Hash::make($validatedData['password']),
                ]);
            }

            // 3. Update Data Anggota (profil, iuran, dan voucher)
            // Hapus 'password' dari data yang akan diupdate ke model Anggota
            $anggotaData = $validatedData;
            unset($anggotaData['password']); 

            $anggota->update($anggotaData); // Voucher sekarang akan terupdate

            DB::commit();

            return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Anggota: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui anggota: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data anggota dari database.
     */
    public function destroy(Anggota $anggota)
    {
        // Hapus juga akun user secara permanen
        if ($anggota->user) {
            $anggota->user->delete(); // Ini akan menghapus permanen
        }

        // Hapus data anggota secara soft delete
        $anggota->delete();

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus!');
    }
}
