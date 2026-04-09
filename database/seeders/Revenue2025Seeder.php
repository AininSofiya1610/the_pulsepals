<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class Revenue2025Seeder extends Seeder
{
    /**
     * Seed sample invoices for 2025 Revenue.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Buat 15 invoice random untuk tahun 2025
        for ($i = 1; $i <= 15; $i++) {
            $month = rand(1, 12);
            $day = rand(1, 28);
            $date = "2025-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            
            DB::table('customer_finances')->insert([
                'invoice_no' => 'INV-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'customer_name' => $faker->company,
                'invoice_date' => $date, // Tarikh 2025
                'due_date' => date('Y-m-d', strtotime($date . ' +30 days')),
                'amount' => rand(5000, 30000), // Random amount RM 5k - 30k
                'type' => 'Service',
                'cogs' => 0,
                'description' => 'Sample Invoice for 2025 History',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Berjaya tambah 15 invoice untuk tahun 2025!');
    }
}
