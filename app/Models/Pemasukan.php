<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemasukan extends Model
{
    use SoftDeletes;

    protected $table = 'transaksis';

    protected $fillable = [
        'tanggal',
        'tipe',
        'jenis_transaksi',
        'jumlah',
        'keterangan',
        'bukti_transaksi',
        'siswa_id',
        'id_admin',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('pemasukan', function (Builder $builder) {
            $builder->where('tipe', 'pemasukan');
        });

        static::creating(function (self $model): void {
            $model->tipe = 'pemasukan';
            $model->jenis = 'pemasukan';
            $model->status = 'approved';
        });
    }

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function getFormatUangAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->jumlah, 0, ',', '.');
    }
}