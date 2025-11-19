<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // PASTIKAN INI ADA DAN AKTIF
use Illuminate\Database\Eloquent\SoftDeletes;

class Pinjaman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pinjamans'; 

    protected $fillable = [
        'anggota_id',
        'jumlah_pinjaman',
        'loan_type', 
        'tenor',
        'payment_date_type', 
        'deskripsi', 
        'bunga',
        'biaya_admin', 
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

    /**
     * Relasi ke Payments (Angsuran Pinjaman)
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'pinjaman_id');
    }
}