<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RoomReservation;
class NewRoomReservation extends Notification
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
        $requester = match($this->reservation->requester_type) {
            'group' => 'Grupo ' . $this->reservation->group_name,
            'teacher' => $this->reservation->teacher_name,
            default => $this->reservation->user->username,
        };
        return [
            'message' => 'Nueva reservación de ' . $requester,
            'item' => $this->reservation->computerRoom->name,
            'loan_id' => $this->reservation->id,
            'action_url' => route('admin.rooms.index'),
        ];
    }
}