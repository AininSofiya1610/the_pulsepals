<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerFactorySeeder extends Seeder
{
    public function run()
    {
        // Clear existing data if needed (optional)
        // Customer::truncate();
        
        // Generate 80 unique customers using the factory
        Customer::factory()->count(80)->create();
        
        $this->command->info('80 unique customers created with Faker!');
    }
}
