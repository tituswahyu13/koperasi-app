<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pinjaman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pinjamans'; 

    protected $fillable = [
        'anggota_id',
        'jumlah_pinjaman',
        'loan_type', // DITAMBAHKAN
        'tenor',
        'payment_date_type', // DITAMBAHKAN
        'deskripsi', // DITAMBAHKAN
        'bunga',
        'biaya_admin', // DITAMBAHKAN
        'sisa_pinjaman',
        'status',
        'tanggal_pengajuan',
        'tanggal_jatuh_tempo',
    ];
    
    // Pastikan field tanggal diproses sebagai instance Carbon
    protected $dates = [
        'tanggal_pengajuan', 
        'tanggal_jatuh_tempo'
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}