<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Get admin user for assignment (or null if no users)
        $admin = User::first();
        $adminId = $admin ? $admin->id : null;

        $statuses = ['New', 'Contacted', 'Qualified', 'Proposal Sent', 'Negotiation', 'Won', 'Lost'];
        $sources = ['Website', 'Referral', 'Social Media', 'Email Campaign', 'Cold Call', 'Event'];

        for ($i = 0; $i < 50; $i++) {
            $createdDate = now()->subDays(rand(1, 60));
            
            $lead = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'source' => $faker->randomElement($sources),
                'status' => $faker->randomElement($statuses),
                'assigned_to' => $adminId, // Assign mostly to admin for visibility
                'created_at' => $createdDate,
                'updated_at' => now(),
            ];

            // Safety check for columns in case migrations didn't run perfectly
            // But we confirmed they exist in migration files.
            // We use create() which filters by fillable.
            
            Lead::create($lead);
        }
    }
}
