<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pinjamans', function (Blueprint $table) {
            // 1. Tambahkan kolom baru yang hilang dari PinjamanController
            $table->string('loan_type')->after('jumlah_pinjaman')->nullable(); // Jenis Pinjaman (uang_jk_panjang, sebrak, dll.)
            $table->string('payment_date_type')->after('tenor')->nullable(); // Metode Pembayaran (tgl_1, tgl_15, manual)
            $table->text('deskripsi')->after('payment_date_type')->nullable(); // Deskripsi Pinjaman
            $table->decimal('biaya_admin', 15, 2)->after('bunga')->default(0.00); // Biaya Admin
            
            // 2. Perbarui kolom yang sudah ada (sesuai model dan kebutuhan)
            $table->decimal('jumlah_bayar', 15, 2)->default(0.00)->change(); // Memastikan default value
            $table->decimal('bunga', 15, 2)->default(0.00)->change(); // Memastikan default value
            $table->decimal('sisa_pinjaman', 15, 2)->default(0.00)->change(); // Memastikan default value
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pinjamans', function (Blueprint $table) {
            // Drop kolom yang ditambahkan jika migration di-rollback
            $table->dropColumn(['loan_type', 'payment_date_type', 'deskripsi', 'biaya_admin']);
            
            // Mengembalikan kolom ke tipe awal jika diperlukan (opsional, tergantung skema asli)
            // Namun, untuk amannya, kita asumsikan kolom yang diubah tipenya sudah benar.
        });
    }
};