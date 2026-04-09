<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorFinance;
use Carbon\Carbon;

class OverdueVendorSeeder extends Seeder
{
    public function run()
    {
        $vendors = [
            'Global Tech Solutions', 'Summit Enterprises', 'Nexus Logistics', 'Pinnacle Systems',
            'Quantum Dynamics', 'Horizon Group', 'Velocity Corp', 'Matrix Industries',
            'Blue Chip Data', 'Silver Line Services', 'Golden Gate Media', 'Iron Clad Security',
            'Swift Delivery Co', 'Urban Living Inc', 'Oceanic Explorations', 'Mountain Peak Gear',
            'Zenith Biotech', 'Infinity Software', 'Core Fiber Networks', 'Delta Heavy Machineries',
            'Alpha Consulting', 'Beta Manufacturing', 'Gamma Electronics', 'Omega Services',
            'Prime Solutions', 'Apex Systems', 'Nova Technologies', 'Star Communications'
        ];

        // Generate 50 overdue vendor invoices
        for ($i = 1; $i <= 50; $i++) {
            $vendor = $vendors[array_rand($vendors)];
            $daysOverdue = rand(1, 90); // Overdue by 1 to 90 days
            $dueDate = Carbon::now()->subDays($daysOverdue);
            $invoiceDate = $dueDate->copy()->subDays(rand(14, 45)); // Invoice created before due date
            $amount = rand(500, 25000);
            $paidAmount = rand(0, 1) ? 0 : rand(0, (int)($amount * 0.5)); // Mostly unpaid or partially paid

            VendorFinance::create([
                'invoice_no' => 'OVD-V-2025-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vendor_name' => $vendor,
                'description' => 'Overdue service invoice for ' . $vendor,
                'invoice_date' => $invoiceDate->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'invoice' => $amount,
                'paid_amount' => $paidAmount,
                'created_at' => $invoiceDate->copy()->addHours(rand(0, 23)),
            ]);
        }
    }
}
