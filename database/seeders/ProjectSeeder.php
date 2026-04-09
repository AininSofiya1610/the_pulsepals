<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Seed demonstration projects that clearly show auto-status calculation.
     */
    public function run(): void
    {
        // Clear existing projects
        Project::truncate();

        $today = Carbon::now();

        $projects = [
            // ========================================
            // 🟢 GREEN STATUS - Perfect Examples
            // ========================================
            
            // Example 1: Just Started - Fresh Project
            [
                'name' => '✅ Fresh Start - New Equipment Order',
                'deadline' => $today->copy()->addDays(30),
                'description' => 'Brand new project, just created today. Should be GREEN.',
                'created_at' => $today->copy(),
                // No stages yet - but that's okay, just started!
                // Expected: 0%, Actual: 0%, Difference: 0% → GREEN ✅
            ],

            // Example 2: Ahead of Schedule
            [
                'name' => '✅ Ahead of Schedule - Quick Progress',
                'deadline' => $today->copy()->addDays(21), // 3 weeks from now
                'description' => 'Started 1 week ago, already 50% done! Ahead of schedule.',
                'created_at' => $today->copy()->subDays(7),
                'order_date' => $today->copy()->subDays(6),
                'vendor_name' => 'FastTrack Supplies',
                'po_number' => 'PO-2026-FAST',
                'delivery_date' => $today->copy()->subDays(3),
                'received_by' => 'Ahmad',
                // 28 days total, 7 days passed = 25% time
                // Stage 2 done = 50% progress
                // Ahead by 25%! → GREEN ✅
            ],

            // Example 3: Perfectly On Track
            [
                'name' => '✅ Perfect Timing - Exactly On Schedule',
                'deadline' => $today->copy()->addDays(30),
                'description' => 'Started 10 days ago, completed Stage 1. Bang on schedule!',
                'created_at' => $today->copy()->subDays(10),
                'order_date' => $today->copy()->subDays(9),
                'vendor_name' => 'Precision Equipment',
                'po_number' => 'PO-2026-PERF',
                // 40 days total, 10 days passed = 25% time
                // Stage 1 done = 25% progress
                // Difference: 0% → GREEN ✅
            ],

            // Example 4: Completed Project
            [
                'name' => '✅ COMPLETED - All Stages Done',
                'deadline' => $today->copy()->addDays(5),
                'description' => 'Finished project with all stages completed successfully.',
                'created_at' => $today->copy()->subDays(25),
                'order_date' => $today->copy()->subDays(24),
                'vendor_name' => 'Success Suppliers',
                'po_number' => 'PO-2026-DONE',
                'delivery_date' => $today->copy()->subDays(18),
                'received_by' => 'Siti',
                'installation_date' => $today->copy()->subDays(12),
                'installed_by' => 'Team A',
                'closing_date' => $today->copy()->subDays(5),
                'closing_notes' => 'Project completed successfully. All tests passed.',
                // All stages complete → Always GREEN ✅
            ],

            // ========================================
            // 🟡 YELLOW STATUS - Warning Examples
            // ========================================

            // Example 5: Slightly Behind (15% behind)
            [
                'name' => '⚠️ WARNING - Falling Behind Schedule',
                'deadline' => $today->copy()->addDays(16), // 16 days left
                'description' => 'Started 2 weeks ago but only Stage 1 done. Need to speed up!',
                'created_at' => $today->copy()->subDays(14),
                'order_date' => $today->copy()->subDays(12),
                'vendor_name' => 'Slow Supplies',
                'po_number' => 'PO-2026-SLOW',
                // 30 days total, 14 days passed = 47% time
                // Stage 1 done = 25% progress
                // Behind by 22% → YELLOW ⚠️
            ],

            // Example 6: At Risk - 20% Behind
            [
                'name' => '⚠️ AT RISK - Need Immediate Action',
                'deadline' => $today->copy()->addDays(10),
                'description' => 'Halfway through timeline but barely started. High risk!',
                'created_at' => $today->copy()->subDays(20),
                'order_date' => $today->copy()->subDays(18),
                'vendor_name' => 'Delay Solutions',
                'po_number' => 'PO-2026-RISK',
                // 30 days total, 20 days passed = 67% time
                // Stage 1 done = 25% progress
                // Behind by 42% → Actually RED! (>25%)
            ],

            // Example 7: Yellow Zone (10-15% behind)
            [
                'name' => '⚠️ CAUTION - Minor Delay',
                'deadline' => $today->copy()->addDays(24),
                'description' => 'Small delay but manageable. Keep monitoring.',
                'created_at' => $today->copy()->subDays(6),
                // 30 days total, 6 days passed = 20% time
                // No stages = 0% progress
                // Behind by 20% → YELLOW ⚠️
            ],

            // ========================================
            // 🔴 RED STATUS - Critical Examples
            // ========================================

            // Example 8: Past Deadline!
            [
                'name' => '🚨 OVERDUE - Deadline Passed!',
                'deadline' => $today->copy()->subDays(5),
                'description' => 'URGENT: Deadline was 5 days ago! Immediate escalation needed.',
                'created_at' => $today->copy()->subDays(35),
                'order_date' => $today->copy()->subDays(32),
                'vendor_name' => 'Late Deliveries Inc',
                'po_number' => 'PO-2026-LATE',
                'delivery_date' => $today->copy()->subDays(20),
                'received_by' => 'Kumar',
                // Past deadline → Automatic RED 🚨
            ],

            // Example 9: Way Behind Schedule (50% behind)
            [
                'name' => '🚨 CRITICAL - Severely Behind',
                'deadline' => $today->copy()->addDays(10),
                'description' => 'Critical delay! 75% time passed but nothing done!',
                'created_at' => $today->copy()->subDays(30),
                // 40 days total, 30 days passed = 75% time
                // No stages = 0% progress
                // Behind by 75% → RED 🚨
            ],

            // Example 10: Barely Any Progress (40% behind)
            [
                'name' => '🚨 DANGER - No Progress Made',
                'deadline' => $today->copy()->addDays(5),
                'description' => 'Emergency! Deadline approaching fast with zero progress!',
                'created_at' => $today->copy()->subDays(25),
                'order_date' => $today->copy()->subDays(23),
                'vendor_name' => 'Crisis Management',
                'po_number' => 'PO-2026-CRIS',
                // 30 days total, 25 days passed = 83% time
                // Stage 1 done = 25% progress
                // Behind by 58% → RED 🚨
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->command->info('✅ Created ' . count($projects) . ' demonstration projects');
        $this->command->info('');
        $this->command->info('Status Distribution:');
        $this->command->info('  🟢 GREEN: 4 projects (Fresh, Ahead, On-track, Completed)');
        $this->command->info('  🟡 YELLOW: 3 projects (10-25% behind schedule)');
        $this->command->info('  🔴 RED: 3 projects (Overdue or >25% behind)');
        $this->command->info('');
        $this->command->info('Perfect for supervisor demonstration! 🎯');
    }
}
