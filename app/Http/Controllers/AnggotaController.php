<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Diperlukan untuk mengenkripsi password
use Illuminate\Support\Facades\Log; // Untuk logging
use Illuminate\Support\Facades\DB;
use App\Models\Simpanan;

class AnggotaController extends Controller
{
    /**
     * Menampilkan daftar semua anggota.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Anggota::query();

        // Restriction: If not admin, only show self
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('no_anggota', 'like', "%{$search}%");
        }

        $anggotas = $query->paginate(10);

        return view('anggota.index', compact('anggotas'));
    }

    /**
     * Menampilkan form untuk membuat anggota baru.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('anggota.create');
    }

    /**
     * Menyimpan data anggota baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (menggunakan nama field dari form create.blade.php)
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',

            // Iuran Bulanan
            'simpanan_wajib_per_bulan' => 'required|numeric|min:0',
            'simpanan_wajib_khusus_per_bulan' => 'required|numeric|min:0',
            'simpanan_manasuka_per_bulan' => 'required|numeric|min:0',

            // Saldo Awal
            'simpanan_pokok_awal' => 'required|numeric|min:0',
            'voucher_awal' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $tanggal_gabung = now();
            $jumlah_pokok = $validatedData['simpanan_pokok_awal'];
            $jumlah_voucher = $validatedData['voucher_awal'];

            // 2. Buat Akun User
            $user = User::create([
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Assign role 'anggota'
            $user->assignRole('anggota');

            // 3. Buat Data Anggota (Mapping input form ke kolom DB)
            $anggota = Anggota::create([
                'user_id' => $user->id,
                'status_aktif' => 1, // Set default aktif
                'nama_lengkap' => $validatedData['nama_lengkap'],
                'alamat' => $validatedData['alamat'],
                'no_hp' => $validatedData['no_hp'],

                // --- IURAN BULANAN ---
                'simpanan_wajib' => $validatedData['simpanan_wajib_per_bulan'],
                'simpanan_wajib_khusus' => $validatedData['simpanan_wajib_khusus_per_bulan'],
                'simpanan_manasuka' => $validatedData['simpanan_manasuka_per_bulan'],

                // --- SALDO AWAL ---
                'saldo_pokok' => $jumlah_pokok,
                'voucher' => $jumlah_voucher,
                // Saldo Mandiri = Voucher Awal (jika Voucher dianggap Saldo Mandiri, atau bisa diatur 0)
                'saldo_mandiri' => 0,

                // Sisanya disetel ke nilai default (0)
            ]);

            // 4. Catat Transaksi Simpanan Pokok
            if ($jumlah_pokok > 0) {
                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jumlah_simpanan' => $jumlah_pokok,
                    'jenis_simpanan' => 'pokok',
                    'deskripsi' => 'Simpanan Pokok Awal Saat Pendaftaran',
                    'tanggal_simpanan' => $tanggal_gabung,
                ]);
            }
            // Catatan: Voucher tidak dicatat sebagai Simpanan, hanya sebagai Saldo Awal

            DB::commit();

            return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan dan simpanan awal dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat anggota: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat anggota. Silakan coba lagi. ERROR: ' . $e->getMessage());
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
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // Ambil data anggota berdasarkan ID
        $anggota = Anggota::with('user')->findOrFail($id);
        return view('anggota.edit', compact('anggota'));
    }

    /**
     * Memperbarui data anggota di database.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // 1. Validasi Input
        $validatedData = $request->validate([
            'password' => 'nullable|string|min:8',
            'status_aktif' => 'required|in:0,1', // Ditambahkan
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',

            // Iuran Bulanan
            'simpanan_wajib' => 'required|numeric|min:0',
            'simpanan_wajib_khusus' => 'required|numeric|min:0',
            'simpanan_manasuka' => 'required|numeric|min:0',

            // Saldo
            'voucher' => 'required|numeric|min:0', // Voucher diperbolehkan diubah
        ]);

        DB::beginTransaction();
        try {
            $anggota = Anggota::findOrFail($id);
            // 2. Update Akun User (Password)
            if (!empty($validatedData['password'])) {
                $anggota->user->update(['password' => Hash::make($validatedData['password'])]);
            }


            // 3. Update Data Anggota (profil, iuran, dan voucher)
            $anggotaData = [
                'status_aktif' => $validatedData['status_aktif'],
                'nama_lengkap' => $validatedData['nama_lengkap'],
                'alamat' => $validatedData['alamat'],
                'no_hp' => $validatedData['no_hp'],
                'simpanan_wajib' => $validatedData['simpanan_wajib'],
                'simpanan_wajib_khusus' => $validatedData['simpanan_wajib_khusus'],
                'simpanan_manasuka' => $validatedData['simpanan_manasuka'],
                'voucher' => $validatedData['voucher'],
            ];

            $anggota->update($anggotaData);

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
    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $anggota = Anggota::findOrFail($id);
        DB::beginTransaction();

        try {
            // 1. Soft Delete semua transaksi turunan (Simpanan dan Pinjaman)
            $anggota->pinjaman()->delete();
            $anggota->simpanan()->delete();

            // 2. Soft Delete data anggota (Ini harus mengisi deleted_at)
            $anggota->delete();

            // 3. Hapus akun user secara permanen
            if ($anggota->user) {
                $anggota->user->forceDelete();
            }

            DB::commit();

            return redirect()->route('anggota.index')->with('success', 'Anggota dan semua transaksi terkait berhasil dihapus (soft delete)!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus anggota (Rollback): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus anggota. Terjadi kesalahan pada database.');
        }
    }
}
