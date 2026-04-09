<?php

namespace Database\Factories;

use App\Models\CompanyFinance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CompanyFinanceFactory
 * 
 * Generates realistic company finance records with:
 * - Dates spanning the last 6 months
 * - Bank balances between RM 100 - RM 10,000
 * - Net pay calculated logically
 * - Total cash derived from bank balances
 */
class CompanyFinanceFactory extends Factory
{
    protected $model = CompanyFinance::class;

    public function definition()
    {
        // Generate realistic bank balances (RM 100 - RM 10,000)
        $mbbBalance = $this->faker->randomFloat(2, 100, 10000);
        $rhbBalance = $this->faker->randomFloat(2, 100, 10000);
        
        // Net pay is a portion of total cash (realistic business scenario)
        $totalCash = $mbbBalance + $rhbBalance;
        $netPay = $this->faker->randomFloat(2, $totalCash * 0.1, $totalCash * 0.5);
        
        // Random date within last 6 months
        $recordDate = $this->faker->dateTimeBetween('-6 months', 'now');

        return [
            'mbb_balance' => $mbbBalance,
            'rhb_balance' => $rhbBalance,
            'net_pay' => $netPay,
            'record_date' => $recordDate->format('Y-m-d'),
            'created_at' => $recordDate,
            'updated_at' => $recordDate,
        ];
    }
}
