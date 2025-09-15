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
            'saldo_simpanan' => 5000000,
        ]);

        // Tambahkan beberapa data simpanan
        Simpanan::create([
            'anggota_id' => $anggota->id,
            'jumlah_simpanan' => 1000000,
            'jenis_simpanan' => 'bulanan',
            'tanggal_simpanan' => '2025-01-10',
        ]);
        Simpanan::create([
            'anggota_id' => $anggota->id,
            'jumlah_simpanan' => 2000000,
            'jenis_simpanan' => 'harian',
            'tanggal_simpanan' => '2025-02-15',
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