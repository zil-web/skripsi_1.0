<?php

namespace App\Models;

use App\Enums\TransaksiJenis;
use App\Enums\TransaksiStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal',
        'jenis',
        'jumlah',
        'keterangan',
        'bukti_transaksi',
        'status',
        'id_admin',
        'id_siswa',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jenis' => TransaksiJenis::class,
        'status' => TransaksiStatus::class,
        'jumlah' => 'decimal:2',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'id_transaksi');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'id_transaksi');
    }

    /**
     * Get jumlah formatted in Rupiah currency
     */
    public function getJumlahRupiahAttribute(): string
    {
        return rupiah((int) $this->jumlah);
    }

    /**
     * Get status badge HTML with Tailwind CSS styling
     */
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];

        $colorClass = $colors[$this->status->value] ?? 'bg-gray-100 text-gray-800';

        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium %s">%s</span>',
            $colorClass,
            ucfirst($this->status->value)
        );
    }
}
