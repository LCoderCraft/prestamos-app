<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Loan;

class LoanStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;

    // Recibimos el préstamo para usar sus datos en el correo
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    // Asunto del correo
    public function envelope(): Envelope
    {
        $status = $this->loan->status == 'active' ? 'APROBADA' : 'RECHAZADA';
        return new Envelope(
            subject: 'Actualización de Solicitud: ' . $status,
        );
    }

    // Definimos qué archivo HTML (Vista) se va a enviar
    public function content(): Content
    {
        return new Content(
            view: 'emails.loan_status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}