<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswas';

    protected $fillable = [
        'nik',
        'nama',
        'nama_orangtua',
        'kelas',
        'jenis_kelamin',
        'no_telepon',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jenis_kelamin' => 'string',
    ];

    // ── Relasi ──────────────────────────
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_siswa');
    }

    // ── Accessor ─────────────────────────
    public function getNamaKelasAttribute(): string
    {
        return trim(($this->nama ?? '') . ' - Kelas ' . ($this->kelas ?? '')); 
    }

    public function getNamaNikAttribute(): string
    {
        return trim(($this->nama ?? '') . ' (NIK: ' . ($this->nik ?? '-') . ')');
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return match($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    // ── Scope ────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKelas($query, $kelas)
    {
        return $query->where('kelas', $kelas);
    }
}
