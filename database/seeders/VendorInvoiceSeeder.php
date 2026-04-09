<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorFinance;
use App\Models\Vendor;
use Carbon\Carbon;

class VendorInvoiceSeeder extends Seeder
{
    public function run()
    {
        $vendors = Vendor::all();
        
        if ($vendors->isEmpty()) {
            $vendors = collect([
                Vendor::create(['vendorName' => 'Synergy Tech Solutions']),
                Vendor::create(['vendorName' => 'Global Logistics Co.']),
                Vendor::create(['vendorName' => 'Creative Media Agency']),
                Vendor::create(['vendorName' => 'Prime Office Supplies']),
            ]);
        }

        $vendorsCount = $vendors->count();
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::now();

        // Create 50 dummy invoices spread across 2025
        for ($i = 1; $i <= 50; $i++) {
            $vendor = $vendors->random();
            $invoiceDate = Carbon::instance($startDate)->addDays(rand(0, $startDate->diffInDays($endDate)));
            $dueDate = Carbon::instance($invoiceDate)->addDays(rand(7, 30));
            $amount = rand(500, 15000);
            $paidAmount = rand(0, 1) ? $amount : (rand(0, 1) ? rand(0, $amount) : 0);

            VendorFinance::create([
                'invoice_no' => 'INV-2025-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vendor_name' => $vendor->vendorName,
                'description' => 'Dummy invoice for ' . $vendor->vendorName . ' - Service ' . $i,
                'invoice_date' => $invoiceDate->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'invoice' => $amount,
                'paid_amount' => $paidAmount,
                'created_at' => $invoiceDate->copy()->addHours(rand(0, 23)), // Set created_at close to invoice_date
            ]);
        }
    }
}
