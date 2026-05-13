<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RoomReservation;
class RoomReservationStatusChanged extends Notification
{
    use Queueable;
    public $reservation;
    public function __construct(RoomReservation $reservation)
    {
        $this->reservation = $reservation;
    }
    public function via(object $notifiable): array
    {
        return ['database'];
    }
    public function toArray(object $notifiable): array
    {
        $status = $this->reservation->status === 'active' ? 'APROBADA' : 'RECHAZADA';
        return [
            'message' => "Tu reservación para {$this->reservation->computerRoom->name} fue {$status}",
            'status' => $this->reservation->status,
            'loan_id' => $this->reservation->id,
        ];
    }
}