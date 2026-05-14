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
        $statusText = match($this->loan->status) {
            'active' => 'APROBADA',
            'rejected' => 'RECHAZADA',
            'finished' => 'DEVUELTA - Equipo recibido',
            default => strtoupper($this->loan->status),
        };
        return [
            'message' => "Tu solicitud para {$this->loan->item->name} fue {$statusText}",
            'status' => $this->loan->status,
            'loan_id' => $this->loan->id
        ];
    }
}