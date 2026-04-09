<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name', 'status', 'deadline', 'description',
        'order_date', 'vendor_name', 'po_number',
        'delivery_date', 'received_by',
        'installation_date', 'installed_by',
        'closing_date', 'closing_notes'
    ];

    protected $casts = [
        'deadline' => 'date',
        'order_date' => 'date',
        'delivery_date' => 'date',
        'installation_date' => 'date',
        'closing_date' => 'date',
    ];

    public function getStatusLabelAttribute()
    {
        return [
            'green' => 'On Track',
            'yellow' => 'At Risk',
            'red' => 'Delayed'
        ][$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get current stage number (1-5)
     */
    public function getCurrentStage()
    {
        if ($this->closing_date) return 5; // Complete
        if ($this->installation_date) return 4; // Installation done, awaiting closing
        if ($this->delivery_date) return 3; // Delivered to Microlab
        if ($this->order_date) return 2; // Ordered from vendor
        return 1; // Not yet ordered
    }

    /**
     * Get stage progress percentage (0-100)
     */
    public function getProgressPercentage()
    {
        $stage = $this->getCurrentStage();
        if ($stage >= 5) return 100;
        return ($stage - 1) * 25; // Each stage = 25%
    }

    /**
     * Calculate status based on progress and deadline
     */
    public function calculateStatus()
    {
        // If closed, always green
        if ($this->closing_date) {
            return 'green';
        }

        // If no deadline set, use current status
        if (!$this->deadline) {
            return $this->status ?? 'green';
        }

        $now = \Carbon\Carbon::now();
        $deadline = \Carbon\Carbon::parse($this->deadline);
        $daysRemaining = $now->diffInDays($deadline, false);

        // Past deadline = red
        if ($daysRemaining < 0) {
            return 'red';
        }

        // Calculate expected progress
        $created = \Carbon\Carbon::parse($this->created_at);
        $totalDays = $created->diffInDays($deadline);
        
        if ($totalDays > 0) {
            $daysPassed = $created->diffInDays($now);
            $expectedProgress = ($daysPassed / $totalDays) * 100;
            $actualProgress = $this->getProgressPercentage();

            // Significantly behind (> 25% behind) = red
            if ($actualProgress < ($expectedProgress - 25)) {
                return 'red';
            }

            // Slightly behind (10-25% behind) = yellow
            if ($actualProgress < ($expectedProgress - 10)) {
                return 'yellow';
            }
        }

        // On track or ahead = green
        return 'green';
    }

    /**
     * Auto-update status before saving
     */
    protected static function booted()
    {
        static::saving(function ($project) {
            $project->status = $project->calculateStatus();
        });
    }
}
