<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $report,
        public $admin
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[MONTHLY REPORT] Finance Summary — {$this->report['month']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly-report',
        );
    }
}
