<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjamans', function (Blueprint $table) {
            $table->string('jenis_pinjaman')->after('jumlah_pinjaman');
            $table->integer('tenor')->after('jenis_pinjaman');
        });
    }

    public function down(): void
    {
        Schema::table('pinjamans', function (Blueprint $table) {
            $table->dropColumn('jenis_pinjaman');
            $table->dropColumn('tenor');
        });
    }
};