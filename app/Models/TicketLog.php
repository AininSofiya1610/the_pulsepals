<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'guest_email',
        'is_staff',
        'action',
        'field',
        'old_value',
        'new_value',
        'message',
    ];

    /**
     * Get the ticket that owns this log.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who made the change.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
