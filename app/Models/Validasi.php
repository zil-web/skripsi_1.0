<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Validasi extends Model
{
    protected $table = 'validasis';
    protected $fillable = ['id_transaksi', 'id_kepsek', 'status', 'catatan'];
    protected $casts = [
        'status' => \App\Enums\StatusValidasi::class,
    ];

    // Relationships
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function kepalaSekolah()
    {
        return $this->belongsTo(KepalaSekolah::class, 'id_kepsek');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
