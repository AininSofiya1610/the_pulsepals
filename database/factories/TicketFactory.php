<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        $units = ['System', 'Network', 'Technical Support'];
        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $statuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
        
        $selectedUnit = $this->faker->randomElement($units);
        
        // Context-aware subjects based on Unit
        $subjects = match($selectedUnit) {
            'System' => [
                'Server CPU high utilization', 'Database backup failed', 'OS patch deployment error', 
                'AD User sync issue', 'Application wrapper crash', 'Log rotation failure'
            ],
            'Network' => [
                'VPN connection timeout', 'Switch port flapping', 'Firewall rule request', 
                'Wifi signal weak in Lobby', 'Internet slow', 'LAN cable damaged'
            ],
            'Technical Support' => [
                'Printer jammed', 'Password reset request', 'Monitor flickering', 
                'Outlook not syncing', 'New employee hardware setup', 'Software installation help'
            ]
        };

        $createdAt = $this->faker->dateTimeBetween('-30 days', 'now');
        $status = $this->faker->randomElement($statuses);
        
        // Logic for timestamps based on status
        $firstResponseAt = null;
        $resolvedAt = null;
        $closedAt = null;

        if ($status !== 'Open') {
            // If active/done, must have a response
            $firstResponseAt = clone $createdAt;
            $firstResponseAt->modify('+' . rand(10, 240) . ' minutes'); 
        }

        if (in_array($status, ['Resolved', 'Closed'])) {
            // If resolved/closed, must have resolution time
            $resolvedAt = clone $createdAt;
            $resolvedAt->modify('+' . rand(1, 48) . ' hours');
        }

        if ($status === 'Closed') {
            $closedAt = clone $resolvedAt;
            $closedAt->modify('+' . rand(1, 24) . ' hours');
        }

        return [
            'subject' => $this->faker->randomElement($subjects) . ' - ' . $this->faker->bothify('##??'),
            'unit' => $selectedUnit,
            'priority' => $this->faker->randomElement($priorities),
            'status' => $status,
            'assigned_to' => 1, // Default user for now
            'created_at' => $createdAt,
            'first_response_at' => $firstResponseAt,
            'resolved_at' => $resolvedAt,
            'closed_at' => $closedAt,
            'updated_at' => $createdAt, // Keep consistent
        ];
    }
}
