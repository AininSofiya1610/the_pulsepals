<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetYear extends Model
{
    protected $fillable = ['year'];
    
    /**
     * Relationship: One year has many monthly budgets
     */
    public function budgets()
    {
        return $this->hasMany(RevenueBudget::class, 'year', 'year');
    }
    
    /**
     * Scope: Get years ordered by latest first
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('year', 'desc');
    }
}
