<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RoomReservation;
class RoomReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;
    public $reservation;
    public function __construct(RoomReservation $reservation)
    {
        $this->reservation = $reservation;
    }
    public function envelope(): Envelope
    {
        $status = $this->reservation->status === 'active' ? 'APROBADA' : 'RECHAZADA';
        return new Envelope(
            subject: 'Reservación de Centro de Cómputo: ' . $status,
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.room_reservation_status',
        );
    }
    public function attachments(): array
    {
        return [];
    }
}