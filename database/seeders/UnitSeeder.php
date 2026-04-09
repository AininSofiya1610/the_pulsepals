<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $units = [
            [
                'name' => 'System Unit',
                'description' => 'Handles system-related issues, software problems, and computer maintenance',
                'is_active' => true
            ],
            [
                'name' => 'Network & Infrastructure',
                'description' => 'Manages network connectivity, servers, and infrastructure issues',
                'is_active' => true
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Provides general technical support and user assistance',
                'is_active' => true
            ]
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
