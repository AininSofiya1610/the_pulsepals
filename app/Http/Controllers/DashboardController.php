<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $reportDate = Carbon::now()->format('F j, Y');

        // ── OVERDUE VENDORS (past due_date, still have balance) ───────────────
        $overdueVendorsRaw = \App\Models\VendorFinance::query()
            ->where('due_date', '<', today())
            ->orderBy('due_date', 'desc')
            ->get()
            ->filter(fn($v) => ($v->invoice - ($v->paid_amount ?? 0)) > 0)
            ->map(fn($v) => [
                'vendor_name' => $v->vendor_name,
                'due_date'    => $v->due_date,
                'total'       => round($v->invoice - ($v->paid_amount ?? 0), 2),
            ])
            ->values();

        $toPay = $overdueVendorsRaw->sum('total');

        $overdueVendors = new \Illuminate\Pagination\LengthAwarePaginator(
            $overdueVendorsRaw->forPage(request('vendors_page', 1), 10),
            $overdueVendorsRaw->count(),
            10,
            request('vendors_page', 1),
            ['path' => request()->url(), 'pageName' => 'vendors_page']
        );

        // ── OVERDUE CUSTOMERS (past due_date, still have balance) ─────────────
        // ✅ FIX: Include additional payments from customer_payments table
        $overdueCustomersRaw = \App\Models\CustomerFinance::with('payments')
            ->where('due_date', '<', today())
            ->orderBy('due_date', 'desc')
            ->get()
            ->filter(function ($c) {
                $totalPaid = ($c->received_amount ?? 0) + $c->payments->sum('amount');
                return ($c->amount - $totalPaid) > 0;
            })
            ->map(function ($c) {
                $totalPaid = ($c->received_amount ?? 0) + $c->payments->sum('amount');
                return [
                    'customer_name' => $c->customer_name,
                    'due_date'      => $c->due_date,
                    'total'         => round($c->amount - $totalPaid, 2),
                ];
            })
            ->values();

        $toCollect = $overdueCustomersRaw->sum('total');

        $overdueCustomers = new \Illuminate\Pagination\LengthAwarePaginator(
            $overdueCustomersRaw->forPage(request('customers_page', 1), 10),
            $overdueCustomersRaw->count(),
            10,
            request('customers_page', 1),
            ['path' => request()->url(), 'pageName' => 'customers_page']
        );

        // ── OUTSTANDING TOTALS (all unpaid, regardless of overdue) ───────────
        $totalVendorOutstanding = \App\Models\VendorFinance::all()
            ->sum(fn($v) => max(0, $v->invoice - ($v->paid_amount ?? 0)));

        // ✅ FIX: Include additional payments from customer_payments table
        $totalCustomerOutstanding = \App\Models\CustomerFinance::with('payments')
            ->get()
            ->sum(function ($c) {
                $totalPaid = ($c->received_amount ?? 0) + $c->payments->sum('amount');
                return max(0, $c->amount - $totalPaid);
            });

        // ── CASH BALANCE (from latest Company Finance record) ────────────────
        $latestCash         = \App\Models\CompanyFinance::latest('record_date')->first();
        $cashAvailableMBB   = $latestCash ? (float) $latestCash->mbb_balance : 0;
        $cashAvailableRHB   = $latestCash ? (float) $latestCash->rhb_balance : 0;
        $totalCashAvailable = $cashAvailableMBB + $cashAvailableRHB;
        $netPay             = $toCollect - $toPay;
        $cashBalance        = $totalCashAvailable - $toPay + $netPay;

        return view('dashboard', compact(
            'reportDate',
            'totalCashAvailable',
            'cashAvailableMBB',
            'cashAvailableRHB',
            'toPay',
            'toCollect',
            'netPay',
            'totalCustomerOutstanding',
            'totalVendorOutstanding',
            'cashBalance',
            'overdueVendors',
            'overdueCustomers'
        ));
    }

    public function showReportForm()
    {
        return view('report-form');
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $data        = $this->getDashboardData($startDate, $endDate);
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Financial Dashboard');

        $this->buildReportHeader($sheet, $startDate, $endDate);
        $this->buildCashBalanceSection($sheet, $data);
        $this->buildOverdueVendorsSection($sheet, $data);
        $this->buildOverdueCustomersSection($sheet, $data);
        $this->buildSummarySection($sheet, $data);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Financial_Dashboard_Report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'report');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $data        = $this->getDashboardData($startDate, $endDate);
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Financial Dashboard');

        $this->buildReportHeader($sheet, $startDate, $endDate);
        $this->buildCashBalanceSection($sheet, $data);
        $this->buildOverdueVendorsSection($sheet, $data);
        $this->buildOverdueCustomersSection($sheet, $data);
        $this->buildSummarySection($sheet, $data);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Financial_Dashboard_Report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'report');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // PRIVATE: GET DASHBOARD DATA
    // ==========================================
    private function getDashboardData($startDate, $endDate)
    {
        // ── OVERDUE VENDORS — sorted latest due_date first ────────────────────
        $overdueVendors = \App\Models\VendorFinance::query()
            ->where('due_date', '<', today())
            ->orderBy('due_date', 'desc')
            ->get()
            ->filter(fn($v) => ($v->invoice - ($v->paid_amount ?? 0)) > 0)
            ->map(fn($v) => [
                'vendor_name' => $v->vendor_name,
                'due_date'    => $v->due_date,
                'amount'      => round($v->invoice - ($v->paid_amount ?? 0), 2),
            ])
            ->values()
            ->toArray();

        $toPay = collect($overdueVendors)->sum('amount');

        // ── OVERDUE CUSTOMERS — sorted latest due_date first ──────────────────
        // ✅ FIX: Include additional payments from customer_payments table
        $overdueCustomers = \App\Models\CustomerFinance::with('payments')
            ->where('due_date', '<', today())
            ->orderBy('due_date', 'desc')
            ->get()
            ->filter(function ($c) {
                $totalPaid = ($c->received_amount ?? 0) + $c->payments->sum('amount');
                return ($c->amount - $totalPaid) > 0;
            })
            ->map(function ($c) {
                $totalPaid = ($c->received_amount ?? 0) + $c->payments->sum('amount');
                return [
                    'customer_name' => $c->customer_name,
                    'due_date'      => $c->due_date,
                    'amount'        => round($c->amount - $totalPaid, 2),
                ];
            })
            ->values()
            ->toArray();

        $toCollect = collect($overdueCustomers)->sum('amount');

        // ── OUTSTANDING TOTALS ────────────────────────────────────────────────
        $totalVendorOutstanding = \App\Models\VendorFinance::all()
            ->sum(fn($v) => max(0, $v->invoice - ($v->paid_amount ?? 0)));

        // ✅ FIX: Include additional payments from customer_payments table
        $totalCustomerOutstanding = \App\Models\CustomerFinance::with('payments')
            ->get()
            ->sum(function ($c) {
                $totalPaid = ($c->received_amount ?? 0) + $c->payments->sum('amount');
                return max(0, $c->amount - $totalPaid);
            });

        // ── CASH BALANCE ──────────────────────────────────────────────────────
        $latestCash         = \App\Models\CompanyFinance::latest('record_date')->first();
        $cashAvailableMBB   = $latestCash ? (float) $latestCash->mbb_balance : 0;
        $cashAvailableRHB   = $latestCash ? (float) $latestCash->rhb_balance : 0;
        $totalCashAvailable = $cashAvailableMBB + $cashAvailableRHB;
        $netPay             = $toCollect - $toPay;
        $cashBalance        = $totalCashAvailable - $toPay + $netPay;

        return [
            'report_date'                => $endDate->format('F j, Y'),
            'start_date'                 => $startDate->format('F j, Y'),
            'end_date'                   => $endDate->format('F j, Y'),
            'total_cash_available'       => $totalCashAvailable,
            'cash_available_mbb'         => $cashAvailableMBB,
            'cash_available_rhb'         => $cashAvailableRHB,
            'to_pay'                     => $toPay,
            'to_collect'                 => $toCollect,
            'net_pay'                    => $netPay,
            'total_customer_outstanding' => $totalCustomerOutstanding,
            'total_vendor_outstanding'   => $totalVendorOutstanding,
            'cash_balance'               => $cashBalance,
            'overdue_vendors'            => $overdueVendors,
            'overdue_customers'          => $overdueCustomers,
        ];
    }

    // ==========================================
    // PRIVATE: EXCEL REPORT BUILDERS
    // ==========================================
    private function buildReportHeader($sheet, $startDate, $endDate)
    {
        $sheet->setCellValue('A1', 'FINANCIAL DASHBOARD REPORT');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Report Period:');
        $sheet->setCellValue('B2', $startDate->format('F j, Y') . ' to ' . $endDate->format('F j, Y'));
        $sheet->mergeCells('B2:D2');
        $sheet->getStyle('A2:B2')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $sheet->setCellValue('A3', 'Generated On:');
        $sheet->setCellValue('B3', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B3:D3');
        $sheet->getStyle('A3:B3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $sheet->getRowDimension(4)->setRowHeight(10);
    }

    private function buildCashBalanceSection($sheet, $data)
    {
        $row = 5;

        $sheet->setCellValue("A{$row}", 'CASH BALANCE SUMMARY');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $row++;

        $sheet->setCellValue("A{$row}", 'Description');
        $sheet->setCellValue("B{$row}", 'Amount (RM)');
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $cashData = [
            ['Total Cash Available', $data['total_cash_available'], '22C55E'],
            ['  - MBB',             $data['cash_available_mbb'],   '000000'],
            ['  - RHB',             $data['cash_available_rhb'],   '000000'],
            ['To Pay (Overdue)',     $data['to_pay'],               'EF4444'],
            ['To Collect (Overdue)', $data['to_collect'],           'F59E0B'],
            ['Net Pay',             $data['net_pay'],               $data['net_pay'] >= 0 ? '22C55E' : 'EF4444'],
        ];

        foreach ($cashData as $item) {
            $sheet->setCellValue("A{$row}", $item[0]);
            $sheet->setCellValue("B{$row}", $item[1]);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB($item[2]);
            $row++;
        }

        $row++;
    }

    private function buildOverdueVendorsSection($sheet, $data)
    {
        $row = $sheet->getHighestRow() + 1;

        $sheet->setCellValue("A{$row}", 'OVERDUE VENDORS (TO PAY)');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $row++;

        $sheet->setCellValue("A{$row}", 'Vendor Name');
        $sheet->setCellValue("B{$row}", 'Due Date');
        $sheet->setCellValue("C{$row}", 'Amount (RM)');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $startRow = $row;
        foreach ($data['overdue_vendors'] as $vendor) {
            $sheet->setCellValue("A{$row}", $vendor['vendor_name']);
            $sheet->setCellValue("B{$row}", isset($vendor['due_date'])
                ? Carbon::parse($vendor['due_date'])->format('d M Y')
                : '-');
            $sheet->setCellValue("C{$row}", $vendor['amount']);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("C{$row}", "=SUM(C{$startRow}:C" . ($row - 1) . ")");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

        $row += 2;
    }

    private function buildOverdueCustomersSection($sheet, $data)
    {
        $row = $sheet->getHighestRow() + 1;

        $sheet->setCellValue("A{$row}", 'OVERDUE CUSTOMERS (TO COLLECT)');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $row++;

        $sheet->setCellValue("A{$row}", 'Customer Name');
        $sheet->setCellValue("B{$row}", 'Due Date');
        $sheet->setCellValue("C{$row}", 'Amount (RM)');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        $startRow = $row;
        foreach ($data['overdue_customers'] as $customer) {
            $sheet->setCellValue("A{$row}", $customer['customer_name']);
            $sheet->setCellValue("B{$row}", isset($customer['due_date'])
                ? Carbon::parse($customer['due_date'])->format('d M Y')
                : '-');
            $sheet->setCellValue("C{$row}", $customer['amount']);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("C{$row}", "=SUM(C{$startRow}:C" . ($row - 1) . ")");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

        $row += 2;
    }

    private function buildSummarySection($sheet, $data)
    {
        $row = $sheet->getHighestRow() + 1;

        $sheet->setCellValue("A{$row}", 'PROJECTED CASH BALANCE');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $row++;

        $sheet->setCellValue("A{$row}", 'Cash Available');
        $sheet->setCellValue("B{$row}", $data['total_cash_available']);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $row++;

        $sheet->setCellValue("A{$row}", 'Less: To Pay (Overdue)');
        $sheet->setCellValue("B{$row}", -$data['to_pay']);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB('EF4444');
        $row++;

        $sheet->setCellValue("A{$row}", 'Add: Net Pay');
        $sheet->setCellValue("B{$row}", $data['net_pay']);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB($data['net_pay'] >= 0 ? '22C55E' : 'EF4444');
        $row++;

        $sheet->setCellValue("A{$row}", 'PROJECTED CASH BALANCE');
        $sheet->setCellValue("B{$row}", $data['cash_balance']);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
            'font'    => ['bold' => true, 'size' => 14],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]],
        ]);
        $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB($data['cash_balance'] >= 0 ? '22C55E' : 'EF4444');
    }
}