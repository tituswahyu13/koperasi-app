<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // WAJIB: Diperlukan untuk transaksi database

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::with('user')->latest()->paginate(10);
        return view('anggota.index', compact('anggotas'));
    }

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (disesuaikan dengan nama field di create.blade.php)
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',

            // Iuran Bulanan
            'simpanan_wajib_per_bulan' => 'required|numeric|min:0',
            'simpanan_manasuka_per_bulan' => 'required|numeric|min:0',
            'simpanan_wajib_khusus_per_bulan' => 'required|numeric|min:0',
            'voucher_awal' => 'nullable|numeric|min:0',

            // Pembayaran Awal (Hanya Pokok dan Voucher)
            'simpanan_pokok_awal' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $tanggal_gabung = now();
            $jumlah_pokok = $validatedData['simpanan_pokok_awal'] ?? 0;
            $jumlah_voucher = $validatedData['voucher_awal'] ?? 0;

            // 2. Buat Akun User
            $user = User::create([
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
                'role' => 0,
            ]);

            // 3. Buat Data Anggota
            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nama_lengkap' => $validatedData['nama_lengkap'],
                'alamat' => $validatedData['alamat'],
                'no_hp' => $validatedData['no_hp'],

                // Iuran Bulanan 
                'simpanan_wajib' => $validatedData['simpanan_wajib_per_bulan'],
                'simpanan_manasuka' => $validatedData['simpanan_manasuka_per_bulan'],
                'simpanan_wajib_khusus' => $validatedData['simpanan_wajib_khusus_per_bulan'],
                'voucher' => $validatedData['voucher_awal'],

                // Saldo Awal
                'saldo_pokok' => $jumlah_pokok,

                // Saldo lainnya default 0
                'saldo_wajib' => 0,
                'saldo_manasuka' => 0,
                'saldo_mandiri' => 0,
                'saldo_wajib_pinjam' => 0,
                'saldo_wajib_khusus' => 0, // <--- DISET 0, KARENA TIDAK ADA PEMBAYARAN AWAL
            ]);

            // 4. Catat Transaksi Simpanan Awal (Hanya Pokok dan Voucher)

            if ($jumlah_pokok > 0) {
                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jumlah_simpanan' => $jumlah_pokok,
                    'jenis_simpanan' => 'pokok',
                    'deskripsi' => 'Simpanan Pokok Awal Saat Pendaftaran',
                    'tanggal_simpanan' => $tanggal_gabung,
                ]);
            }

            // LOGIKA LAMA UNTUK WAJIB KHUSUS DIHAPUS.

            if ($jumlah_voucher > 0) {
                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jumlah_simpanan' => $jumlah_voucher,
                    'jenis_simpanan' => 'voucher',
                    'deskripsi' => 'Input Voucher Awal Saat Pendaftaran',
                    'tanggal_simpanan' => $tanggal_gabung,
                ]);
            }

            DB::commit();

            return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan dan simpanan awal dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat anggota: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat anggota. Silakan coba lagi. ERROR: ' . $e->getMessage());
        }
    }

    public function show(Anggota $anggota)
    {
        // ...
    }

    public function edit(Anggota $anggota)
    {
        $anggota->load('user');
        return view('anggota.edit', compact('anggota'));
    }

    /**
     * Memperbarui data anggota di database.
     */
    public function update(Request $request, Anggota $anggota)
    {
        // 1. Validasi Input (disesuaikan dengan field edit.blade.php)
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',

            // Iuran Bulanan yang Boleh Diubah
            'simpanan_wajib' => 'required|numeric|min:0',
            'simpanan_manasuka' => 'required|numeric|min:0',
            'simpanan_wajib_khusus' => 'required|numeric|min:0',

            // Saldo yang Boleh Diubah (Saldo Mandiri/Voucher)
            'saldo_mandiri' => 'required|numeric|min:0',

            'role' => 'required|numeric|in:0,1',
        ]);

        $anggotaData = [
            'nama_lengkap' => $validatedData['nama_lengkap'],
            'alamat' => $validatedData['alamat'],
            'no_hp' => $validatedData['no_hp'],

            // Iuran Bulanan
            'simpanan_wajib' => $validatedData['simpanan_wajib'],
            'simpanan_manasuka' => $validatedData['simpanan_manasuka'],
            'simpanan_wajib_khusus' => $validatedData['simpanan_wajib_khusus'],

            // Saldo
            'saldo_mandiri' => $validatedData['saldo_mandiri'],
        ];

        // 1. Perbarui data di tabel 'anggotas'
        $anggota->update($anggotaData);

        // 2. Perbarui kolom role di tabel users
        $anggota->user->update([
            'role' => $validatedData['role']
        ]);

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui! Saldo Pokok tetap utuh.');
    }

    public function destroy(Anggota $anggota)
    {
        if ($anggota->user) {
            $anggota->user->delete();
        }
        $anggota->delete();
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus!');
    }
}
