<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Deal;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $users = User::all();
        $deals = Deal::all();

        // Fallback if no users exist
        if ($users->isEmpty()) {
            User::factory()->create();
            $users = User::all();
        }

        $statuses = ['open', 'done'];
        
        // Task templates for realistic data
        $taskTemplates = [
            'Follow up with client regarding proposal',
            'Prepare contract for signing',
            'Schedule demo meeting',
            'Research competitor pricing',
            'Update CRM contact details',
            'Draft quarterly review report',
            'Call to discuss renewal',
            'Send welcome packet',
            'Investigate support ticket #',
            'Prepare presentation for team meeting'
        ];

        foreach (range(1, 50) as $index) {
            $assignedUser = $users->random();
            $relatedDeal = ($deals->isNotEmpty() && $faker->boolean(70)) ? $deals->random() : null; // 70% chance to link to a deal
            
            $baseTitle = $faker->randomElement($taskTemplates);
            if (str_contains($baseTitle, '#')) {
                $baseTitle .= $faker->randomNumber(4);
            }

            // Status distribution
            $status = $faker->randomElement($statuses);
            
            // Due date logic
            if ($status === 'done') {
                $dueDate = Carbon::now()->subDays(rand(1, 30)); // Done in the past
            } else {
                // Open tasks can be overdue or upcoming
                $dueDate = $faker->boolean(30) ? Carbon::now()->subDays(rand(1, 10)) : Carbon::now()->addDays(rand(1, 14));
            }

            Task::create([
                'title' => $baseTitle,
                'assigned_to' => $assignedUser->id,
                'related_to_deal' => $relatedDeal ? $relatedDeal->id : null,
                'due_date' => $dueDate,
                'status' => $status,
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
