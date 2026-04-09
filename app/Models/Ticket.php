<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // Auto-generate public_token when creating a new ticket
        static::creating(function ($ticket) {
            if (empty($ticket->public_token)) {
                $ticket->public_token = Str::random(32);
            }
        });
    }

    protected $fillable = [
        'ticket_id',
        'public_token',
        'title',
        'description',
        'full_name',
        'email',
        'phone',
        'phone_ext',
        'unit_id',
        'priority',
        'ticket_type',
        'category',
        'status',
        'assigned_to',
        'created_by',
        'assigned_at',
        'started_at',
        'resolved_at',
        'closed_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class)->orderBy('created_at', 'desc');
    }

    // Helper Methods
    public function getResolutionTimeAttribute()
    {
        if ($this->resolved_at && $this->created_at) {
            return $this->created_at->diffInHours($this->resolved_at);
        }
        return null;
    }

    public function getResponseTimeAttribute()
    {
        if ($this->started_at && $this->created_at) {
            return $this->created_at->diffInMinutes($this->started_at);
        }
        return null;
    }

    public function isOverdue()
    {
        if ($this->status === 'Closed' || $this->status === 'Resolved') {
            return false;
        }
        return $this->created_at->diffInDays(now()) > 3;
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'In Progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'Resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }

    public function scopePreventiveMaintenance($query)
    {
        return $query->where('ticket_type', 'PM');
    }

    public function scopeCorrectiveMaintenance($query)
    {
        return $query->where('ticket_type', 'CM');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}