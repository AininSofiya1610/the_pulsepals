<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Deal;
use Illuminate\Database\Seeder;

class CustomersDummySeeder extends Seeder
{
    /**
     * Seed 150 dummy customers with 0–3 deals each.
     */
    public function run(): void
    {
        $this->command->info('Seeding 150 dummy customers with deals...');

        $customers = Customer::factory()->count(150)->create();

        $dealCount = 0;
        foreach ($customers as $customer) {
            $numDeals = rand(0, 3);
            if ($numDeals > 0) {
                Deal::factory()->count($numDeals)->create([
                    'customer_id' => $customer->id,
                    'created_at'  => $customer->created_at,
                    'updated_at'  => $customer->created_at,
                ]);
                $dealCount += $numDeals;
            }
        }

        $this->command->info("✓ 150 customers created with {$dealCount} deals!");
        $this->command->info('  Date range: Jan 2024 – Dec 2026');
        $this->command->info('  Status: ~80% Active, ~20% Inactive');
        $this->command->info('  Deals per customer: 0–3');
    }
}
