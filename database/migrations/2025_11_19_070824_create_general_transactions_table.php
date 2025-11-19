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
        Schema::create('general_transactions', function (Blueprint $table) {
            $table->id();
            // Jenis transaksi: 'in' (Pemasukan) atau 'out' (Pengeluaran)
            $table->enum('type', ['in', 'out']); 
            $table->decimal('amount', 15, 2);
            $table->string('category')->nullable()->comment('Contoh: Sewa, Gaji, Bunga Bank, Penjualan Barang');
            $table->text('description');
            $table->date('transaction_date');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_transactions');
    }
};