<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporans';
    protected $fillable = ['judul', 'isi', 'tanggal_laporan', 'id_kepsek', 'jenis'];
    protected $casts = [
        'tanggal_laporan' => 'date',
        'jenis' => \App\Enums\JenisLaporan::class,
    ];

    // Relationships
    public function kepalaSekolah()
    {
        return $this->belongsTo(KepalaSekolah::class, 'id_kepsek');
    }

    // Scopes
    public function scopeKeuangan($query)
    {
        return $query->where('jenis', 'keuangan');
    }

    public function scopeOperasional($query)
    {
        return $query->where('jenis', 'operasional');
    }
}
