<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'assigned_to', 
        'related_to_deal', 
        'due_date', 
        'status'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Get the computed status (auto-detects overdue)
     * A task is overdue if it's not done and the due date has passed
     */
    public function getComputedStatusAttribute()
    {
        if ($this->status === 'done') {
            return 'done';
        }
        
        // Check if task is overdue: has due date AND due date is in the past
        if ($this->due_date && $this->due_date->isPast()) {
            return 'overdue';
        }
        
        return 'open';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $status = $this->computed_status;
        
        return match($status) {
            'done' => '<span class="badge badge-success">Done</span>',
            'overdue' => '<span class="badge badge-danger">Overdue</span>',
            default => '<span class="badge badge-warning">Open</span>',
        };
    }

    /**
     * Scope: Get only overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'done')
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now());
    }

    /**
     * Scope: Get only open tasks (not done, not overdue)
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                     ->where(function($q) {
                         $q->whereNull('due_date')
                           ->orWhere('due_date', '>=', now());
                     });
    }

    /**
     * Scope: Get only done tasks
     */
    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'related_to_deal');
    }
}
