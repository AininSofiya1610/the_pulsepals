<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerFinance;
use App\Models\Customer;
use Carbon\Carbon;

class CustomerInvoiceSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::whereNotNull('name')->get();
        
        if ($customers->isEmpty()) {
            $customers = collect([
                Customer::create(['name' => 'TechNova Solutions', 'email' => 'contact@technova.com']),
                Customer::create(['name' => 'Green Horizon Ltd', 'email' => 'info@greenhorizon.com']),
                Customer::create(['name' => 'Blue Sky Innovations', 'email' => 'hello@bluesky.com']),
                Customer::create(['name' => 'Apex Systems', 'email' => 'support@apex.com']),
            ]);
        }

        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::now();

        // Create 50 dummy invoices for 2025
        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $invoiceDate = Carbon::instance($startDate)->addDays(rand(0, $startDate->diffInDays($endDate)));
            $dueDate = Carbon::instance($invoiceDate)->addDays(rand(14, 45));
            $amount = rand(1000, 25000);
            $receivedAmount = rand(0, 1) ? $amount : (rand(0, 1) ? rand(0, $amount) : 0);

            CustomerFinance::create([
                'invoice_no' => 'CUST-INV-2025-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_name' => $customer->name,
                'description' => 'Dummy service invoice for ' . $customer->name . ' - Project ' . $i,
                'invoice_date' => $invoiceDate->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'invoice' => $amount,
                'received_amount' => $receivedAmount,
                'created_at' => $invoiceDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
            ]);
        }
    }
}
