<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLoanRequest extends Notification
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
        return [
            'message' => 'Nueva solicitud de ' . $this->loan->user->username,
            'item' => $this->loan->item->name,
            'loan_id' => $this->loan->id,
            'action_url' => route('admin.dashboard')
        ];
    }
}