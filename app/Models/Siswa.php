<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'nama',
        'nis',
        'kelas',
    ];

    /**
     * Transaksis related to this siswa
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_siswa');
    }
}
