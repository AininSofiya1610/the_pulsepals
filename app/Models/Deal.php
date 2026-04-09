<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deal extends Model
{
    use HasFactory;

    // Define all valid stages in order
    const STAGES = [
        'new_opportunity' => 'New Opportunity',
        'qualified' => 'Qualified',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'closed_won' => 'Won',
        'closed_lost' => 'Lost'
    ];

    protected $fillable = [
        'customer_id', 
        'title', 
        'value', 
        'stage', 
        'closed_reason'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'related_to_deal');
    }

    /**
     * Get the next stage in the pipeline
     */
    public function getNextStage()
    {
        $stageKeys = array_keys(self::STAGES);
        $currentIndex = array_search($this->stage, $stageKeys);
        
        // If at negotiation, next should be closed_won
        if ($this->stage == 'negotiation') {
            return 'closed_won';
        }
        
        // If already closed, no next stage
        if (in_array($this->stage, ['closed_won', 'closed_lost'])) {
            return null;
        }
        
        return $stageKeys[$currentIndex + 1] ?? null;
    }

    /**
     * Check if deal is still active (not closed)
     */
    public function isActive()
    {
        return !in_array($this->stage, ['closed_won', 'closed_lost']);
    }

    /**
     * Get stage label
     */
    public function getStageLabelAttribute()
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }
}
