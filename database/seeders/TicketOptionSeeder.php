<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketOption;

class TicketOptionSeeder extends Seeder
{
    public function run()
    {
        // Clear existing to avoid duplicates if re-run
        TicketOption::truncate();

        // Priorities
        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        foreach ($priorities as $index => $priority) {
            TicketOption::create([
                'type' => 'priority',
                'value' => $priority,
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        // Categories (Help Topics)
        $categories = [
            'Hardware Issue',
            'Software Issue',
            'Network Problem',
            'Server Issue',
            'Access Request',
            'Maintenance Request',
            'Other'
        ];
        foreach ($categories as $index => $category) {
            TicketOption::create([
                'type' => 'category',
                'value' => $category,
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        // Ticket Types
        $ticketTypes = ['PM', 'CM'];
        foreach ($ticketTypes as $index => $type) {
            TicketOption::create([
                'type' => 'ticket_type',
                'value' => $type,
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}
