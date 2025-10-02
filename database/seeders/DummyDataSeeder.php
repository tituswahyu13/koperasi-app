<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat user admin (role = 1)
        $userAdmin = User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 1, // SET ROLE ADMIN
        ]);

        // 2. Buat user anggota dummy (role = 0)
        $userAnggota = User::create([
            'username' => 'anggota1',
            'password' => Hash::make('password'),
            'role' => 0, // SET ROLE ANGGOTA
        ]);

        // 3. Buat anggota dummy
        $anggota = Anggota::create([
            'user_id' => $userAnggota->id,
            'nama_lengkap' => 'Budi Santoso',
            'alamat' => 'Jl. Kenangan Indah No. 123',
            'no_hp' => '081234567890',
            'simpanan_wajib' => 100000,
            'simpanan_manasuka' => 200000,
            'saldo_wajib' => 0,
            'saldo_manasuka' => 0,
            'saldo_mandiri' => 0,
        ]);

        // 4. Tambahkan simpanan dummy (jika diperlukan untuk pengujian saldo awal,
        //    tetapi untuk pengujian ini, kita hanya fokus pada pinjaman dan role)
        // Hapus kode simpanan dummy yang lama agar tidak menimbulkan konflik.

        // 5. Tambahkan pinjaman dummy dengan kolom baru
        Pinjaman::create([
            'anggota_id' => $anggota->id,
            'jumlah_pinjaman' => 10000000,
            'jenis_pinjaman' => 'uang', // Tambahkan ini
            'tenor' => 12, // Tambahkan ini
            'jumlah_bayar' => 2000000,
            'bunga' => 120000, // (10jt * 1% * 12)
            'sisa_pinjaman' => 8120000, // (10jt + 120rb) - 2jt
            'status' => 'approved',
            'tanggal_pengajuan' => '2025-03-01',
            'tanggal_jatuh_tempo' => '2026-03-01',
        ]);
    }
}