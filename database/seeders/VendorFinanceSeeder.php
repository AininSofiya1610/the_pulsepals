<?php

namespace Database\Seeders;

use App\Models\VendorFinance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VendorFinanceSeeder extends Seeder
{
    /**
     * Seed vendor transaction data for 2025 and 2026
     * Creates 8-15 transactions per month with realistic payment statuses
     */
    public function run()
    {
        // Clear existing vendor finance records
        VendorFinance::query()->delete();
        
        $years = [2025, 2026];
        
        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                // Random number of transactions per month (8-15)
                $transactionCount = rand(8, 15);
                
                for ($i = 0; $i < $transactionCount; $i++) {
                    // Random date within the month (avoiding day 29-31 for safety)
                    $day = rand(1, 28);
                    $date = Carbon::create($year, $month, $day);
                    
                    // Create transaction with random payment status distribution
                    // 40% fully paid, 30% partial, 30% unpaid
                    $randomStatus = rand(1, 10);
                    
                    if ($randomStatus <= 4) {
                        // 40% - Fully Paid
                        VendorFinance::factory()->paid()->create([
                            'invoice_date' => $date,
                            'due_date' => $date->copy()->addDays(rand(15, 30)),
                        ]);
                    } elseif ($randomStatus <= 7) {
                        // 30% - Partially Paid
                        VendorFinance::factory()->partial()->create([
                            'invoice_date' => $date,
                            'due_date' => $date->copy()->addDays(rand(15, 30)),
                        ]);
                    } else {
                        // 30% - Unpaid
                        VendorFinance::factory()->unpaid()->create([
                            'invoice_date' => $date,
                            'due_date' => $date->copy()->addDays(rand(15, 30)),
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('✅ Vendor transactions seeded successfully!');
        $this->command->info('📊 Total transactions created: ' . VendorFinance::count());
        $this->command->info('💵 Total bill amount: RM ' . number_format(VendorFinance::sum('invoice'), 2));
        $this->command->info('✅ Total paid amount: RM ' . number_format(VendorFinance::sum('paid_amount'), 2));
    }
}
