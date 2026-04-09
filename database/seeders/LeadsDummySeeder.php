<?php

namespace Database\Seeders;

use App\Models\Lead;
use Illuminate\Database\Seeder;

class LeadsDummySeeder extends Seeder
{
    /**
     * Seed 200 dummy leads with Malaysian-style data.
     * Dates are spread across Jan 2024 – Dec 2026.
     */
    public function run(): void
    {
        $this->command->info('Seeding 200 dummy leads...');

        Lead::factory()->count(200)->create();

        $this->command->info('✓ 200 leads created successfully!');
        $this->command->info('  Date range: Jan 2024 – Dec 2026');
        $this->command->info('  Sources: Cold Call, Referral, LinkedIn, Website Form');
        $this->command->info('  Statuses: new, contacted, lost');
    }
}
