<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueBudget extends Model
{
    use HasFactory;

    protected $table = 'revenue_budgets';  // ← ADD THIS LINE

    protected $fillable = [
        'year',
        'month',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get monthly budgets for a specific year as array keyed by month.
     */
    public static function getMonthlyBudgets(int $year): array
    {
        return self::where('year', $year)
            ->pluck('amount', 'month')
            ->toArray();
    }

    /**
     * Get month name from month number.
     */
    public static function getMonthName(int $month): string
    {
        return date('F', mktime(0, 0, 0, $month, 1));
    }
}