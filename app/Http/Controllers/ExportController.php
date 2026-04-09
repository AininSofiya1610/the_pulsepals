<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use App\Models\CompanyFinance;
use App\Models\CustomerFinance;
use App\Models\VendorFinance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportController extends Controller
{
    // ─── Brand colours ───────────────────────────────────────────────────
    private const C_DARK       = '18181B';
    private const C_PRIMARY    = '2563EB';
    private const C_ACCENT     = '4F46E5';
    private const C_ACCENT_BG  = 'EEF2FF';
    private const C_STRIPE     = 'F8FAFC';
    private const C_BORDER     = 'E2E8F0';
    private const C_SUMMARY_BG = 'F1F5F9';
    private const C_WHITE      = 'FFFFFF';
    private const C_TEXT       = '1E293B';
    private const C_MUTED      = '64748B';
    private const C_GREEN      = '16A34A';
    private const C_RED        = 'DC2626';
    private const C_ORANGE     = 'F97316';
    private const C_YELLOW     = 'F59E0B';
    private const C_BLUE       = '3B82F6';
    private const C_GRAY       = '6B7280';

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: LEADS
    // ═══════════════════════════════════════════════════════════════════

    public function exportLeads(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = Lead::with('assignedTo')
            ->whereNotIn('status', ['qualified', 'converted'])
            ->latest('created_at');
        if ($sd) $query->whereDate('created_at', '>=', $sd);
        if ($ed) $query->whereDate('created_at', '<=', $ed);
        $data = $query->get();

        $cols    = ['A','B','C','D','E','F','G'];
        $headers = ['No', 'Name', 'Email', 'Phone', 'Source', 'Assigned To', 'Created'];
        $widths  = [6, 24, 28, 16, 14, 20, 16];
        $row = $this->header($sheet, 'LEADS REPORT', $cols, $headers, $widths, $data->count(), 'Total Active Leads', $sd, $ed);

        foreach ($data as $i => $lead) {
            $r = $row + $i;
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", $lead->name);
            $sheet->setCellValue("C{$r}", $lead->email ?? 'N/A');
            $sheet->setCellValue("D{$r}", $lead->phone ?? 'N/A');
            $sheet->setCellValue("E{$r}", $lead->source ?? 'N/A');
            $sheet->setCellValue("F{$r}", $lead->assignedTo->name ?? 'Unassigned');
            $sheet->setCellValue("G{$r}", Carbon::parse($lead->created_at)->format('M d, Y'));
            $this->stripe($sheet, $r, 'G', $i);
            $this->center($sheet, $r, ['A','G']);
        }

        $this->finish($sheet, $row, $data->count(), 'G');
        return $this->download($spreadsheet, 'Leads_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: CUSTOMERS
    // ═══════════════════════════════════════════════════════════════════

    public function exportCustomers(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Finance Customers');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = \App\Models\FinanceCustomer::orderBy('name', 'asc');
        if ($sd) $query->whereDate('created_at', '>=', $sd);
        if ($ed) $query->whereDate('created_at', '<=', $ed);
        $data = $query->get();

        $cols    = ['A','B','C'];
        $headers = ['No', 'Customer Name', 'Created Date'];
        $widths  = [6, 34, 18];
        $row = $this->header($sheet, 'FINANCE CUSTOMER LIST', $cols, $headers, $widths, $data->count(), 'Total Customers', $sd, $ed);

        foreach ($data as $i => $c) {
            $r = $row + $i;
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", $c->name ?: 'N/A');
            $sheet->setCellValue("C{$r}", $c->created_at->format('M d, Y'));
            $this->stripe($sheet, $r, 'C', $i);
            $this->center($sheet, $r, ['A','C']);
        }

        $this->finish($sheet, $row, $data->count(), 'C');
        return $this->download($spreadsheet, 'Finance_Customers_Report');
    }

    public function exportCrmCustomers(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CRM Customers');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = Customer::orderBy('name', 'asc');
        if ($sd) $query->whereDate('created_at', '>=', $sd);
        if ($ed) $query->whereDate('created_at', '<=', $ed);
        $data = $query->get();

        $cols    = ['A','B','C','D','E'];
        $headers = ['No', 'Customer Name', 'Email', 'Phone', 'Created Date'];
        $widths  = [6, 30, 28, 18, 18];
        $row = $this->header($sheet, 'CRM CUSTOMER LIST', $cols, $headers, $widths, $data->count(), 'Total Customers', $sd, $ed);

        foreach ($data as $i => $c) {
            $r = $row + $i;
            $name = $c->name ?: 'N/A';
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", $name);
            $sheet->setCellValue("C{$r}", $c->email ?: '-');
            $sheet->setCellValue("D{$r}", $c->phone ?: '-');
            $sheet->setCellValue("E{$r}", $c->created_at->format('M d, Y'));
            $this->stripe($sheet, $r, 'E', $i);
            $this->center($sheet, $r, ['A','E']);
        }

        $this->finish($sheet, $row, $data->count(), 'E');
        return $this->download($spreadsheet, 'CRM_Customers_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: SALES PIPELINE
    // ═══════════════════════════════════════════════════════════════════

    public function exportDeals(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Pipeline');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = Deal::with('customer')->orderBy('updated_at', 'desc');
        if ($sd) $query->whereDate('created_at', '>=', $sd);
        if ($ed) $query->whereDate('created_at', '<=', $ed);
        $data = $query->get();

        $pv = $data->whereNotIn('stage', ['closed_won','closed_lost'])->sum('value');
        $wv = $data->where('stage', 'closed_won')->sum('value');
        $summary = $data->count() . '   |   Pipeline: RM ' . number_format($pv, 2) . '   |   Won: RM ' . number_format($wv, 2);

        $cols    = ['A','B','C','D','E','F','G'];
        $headers = ['No', 'Deal Title', 'Customer', 'Value (RM)', 'Stage', 'Closed Reason', 'Created'];
        $widths  = [6, 28, 22, 18, 16, 22, 16];
        $row = $this->header($sheet, 'SALES PIPELINE REPORT', $cols, $headers, $widths, $summary, 'Summary', $sd, $ed);

        foreach ($data as $i => $deal) {
            $r = $row + $i;
            $stage = Deal::STAGES[$deal->stage] ?? ucfirst(str_replace('_', ' ', $deal->stage));
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", $deal->title);
            $sheet->setCellValue("C{$r}", $deal->customer->name ?? 'N/A');
            $sheet->setCellValue("D{$r}", $deal->value);
            $sheet->setCellValue("E{$r}", $stage);
            $sheet->setCellValue("F{$r}", $deal->closed_reason ?? '-');
            $sheet->setCellValue("G{$r}", Carbon::parse($deal->created_at)->format('M d, Y'));
            $this->stripe($sheet, $r, 'G', $i);
            $this->center($sheet, $r, ['A','E','G']);
            $sheet->getStyle("D{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sc = match($deal->stage) {
                'closed_won' => self::C_GREEN, 'closed_lost' => self::C_RED,
                'negotiation' => self::C_ORANGE, 'proposal' => self::C_BLUE, default => self::C_TEXT,
            };
            $sheet->getStyle("E{$r}")->getFont()->getColor()->setRGB($sc);
            $sheet->getStyle("E{$r}")->getFont()->setBold(true);
        }

        $this->finish($sheet, $row, $data->count(), 'G');
        return $this->download($spreadsheet, 'Sales_Pipeline_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: TASKS
    // ═══════════════════════════════════════════════════════════════════

    public function exportTasks(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tasks');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = Task::with(['user','deal'])->orderBy('created_at', 'desc');
        if ($sd) $query->whereDate('created_at', '>=', $sd);
        if ($ed) $query->whereDate('created_at', '<=', $ed);
        $data = $query->get();

        $oc = $data->where('status', 'open')->count();
        $dc = $data->where('status', 'done')->count();
        $summary = $data->count() . '   |   Open: ' . $oc . '   |   Done: ' . $dc;

        $cols    = ['A','B','C','D','E','F'];
        $headers = ['No', 'Task Title', 'Assigned To', 'Related Deal', 'Due Date', 'Status'];
        $widths  = [6, 30, 20, 24, 16, 12];
        $row = $this->header($sheet, 'TASKS MANAGEMENT REPORT', $cols, $headers, $widths, $summary, 'Summary', $sd, $ed);

        foreach ($data as $i => $task) {
            $r = $row + $i;
            $status = match($task->computed_status) {
                'done' => 'Done', 'overdue' => 'Overdue', default => 'Open',
            };
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", $task->title);
            $sheet->setCellValue("C{$r}", $task->user->name ?? 'N/A');
            $sheet->setCellValue("D{$r}", $task->deal->title ?? 'N/A');
            $sheet->setCellValue("E{$r}", $task->due_date ? Carbon::parse($task->due_date)->format('M d, Y') : 'No due date');
            $sheet->setCellValue("F{$r}", $status);
            $this->stripe($sheet, $r, 'F', $i);
            $this->center($sheet, $r, ['A','E','F']);

            $sc = match($status) { 'Overdue' => self::C_RED, 'Done' => self::C_GREEN, default => self::C_YELLOW };
            $sheet->getStyle("F{$r}")->getFont()->getColor()->setRGB($sc);
            $sheet->getStyle("F{$r}")->getFont()->setBold(true);
        }

        $this->finish($sheet, $row, $data->count(), 'F');
        return $this->download($spreadsheet, 'Tasks_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: TICKETS
    // ═══════════════════════════════════════════════════════════════════

    public function exportTickets(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tickets');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = \App\Models\Ticket::with('unit')->orderBy('created_at', 'desc');
        if ($sd) $query->whereDate('created_at', '>=', $sd);
        if ($ed) $query->whereDate('created_at', '<=', $ed);

        // Search filter (same as TicketController@index)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $data = $query->get();

        $oc = $data->where('status', 'Open')->count();
        $ip = $data->where('status', 'In Progress')->count();
        $rv = $data->where('status', 'Resolved')->count();
        $cl = $data->where('status', 'Closed')->count();
        $summary = $data->count() . '   |   Open: ' . $oc . '   |   In Progress: ' . $ip . '   |   Resolved: ' . $rv . '   |   Closed: ' . $cl;

        $cols    = ['A','B','C','D','E','F','G','H','I','J'];
        $headers = ['No', 'Ticket ID', 'Title', 'Name', 'Email', 'Status', 'Priority', 'Category', 'Unit', 'Created'];
        $widths  = [6, 18, 30, 20, 28, 14, 12, 18, 14, 16];
        $row = $this->header($sheet, 'ALL TICKETS REPORT', $cols, $headers, $widths, $summary, 'Summary', $sd, $ed);

        foreach ($data as $i => $t) {
            $r = $row + $i;
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValueExplicit("B{$r}", $t->ticket_id, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$r}", $t->title);
            $sheet->setCellValue("D{$r}", $t->full_name ?? 'N/A');
            $sheet->setCellValue("E{$r}", $t->email ?? 'N/A');
            $sheet->setCellValue("F{$r}", $t->status);
            $sheet->setCellValue("G{$r}", $t->priority);
            $sheet->setCellValue("H{$r}", $t->category);
            $sheet->setCellValue("I{$r}", $t->unit->name ?? 'N/A');
            $sheet->setCellValue("J{$r}", Carbon::parse($t->created_at)->format('M d, Y'));
            $this->stripe($sheet, $r, 'J', $i);
            $this->center($sheet, $r, ['A','B','F','G','J']);

            // Status colour
            $sc = match($t->status) {
                'Open' => self::C_YELLOW, 'In Progress' => self::C_BLUE, 'Resolved' => self::C_GREEN,
                'Closed' => self::C_GRAY, 'Critical' => self::C_RED, default => self::C_TEXT,
            };
            $sheet->getStyle("F{$r}")->getFont()->getColor()->setRGB($sc);
            $sheet->getStyle("F{$r}")->getFont()->setBold(true);

            // Priority colour
            $pc = match($t->priority) {
                'Critical' => self::C_RED, 'High' => self::C_ORANGE,
                'Medium' => self::C_BLUE, 'Low' => self::C_GRAY, default => self::C_TEXT,
            };
            $sheet->getStyle("G{$r}")->getFont()->getColor()->setRGB($pc);
            $sheet->getStyle("G{$r}")->getFont()->setBold(true);
        }

        $this->finish($sheet, $row, $data->count(), 'J');
        return $this->download($spreadsheet, 'Tickets_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: COMPANY FINANCE
    // ═══════════════════════════════════════════════════════════════════

    public function exportCompanyFinance(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Company Finance');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = CompanyFinance::orderBy('record_date', 'desc')->orderBy('id', 'desc');
        if ($sd) $query->whereDate('record_date', '>=', $sd);
        if ($ed) $query->whereDate('record_date', '<=', $ed);
        $data = $query->get();

        $cols    = ['A','B','C','D','E','F'];
        $headers = ['No', 'Record Date', 'MBB Balance (RM)', 'RHB Balance (RM)', 'Total Cash (RM)', 'Net Pay (RM)'];
        $widths  = [6, 16, 20, 20, 20, 20];
        $row = $this->header($sheet, 'COMPANY FINANCE RECORDS', $cols, $headers, $widths, $data->count(), 'Total Records', $sd, $ed);

        foreach ($data as $i => $rec) {
            $r = $row + $i;
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", Carbon::parse($rec->record_date)->format('M d, Y'));
            $sheet->setCellValue("C{$r}", $rec->mbb_balance);
            $sheet->setCellValue("D{$r}", $rec->rhb_balance);
            $sheet->setCellValue("E{$r}", $rec->mbb_balance + $rec->rhb_balance);
            $sheet->setCellValue("F{$r}", $rec->net_pay);
            $this->stripe($sheet, $r, 'F', $i);
            $this->center($sheet, $r, ['A','B']);
            $sheet->getStyle("C{$r}:F{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("C{$r}:F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $this->finish($sheet, $row, $data->count(), 'F');
        return $this->download($spreadsheet, 'Company_Finance_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: CUSTOMER FINANCE
    // ═══════════════════════════════════════════════════════════════════

    public function exportCustomerFinance(Request $request)
    {
        $spreadsheet = $this->make();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Finance');
        $sd = $request->input('start_date');
        $ed = $request->input('end_date');

        $query = CustomerFinance::with('payments')->orderBy('invoice_date', 'desc');
        if ($sd) $query->whereDate('invoice_date', '>=', $sd);
        if ($ed) $query->whereDate('invoice_date', '<=', $ed);
        $data = $query->get();

        $cols    = ['A','B','C','D','E','F','G','H','I'];
        $headers = ['No', 'Invoice No', 'Customer', 'Invoice Date', 'Due Date', 'Status', 'Invoice Amt (RM)', 'Received (RM)', 'Balance (RM)'];
        $widths  = [6, 16, 24, 16, 16, 12, 18, 18, 18];
        $row = $this->header($sheet, 'CUSTOMER FINANCE INVOICES', $cols, $headers, $widths, $data->count(), 'Total Invoices', $sd, $ed);

        foreach ($data as $i => $inv) {
            $r        = $row + $i;
            $received = ($inv->received_amount ?? 0) + $inv->payments->sum('amount');
            $balance  = $inv->amount - $received;
            $dueDate  = Carbon::parse($inv->due_date);

            // ── Calculate status (same logic as blade view) ──────────
            if ($balance <= 0) {
                $status      = 'Paid';
                $statusColor = self::C_GREEN;
            } elseif ($received > 0) {
                $status      = 'Partial';
                $statusColor = self::C_ORANGE;
            } elseif ($dueDate->isPast()) {
                $status      = 'Overdue';
                $statusColor = self::C_RED;
            } else {
                $status      = 'Pending';
                $statusColor = self::C_GRAY;
            }

            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValueExplicit("B{$r}", $inv->invoice_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$r}", $inv->customer_name);
            $sheet->setCellValue("D{$r}", Carbon::parse($inv->invoice_date)->format('d M Y'));
            $sheet->setCellValue("E{$r}", $dueDate->format('d M Y'));
            $sheet->setCellValue("F{$r}", $status);          // ← Status now beside Due Date
            $sheet->setCellValue("G{$r}", $inv->amount);
            $sheet->setCellValue("H{$r}", $received);
            $sheet->setCellValue("I{$r}", $balance);

            $this->stripe($sheet, $r, 'I', $i);
            $this->center($sheet, $r, ['A','B','D','E','F']);
            $sheet->getStyle("G{$r}:I{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("G{$r}:I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Red balance when still owed
            if ($balance > 0) {
                $sheet->getStyle("I{$r}")->getFont()->getColor()->setRGB(self::C_RED);
                $sheet->getStyle("I{$r}")->getFont()->setBold(true);
            }

            // Colour-code the Status cell
            $sheet->getStyle("F{$r}")->getFont()->getColor()->setRGB($statusColor);
            $sheet->getStyle("F{$r}")->getFont()->setBold(true);
        }

        $this->finish($sheet, $row, $data->count(), 'I');
        return $this->download($spreadsheet, 'Customer_Finance_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: VENDOR FINANCE
    // ═══════════════════════════════════════════════════════════════════
public function exportVendorFinance(Request $request)
{
    $spreadsheet = $this->make();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Vendor Finance');
    $sd = $request->input('start_date');
    $ed = $request->input('end_date');

    $query = VendorFinance::with('payments')->orderBy('invoice_date', 'desc');
    if ($sd) $query->whereDate('invoice_date', '>=', $sd);
    if ($ed) $query->whereDate('invoice_date', '<=', $ed);
    $data = $query->get();

    $cols    = ['A','B','C','D','E','F','G','H','I'];
    $headers = ['No', 'Invoice No', 'Vendor', 'Invoice Date', 'Due Date', 'Status', 'Invoice (RM)', 'Paid (RM)', 'Balance (RM)'];
    $widths  = [6, 16, 24, 16, 16, 12, 18, 18, 18];
    $row = $this->header($sheet, 'VENDOR FINANCE TRANSACTIONS', $cols, $headers, $widths, $data->count(), 'Total Transactions', $sd, $ed);

    foreach ($data as $i => $txn) {
        $r       = $row + $i;
        $paid    = ($txn->paid_amount ?? 0) + $txn->payments->sum('amount');
        $balance = $txn->invoice - $paid;
        $dueDate = Carbon::parse($txn->due_date);

        if ($balance <= 0) {
            $status = 'Paid';    $sc = self::C_GREEN;
        } elseif ($paid > 0) {
            $status = 'Partial'; $sc = self::C_ORANGE;
        } elseif ($dueDate->isPast()) {
            $status = 'Overdue'; $sc = self::C_RED;
        } else {
            $status = 'Pending'; $sc = self::C_GRAY;
        }

        $sheet->setCellValue("A{$r}", $i + 1);
        $sheet->setCellValueExplicit("B{$r}", $txn->invoice_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue("C{$r}", $txn->vendor_name);
        $sheet->setCellValue("D{$r}", Carbon::parse($txn->invoice_date)->format('M d, Y'));
        $sheet->setCellValue("E{$r}", $dueDate->format('M d, Y'));
        $sheet->setCellValue("F{$r}", $status);
        $sheet->setCellValue("G{$r}", $txn->invoice);
        $sheet->setCellValue("H{$r}", $paid);
        $sheet->setCellValue("I{$r}", $balance);

        $this->stripe($sheet, $r, 'I', $i);
        $this->center($sheet, $r, ['A','B','D','E','F']);
        $sheet->getStyle("G{$r}:I{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("G{$r}:I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("F{$r}")->getFont()->getColor()->setRGB($sc);
        $sheet->getStyle("F{$r}")->getFont()->setBold(true);

        if ($balance > 0) {
            $sheet->getStyle("I{$r}")->getFont()->getColor()->setRGB(self::C_RED);
            $sheet->getStyle("I{$r}")->getFont()->setBold(true);
        }
    }

    $this->finish($sheet, $row, $data->count(), 'I');
    return $this->download($spreadsheet, 'Vendor_Finance_Report');
}
    // ═══════════════════════════════════════════════════════════════════
    //  EXPORT: PROJECT SNAPSHOT (Performance/ITSM)
    // ═══════════════════════════════════════════════════════════════════

    public function exportSnapshot(Request $request)
    {
        $spreadsheet = $this->make();
        $selectedYear = $request->input('year', Carbon::now()->year);

        // ─── SHEET 1: Revenue Performance ────────────────────────────
        $s1 = $spreadsheet->getActiveSheet();
        $s1->setTitle('Revenue Performance');

        // Calculate actual monthly revenue (same logic as controller)
        $initialPayments = CustomerFinance::selectRaw('MONTH(invoice_date) as month, SUM(received_amount) as total')
            ->whereYear('invoice_date', $selectedYear)
            ->where('received_amount', '>', 0)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $additionalPayments = \DB::table('customer_payments')
            ->join('customer_finances', 'customer_payments.customer_finance_id', '=', 'customer_finances.id')
            ->selectRaw('MONTH(customer_finances.invoice_date) as month, SUM(customer_payments.amount) as total')
            ->whereYear('customer_finances.invoice_date', $selectedYear)
            ->where('customer_payments.amount', '>', 0)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyReceived = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyReceived[$m] = ($initialPayments[$m] ?? 0) + ($additionalPayments[$m] ?? 0);
        }

        $budgetData = \App\Models\RevenueBudget::getMonthlyBudgets($selectedYear);
        if (empty($budgetData)) {
            $budgetData = array_fill(1, 12, 0);
        }

        $ytdReceivedTotal = array_sum($monthlyReceived);
        $ytdBudgetTotal = array_sum($budgetData);
        $ytdPercent = $ytdBudgetTotal > 0 ? round(($ytdReceivedTotal / $ytdBudgetTotal) * 100, 1) : 0;
        $summaryText = 'Year: ' . $selectedYear . '   |   YTD Actual: RM ' . number_format($ytdReceivedTotal, 2) . '   |   YTD Budget: RM ' . number_format($ytdBudgetTotal, 2) . '   |   Achievement: ' . $ytdPercent . '%';

        $cols1    = ['A','B','C','D','E'];
        $headers1 = ['Month', 'Budget Target (RM)', 'Actual Revenue (RM)', 'Variance (RM)', 'Achievement (%)'];
        $widths1  = [14, 22, 22, 22, 18];
        $row = $this->header($s1, 'REVENUE PERFORMANCE REPORT', $cols1, $headers1, $widths1, $summaryText, 'Summary', null, null);

        $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        for ($m = 1; $m <= 12; $m++) {
            $r = $row + ($m - 1);
            $budget  = $budgetData[$m] ?? 0;
            $actual  = $monthlyReceived[$m];
            $variance = $actual - $budget;
            $pct = $budget > 0 ? round(($actual / $budget) * 100, 1) : 0;

            $s1->setCellValue("A{$r}", $monthNames[$m - 1] . ' ' . $selectedYear);
            $s1->setCellValue("B{$r}", $budget);
            $s1->setCellValue("C{$r}", $actual);
            $s1->setCellValue("D{$r}", $variance);
            $s1->setCellValue("E{$r}", $pct . '%');
            $this->stripe($s1, $r, 'E', $m - 1);
            $this->center($s1, $r, ['A','E']);
            $s1->getStyle("B{$r}:D{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
            $s1->getStyle("B{$r}:D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Red for negative variance, green for positive
            if ($variance < 0) {
                $s1->getStyle("D{$r}")->getFont()->getColor()->setRGB(self::C_RED);
                $s1->getStyle("D{$r}")->getFont()->setBold(true);
            } elseif ($variance > 0) {
                $s1->getStyle("D{$r}")->getFont()->getColor()->setRGB(self::C_GREEN);
                $s1->getStyle("D{$r}")->getFont()->setBold(true);
            }
        }

        // Totals row
        $totalRow = $row + 12;
        $s1->setCellValue("A{$totalRow}", 'TOTAL');
        $s1->setCellValue("B{$totalRow}", $ytdBudgetTotal);
        $s1->setCellValue("C{$totalRow}", $ytdReceivedTotal);
        $s1->setCellValue("D{$totalRow}", $ytdReceivedTotal - $ytdBudgetTotal);
        $s1->setCellValue("E{$totalRow}", $ytdPercent . '%');
        $s1->getStyle("A{$totalRow}:E{$totalRow}")->applyFromArray([
            'font'    => ['bold' => true, 'size' => 10],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_SUMMARY_BG]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BORDER]]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $s1->getRowDimension($totalRow)->setRowHeight(24);
        $this->center($s1, $totalRow, ['A','E']);
        $s1->getStyle("B{$totalRow}:D{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $s1->getStyle("B{$totalRow}:D{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $this->finish($s1, $row, 13, 'E'); // 12 months + total row

        // ─── SHEET 2: Project Delivery Status ────────────────────────
        $s2 = $spreadsheet->createSheet();
        $s2->setTitle('Project Delivery');

        $projects = \App\Models\Project::orderBy('status', 'asc')->orderBy('name', 'asc')->get();

        $gc = $projects->where('status', 'green')->count();
        $yc = $projects->where('status', 'yellow')->count();
        $rc = $projects->where('status', 'red')->count();
        $summaryText2 = $projects->count() . ' Projects   |   On Track: ' . $gc . '   |   At Risk: ' . $yc . '   |   Delayed: ' . $rc;

        $cols2    = ['A','B','C','D','E','F','G'];
        $headers2 = ['No', 'Project Name', 'Status', 'Progress', 'Deadline', 'PO Number', 'Vendor'];
        $widths2  = [6, 30, 14, 14, 16, 16, 22];
        $row2 = $this->header($s2, 'PROJECT DELIVERY STATUS', $cols2, $headers2, $widths2, $summaryText2, 'Summary', null, null);

        foreach ($projects as $i => $p) {
            $r = $row2 + $i;
            $statusLabel = ['green' => 'On Track', 'yellow' => 'At Risk', 'red' => 'Delayed'][$p->status] ?? ucfirst($p->status);
            $s2->setCellValue("A{$r}", $i + 1);
            $s2->setCellValue("B{$r}", $p->name);
            $s2->setCellValue("C{$r}", $statusLabel);
            $s2->setCellValue("D{$r}", $p->getProgressPercentage() . '%');
            $s2->setCellValue("E{$r}", $p->deadline ? $p->deadline->format('M d, Y') : 'No deadline');
            $s2->setCellValueExplicit("F{$r}", $p->po_number ?? 'N/A', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $s2->setCellValue("G{$r}", $p->vendor_name ?? 'N/A');
            $this->stripe($s2, $r, 'G', $i);
            $this->center($s2, $r, ['A','C','D','E']);

            $sc = match($p->status) {
                'green' => self::C_GREEN, 'yellow' => self::C_ORANGE, 'red' => self::C_RED, default => self::C_TEXT,
            };
            $s2->getStyle("C{$r}")->getFont()->getColor()->setRGB($sc);
            $s2->getStyle("C{$r}")->getFont()->setBold(true);
        }

        $this->finish($s2, $row2, $projects->count(), 'G');
        return $this->download($spreadsheet, 'Project_Snapshot_Report');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  STYLING ENGINE
    // ═══════════════════════════════════════════════════════════════════

    private function make(): Spreadsheet
    {
        $s = new Spreadsheet();
        $s->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
        $s->getDefaultStyle()->getFont()->getColor()->setRGB(self::C_TEXT);
        return $s;
    }

    /**
     * Build header block and return first data row number.
     */
    private function header($sheet, string $title, array $cols, array $headers, array $widths, $summaryVal, string $summaryLabel, ?string $sd, ?string $ed): int
    {
        $lastCol = end($cols);

        // Column widths
        foreach ($widths as $idx => $w) {
            $sheet->getColumnDimension($cols[$idx])->setWidth($w);
        }

        // Row 1 — Title
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getRowDimension(1)->setRowHeight(34);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => self::C_WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_DARK]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Row 2 — Generated On
        $sheet->setCellValue('A2', 'Generated: ' . Carbon::now()->format('F j, Y - h:i A'));
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => self::C_MUTED]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_SUMMARY_BG]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Row 3 — Summary
        $sheet->setCellValue('A3', $summaryLabel . ':');
        $sheet->setCellValue('B3', $summaryVal);
        $sheet->mergeCells("B3:{$lastCol}3");
        $sheet->getRowDimension(3)->setRowHeight(22);
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_SUMMARY_BG]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $next = 4;

        // Row 4 (optional) — Date Range
        if ($sd || $ed) {
            $from = $sd ? Carbon::parse($sd)->format('M d, Y') : 'Beginning';
            $to   = $ed ? Carbon::parse($ed)->format('M d, Y') : 'Present';
            $sheet->setCellValue("A{$next}", 'Date Range:');
            $sheet->setCellValue("B{$next}", $from . '  to  ' . $to);
            $sheet->mergeCells("B{$next}:{$lastCol}{$next}");
            $sheet->getRowDimension($next)->setRowHeight(20);
            $sheet->getStyle("A{$next}:{$lastCol}{$next}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::C_ACCENT]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_ACCENT_BG]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $next++;
        }

        // Spacer row
        $sheet->getRowDimension($next)->setRowHeight(5);
        $next++;

        // Table header row
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue("{$cols[$idx]}{$next}", $h);
        }
        $sheet->getRowDimension($next)->setRowHeight(26);
        $sheet->getStyle("A{$next}:{$lastCol}{$next}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::C_WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_PRIMARY]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1D4ED8']]],
        ]);

        return $next + 1;
    }

    /**
     * Zebra stripe + border for a data row.
     */
    private function stripe($sheet, int $row, string $lastCol, int $idx): void
    {
        $sheet->getRowDimension($row)->setRowHeight(20);
        $style = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::C_BORDER]]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        if ($idx % 2 === 1) {
            $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::C_STRIPE]];
        }
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($style);
    }

    /**
     * Centre specific cells.
     */
    private function center($sheet, int $row, array $cols): void
    {
        foreach ($cols as $c) {
            $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    /**
     * Freeze pane, auto-filter, outer border.
     */
    private function finish($sheet, int $dataStart, int $count, string $lastCol): void
    {
        $hdr  = $dataStart - 1;
        $last = max($dataStart, $dataStart + $count - 1);

        // Auto-filter
        $sheet->setAutoFilter("A{$hdr}:{$lastCol}{$last}");

        // Bold outer border around data table
        $sheet->getStyle("A{$hdr}:{$lastCol}{$last}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::C_PRIMARY]]],
        ]);

        // Print landscape, fit to width
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    /**
     * Save and download.
     */
    private function download(Spreadsheet $s, string $prefix)
    {
        $fn   = $prefix . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $w    = new Xlsx($s);
        $tmp  = tempnam(sys_get_temp_dir(), 'pp');
        $w->save($tmp);
        return response()->download($tmp, $fn)->deleteFileAfterSend(true);
    }
}
