<?php
// ============================================
// File: app/Mail/InvoiceCreatedMail.php
// ============================================

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $invoice,
        public string $type,
        public $admin = null
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->type === 'customer' ? 'Customer' : 'Vendor';
        $name  = $this->type === 'customer'
            ? $this->invoice->customer_name
            : $this->invoice->vendor_name;

        return new Envelope(
            subject: "[NEW INVOICE] {$label} — {$this->invoice->invoice_no} | {$name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-created',
        );
    }
}


// ============================================
// File: app/Mail/MonthlyReportMail.php
// ============================================

// namespace App\Mail;   ← sama, cuma letak dalam fail lain

// class MonthlyReportMail extends Mailable
// {
//     use Queueable, SerializesModels;
//
//     public function __construct(
//         public array $report,
//         public $admin
//     ) {}
//
//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: "[MONTHLY REPORT] Finance Summary — {$this->report['month']}",
//         );
//     }
//
//     public function content(): Content
//     {
//         return new Content(view: 'emails.monthly-report');
//     }
// }
