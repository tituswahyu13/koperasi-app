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
        // Buat user admin
        $userAdmin = User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Buat user anggota dummy
        $userAnggota = User::create([
            'username' => 'anggota1',
            'password' => Hash::make('password'),
        ]);

        // Buat anggota dummy
        $anggota = Anggota::create([
            'user_id' => $userAnggota->id,
            'nama_lengkap' => 'Budi Santoso',
            'alamat' => 'Jl. Kenangan Indah No. 123',
            'no_hp' => '081234567890',
            'simpanan_wajib' => 100000, // Tambahkan nilai dummy
            'simpanan_manasuka' => 200000, // Tambahkan nilai dummy
            'saldo_wajib' => 0, // Perbarui ini
            'saldo_manasuka' => 0, // Perbarui ini
        ]);

        // Tambahkan pinjaman dummy
        Pinjaman::create([
            'anggota_id' => $anggota->id,
            'jumlah_pinjaman' => 10000000,
            'jumlah_bayar' => 2000000,
            'bunga' => 5,
            'sisa_pinjaman' => 8000000,
            'status' => 'approved',
            'tanggal_pengajuan' => '2025-03-01',
            'tanggal_jatuh_tempo' => '2025-09-20',
        ]);
    }
}