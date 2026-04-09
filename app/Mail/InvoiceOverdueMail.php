<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InvoiceOverdueMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $displayInvoices;
    public int $totalCount;
    public int $remaining;

    public function __construct(
        public Collection $invoices,   // full overdue collection
        public string $type,           // 'customer' or 'vendor'
        public $admin,
        public int $perPage = 25
    ) {
        // Sort by oldest due date first (most overdue on top)
        $sorted = $invoices->sortBy('due_date');

        $this->totalCount       = $sorted->count();
        $this->displayInvoices  = $sorted->take($perPage);
        $this->remaining        = max(0, $this->totalCount - $perPage);
    }

    public function envelope(): Envelope
    {
        $label = $this->type === 'customer' ? 'Customer' : 'Vendor';
        return new Envelope(
            subject: "[OVERDUE] {$label} Invoice Summary — {$this->totalCount} Invoice(s) Require Attention",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-overdue',
            with: [
                'invoices'   => $this->displayInvoices,
                'type'       => $this->type,
                'admin'      => $this->admin,
                'total'      => $this->totalCount,
                'remaining'  => $this->remaining,
                'perPage'    => $this->perPage,
            ],
        );
    }
}
