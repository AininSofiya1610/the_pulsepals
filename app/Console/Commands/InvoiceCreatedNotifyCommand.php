<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerFinance;
use App\Models\VendorFinance;
use App\Models\User;
use App\Mail\InvoiceCreatedMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoiceCreatedNotifyCommand extends Command
{
    protected $signature   = 'invoice:notify-new';
    protected $description = 'Send email notifications for invoices created today';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // Customer invoices created today
        $newCustomer = CustomerFinance::whereDate('created_at', $today)->get();
        foreach ($newCustomer as $invoice) {
            $this->notify($invoice, 'customer');
        }

        // Vendor invoices created today
        $newVendor = VendorFinance::whereDate('created_at', $today)->get();
        foreach ($newVendor as $invoice) {
            $this->notify($invoice, 'vendor');
        }

        $this->info("New invoice notifications sent: " . ($newCustomer->count() + $newVendor->count()));
    }

    private function notify($invoice, string $type): void
    {
        $admins = User::role('Admin')->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new InvoiceCreatedMail($invoice, $type));
                Log::info("New invoice notification [{$type}] #{$invoice->invoice_no} to {$admin->email}");
            } catch (\Exception $e) {
                Log::error("Failed new invoice notify [{$type}] #{$invoice->invoice_no}: " . $e->getMessage());
            }
        }
    }
}
