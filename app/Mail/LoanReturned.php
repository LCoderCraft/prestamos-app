<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Loan;

class LoanReturned extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;
    public $observation;

    public function __construct(Loan $loan, $observation)
    {
        $this->loan = $loan;
        $this->observation = $observation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Devolución de Material',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loan_returned',
        );
    }

    
}