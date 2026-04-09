<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Unit;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Truncate tickets to prevent duplicate IDs
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ticket::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure Units exist
        $techUnit = Unit::firstOrCreate(['name' => 'Technical Support']);
        $sysUnit = Unit::firstOrCreate(['name' => 'System Unit']);
        
        $units = [$techUnit->id, $sysUnit->id];


        // Totals: 50 Tickets
        // Open: 13
        // In Progress: 8
        // Resolved: 7
        // Critical: 4
        // Closed: 18
        
        $counts = [
            'Open' => 13,
            'In Progress' => 8,
            'Resolved' => 7,
            'Critical' => 4,
            'Closed' => 18
        ];

        $categories = ['Hardware Issue', 'Software Issue', 'Server Issue', 'Maintenance Request', 'Other'];
        $priorities = ['Low', 'Medium', 'High', 'Critical'];

        $ticketIdCounter = 1;

        foreach ($counts as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $createdDate = Carbon::now()->subDays(rand(1, 30));
                
                $data = [
                    'ticket_id' => 'TKT-2024-' . str_pad($ticketIdCounter++, 3, '0', STR_PAD_LEFT),
                    'title' => $faker->sentence(4),
                    'description' => $faker->paragraph(),
                    'full_name' => $faker->name,
                    'email' => $faker->companyEmail,
                    'phone' => $faker->phoneNumber,
                    'unit_id' => $faker->randomElement($units),
                    'priority' => $status === 'Critical' ? 'Critical' : $faker->randomElement($priorities),
                    'category' => $faker->randomElement($categories),
                    'status' => $status,
                    'ticket_type' => $faker->randomElement(['CM', 'PM']),
                    'created_at' => $createdDate,
                    'updated_at' => Carbon::now(),
                ];

                if ($status === 'In Progress' || $status === 'Resolved' || $status === 'Closed') {
                    $data['started_at'] = $createdDate->copy()->addHours(rand(1, 24));
                    $data['assigned_at'] = $createdDate->copy()->addHours(rand(1, 5));
                    $data['assigned_to'] = 1; // Assign to admin
                }

                if ($status === 'Resolved' || $status === 'Closed') {
                     $data['resolved_at'] = $data['started_at']->copy()->addHours(rand(2, 48));
                }

                if ($status === 'Closed') {
                     $data['closed_at'] = $data['resolved_at']->copy()->addDays(rand(1, 3));
                }

                Ticket::create($data);
            }
        }
    }
}
