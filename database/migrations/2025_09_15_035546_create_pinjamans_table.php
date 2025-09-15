<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('jumlah_bayar', 15, 2)->default(0);
            $table->decimal('bunga', 5, 2)->default(0);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->string('status');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};