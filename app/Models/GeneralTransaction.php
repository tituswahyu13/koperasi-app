<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'amount',
        'category',
        'description',
        'transaction_date',
    ];
    
    // PERBAIKAN KRITIS: Tambahkan $casts untuk otomatis mengubah string ke objek Carbon
    protected $casts = [
        'transaction_date' => 'date', 
        'amount' => 'decimal:2', // Opsional: Pastikan amount di-cast
    ];
    
    // Properti $dates tidak lagi diperlukan di Laravel modern jika menggunakan $casts
    // Namun, jika Anda menggunakan Laravel lama, pastikan Anda menggunakan `$dates = ['transaction_date'];`
    // Jika menggunakan $casts, $dates diabaikan.
}