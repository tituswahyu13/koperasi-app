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
            $table->decimal('bunga', 15, 2)->change(); // Pastikan 15, 2
            $table->decimal('sisa_pinjaman', 15, 2)->change(); // Pastikan 15, 2
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pinjamans', function (Blueprint $table) {
            //
        });
    }
};
