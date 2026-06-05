<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'requested_by',
        'old_jumlah',
        'new_jumlah',
        'old_jenis',
        'new_jenis',
        'old_keterangan',
        'new_keterangan',
        'status',
        'catatan_kepsek',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────

    /**
     * The transaction this edit request is for.
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    /**
     * The admin (bendahara) who requested this edit.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * The kepala sekolah who reviewed this edit request.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFormatUangAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->new_jumlah, 0, ',', '.');
    }

    public function getFormatUangLamaAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->old_jumlah, 0, ',', '.');
    }

    public function getFormatUangBaruAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->new_jumlah, 0, ',', '.');
    }

    // ── Scopes ──────────────────────────────────

    /**
     * Scope to get only pending edit requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only approved edit requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get only rejected edit requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
