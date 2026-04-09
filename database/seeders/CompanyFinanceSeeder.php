<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyFinance;

/**
 * CompanyFinanceSeeder
 * 
 * Generates 50 realistic company finance records
 * using the CompanyFinanceFactory with Faker
 */
class CompanyFinanceSeeder extends Seeder
{
    public function run()
    {
        // Generate 50 unique finance records with varied dates
        CompanyFinance::factory()->count(50)->create();
        
        $this->command->info('50 Company Finance records created with Faker!');
    }
}
