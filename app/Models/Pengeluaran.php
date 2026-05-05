<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
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
        'status',
        'siswa_id',
        'id_admin',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('pengeluaran', function (Builder $builder) {
            $builder->where('tipe', 'pengeluaran');
        });

        static::creating(function (self $model): void {
            $model->tipe = 'pengeluaran';
            $model->jenis = 'pengeluaran';
            $model->status = $model->status ?: 'pending';
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
}