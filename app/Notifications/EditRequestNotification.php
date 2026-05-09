<?php

namespace App\Notifications;

use App\Models\EditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EditRequestNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly EditRequest $editRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Bendahara ' . ($this->editRequest->requestedBy->name ?? '-') . ' mengajukan perubahan transaksi.',
            'edit_request_id' => $this->editRequest->id,
            'url' => route('kepsek.approvals'),
        ];
    }
}
