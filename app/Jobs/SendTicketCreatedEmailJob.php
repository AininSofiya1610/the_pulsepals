<?php

namespace App\Jobs;

use App\Mail\TicketCreatedMail;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketCreatedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public Ticket $ticket) {}

    public function handle(): void
    {
        Mail::to($this->ticket->email)
            ->send(new TicketCreatedMail($this->ticket));
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error("Failed to send ticket created email", [
            'ticket_id' => $this->ticket->ticket_id,
            'email'     => $this->ticket->email,
            'error'     => $exception->getMessage(),
        ]);
    }
}