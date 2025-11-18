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
        // Tabel untuk mencatat setiap pembayaran/angsuran pinjaman
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('cascade');
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            
            // Komponen Pembayaran
            $table->decimal('pokok', 15, 2)->default(0.00)->comment('Angsuran Pokok');
            $table->decimal('bunga', 15, 2)->default(0.00)->comment('Angsuran Bunga');
            $table->decimal('total_bayar', 15, 2)->comment('Pokok + Bunga');

            // Tanggal dan Status
            $table->date('tanggal_bayar');
            $table->string('sumber_pembayaran')->nullable()->comment('Contoh: Potongan Gaji, Tunai, Tutup Bulan Otomatis');
            $table->text('deskripsi')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};