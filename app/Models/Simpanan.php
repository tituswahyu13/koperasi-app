<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Simpanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'anggota_id',
        'jumlah_simpanan',
        'jenis_simpanan',
        'deskripsi',
        'tanggal_simpanan',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}
