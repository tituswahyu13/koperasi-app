<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    // Tambahkan properti fillable untuk mengizinkan mass assignment
    protected $fillable = [
        'pinjaman_id',
        'anggota_id',
        'pokok',
        'bunga',
        'total_bayar',
        'tanggal_bayar',
        'sumber_pembayaran',
        'deskripsi',
    ];

    // Pastikan field tanggal diproses sebagai instance Carbon
    protected $dates = ['tanggal_bayar'];

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class);
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}