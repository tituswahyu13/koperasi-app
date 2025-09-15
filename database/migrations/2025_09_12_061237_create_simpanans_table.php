<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->decimal('jumlah_simpanan', 15, 2);
            $table->string('jenis_simpanan'); // harian/bulanan
            $table->string('deskripsi')->nullable();
            $table->date('tanggal_simpanan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanans');
    }
};