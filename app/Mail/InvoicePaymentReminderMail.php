<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoicePaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $invoice,
        public string $type,
        public float $balance
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->type === 'customer' ? 'Customer' : 'Vendor';
        return new Envelope(
            subject: "[REMINDER] {$label} Invoice #{$this->invoice->invoice_no} — Due in 3 Days",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-payment-reminder',
        );
    }
}

