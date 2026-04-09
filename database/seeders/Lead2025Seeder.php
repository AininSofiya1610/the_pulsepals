<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Lead2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['new_lead', 'contacted', 'qualified', 'converted'];
        $sources = ['Website', 'Referral', 'Social Media', 'Cold Call', 'Email Marketing'];

        // Shuffle years or just specifically 2025 as requested
        $year = 2025;

        for ($month = 1; $month <= 12; $month++) {
            // Random number of leads per month (e.g., 3 to 12)
            $count = rand(3, 12);
            
            for ($i = 0; $i < $count; $i++) {
                $day = rand(1, 28);
                $createdAt = Carbon::create($year, $month, $day, rand(8, 18), rand(0, 59));
                
                Lead::create([
                    'name' => 'Lead ' . Str::random(5),
                    'email' => Str::lower(Str::random(10)) . '@example.com',
                    'phone' => '01' . rand(1, 9) . '-' . rand(1000000, 9999999),
                    'source' => $sources[array_rand($sources)],
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
