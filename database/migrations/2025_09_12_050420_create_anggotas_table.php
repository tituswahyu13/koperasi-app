<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('alamat')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->decimal('simpanan_wajib', 15, 2)->default(0);
            $table->decimal('simpanan_manasuka', 15, 2)->default(0);
            $table->decimal('saldo_wajib', 15, 2)->default(0);
            $table->decimal('saldo_manasuka', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};