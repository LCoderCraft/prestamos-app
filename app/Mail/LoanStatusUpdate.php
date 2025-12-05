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

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function envelope(): Envelope
    {
        $status = $this->loan->status == 'active' ? 'APROBADA' : 'RECHAZADA';
        return new Envelope(
            subject: 'Actualización de Solicitud: ' . $status,
        );
    }

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