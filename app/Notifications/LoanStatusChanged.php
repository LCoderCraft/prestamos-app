<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoanStatusChanged extends Notification
{
    use Queueable;
    public $loan;

    public function __construct($loan)
    {
        $this->loan = $loan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->loan->status == 'active' ? 'APROBADA' : 'RECHAZADA';
        return [
            'message' => "Tu solicitud para {$this->loan->item->name} fue {$status}",
            'status' => $this->loan->status,
            'loan_id' => $this->loan->id
        ];
    }
}