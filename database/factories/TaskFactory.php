<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * TaskFactory
 * 
 * Generates realistic task data with:
 * - Random task titles
 * - Assignment to random users
 * - Links to sample deals
 * - Mix of statuses (open, done, overdue scenarios)
 * - Due dates spanning past and next 2 months
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        $taskTitles = [
            'Initial Opportunity Review',
            'Follow-up Call with Client',
            'Send Proposal Document',
            'Contract Negotiation',
            'Schedule Demo Meeting',
            'Prepare Presentation',
            'Client Onboarding',
            'Quarterly Review',
            'Invoice Follow-up',
            'Technical Support Request',
            'Product Training Session',
            'Renewal Discussion',
            'Upsell Opportunity',
            'Customer Feedback Collection',
            'Partnership Proposal',
            'Budget Approval',
            'Stakeholder Meeting',
            'Risk Assessment',
            'Quality Assurance Check',
            'Final Delivery Confirmation',
        ];

        // Get random user and deal IDs
        $userIds = User::pluck('id')->toArray();
        $dealIds = Deal::pluck('id')->toArray();
        
        // Random due date: -2 months to +2 months
        $dueDate = $this->faker->dateTimeBetween('-2 months', '+2 months');
        $isPast = $dueDate < now();
        
        // Status logic: past dates can be done or remain open (overdue)
        // Future dates are mostly open
        if ($isPast) {
            $status = $this->faker->randomElement(['done', 'open', 'open']); // 33% done, 67% overdue
        } else {
            $status = $this->faker->randomElement(['open', 'open', 'done']); // 67% open, 33% done
        }

        return [
            'title' => $this->faker->randomElement($taskTitles),
            'assigned_to' => !empty($userIds) ? $this->faker->randomElement($userIds) : 1,
            'related_to_deal' => !empty($dealIds) ? $this->faker->optional(0.7)->randomElement($dealIds) : null,
            'due_date' => $dueDate,
            'status' => $status,
            'created_at' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
        ];
    }
}
