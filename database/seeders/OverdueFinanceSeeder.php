<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerFinance;
use App\Models\VendorFinance;
use App\Models\Customer;
use App\Models\Vendor;
use Faker\Factory as Faker;
use Carbon\Carbon;

class OverdueFinanceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Overdue Customer Invoices (Accounts Receivable)
        // We need records where due_date < today AND status != Paid (or no payment linked)
        
        // Ensure some customers exist
        $customers = Customer::all();
        if ($customers->isEmpty()) {
            Customer::factory()->count(10)->create();
            $customers = Customer::all();
        }

        foreach (range(1, 20) as $index) {
            // Create a unique customer for each overdue record to ensure list size matches
            $customer = Customer::create([
                'name' => $faker->firstName . ' ' . $faker->lastName . ' ' . $faker->unique()->numerify('####'),
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'company' => $faker->company
            ]);
            
            $amount = $faker->randomFloat(2, 1000, 20000);
            
            // Create overdue invoice
            CustomerFinance::create([
                'invoice_no' => 'INV-OVERDUE-' . $faker->unique()->randomNumber(5),
                'customer_name' => $customer->name, // Ensure we use the freshly created unique name
                'type' => $faker->randomElement(['Service', 'Product', 'Subscription']),
                'description' => 'Overdue payment for ' . $faker->word,
                'invoice_date' => Carbon::now()->subDays(rand(60, 120)),
                'due_date' => Carbon::now()->subDays(rand(1, 45)), // Overdue by 1-45 days
                'amount' => $amount,
                'cogs' => $amount * 0.4, // 40% COGS
                'created_at' => Carbon::now()->subDays(rand(60, 120)),
                'updated_at' => Carbon::now(),
            ]);
            // Note: We deliberately do NOT create a linked CustomerPayment record, implying it is unpaid.
        }

        // 2. Overdue Vendor Bills (Accounts Payable)
        // We need records where due_date < today AND paid_amount < invoice_amount
        
        // Ensure some vendors exist
        $vendors = Vendor::all();
        
        // Fallback if no vendors exist (using model if available, or just strings)
        // Assuming Vendor model exists based on previous conversations.
        if ($vendors->isEmpty()) {
            // Minimal fallback if we can't create actual vendor models easily here
            $vendors = collect([['name' => 'Acme Supplies'], ['name' => 'Global Tech']]);
        }

        foreach (range(1, 50) as $index) {
            $vendor = $vendors->random();
            $invoiceAmount = $faker->randomFloat(2, 500, 10000);
            $paidAmount = 0; // Completely unpaid

            VendorFinance::create([
                'invoice_no' => 'BILL-OVERDUE-' . $faker->unique()->randomNumber(5),
                'vendor_name' => $vendor['name'] ?? 'Unknown Vendor',
                'description' => 'Overdue bill for ' . $faker->word,
                'invoice_date' => Carbon::now()->subDays(rand(60, 120)),
                'due_date' => Carbon::now()->subDays(rand(1, 45)), 
                'invoice' => $invoiceAmount, // Note: column name is 'invoice' not 'amount'
                'paid_amount' => $paidAmount,
                'created_at' => Carbon::now()->subDays(rand(60, 120)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
