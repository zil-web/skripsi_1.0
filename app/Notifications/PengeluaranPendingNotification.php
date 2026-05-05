<?php

namespace App\Notifications;

use App\Models\Pengeluaran;
use App\Models\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengeluaranPendingNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Pengeluaran|Transaksi $transaksi)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pengeluaran baru menunggu persetujuan',
            'message' => 'Pengeluaran ' . ($this->transaksi->jenis_transaksi ?? '-') . ' sebesar ' . rupiah((int) $this->transaksi->jumlah) . ' menunggu validasi.',
            'transaction_id' => $this->transaksi->id,
            'jenis_pengeluaran' => $this->transaksi->jenis_transaksi,
            'jumlah' => (int) $this->transaksi->jumlah,
            'tanggal' => optional($this->transaksi->tanggal)->format('Y-m-d'),
            'status' => is_object($this->transaksi->status) && isset($this->transaksi->status->value)
                ? $this->transaksi->status->value
                : (string) $this->transaksi->status,
        ];
    }
}