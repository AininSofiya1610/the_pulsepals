<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Mail\TicketActivityNotification;

class SendTicketEmailJob implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $log;

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket, TicketLog $log)
    {
        $this->ticket = $ticket;
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send email notification
        Mail::to($this->ticket->email)
            ->send(new TicketActivityNotification($this->ticket, $this->log));
    }
}
