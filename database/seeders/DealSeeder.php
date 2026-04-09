<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deal;
use App\Models\Customer;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DealSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ensure we have customers
        $customers = Customer::all();
        if ($customers->isEmpty()) {
            $this->command->info('No customers found. Creating one...');
            Customer::create(['name' => 'Sample Customer', 'email' => 'sample@test.com']);
            $customers = Customer::all();
        }

        $stages = ['new_opportunity', 'contacted', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];
        
        foreach (range(1, 50) as $index) {
            $customer = $customers->random();
            $stage = $faker->randomElement($stages);
            $value = $faker->randomFloat(2, 5000, 150000);
            $createdAt = Carbon::now()->subDays(rand(1, 100));
            
            $dealTitle = $faker->randomElement(['Software License', 'Consulting Project', 'Yearly Maintenance', 'Hardware Upgrade', 'Cloud Migration']) 
                        . ' - ' . $customer->name;

            $closedReason = null;
            if ($stage === 'closed_lost') {
                $closedReason = $faker->sentence(6);
            }

            Deal::create([
                'customer_id' => $customer->id,
                'title' => $dealTitle,
                'value' => $value,
                'stage' => $stage,
                'closed_reason' => $closedReason,
                'created_at' => $createdAt,
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
