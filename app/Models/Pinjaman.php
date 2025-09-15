<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjamans'; 

    protected $fillable = [
        'anggota_id',
        'jumlah_pinjaman',
        'jumlah_bayar',
        'bunga',
        'sisa_pinjaman',
        'status',
        'tanggal_pengajuan',
        'tanggal_jatuh_tempo',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}
