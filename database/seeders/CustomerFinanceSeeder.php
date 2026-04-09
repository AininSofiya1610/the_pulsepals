<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerFinance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerFinanceSeeder extends Seeder
{
    /**
     * Seed sample invoices for Revenue Performance chart.
     */
    public function run(): void
    {
        // Sample invoice data for current year (2026)
        $invoices = [
            // January - RM75,000
            ['invoice_no' => 'INV-2026-001', 'customer_name' => 'ABC Sdn Bhd', 'amount' => 45000, 'invoice_date' => '2026-01-10', 'type' => 'Service', 'cogs' => 20000],
            ['invoice_no' => 'INV-2026-002', 'customer_name' => 'XYZ Enterprise', 'amount' => 30000, 'invoice_date' => '2026-01-15', 'type' => 'Product', 'cogs' => 15000],
        ];

        foreach ($invoices as $invoice) {
            DB::table('customer_finances')->insert([
                'invoice_no' => $invoice['invoice_no'],
                'customer_name' => $invoice['customer_name'],
                'amount' => $invoice['amount'],
                'invoice_date' => $invoice['invoice_date'],
                'due_date' => Carbon::parse($invoice['invoice_date'])->addDays(30),
                'type' => $invoice['type'],
                'cogs' => $invoice['cogs'],
                'description' => 'Sample invoice for Revenue Performance demo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Created sample invoices for Revenue Performance.');
        $this->command->info('   Total: RM' . number_format(collect($invoices)->sum('amount')));
    }
}
