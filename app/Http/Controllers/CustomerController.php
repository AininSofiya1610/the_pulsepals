<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;          // CRM only — table: customers
use App\Models\FinanceCustomer;   // Finance customer list — table: finance_customers
use App\Models\CustomerFinance;   // Finance invoices — table: customer_finances
use App\Models\CustomerPayment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class CustomerController extends Controller
{
    // ==========================================
    // CRM CUSTOMERS — READ ONLY
    // ==========================================
    // CRM Customers are created ONLY via Lead conversion (LeadController@convert).
    // They are never created manually here.
    // Table: customers

    /**
     * CRM Customer list — used by CRM module.
     */
    public function index()
{
    $customers = Customer::with('deals')
        ->whereNotNull('created_from_lead')
        ->latest()
        ->paginate(10);
    return view('crm.customers.index', compact('customers'));
}

    /**
     * CRM Customer detail page.
     */
    public function show($id)
    {
        $customer = Customer::with(['deals', 'activities.user', 'lead'])->findOrFail($id);
        $users    = \App\Models\User::all();
        return view('crm.customers.show', compact('customer', 'users'));
    }

    /**
     * Update CRM Customer profile fields.
     * ✅ Updates: customers table
     * ❌ Never touches: finance_customers table
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'nullable|email|max:255',
                'phone'   => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'status'  => 'required|in:active,inactive',
            ]);

            Customer::findOrFail($id)->update([
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'company' => $request->company,
                'status'  => $request->status,
            ]);

            return redirect()->route('crm.customers.show', $id)
                ->with('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    // ==========================================
    // FINANCE CUSTOMER LIST — CRUD
    // ==========================================
    // Finance Customers = billing name list for invoice dropdowns.
    // Table: finance_customers

    /**
     * Finance Customer list page.
     * ✅ Reads from: finance_customers table
     */
    public function create()
    {
        $customers = FinanceCustomer::orderBy('id', 'desc')->paginate(10);
        return view('customers.create', compact('customers'));
    }

    /**
     * Add a Finance Customer to the billing name list.
     * ✅ Inserts into: finance_customers table
     * ❌ Never touches: customers table (CRM)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customerName' => 'required|string|max:255',
            ]);

            FinanceCustomer::create([
                'name' => $request->customerName,
            ]);

            return redirect()->route('customers.create')
                ->with('success', 'Customer added successfully!');
        } catch (\Exception $e) {
            \Log::error('Finance Customer creation failed: ' . $e->getMessage());
            return redirect()->route('customers.create')
                ->with('error', 'Failed to add customer: ' . $e->getMessage());
        }
    }

    /**
     * Delete a Finance Customer from the billing name list.
     * ✅ Deletes from: finance_customers table
     * ❌ Never touches: customers table (CRM)
     */
    public function destroy($id)
    {
        try {
            FinanceCustomer::findOrFail($id)->delete();
            return redirect()->route('customers.create')
                ->with('success', 'Customer deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('customers.create')
                ->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }

    // ==========================================
    // FINANCE CUSTOMER LIST — TEMPLATE & IMPORT
    // ==========================================

    /**
     * Download the Finance Customer import template.
     * ✅ On import: inserts into finance_customers table
     * ❌ Never touches: customers table (CRM)
     */
    public function downloadCustomerTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customers');

        $sheet->setCellValue('A1', 'CUSTOMER IMPORT TEMPLATE');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Fill in customer names below. One customer per row. Do not modify the header.');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF9C4']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(8);

        $sheet->setCellValue('A4', 'customerName');
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        $samples = ['Customer ABC Sdn Bhd', 'Customer XYZ Enterprise', 'Example Corp'];
        $row     = 5;
        foreach ($samples as $sample) {
            $sheet->setCellValue("A{$row}", $sample);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'    => ['color' => ['rgb' => 'A0AEC0'], 'italic' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
            ]);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(45);

        $filename = 'Customer_Import_Template.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'customer_template');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import Finance Customers from Excel.
     * ✅ Inserts into: finance_customers table
     * ❌ Never touches: customers table (CRM)
     */
    public function importCustomerExcel(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $reader      = new XlsxReader();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($request->file('import_file')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            $headerRowIndex  = null;
            $acceptedHeaders = ['customername', 'customer name', 'customer_name', 'name'];
            foreach ($rows as $index => $row) {
                $cellVal = strtolower(trim((string) ($row[0] ?? '')));
                if (in_array($cellVal, $acceptedHeaders)) {
                    $headerRowIndex = $index;
                    break;
                }
            }
            if ($headerRowIndex === null) {
                $headerRowIndex = 3;
            }

            $dataRows     = array_slice($rows, $headerRowIndex + 1);
            $errors       = [];
            $toInsert     = [];
            $sampleValues = ['customer abc sdn bhd', 'customer xyz enterprise', 'example corp'];

            foreach ($dataRows as $i => $row) {
                $rowNum       = $headerRowIndex + $i + 2;
                $customerName = trim((string) ($row[0] ?? ''));

                if ($customerName === '') continue;
                if (in_array(strtolower($customerName), $sampleValues)) continue;

                if (in_array(strtolower($customerName), array_map('strtolower', array_column($toInsert, 'name')))) {
                    $errors[] = "Row {$rowNum}: Duplicate dalam fail — \"{$customerName}\".";
                    continue;
                }

                // ✅ Check duplicate in finance_customers table (NOT customers table)
                if (FinanceCustomer::whereRaw('LOWER(name) = ?', [strtolower($customerName)])->exists()) {
                    $errors[] = "Row {$rowNum}: Customer \"{$customerName}\" sudah wujud dalam sistem.";
                    continue;
                }

                $toInsert[] = ['name' => $customerName];
            }

            if (!empty($errors)) {
                return redirect()->route('customers.create')->with('import_errors', $errors);
            }

            if (empty($toInsert)) {
                return redirect()->route('customers.create')
                    ->with('import_errors', ['Tiada data customer ditemui dalam fail. Pastikan data diisi bermula dari row 5 dalam template.']);
            }

            // ✅ Insert into finance_customers table ONLY
            foreach ($toInsert as $item) {
                FinanceCustomer::create($item);
            }

            return redirect()->route('customers.create')
                ->with('import_success_data', array_map(fn($i) => ['customerName' => $i['name']], $toInsert))
                ->with('success', count($toInsert) . ' customer berjaya diimport!');

        } catch (\Exception $e) {
            return redirect()->route('customers.create')
                ->with('import_errors', ['Gagal membaca fail Excel: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // CUSTOMER FINANCE — INDEX
    // ==========================================

    public function finance(Request $request)
    {
        $timeline  = $request->input('timeline', 'all_time');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $search    = $request->input('search');

        $query = CustomerFinance::with('payments');

        $query->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('invoice_no', 'like', "%{$search}%")
                     ->orWhere('customer_name', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
            });
        });

        if ($startDate && $endDate) {
            $query->whereBetween('invoice_date', [$startDate, $endDate]);
            $timeline = 'custom';
        } elseif ($timeline == 'this_month') {
            $query->where('invoice_date', '>=', Carbon::now()->startOfMonth());
        } elseif ($timeline == 'last_month') {
            $query->whereBetween('invoice_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ]);
        } elseif ($timeline == 'this_quarter') {
            $query->where('invoice_date', '>=', Carbon::now()->startOfQuarter());
        } elseif ($timeline == 'this_year') {
            $query->where('invoice_date', '>=', Carbon::now()->startOfYear());
        }

        $allInvoices = $query->latest('invoice_date')->latest('id')->get();

        $groupedInvoices = $allInvoices->groupBy(function ($invoice) {
            return Carbon::parse($invoice->invoice_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($invoice) {
                return Carbon::parse($invoice->invoice_date)->format('m');
            })->map(function ($monthGroup) {
                $totalInvoice  = $monthGroup->sum('amount');
                $totalReceived = $monthGroup->sum(function ($invoice) {
                    return ($invoice->received_amount ?? 0) + $invoice->payments->sum('amount');
                });
                return [
                    'invoices'       => $monthGroup,
                    'month_number'   => $monthGroup->first()->invoice_date,
                    'total_invoice'  => $totalInvoice,
                    'total_received' => $totalReceived,
                    'balance'        => $totalInvoice - $totalReceived,
                    'count'          => $monthGroup->count(),
                ];
            })->sortKeysDesc();
        })->sortKeysDesc();

        $yearlyTotals = $groupedInvoices->map(function ($yearGroup) {
            return collect($yearGroup)->sum('total_received');
        });

        // ✅ Dropdown uses FinanceCustomer (finance_customers table)
        $customers = FinanceCustomer::orderBy('name')->get();

        return view('customers.finance', compact(
            'groupedInvoices', 'yearlyTotals', 'customers',
            'startDate', 'endDate', 'timeline', 'search'
        ));
    }

    // ==========================================
    // CUSTOMER FINANCE — CRUD
    // ==========================================

    public function financeStore(Request $request)
    {
        try {
            $request->validate([
                'invoice_no'    => 'required|string',
                'customer_name' => 'required|string',
                'invoice_date'  => 'required|date',
                'due_date'      => 'required|date',
                'amount'        => 'required|numeric',
                'type'          => 'nullable|string',
                'cogs'          => 'nullable|numeric',
            ]);

            CustomerFinance::create([
                'invoice_no'      => $request->invoice_no,
                'customer_name'   => $request->customer_name,
                'invoice_date'    => $request->invoice_date,
                'due_date'        => $request->due_date,
                'amount'          => $request->amount,
                'received_amount' => $request->received_amount ?? 0,
                'payment_date'    => ($request->received_amount ?? 0) > 0 ? $request->invoice_date : null,
                'type'            => $request->type ?? 'Service',
                'cogs'            => $request->cogs ?? 0,
                'description'     => $request->description,
            ]);

            return redirect()->route('customers.finance')
                ->with('success', 'Invoice created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('customers.finance')
                ->with('error', 'Failed to add invoice: ' . $e->getMessage());
        }
    }

    public function financeShow($id)
    {
        $finance = CustomerFinance::with('payments')->findOrFail($id);
        return response()->json($finance);
    }

    public function financeUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'invoice_no'    => 'required|string',
                'customer_name' => 'required|string',
                'invoice_date'  => 'required|date',
                'due_date'      => 'required|date',
                'amount'        => 'required|numeric',
            ]);

            CustomerFinance::findOrFail($id)->update([
                'invoice_no'    => $request->invoice_no,
                'customer_name' => $request->customer_name,
                'invoice_date'  => $request->invoice_date,
                'due_date'      => $request->due_date,
                'amount'        => $request->amount,
                'description'   => $request->description,
            ]);

            return redirect()->route('customers.finance')
                ->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('customers.finance')
                ->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    public function financeDestroy($id)
    {
        try {
            CustomerFinance::findOrFail($id)->delete();
            return redirect()->route('customers.finance')
                ->with('success', 'Invoice deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('customers.finance')
                ->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    public function updateVendorPayment(Request $request)
    {
        $request->validate([
            'finance_id'  => 'required|exists:customer_finances,id',
            'vendor_paid' => 'required|numeric|min:0',
        ]);

        $finance              = CustomerFinance::findOrFail($request->finance_id);
        $finance->vendor_paid = $request->vendor_paid;
        $finance->save();

        return redirect()->back()->with('success', 'Vendor payment updated successfully!');
    }

    // ==========================================
    // PAYMENT METHODS
    // ==========================================

    public function paymentStore(Request $request)
    {
        try {
            $request->validate([
                'customer_finance_id' => 'required|exists:customer_finances,id',
                'payment_date'        => 'required|date',
                'amount'              => 'required|numeric|min:0.01',
            ]);

            CustomerPayment::create($request->all());
            return redirect()->route('customers.finance')
                ->with('success', 'Payment added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('customers.finance')
                ->with('error', 'Failed to add payment: ' . $e->getMessage());
        }
    }

    public function paymentDestroy($id)
    {
        try {
            CustomerPayment::findOrFail($id)->delete();
            return redirect()->route('customers.finance')
                ->with('success', 'Payment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('customers.finance')
                ->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    // ==========================================
    // EXPORT — CRM CUSTOMER LIST
    // ==========================================
    /**
     * Export CRM Customers (from Lead conversions).
     * ✅ Reads from: customers table
     * ❌ Never touches: finance_customers table
     */
    public function export()
    {
        $customers   = Customer::orderBy('name', 'asc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CRM Customer List');

        $sheet->setCellValue('A1', 'CRM CUSTOMER LIST');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Generated On:');
        $sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B2:C2');
        $sheet->getStyle('A2:C2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $sheet->setCellValue('A3', 'Total CRM Customers:');
        $sheet->setCellValue('B3', $customers->count());
        $sheet->mergeCells('B3:C3');
        $sheet->getStyle('A3:C3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(10);

        $row = 5;
        foreach (['A' => '#', 'B' => 'Customer Name', 'C' => 'Created Date'] as $col => $label) {
            $sheet->setCellValue($col . $row, $label);
        }
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        $num = 1;
        foreach ($customers as $customer) {
            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", $customer->name);
            $sheet->setCellValue("C{$row}", $customer->created_at->format('M d, Y'));
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
            $num++;
        }

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setWidth(20);

        $filename = 'CRM_Customer_List_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'crm_customers');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // EXPORT — CUSTOMER FINANCE INVOICES
    // ==========================================
    public function exportFinance()
    {
        $invoices    = CustomerFinance::with('payments')->orderBy('invoice_date', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Finance');

        $sheet->setCellValue('A1', 'CUSTOMER FINANCE INVOICES');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Generated On:');
        $sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B2:I2');
        $sheet->getStyle('A2:I2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $sheet->setCellValue('A3', 'Total Invoices:');
        $sheet->setCellValue('B3', $invoices->count());
        $sheet->mergeCells('B3:I3');
        $sheet->getStyle('A3:I3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(10);

        $row     = 5;
        $headers = ['#', 'Invoice No', 'Customer', 'Invoice Date', 'Due Date', 'Status', 'Invoice Amt (RM)', 'Amount Received (RM)', 'Balance (RM)'];
        $cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . $row, $h);
        }
        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(24);
        $row++;

        $num = 1;
        foreach ($invoices as $invoice) {
            $totalReceived = ($invoice->received_amount ?? 0) + $invoice->payments->sum('amount');
            $balance       = $invoice->amount - $totalReceived;
            $dueDate       = Carbon::parse($invoice->due_date);

            if ($balance <= 0)          { $status = 'Paid';    $sc = '16A34A'; }
            elseif ($totalReceived > 0) { $status = 'Partial'; $sc = 'F97316'; }
            elseif ($dueDate->isPast()) { $status = 'Overdue'; $sc = 'DC2626'; }
            else                        { $status = 'Pending'; $sc = '6B7280'; }

            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValueExplicit("B{$row}", $invoice->invoice_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $invoice->customer_name);
            $sheet->setCellValue("D{$row}", Carbon::parse($invoice->invoice_date)->format('d M Y'));
            $sheet->setCellValue("E{$row}", $dueDate->format('d M Y'));
            $sheet->setCellValue("F{$row}", $status);
            $sheet->setCellValue("G{$row}", $invoice->amount);
            $sheet->setCellValue("H{$row}", $totalReceived);
            $sheet->setCellValue("I{$row}", $balance);

            $fillColor = ($num % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$row}:I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB($sc);
            $sheet->getStyle("F{$row}")->getFont()->setBold(true);

            if ($balance > 0) {
                $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('DC2626');
                $sheet->getStyle("I{$row}")->getFont()->setBold(true);
            }

            $row++;
            $num++;
        }

        $widths = ['A' => 6, 'B' => 18, 'C' => 24, 'D' => 16, 'E' => 16, 'F' => 12, 'G' => 20, 'H' => 22, 'I' => 18];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $filename = 'Customer_Finance_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'customer_finance');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // DOWNLOAD TEMPLATE — CUSTOMER FINANCE
    // ==========================================
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Finance Import');

        $sheet->setCellValue('A1', 'CUSTOMER FINANCE IMPORT TEMPLATE');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', '⚠️  Fill data from row 5 onwards. Do NOT rename headers. Date format: DD-MM-YYYY (e.g. 25-02-2026). Status & Balance are AUTO-CALCULATED — your values will be overwritten.');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '92400E']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(28);

        $legend     = ['', '', '', '', '← Auto-calculated', '', '', '← Auto-calculated'];
        $legendCols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($legendCols as $i => $col) {
            $sheet->setCellValue($col . '3', $legend[$i]);
            if ($legend[$i] !== '') {
                $sheet->getStyle($col . '3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }
        $sheet->getRowDimension(3)->setRowHeight(14);

        $headers = ['Invoice No', 'Customer', 'Invoice Date', 'Due Date', 'Status', 'Invoice Amt (RM)', 'Amount Received (RM)', 'Balance (RM)'];
        $cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue($cols[$i] . '4', $header);
        }

        $sheet->getStyle('A4:H4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        foreach (['E4', 'H4'] as $cell) {
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6B7280']],
            ]);
        }
        $sheet->getRowDimension(4)->setRowHeight(22);

        $dt = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
        $sheet->setCellValueExplicit('A5', 'INV-001',    $dt);
        $sheet->setCellValueExplicit('B5', 'Customer A', $dt);
        $sheet->setCellValueExplicit('C5', '01-03-2026', $dt);
        $sheet->setCellValueExplicit('D5', '01-03-2026', $dt);
        $sheet->setCellValueExplicit('E5', '(auto)',      $dt);
        $sheet->setCellValue('F5', 5000.00);
        $sheet->setCellValue('G5', 2000.00);
        $sheet->setCellValueExplicit('H5', '(auto)',      $dt);

        $sheet->getStyle('A5:H5')->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '71717A']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F4F4F5']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E4E7']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('F5:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension(5)->setRowHeight(18);

        $sheet->getStyle('C:C')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('D:D')->getNumberFormat()->setFormatCode('@');

        $widths = ['A' => 20, 'B' => 28, 'C' => 18, 'D' => 18, 'E' => 14, 'F' => 20, 'G' => 22, 'H' => 16];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = 'Customer_Finance_Import_Template.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'cf_template');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // HELPERS (private)
    // ==========================================

    private function parseDateCell(mixed $value): ?string
    {
        if (empty($value)) return null;

        if ($value instanceof \DateTime) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->format('Y-m-d');
        }

        $value   = trim((string) $value);
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d', 'm/d/Y'];
        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $value);
            if ($parsed && $parsed->format($format) === $value) {
                return Carbon::instance($parsed)->format('Y-m-d');
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function calculateStatus(float $balance, float $paid, string $dueDate): string
    {
        if ($balance <= 0)                     return 'Paid';
        if (Carbon::parse($dueDate)->isPast()) return 'Overdue';
        return $paid > 0 ? 'Partial' : 'Unpaid';
    }

    // ==========================================
    // IMPORT FROM EXCEL — CUSTOMER FINANCE
    // ==========================================
    /**
     * Import customer finance invoices from Excel.
     * ✅ Inserts into: customer_finances table
     * ❌ Never touches: customers table (CRM)
     * ❌ Never touches: finance_customers table
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $reader      = new XlsxReader();
            $spreadsheet = $reader->load($request->file('import_file')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $highestRow  = $sheet->getHighestRow();
            $highestCol  = $sheet->getHighestColumn();

            $headerMap = [];
            foreach ($sheet->getColumnIterator('A', $highestCol) as $column) {
                $col   = $column->getColumnIndex();
                $label = strtolower(trim((string) $sheet->getCell($col . '4')->getValue()));
                if ($label !== '') {
                    $headerMap[$label] = $col;
                }
            }

            $required = ['invoice no', 'customer', 'invoice date', 'due date', 'invoice amt (rm)', 'amount received (rm)'];
            $missing  = array_filter($required, fn($h) => !isset($headerMap[$h]));
            if (!empty($missing)) {
                return redirect()->route('customers.finance')
                    ->with('error', 'Import failed: Missing required columns: ' . implode(', ', $missing) . '. Please re-download the template.');
            }

            $colInvoiceNo   = $headerMap['invoice no'];
            $colCustomer    = $headerMap['customer'];
            $colInvoiceDate = $headerMap['invoice date'];
            $colDueDate     = $headerMap['due date'];
            $colBill        = $headerMap['invoice amt (rm)'];
            $colPaid        = $headerMap['amount received (rm)'];

            $errors       = [];
            $rowsToImport = [];
            $seenNos      = [];

            for ($row = 5; $row <= $highestRow; $row++) {
                $invoiceNo = trim((string) $sheet->getCell($colInvoiceNo . $row)->getValue());
                $customer  = trim((string) $sheet->getCell($colCustomer  . $row)->getValue());
                $billRaw   = trim((string) $sheet->getCell($colBill      . $row)->getValue());
                $paidRaw   = trim((string) $sheet->getCell($colPaid      . $row)->getValue());

                $invoiceDateCell = $sheet->getCell($colInvoiceDate . $row);
                $invoiceDateRaw  = $invoiceDateCell->getValue();
                if (empty($invoiceDateRaw)) $invoiceDateRaw = $invoiceDateCell->getFormattedValue();

                $dueDateCell = $sheet->getCell($colDueDate . $row);
                $dueDateRaw  = $dueDateCell->getValue();
                if (empty($dueDateRaw)) $dueDateRaw = $dueDateCell->getFormattedValue();

                if ($invoiceNo === '' && $customer === '') continue;
                if ($invoiceNo === 'INV-001' && $customer === 'Customer A') continue;

                if ($invoiceNo === '') { $errors[] = "Row {$row}: Invoice No is required."; continue; }
                if ($customer === '')  { $errors[] = "Row {$row}: Customer name is required."; continue; }
                if (empty($invoiceDateRaw)) { $errors[] = "Row {$row}: Invoice Date is required."; continue; }
                if (empty($dueDateRaw))     { $errors[] = "Row {$row}: Due Date is required."; continue; }

                if (!is_numeric($billRaw) || (float) $billRaw < 0) {
                    $errors[] = "Row {$row}: Invoice Amt (RM) must be a valid non-negative number (got: '{$billRaw}').";
                    continue;
                }
                if ($paidRaw !== '' && (!is_numeric($paidRaw) || (float) $paidRaw < 0)) {
                    $errors[] = "Row {$row}: Amount Received (RM) must be a valid non-negative number (got: '{$paidRaw}').";
                    continue;
                }

                if (isset($seenNos[$invoiceNo])) {
                    $errors[] = "Row {$row}: Invoice No '{$invoiceNo}' is duplicated in the file (first seen at row {$seenNos[$invoiceNo]}).";
                    continue;
                }
                $seenNos[$invoiceNo] = $row;

                if (CustomerFinance::where('invoice_no', $invoiceNo)->exists()) {
                    $errors[] = "Row {$row}: Invoice No '{$invoiceNo}' already exists in the system.";
                    continue;
                }

                $parsedInvoiceDate = $this->parseDateCell($invoiceDateRaw);
                if (!$parsedInvoiceDate) {
                    $errors[] = "Row {$row}: Invalid Invoice Date '{$invoiceDateRaw}'. Use DD-MM-YYYY format (e.g. 25-02-2026).";
                    continue;
                }

                $parsedDueDate = $this->parseDateCell($dueDateRaw);
                if (!$parsedDueDate) {
                    $errors[] = "Row {$row}: Invalid Due Date '{$dueDateRaw}'. Use DD-MM-YYYY format (e.g. 25-02-2026).";
                    continue;
                }

                $billAmt = (float) $billRaw;
                $paidAmt = $paidRaw !== '' ? (float) $paidRaw : 0.0;
                $balance = round($billAmt - $paidAmt, 2);
                $status  = $this->calculateStatus($balance, $paidAmt, $parsedDueDate);

                $rowsToImport[] = [
                    'invoice_no'      => $invoiceNo,
                    'customer_name'   => $customer,
                    'invoice_date'    => $parsedInvoiceDate,
                    'due_date'        => $parsedDueDate,
                    'amount'          => $billAmt,
                    'received_amount' => $paidAmt,
                    'payment_date'    => $paidAmt > 0 ? $parsedDueDate : null,
                    'type'            => 'Service',
                    'cogs'            => 0,
                    'description'     => null,
                    '_status'         => $status,
                    '_balance'        => $balance,
                ];
            }

            if (!empty($errors)) {
                return redirect()->route('customers.finance')
                    ->with('error', 'Import cancelled. Fix the errors below and try again.')
                    ->with('import_errors', $errors);
            }

            if (empty($rowsToImport)) {
                return redirect()->route('customers.finance')
                    ->with('error', 'No valid data rows found. Please check the file and try again.');
            }

            $summary = [];
            foreach ($rowsToImport as $data) {
                $summary[] = [
                    'invoice_no'    => $data['invoice_no'],
                    'customer_name' => $data['customer_name'],
                    'invoice_date'  => $data['invoice_date'],
                    'due_date'      => $data['due_date'],
                    'amount'        => $data['amount'],
                    'received'      => $data['received_amount'],
                    'status'        => $data['_status'],
                    'balance'       => $data['_balance'],
                ];

                CustomerFinance::create(array_diff_key($data, array_flip(['_status', '_balance'])));
            }

            return redirect()->route('customers.finance')
                ->with('success', count($rowsToImport) . ' invoice(s) imported successfully.')
                ->with('import_summary', $summary);

        } catch (\Exception $e) {
            \Log::error('CustomerFinance import failed', ['error' => $e->getMessage()]);
            return redirect()->route('customers.finance')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
