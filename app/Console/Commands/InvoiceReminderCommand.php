<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerFinance;
use App\Models\VendorFinance;
use App\Models\User;
use App\Mail\InvoiceOverdueMail;
use App\Mail\InvoicePaymentReminderMail;
use App\Mail\MonthlyReportMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvoiceReminderCommand extends Command
{
    protected $signature   = 'invoice:reminders';
    protected $description = 'Send invoice reminder, overdue, and monthly report emails';

    public function handle()
    {
        $this->info('Running invoice reminders...');

        $this->sendPaymentReminders();
        $this->sendOverdueNotices();

        // Monthly report — TESTING MODE (remove condition to send every run)
        $this->sendMonthlyReport();

        // Asal (1hb setiap bulan):(ubah tarikh monthly report kat sini!!!)
            //if (Carbon::today()->day === 1) {
                //$this->sendMonthlyReport();


        $this->info('Done.');
    }

    // ============================================
    // 1. PAYMENT REMINDER — 3 days before due date
    // ============================================
    private function sendPaymentReminders()
    {
        $targetDate = Carbon::today()->addDays(3)->toDateString();

        // Customer Finance
        $customerInvoices = CustomerFinance::with('payments')
            ->whereDate('due_date', $targetDate)
            ->get()
            ->filter(fn($inv) => $this->getBalance($inv) > 0);

        foreach ($customerInvoices as $invoice) {
            $this->sendReminderEmail($invoice, 'customer');
        }

        // Vendor Finance
        $vendorInvoices = VendorFinance::with('payments')
            ->whereDate('due_date', $targetDate)
            ->get()
            ->filter(fn($inv) => $this->getBalance($inv) > 0);

        foreach ($vendorInvoices as $invoice) {
            $this->sendReminderEmail($invoice, 'vendor');
        }

        $this->info("Payment reminders sent: " . ($customerInvoices->count() + $vendorInvoices->count()));
    }

    // ============================================
    // 2. OVERDUE NOTICE — due date dah lepas (consolidated)
    // ============================================
    private function sendOverdueNotices()
    {
        // Collect all overdue customer invoices with balance
        $overdueCustomer = CustomerFinance::with('payments')
            ->whereDate('due_date', '<', Carbon::today())
            ->get()
            ->filter(fn($inv) => $this->getBalance($inv) > 0)
            ->each(fn($inv) => $inv->_balance = $this->getBalance($inv));

        // Collect all overdue vendor invoices with balance
        $overdueVendor = VendorFinance::with('payments')
            ->whereDate('due_date', '<', Carbon::today())
            ->get()
            ->filter(fn($inv) => $this->getBalance($inv) > 0)
            ->each(fn($inv) => $inv->_balance = $this->getBalance($inv));

        if ($overdueCustomer->isEmpty() && $overdueVendor->isEmpty()) {
            $this->info("Overdue notices: no overdue invoices found.");
            return;
        }

        $admins = User::role('Admin')->get();

        foreach ($admins as $admin) {
            // Email 1: Customer overdue (skip if none)
            if ($overdueCustomer->isNotEmpty()) {
                try {
                    Mail::to($admin->email)->send(
                        new InvoiceOverdueMail($overdueCustomer, 'customer', $admin)
                    );
                    Log::info("Customer overdue email sent to {$admin->email} ({$overdueCustomer->count()} invoices).");
                } catch (\Exception $e) {
                    Log::error("Failed customer overdue email to {$admin->email}: " . $e->getMessage());
                }
            }

            // Email 2: Vendor overdue (skip if none)
            if ($overdueVendor->isNotEmpty()) {
                try {
                    Mail::to($admin->email)->send(
                        new InvoiceOverdueMail($overdueVendor, 'vendor', $admin)
                    );
                    Log::info("Vendor overdue email sent to {$admin->email} ({$overdueVendor->count()} invoices).");
                } catch (\Exception $e) {
                    Log::error("Failed vendor overdue email to {$admin->email}: " . $e->getMessage());
                }
            }
        }

        $this->info("Overdue emails sent to {$admins->count()} admin(s) — Customer: {$overdueCustomer->count()}, Vendor: {$overdueVendor->count()}.");
    }

    // ============================================
    // 3. MONTHLY REPORT — 1st of every month
    // ============================================
    private function sendMonthlyReport()
    {
        $lastMonth     = Carbon::now()->subMonth();
        $monthName     = $lastMonth->format('F Y');
        $startOfMonth  = $lastMonth->copy()->startOfMonth();
        $endOfMonth    = $lastMonth->copy()->endOfMonth();

        // Gather last month's data
        $customerInvoices = CustomerFinance::with('payments')
            ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
            ->get();

        $vendorInvoices = VendorFinance::with('payments')
            ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
            ->get();

        $totalRevenue = $customerInvoices->sum(fn($inv) =>
            ($inv->received_amount ?? 0) + $inv->payments->sum('amount')
        );

        $totalExpense = $vendorInvoices->sum(fn($inv) =>
            ($inv->paid_amount ?? 0) + $inv->payments->sum('amount')
        );

        $reportData = [
            'month'                   => $monthName,
            'total_revenue'           => $totalRevenue,
            'total_expense'           => $totalExpense,
            'net'                     => $totalRevenue - $totalExpense,
            'customer_invoice_count'  => $customerInvoices->count(),
            'vendor_invoice_count'    => $vendorInvoices->count(),
            'outstanding_customer'    => $customerInvoices->filter(fn($inv) => $this->getBalance($inv) > 0)->count(),
            'outstanding_vendor'      => $vendorInvoices->filter(fn($inv) => $this->getBalance($inv) > 0)->count(),
        ];

        // Send to all admin users
        $admins = User::role('Admin')->get(); // requires spatie/laravel-permission

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new MonthlyReportMail($reportData, $admin));
                Log::info("Monthly report sent to {$admin->email}");
            } catch (\Exception $e) {
                Log::error("Failed monthly report to {$admin->email}: " . $e->getMessage());
            }
        }

        $this->info("Monthly report sent to " . $admins->count() . " admin(s).");
    }

    // ============================================
    // HELPERS
    // ============================================
    private function getBalance($invoice): float
    {
        // VendorFinance uses 'invoice' column & 'paid_amount'
        // CustomerFinance uses 'amount' column & 'received_amount'
        $total    = $invoice->invoice ?? $invoice->amount ?? 0;
        $paid     = ($invoice->paid_amount ?? $invoice->received_amount ?? 0)
                    + $invoice->payments->sum('amount');
        return max(0, $total - $paid);
    }

    private function sendReminderEmail($invoice, string $type): void
    {
        // Get admin emails
        $admins = User::role('Admin')->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new InvoicePaymentReminderMail($invoice, $type, $this->getBalance($invoice)));
                Log::info("Payment reminder sent [{$type}] invoice #{$invoice->invoice_no} to {$admin->email}");
            } catch (\Exception $e) {
                Log::error("Failed reminder [{$type}] #{$invoice->invoice_no}: " . $e->getMessage());
            }
        }
    }

}
