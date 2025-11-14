<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anggota extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'status_aktif', // Ditambahkan: Status keanggotaan
        'nama_lengkap',
        'alamat',
        'no_hp',
        'simpanan_wajib',
        'simpanan_wajib_khusus', // Ditambahkan
        'simpanan_manasuka',
        'voucher', // Ditambahkan
        'saldo_pokok', // Ditambahkan
        'saldo_wajib',
        'saldo_wajib_khusus', // Ditambahkan
        'saldo_manasuka',
        'saldo_mandiri',
        'saldo_wajib_pinjam',
        'saldo_jasa_anggota',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class);
    }

    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class);
    }
}
