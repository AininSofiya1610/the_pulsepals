<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadSource;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'Website',        'order' => 1, 'is_active' => true],
            ['name' => 'Email Campaign', 'order' => 2, 'is_active' => true],
            ['name' => 'Phone Call',     'order' => 3, 'is_active' => true],
            ['name' => 'Social Media',   'order' => 4, 'is_active' => true],
            ['name' => 'Referral',       'order' => 5, 'is_active' => true],
            ['name' => 'Other',          'order' => 6, 'is_active' => true],
        ];

        foreach ($sources as $source) {
            LeadSource::updateOrCreate(['name' => $source['name']], $source);
        }
    }
}