<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggotas', function (Blueprint $table) {
            // Saldo untuk Simpanan Pokok
            $table->decimal('saldo_pokok', 15, 2)->default(0)->after('saldo_mandiri');

            // Saldo untuk Simpanan Wajib Khusus
            $table->decimal('saldo_wajib_khusus', 15, 2)->default(0)->after('saldo_pokok');
            
            // Saldo untuk Simpanan Wajib Pinjam (sebagai jaminan pinjaman)
            $table->decimal('saldo_wajib_pinjam', 15, 2)->default(0)->after('saldo_wajib_khusus');

            // Kita asumsikan 'Voucher' tidak memiliki saldo yang diakumulasi seperti simpanan.
            // Jika 'Voucher' adalah jenis transaksi/pemasukan, dia hanya perlu dicatat di tabel simpanan.
        });
    }

    public function down(): void
    {
        Schema::table('anggotas', function (Blueprint $table) {
            $table->dropColumn(['saldo_pokok', 'saldo_wajib_khusus', 'saldo_wajib_pinjam']);
        });
    }
};