<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorFinance;
use App\Models\VendorPayment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class VendorController extends Controller
{
    // ==========================================
    // VENDOR LIST — CRUD
    // ==========================================
    public function create()
    {
        $vendors = Vendor::orderBy('id', 'desc')->paginate(10);
        return view('vendors.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'vendorName'    => 'required|string|max:255',
                'vendorPhone'   => 'nullable|string|max:20',
                'vendorEmail'   => 'nullable|email|max:255',
                'vendorAddress' => 'nullable|string',
            ]);

            Vendor::create($request->all());

            return redirect()->route('vendor.create')->with('success', 'Vendor added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.create')->with('error', 'Failed to add vendor: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'vendorName'    => 'required|string|max:255',
                'vendorPhone'   => 'nullable|string|max:20',
                'vendorEmail'   => 'nullable|email|max:255',
                'vendorAddress' => 'nullable|string',
            ]);

            Vendor::findOrFail($id)->update($request->all());

            return redirect()->route('vendor.create')->with('success', 'Vendor updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.create')->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Vendor::findOrFail($id)->delete();
            return redirect()->route('vendor.create')->with('success', 'Vendor deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.create')->with('error', 'Failed to delete vendor: ' . $e->getMessage());
        }
    }

    // ==========================================
    // VENDOR LIST — EXPORT
    // ==========================================
    public function export()
    {
        $vendors     = Vendor::orderBy('vendorName', 'asc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vendor List');

        $sheet->setCellValue('A1', 'VENDOR LIST');
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

        $sheet->setCellValue('A3', 'Total Vendors:');
        $sheet->setCellValue('B3', $vendors->count());
        $sheet->mergeCells('B3:C3');
        $sheet->getStyle('A3:C3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(10);

        $row = 5;
        $sheet->setCellValue("A{$row}", '#');
        $sheet->setCellValue("B{$row}", 'Vendor Name');
        $sheet->setCellValue("C{$row}", 'Created Date');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        $num = 1;
        foreach ($vendors as $vendor) {
            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", $vendor->vendorName);
            $sheet->setCellValue("C{$row}", $vendor->created_at->format('M d, Y'));
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

        $filename = 'Vendor_List_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'vendors');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // VENDOR LIST — TEMPLATE & IMPORT
    // ==========================================
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vendors');

        $sheet->setCellValue('A1', 'VENDOR IMPORT TEMPLATE');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Fill in vendor names below. One vendor per row. Do not modify the header.');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF9C4']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(8);

        $sheet->setCellValue('A4', 'vendorName');
        $sheet->getStyle('A4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        $samples = ['Vendor ABC Sdn Bhd', 'Vendor XYZ Enterprise', 'Example Supplies Co.'];
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

        $filename = 'Vendor_Import_Template.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'vendor_template');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    public function importExcel(Request $request)
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

            $headerRowIndex = null;
            foreach ($rows as $index => $row) {
                if (isset($row[0]) && strtolower(trim($row[0])) === 'vendorname') {
                    $headerRowIndex = $index;
                    break;
                }
            }

            if ($headerRowIndex === null) {
                return redirect()->route('vendor.create')
                    ->with('import_errors', ['Header row "vendorName" tidak dijumpai. Sila gunakan template yang betul.']);
            }

            $dataRows     = array_slice($rows, $headerRowIndex + 1);
            $errors       = [];
            $toInsert     = [];
            $sampleValues = ['vendor abc sdn bhd', 'vendor xyz enterprise', 'example supplies co.'];

            foreach ($dataRows as $i => $row) {
                $rowNum     = $headerRowIndex + $i + 2;
                $vendorName = isset($row[0]) ? trim($row[0]) : null;

                if (empty($vendorName)) continue;
                if (in_array(strtolower($vendorName), $sampleValues)) continue;

                if (in_array(strtolower($vendorName), array_map('strtolower', array_column($toInsert, 'vendorName')))) {
                    $errors[] = "Row {$rowNum}: Duplicate dalam fail — \"{$vendorName}\".";
                    continue;
                }

                if (Vendor::whereRaw('LOWER(vendorName) = ?', [strtolower($vendorName)])->exists()) {
                    $errors[] = "Row {$rowNum}: Vendor \"{$vendorName}\" sudah wujud dalam sistem.";
                    continue;
                }

                $toInsert[] = ['vendorName' => $vendorName];
            }

            if (!empty($errors)) {
                return redirect()->route('vendor.create')->with('import_errors', $errors);
            }

            if (empty($toInsert)) {
                return redirect()->route('vendor.create')
                    ->with('import_errors', ['Tiada data vendor ditemui dalam fail. Sila semak fail anda.']);
            }

            foreach ($toInsert as $item) {
                Vendor::create($item);
            }

            return redirect()->route('vendor.create')
                ->with('import_success_data', $toInsert)
                ->with('success', count($toInsert) . ' vendor berjaya diimport!');

        } catch (\Exception $e) {
            return redirect()->route('vendor.create')
                ->with('import_errors', ['Gagal membaca fail Excel: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // VENDOR FINANCE — INDEX
    // ==========================================
    public function finance(Request $request)
    {
        $timeline  = $request->input('timeline', 'all_time');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $search    = $request->input('search');

        $query = VendorFinance::with('payments');

        $query->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('invoice_no', 'like', "%{$search}%")
                     ->orWhere('vendor_name', 'like', "%{$search}%");
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

        $allTransactions = $query->latest('invoice_date')->latest('id')->get();

        $groupedTransactions = $allTransactions->groupBy(function ($txn) {
            return Carbon::parse($txn->invoice_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($txn) {
                return Carbon::parse($txn->invoice_date)->format('m');
            })->map(function ($monthGroup) {
                $totalBill = $monthGroup->sum('invoice');
                $totalPaid = $monthGroup->sum(function ($txn) {
                    return ($txn->paid_amount ?? 0) + $txn->payments->sum('amount');
                });
                return [
                    'transactions' => $monthGroup,
                    'total_bill'   => $totalBill,
                    'total_paid'   => $totalPaid,
                    'balance'      => $totalBill - $totalPaid,
                    'count'        => $monthGroup->count(),
                ];
            })->sortKeysDesc();
        })->sortKeysDesc();

        $yearlyTotals = $groupedTransactions->map(function ($yearGroup) {
            return collect($yearGroup)->sum('total_paid');
        });

        $vendors = Vendor::all();

        return view('vendors.finance', compact(
            'groupedTransactions', 'yearlyTotals', 'vendors',
            'startDate', 'endDate', 'timeline', 'search'
        ));
    }

    // ==========================================
    // VENDOR FINANCE — CRUD
    // ==========================================
    public function financeStore(Request $request)
    {
        \Log::info('Vendor Finance Store Request Data:', $request->all());

        try {
            $validated = $request->validate([
                'invoice_no'   => 'required|string',
                'vendor_name'  => 'required|string',
                'invoice_date' => 'required|date',
                'due_date'     => 'required|date',
                'invoice'      => 'required|numeric',
                'paid_amount'  => 'nullable|numeric|min:0',
            ]);

            VendorFinance::create([
                'invoice_no'   => $request->invoice_no,
                'vendor_name'  => $request->vendor_name,
                'invoice_date' => $request->invoice_date,
                'due_date'     => $request->due_date,
                'invoice'      => $request->invoice,
                'paid_amount'  => $request->paid_amount ?? 0,
                'description'  => $request->description,
            ]);

            return redirect()->route('vendor.finance')->with('success', 'Invoice added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Failed:', $e->errors());
            return redirect()->route('vendor.finance')->with('error', 'Validation failed: ' . json_encode($e->errors()));
        } catch (\Exception $e) {
            \Log::error('Invoice Creation Failed: ' . $e->getMessage());
            return redirect()->route('vendor.finance')->with('error', 'Failed to add invoice: ' . $e->getMessage());
        }
    }

    public function financeShow($id)
    {
        $finance = VendorFinance::with('payments')->findOrFail($id);
        return response()->json($finance);
    }

    public function financeUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'invoice_no'   => 'required|string',
                'vendor_name'  => 'required|string',
                'invoice_date' => 'required|date',
                'due_date'     => 'required|date',
                'invoice'      => 'required|numeric',
            ]);

            VendorFinance::findOrFail($id)->update($request->all());

            return redirect()->route('vendor.finance')->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.finance')->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    public function financeDestroy($id)
    {
        try {
            VendorFinance::findOrFail($id)->delete();
            return redirect()->route('vendor.finance')->with('success', 'Invoice deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.finance')->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    // ==========================================
    // VENDOR FINANCE — EXPORT
    // ==========================================
    public function exportFinance(Request $request)
    {
        $sd    = $request->input('start_date');
        $ed    = $request->input('end_date');
        $query = VendorFinance::with('payments')->orderBy('invoice_date', 'desc');
        if ($sd) $query->whereDate('invoice_date', '>=', $sd);
        if ($ed) $query->whereDate('invoice_date', '<=', $ed);
        $transactions = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vendor Finance');

        $sheet->setCellValue('A1', 'VENDOR FINANCE TRANSACTIONS');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Generated: ' . Carbon::now()->format('F j, Y - h:i A'));
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2:I2')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '64748B']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $sheet->setCellValue('A3', 'Total Transactions:');
        $sheet->setCellValue('B3', $transactions->count());
        $sheet->mergeCells('B3:I3');
        $sheet->getStyle('A3:I3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(10);

        $row     = 5;
        $headers = ['#', 'Invoice No', 'Vendor', 'Invoice Date', 'Due Date', 'Status', 'Invoice (RM)', 'Amount Paid (RM)', 'Balance (RM)'];
        $cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . $row, $h);
        }
        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1D4ED8']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(24);
        $row++;

        $num = 1;
        foreach ($transactions as $txn) {
            $totalPaid = ($txn->paid_amount ?? 0) + $txn->payments->sum('amount');
            $balance   = $txn->invoice - $totalPaid;
            $dueDate   = Carbon::parse($txn->due_date);

            if ($balance <= 0) {
                $status = 'Paid';     $sc = '16A34A';
            } elseif ($totalPaid > 0) {
                $status = 'Partial';  $sc = 'F97316';
            } elseif ($dueDate->isPast()) {
                $status = 'Overdue';  $sc = 'DC2626';
            } else {
                $status = 'Pending';  $sc = '6B7280';
            }

            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValueExplicit("B{$row}", $txn->invoice_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $txn->vendor_name);
            $sheet->setCellValue("D{$row}", Carbon::parse($txn->invoice_date)->format('d M Y'));
            $sheet->setCellValue("E{$row}", $dueDate->format('d M Y'));
            $sheet->setCellValue("F{$row}", $status);
            $sheet->setCellValue("G{$row}", $txn->invoice);
            $sheet->setCellValue("H{$row}", $totalPaid);
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

        $widths = ['A' => 6, 'B' => 18, 'C' => 24, 'D' => 16, 'E' => 16, 'F' => 12, 'G' => 20, 'H' => 20, 'I' => 18];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $filename = 'Vendor_Finance_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'vendor_finance');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // VENDOR FINANCE — DOWNLOAD TEMPLATE
    // ==========================================
    public function downloadFinanceTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vendor Finance Import');

        // ── Row 1: Title ──────────────────────────────────────────────────────
        $sheet->setCellValue('A1', 'VENDOR FINANCE IMPORT TEMPLATE');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Row 2: Instructions ───────────────────────────────────────────────
        $sheet->setCellValue('A2', '⚠️  Fill data from row 5 onwards. Do NOT rename headers. Date format: DD-MM-YYYY (e.g. 25-02-2026). Status & Balance are AUTO-CALCULATED — your values will be overwritten.');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '92400E']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(28);

        // ── Row 3: Auto-calc legend ───────────────────────────────────────────
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

        // ── Row 4: Column headers ─────────────────────────────────────────────
        $headers = ['Invoice No', 'Vendor', 'Invoice Date', 'Due Date', 'Status', 'Invoice (RM)', 'Amount Paid (RM)', 'Balance (RM)'];
        $cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue($cols[$i] . '4', $header);
        }
        $sheet->getStyle('A4:H4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Grey for auto-calculated columns: E = Status, H = Balance
        foreach (['E4', 'H4'] as $cell) {
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6B7280']],
            ]);
        }
        $sheet->getRowDimension(4)->setRowHeight(22);

        // ── Row 5: Example data ───────────────────────────────────────────────
        $dt = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;
        $sheet->setCellValueExplicit('A5', 'VND-001',    $dt); // Invoice No
        $sheet->setCellValueExplicit('B5', 'Vendor A',   $dt); // Vendor
        $sheet->setCellValueExplicit('C5', '01-03-2026', $dt); // Invoice Date
        $sheet->setCellValueExplicit('D5', '01-04-2026', $dt); // Due Date
        $sheet->setCellValueExplicit('E5', '(auto)',      $dt); // Status — auto
        $sheet->setCellValue('F5', 5000.00);                    // Invoice (RM)
        $sheet->setCellValue('G5', 2000.00);                    // Amount Paid (RM)
        $sheet->setCellValueExplicit('H5', '(auto)',      $dt); // Balance — auto

        $sheet->getStyle('A5:H5')->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '71717A']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F4F4F5']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E4E7']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('F5:G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension(5)->setRowHeight(18);

        // Force date columns to TEXT
        $sheet->getStyle('C:C')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('D:D')->getNumberFormat()->setFormatCode('@');

        // ── Column widths ─────────────────────────────────────────────────────
        $widths = ['A' => 20, 'B' => 28, 'C' => 18, 'D' => 18, 'E' => 14, 'F' => 20, 'G' => 20, 'H' => 16];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = 'Vendor_Finance_Import_Template.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'vf_template');
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

        $value = trim((string) $value);

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
    // VENDOR FINANCE — IMPORT
    // ==========================================
    public function importFinanceExcel(Request $request)
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

            // ── STEP 1: Build header map from row 4 ──────────────────────────
            $headerMap = [];
            foreach ($sheet->getColumnIterator('A', $highestCol) as $column) {
                $col   = $column->getColumnIndex();
                $label = strtolower(trim((string) $sheet->getCell($col . '4')->getValue()));
                if ($label !== '') {
                    $headerMap[$label] = $col;
                }
            }

            // Verify required columns exist
            $required = ['invoice no', 'vendor', 'invoice date', 'due date', 'invoice (rm)', 'amount paid (rm)'];
            $missing  = array_filter($required, fn($h) => !isset($headerMap[$h]));
            if (!empty($missing)) {
                return redirect()->route('vendor.finance')
                    ->with('error', 'Import failed: Missing required columns: ' . implode(', ', $missing) . '. Please re-download the template.');
            }

            // Resolve column letters
            $colInvoiceNo   = $headerMap['invoice no'];
            $colVendor      = $headerMap['vendor'];
            $colInvoiceDate = $headerMap['invoice date'];
            $colDueDate     = $headerMap['due date'];
            $colBill        = $headerMap['invoice (rm)'];
            $colPaid        = $headerMap['amount paid (rm)'];

            // ── STEP 2: Validate all rows ─────────────────────────────────────
            $errors       = [];
            $rowsToImport = [];
            $seenNos      = [];

            for ($row = 5; $row <= $highestRow; $row++) {
                $invoiceNo = trim((string) $sheet->getCell($colInvoiceNo . $row)->getValue());
                $vendor    = trim((string) $sheet->getCell($colVendor    . $row)->getValue());
                $billRaw   = trim((string) $sheet->getCell($colBill      . $row)->getValue());
                $paidRaw   = trim((string) $sheet->getCell($colPaid      . $row)->getValue());

                $invoiceDateCell = $sheet->getCell($colInvoiceDate . $row);
                $invoiceDateRaw  = $invoiceDateCell->getValue();
                if (empty($invoiceDateRaw)) $invoiceDateRaw = $invoiceDateCell->getFormattedValue();

                $dueDateCell = $sheet->getCell($colDueDate . $row);
                $dueDateRaw  = $dueDateCell->getValue();
                if (empty($dueDateRaw)) $dueDateRaw = $dueDateCell->getFormattedValue();

                if ($invoiceNo === '' && $vendor === '') continue;
                if ($invoiceNo === 'VND-001' && $vendor === 'Vendor A') continue;

                if ($invoiceNo === '') {
                    $errors[] = "Row {$row}: Invoice No is required.";
                    continue;
                }
                if ($vendor === '') {
                    $errors[] = "Row {$row}: Vendor name is required.";
                    continue;
                }
                if (empty($invoiceDateRaw)) {
                    $errors[] = "Row {$row}: Invoice Date is required.";
                    continue;
                }
                if (empty($dueDateRaw)) {
                    $errors[] = "Row {$row}: Due Date is required.";
                    continue;
                }
                if (!is_numeric($billRaw) || (float) $billRaw < 0) {
                    $errors[] = "Row {$row}: Invoice (RM) must be a valid non-negative number (got: '{$billRaw}').";
                    continue;
                }
                if ($paidRaw !== '' && (!is_numeric($paidRaw) || (float) $paidRaw < 0)) {
                    $errors[] = "Row {$row}: Amount Paid (RM) must be a valid non-negative number (got: '{$paidRaw}').";
                    continue;
                }

                if (isset($seenNos[$invoiceNo])) {
                    $errors[] = "Row {$row}: Invoice No '{$invoiceNo}' is duplicated in the file (first seen at row {$seenNos[$invoiceNo]}).";
                    continue;
                }
                $seenNos[$invoiceNo] = $row;

                if (VendorFinance::where('invoice_no', $invoiceNo)->exists()) {
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
                    'invoice_no'   => $invoiceNo,
                    'vendor_name'  => $vendor,
                    'invoice_date' => $parsedInvoiceDate,
                    'due_date'     => $parsedDueDate,
                    'invoice'      => $billAmt,
                    'paid_amount'  => $paidAmt,
                    'description'  => null,
                    '_status'      => $status,
                    '_balance'     => $balance,
                ];
            }

            // ── STEP 3: Abort if any errors ───────────────────────────────────
            if (!empty($errors)) {
                return redirect()->route('vendor.finance')
                    ->with('error', 'Import cancelled. Fix the errors below and try again.')
                    ->with('import_errors', $errors);
            }

            if (empty($rowsToImport)) {
                return redirect()->route('vendor.finance')
                    ->with('error', 'No valid data rows found. Please check the file and try again.');
            }

            // ── STEP 4: Insert all valid rows ─────────────────────────────────
            $summary = [];
            foreach ($rowsToImport as $data) {
                $summary[] = [
                    'invoice_no'   => $data['invoice_no'],
                    'vendor_name'  => $data['vendor_name'],
                    'invoice_date' => $data['invoice_date'],
                    'due_date'     => $data['due_date'],
                    'invoice'      => $data['invoice'],
                    'paid_amount'  => $data['paid_amount'],
                    'status'       => $data['_status'],
                    'balance'      => $data['_balance'],
                ];

                VendorFinance::create(array_diff_key($data, array_flip(['_status', '_balance'])));
            }

            return redirect()->route('vendor.finance')
                ->with('success', count($rowsToImport) . ' invoice(s) imported successfully.')
                ->with('import_success_data', $summary);

        } catch (\Exception $e) {
            \Log::error('VendorFinance import failed', ['error' => $e->getMessage()]);
            return redirect()->route('vendor.finance')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ==========================================
    // PAYMENT METHODS
    // ==========================================
    public function paymentStore(Request $request)
    {
        try {
            $request->validate([
                'vendor_finance_id' => 'required|exists:vendor_finances,id',
                'payment_date'      => 'required|date',
                'amount'            => 'required|numeric|min:0.01',
            ]);

            VendorPayment::create($request->all());

            return redirect()->route('vendor.finance')->with('success', 'Payment added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.finance')->with('error', 'Failed to add payment: ' . $e->getMessage());
        }
    }

    public function paymentDestroy($id)
    {
        try {
            VendorPayment::findOrFail($id)->delete();
            return redirect()->route('vendor.finance')->with('success', 'Payment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.finance')->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    public function updatePaid(Request $request)
    {
        $request->validate([
            'vendor_id'  => 'required|exists:vendors,id',
            'total_paid' => 'required|numeric|min:0',
        ]);

        $vendor             = Vendor::findOrFail($request->vendor_id);
        $vendor->total_paid = $request->total_paid;
        $vendor->save();

        return redirect()->back()->with('success', 'Payment amount updated successfully!');
    }
}