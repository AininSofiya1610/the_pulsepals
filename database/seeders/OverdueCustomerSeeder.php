<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerFinance;
use Carbon\Carbon;

class OverdueCustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            'TechNova Solutions', 'Green Horizon Ltd', 'Blue Sky Innovations', 'Apex Systems',
            'Stellar Enterprises', 'Titan Industries', 'Phoenix Digital', 'Aurora Tech',
            'Vanguard Corp', 'Eclipse Media', 'Harmony Group', 'Fusion Dynamics',
            'Catalyst Partners', 'Synergy Labs', 'Pioneer Holdings', 'Vertex Solutions',
            'Summit Digital', 'Quantum Media', 'Radiant Systems', 'Prism Technologies',
            'Pulse Enterprises', 'Nexus Partners', 'Orbit Communications', 'Spark Innovations',
            'Wave Analytics', 'River Tech', 'Forest Industries', 'Ocean Logistics'
        ];

        // Generate 50 overdue customer invoices
        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers[array_rand($customers)];
            $daysOverdue = rand(1, 90); // Overdue by 1 to 90 days
            $dueDate = Carbon::now()->subDays($daysOverdue);
            $invoiceDate = $dueDate->copy()->subDays(rand(14, 45)); // Invoice created before due date
            $amount = rand(1000, 50000);
            $receivedAmount = rand(0, 1) ? 0 : rand(0, (int)($amount * 0.5)); // Mostly unpaid or partially paid

            CustomerFinance::create([
                'invoice_no' => 'OVD-C-2025-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_name' => $customer,
                'description' => 'Overdue service invoice for ' . $customer,
                'invoice_date' => $invoiceDate->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'invoice' => $amount,
                'received_amount' => $receivedAmount,
                'created_at' => $invoiceDate->copy()->addHours(rand(0, 23)),
            ]);
        }
    }
}
