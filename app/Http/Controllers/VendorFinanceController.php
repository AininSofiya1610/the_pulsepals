<?php

namespace App\Http\Controllers;

use App\Models\VendorFinance;
use App\Models\VendorPayment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class VendorFinanceController extends Controller
{
    // ==========================================
    // INDEX
    // ==========================================
    public function index(Request $request)
    {
        $timeline  = $request->input('timeline', 'all_time');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $search    = $request->input('search');

        $query = VendorFinance::with('payments');

        $query->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('invoice_no', 'like', "%{$search}%")
                     ->orWhere('vendor_name', 'like', "%{$search}%")
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
                Carbon::now()->subMonth()->endOfMonth()
            ]);
        } elseif ($timeline == 'this_quarter') {
            $query->where('invoice_date', '>=', Carbon::now()->startOfQuarter());
        } elseif ($timeline == 'this_year') {
            $query->where('invoice_date', '>=', Carbon::now()->startOfYear());
        }

        $allTransactions = $query->latest('invoice_date')->latest('id')->get();

        $groupedTransactions = $allTransactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->invoice_date)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($transaction) {
                return Carbon::parse($transaction->invoice_date)->format('m');
            })->map(function ($monthGroup) {
                $totalBill = $monthGroup->sum('invoice');
                $totalPaid = $monthGroup->sum(function ($transaction) {
                    return ($transaction->paid_amount ?? 0) + $transaction->payments->sum('amount');
                });
                return [
                    'transactions' => $monthGroup,
                    'month_number' => $monthGroup->first()->invoice_date,
                    'total_bill'   => $totalBill,
                    'total_paid'   => $totalPaid,
                    'balance'      => $totalBill - $totalPaid,
                    'count'        => $monthGroup->count()
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
    // STORE
    // ==========================================
    public function store(Request $request)
    {
        \Log::info('VendorFinanceController@store initiated', $request->all());
        try {
            $validated = $request->validate([
                'invoice_no'   => 'required|unique:vendor_finances,invoice_no',
                'vendor_name'  => 'required',
                'invoice_date' => 'required|date',
                'due_date'     => 'required|date',
                'invoice'      => 'required|numeric|min:0',
                'paid_amount'  => 'nullable|numeric|min:0',
                'description'  => 'nullable|string'
            ]);

            VendorFinance::create($validated);

            return redirect()->route('vendor.finance.index')->with('success', 'Invoice created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = implode(' ', $e->validator->errors()->all());
            return redirect()->back()->withErrors($e->validator)->withInput()->with('error', 'Validation Check: ' . $errorMessages);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    // ==========================================
    // SHOW
    // ==========================================
    public function show($id)
    {
        $finance = VendorFinance::with('payments')->findOrFail($id);
        return response()->json($finance);
    }

    // ==========================================
    // UPDATE
    // ==========================================
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'invoice_no'   => 'required|unique:vendor_finances,invoice_no,' . $id,
                'vendor_name'  => 'required',
                'invoice_date' => 'required|date',
                'due_date'     => 'required|date',
                'invoice'      => 'required|numeric|min:0',
                'paid_amount'  => 'nullable|numeric|min:0',
                'description'  => 'nullable|string'
            ]);

            VendorFinance::findOrFail($id)->update($validated);

            return redirect()->route('vendor.finance.index')->with('success', 'Invoice updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = implode(' ', $e->validator->errors()->all());
            return redirect()->back()->withErrors($e->validator)->withInput()->with('error', 'Validation failed: ' . $errorMessages);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    // ==========================================
    // DESTROY
    // ==========================================
    public function destroy($id)
    {
        try {
            $finance = VendorFinance::findOrFail($id);
            $finance->payments()->delete();
            $finance->delete();
            return redirect()->route('vendor.finance.index')->with('success', 'Invoice deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.finance.index')->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    // ==========================================
    // PAYMENT STORE
    // ==========================================
    public function paymentStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'vendor_finance_id' => 'required|exists:vendor_finances,id',
                'payment_date'      => 'required|date',
                'amount'            => 'required|numeric|min:0'
            ]);
            VendorPayment::create($validated);
            return redirect()->route('vendor.finance.index')->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.finance.index')->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    // ==========================================
    // PAYMENT DESTROY
    // ==========================================
    public function paymentDestroy($id)
    {
        try {
            VendorPayment::findOrFail($id)->delete();
            return redirect()->route('vendor.finance.index')->with('success', 'Payment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('vendor.finance.index')->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    // ==========================================
    // EXPORT FINANCE
    // ==========================================
    public function exportFinance()
    {
        $transactions = VendorFinance::with('payments')->orderBy('invoice_date', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vendor Finance');

        $sheet->setCellValue('A1', 'VENDOR FINANCE TRANSACTIONS');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Generated On:');
        $sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B2:H2');
        $sheet->getStyle('A2:H2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']]
        ]);

        $sheet->setCellValue('A3', 'Total Transactions:');
        $sheet->setCellValue('B3', $transactions->count());
        $sheet->mergeCells('B3:H3');
        $sheet->getStyle('A3:H3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(10);

        $row     = 5;
        $headers = ['#', 'Invoice No', 'Vendor', 'Invoice Date', 'Due Date', 'Bill Amount', 'Paid Amount', 'Balance'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue($columns[$i] . $row, $header);
        }
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        $num = 1;
        foreach ($transactions as $transaction) {
            $totalPaid = ($transaction->paid_amount ?? 0) + $transaction->payments->sum('amount');
            $balance   = $transaction->invoice - $totalPaid;

            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", $transaction->invoice_no);
            $sheet->setCellValue("C{$row}", $transaction->vendor_name);
            $sheet->setCellValue("D{$row}", Carbon::parse($transaction->invoice_date)->format('d-m-Y'));
            $sheet->setCellValue("E{$row}", Carbon::parse($transaction->due_date)->format('d-m-Y'));
            $sheet->setCellValue("F{$row}", 'RM ' . number_format($transaction->invoice, 2));
            $sheet->setCellValue("G{$row}", 'RM ' . number_format($totalPaid, 2));
            $sheet->setCellValue("H{$row}", 'RM ' . number_format($balance, 2));

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
            $num++;
        }

        foreach (['A' => 8, 'B' => 20, 'D' => 18, 'E' => 18, 'F' => 18, 'G' => 18, 'H' => 18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $filename = 'Vendor_Finance_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'vendor_finance');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // DOWNLOAD TEMPLATE (config-driven)
    // ==========================================

    /**
     * Download the import template.
     *
     * ALL column headers, widths, sample data, and auto-calc markers are
     * pulled from config/vendor_finance_columns.php — the single source of truth.
     * Nothing is hardcoded here.
     */
    public function downloadTemplate()
    {
        $cfg     = config('vendor_finance_columns');
        $columns = $cfg['columns'];
        $colLetters = [];
        foreach ($columns as $i => $_) {
            $colLetters[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
        }
        $lastCol = end($colLetters);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle($cfg['sheet_name']);

        // ── Row 1: Title ─────────────────────────────────────────────────────
        $sheet->setCellValue('A1', $cfg['template_title']);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Row 2: Instructions ───────────────────────────────────────────────
        $sheet->setCellValue('A2', $cfg['instruction']);
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '92400E']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(28);

        // ── Row 3: Auto-calc legend ───────────────────────────────────────────
        foreach ($columns as $i => $col) {
            $letter = $colLetters[$i];
            if (!empty($col['auto'])) {
                $sheet->setCellValue($letter . '3', '← Auto-calculated');
                $sheet->getStyle($letter . '3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }
        $sheet->getRowDimension(3)->setRowHeight(14);

        // ── Row 4: Column headers (from config) ──────────────────────────────
        $headerRow = $cfg['header_row'];
        foreach ($columns as $i => $col) {
            $sheet->setCellValue($colLetters[$i] . $headerRow, $col['header']);
        }

        // Blue background for all header cells
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Grey override for auto-calculated columns
        foreach ($columns as $i => $col) {
            if (!empty($col['auto'])) {
                $cell = $colLetters[$i] . $headerRow;
                $sheet->getStyle($cell)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6B7280']],
                ]);
            }
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        // ── Row 5: Sample data row (from config) ─────────────────────────────
        $sampleRow = $cfg['data_start_row'];
        $dt = \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING;

        foreach ($columns as $i => $col) {
            $cell  = $colLetters[$i] . $sampleRow;
            $value = $col['sample'];

            if ($col['type'] === 'numeric' && is_numeric($value)) {
                $sheet->setCellValue($cell, (float) $value);
            } else {
                $sheet->setCellValueExplicit($cell, (string) $value, $dt);
            }
        }

        $sheet->getStyle("A{$sampleRow}:{$lastCol}{$sampleRow}")->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '71717A']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F4F4F5']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E4E7']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Right-align numeric sample cells
        foreach ($columns as $i => $col) {
            if ($col['type'] === 'numeric' && is_numeric($col['sample'])) {
                $sheet->getStyle($colLetters[$i] . $sampleRow)
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }
        $sheet->getRowDimension($sampleRow)->setRowHeight(18);

        // ── Force date columns to TEXT format ─────────────────────────────────
        foreach ($columns as $i => $col) {
            if ($col['type'] === 'date') {
                $sheet->getStyle($colLetters[$i] . ':' . $colLetters[$i])
                      ->getNumberFormat()->setFormatCode('@');
            }
        }

        // ── Column widths (from config) ───────────────────────────────────────
        foreach ($columns as $i => $col) {
            $sheet->getColumnDimension($colLetters[$i])->setWidth($col['width']);
        }

        $filename = $cfg['template_filename'];
        $writer   = new Xlsx($spreadsheet);
        $temp     = tempnam(sys_get_temp_dir(), 'vf_template');
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    // ==========================================
    // PARSE DATE CELL (private helper)
    // ==========================================

    /**
     * Safely parse a cell value into Y-m-d string.
     *
     * Handles (in priority order):
     *   1. PHP DateTime object  — returned by PhpSpreadsheet for date-formatted cells
     *   2. Excel serial number  — numeric float/int
     *   3. DD-MM-YYYY string    — primary user-facing format
     *   4. Other common formats — DD/MM/YYYY, YYYY-MM-DD, YYYY/MM/DD, MM/DD/YYYY
     *   5. Carbon auto-parse    — last resort
     */
    private function parseDateCell(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // DateTime object (PhpSpreadsheet native date cell)
        if ($value instanceof \DateTime) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        // Excel serial date number
        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->format('Y-m-d');
        }

        $value = trim((string) $value);

        // Try explicit formats — DD-MM-YYYY first (primary format told to users)
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d', 'm/d/Y'];
        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $value);
            if ($parsed && $parsed->format($format) === $value) {
                return Carbon::instance($parsed)->format('Y-m-d');
            }
        }

        // Last resort — Carbon auto-detect
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    // ==========================================
    // CALCULATE STATUS (private helper)
    // ==========================================

    /**
     * Recalculate status from first principles — always overwrites Excel value.
     *
     * Rules (evaluated in priority order):
     *   1. balance = 0                          → Fully Paid
     *   2. due_date < today AND balance > 0     → Overdue
     *   3. balance > 0 AND paid_amount > 0      → Partial
     *   4. balance > 0 AND paid_amount = 0      → Unpaid
     */
    private function calculateStatus(float $balance, float $paidAmount, string $dueDate): string
    {
        if ($balance <= 0) {
            return 'Fully Paid';
        }

        if (Carbon::parse($dueDate)->isPast()) {
            return 'Overdue';
        }

        return $paidAmount > 0 ? 'Partial' : 'Unpaid';
    }

    // ==========================================
    // IMPORT FROM EXCEL (config-driven)
    // ==========================================

    /**
     * Import vendor finance records from an Excel file.
     *
     * Column mapping is driven by config/vendor_finance_columns.php.
     * Headers in the uploaded file MUST match the config headers exactly
     * (case-insensitive). If any mismatch is found, a clear validation
     * error is returned listing expected vs found headers.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:5120'
        ]);

        try {
            $cfg       = config('vendor_finance_columns');
            $columns   = $cfg['columns'];
            $headerRow = $cfg['header_row'];
            $dataStart = $cfg['data_start_row'];
            $sampleMatch = $cfg['sample_row_match'];

            $reader      = new XlsxReader();
            $spreadsheet = $reader->load($request->file('import_file')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $highestRow  = $sheet->getHighestRow();
            $highestCol  = $sheet->getHighestColumn();

            // ──────────────────────────────────────────────────────────────────
            // STEP 1: Build header → column-letter map from the header row
            //         Normalise to lowercase so matching is case-insensitive
            // ──────────────────────────────────────────────────────────────────
            $fileHeaders = [];
            foreach ($sheet->getColumnIterator('A', $highestCol) as $column) {
                $col   = $column->getColumnIndex();
                $label = strtolower(trim((string) $sheet->getCell($col . $headerRow)->getValue()));
                if ($label !== '') {
                    $fileHeaders[$label] = $col;
                }
            }

            // Build the expected headers from config (non-auto columns are required)
            $expectedHeaders = [];
            $requiredHeaders = [];
            foreach ($columns as $col) {
                $headerLower = strtolower($col['header']);
                $expectedHeaders[] = $headerLower;
                if ($col['required'] && !$col['auto']) {
                    $requiredHeaders[] = $headerLower;
                }
            }

            // Check for missing required headers
            $missingRequired = array_filter($requiredHeaders, fn($h) => !isset($fileHeaders[$h]));
            if (!empty($missingRequired)) {
                $expectedList = implode(', ', array_map(fn($col) => "'" . $col['header'] . "'", $columns));
                $foundList    = implode(', ', array_map(fn($h) => "'" . $h . "'", array_keys($fileHeaders)));
                $missingList  = implode(', ', array_map(fn($h) => "'" . $h . "'", $missingRequired));

                return redirect()->route('vendor.finance.index')
                    ->with('error', "Import failed: Header mismatch detected!")
                    ->with('import_errors', [
                        "Missing required column(s): {$missingList}",
                        "Expected headers: {$expectedList}",
                        "Found headers: {$foundList}",
                        "Please re-download the template and do not rename any headers.",
                    ]);
            }

            // Also validate that all non-auto columns exist (required or optional)
            $allNonAutoHeaders = [];
            foreach ($columns as $col) {
                if (!$col['auto']) {
                    $allNonAutoHeaders[] = strtolower($col['header']);
                }
            }
            $missingOptional = array_filter($allNonAutoHeaders, fn($h) => !isset($fileHeaders[$h]));
            if (!empty($missingOptional)) {
                // Not fatal for optional, but warn — for strictly-required columns this is already caught above.
                // Only fail if a non-auto column that maps to a db field is missing
                $criticalMissing = [];
                foreach ($columns as $col) {
                    $h = strtolower($col['header']);
                    if (!$col['auto'] && $col['db_field'] && !isset($fileHeaders[$h])) {
                        $criticalMissing[] = $col['header'];
                    }
                }
                if (!empty($criticalMissing)) {
                    return redirect()->route('vendor.finance.index')
                        ->with('error', 'Import failed: Missing columns: ' . implode(', ', $criticalMissing) . '. Please re-download the template.');
                }
            }

            // Build column-letter lookup from config headers
            $colMap = [];
            foreach ($columns as $col) {
                $headerLower = strtolower($col['header']);
                if (isset($fileHeaders[$headerLower])) {
                    $colMap[$col['db_field'] ?? '_' . $headerLower] = $fileHeaders[$headerLower];
                }
            }

            // ──────────────────────────────────────────────────────────────────
            // STEP 2: Validate ALL rows first — collect all errors before importing
            //         Nothing is written to the DB until every row is clean.
            // ──────────────────────────────────────────────────────────────────
            $errors       = [];
            $rowsToImport = [];
            $seenNos      = [];

            for ($row = $dataStart; $row <= $highestRow; $row++) {
                // Read values from mapped columns
                $invoiceNo = isset($colMap['invoice_no'])
                    ? trim((string) $sheet->getCell($colMap['invoice_no'] . $row)->getValue()) : '';
                $vendor = isset($colMap['vendor_name'])
                    ? trim((string) $sheet->getCell($colMap['vendor_name'] . $row)->getValue()) : '';

                // Invoice Date
                $invoiceDateRaw = null;
                if (isset($colMap['invoice_date'])) {
                    $invoiceDateCell = $sheet->getCell($colMap['invoice_date'] . $row);
                    $invoiceDateRaw  = $invoiceDateCell->getValue();
                    if (empty($invoiceDateRaw)) $invoiceDateRaw = $invoiceDateCell->getFormattedValue();
                }

                // Due Date
                $dueDateRaw = null;
                if (isset($colMap['due_date'])) {
                    $dueDateCell = $sheet->getCell($colMap['due_date'] . $row);
                    $dueDateRaw  = $dueDateCell->getValue();
                    if (empty($dueDateRaw)) $dueDateRaw = $dueDateCell->getFormattedValue();
                }

                // Bill / Invoice amount
                $billRaw = isset($colMap['invoice'])
                    ? trim((string) $sheet->getCell($colMap['invoice'] . $row)->getValue()) : '';

                // Paid amount
                $paidRaw = isset($colMap['paid_amount'])
                    ? trim((string) $sheet->getCell($colMap['paid_amount'] . $row)->getValue()) : '';

                // Skip completely blank rows
                if ($invoiceNo === '' && $vendor === '') {
                    continue;
                }

                // Skip the sample row from template
                $isSampleRow = true;
                foreach ($sampleMatch as $field => $expectedValue) {
                    $cellValue = '';
                    if ($field === 'invoice_no' && isset($colMap['invoice_no'])) {
                        $cellValue = $invoiceNo;
                    } elseif ($field === 'vendor_name' && isset($colMap['vendor_name'])) {
                        $cellValue = $vendor;
                    }
                    if (strtolower(trim($cellValue)) !== strtolower($expectedValue)) {
                        $isSampleRow = false;
                        break;
                    }
                }
                if ($isSampleRow) continue;

                // ── Field-level validation ────────────────────────────────────
                if ($invoiceNo === '') {
                    $errors[] = "Row {$row}: 'Invoice No' is required.";
                    continue;
                }
                if ($vendor === '') {
                    $errors[] = "Row {$row}: 'Vendor' is required.";
                    continue;
                }
                if (empty($invoiceDateRaw)) {
                    $errors[] = "Row {$row}: 'Invoice Date' is required.";
                    continue;
                }
                if (empty($dueDateRaw)) {
                    $errors[] = "Row {$row}: 'Due Date' is required.";
                    continue;
                }
                if (!is_numeric($billRaw) || (float) $billRaw < 0) {
                    $errors[] = "Row {$row}: 'Invoice (RM)' must be a valid non-negative number (got: '{$billRaw}').";
                    continue;
                }
                if ($paidRaw !== '' && (!is_numeric($paidRaw) || (float) $paidRaw < 0)) {
                    $errors[] = "Row {$row}: 'Amount Paid (RM)' must be a valid non-negative number (got: '{$paidRaw}').";
                    continue;
                }

                // ── Duplicate within file ─────────────────────────────────────
                if (isset($seenNos[$invoiceNo])) {
                    $errors[] = "Row {$row}: Invoice No '{$invoiceNo}' is duplicated in the file (first seen at row {$seenNos[$invoiceNo]}).";
                    continue;
                }
                $seenNos[$invoiceNo] = $row;

                // ── Duplicate in database ─────────────────────────────────────
                if (VendorFinance::where('invoice_no', $invoiceNo)->exists()) {
                    $errors[] = "Row {$row}: Invoice No '{$invoiceNo}' already exists in the system.";
                    continue;
                }

                // ── Parse dates ───────────────────────────────────────────────
                $parsedInvoiceDate = $this->parseDateCell($invoiceDateRaw);
                if (!$parsedInvoiceDate) {
                    $errors[] = "Row {$row}: Invalid 'Invoice Date' '{$invoiceDateRaw}'. Use {$cfg['date_format']} format (e.g. 25-02-2026).";
                    continue;
                }

                $parsedDueDate = $this->parseDateCell($dueDateRaw);
                if (!$parsedDueDate) {
                    $errors[] = "Row {$row}: Invalid 'Due Date' '{$dueDateRaw}'. Use {$cfg['date_format']} format (e.g. 25-02-2026).";
                    continue;
                }

                // ── Recalculate Balance and Status — always overwrite Excel ───
                $billAmt  = (float) $billRaw;
                $paidAmt  = $paidRaw !== '' ? (float) $paidRaw : 0.0;
                $balance  = round($billAmt - $paidAmt, 2);
                $status   = $this->calculateStatus($balance, $paidAmt, $parsedDueDate);

                $rowsToImport[] = [
                    'invoice_no'   => $invoiceNo,
                    'vendor_name'  => $vendor,
                    'invoice_date' => $parsedInvoiceDate,
                    'due_date'     => $parsedDueDate,
                    'invoice'      => $billAmt,
                    'paid_amount'  => $paidAmt,
                    '_status'      => $status,
                    '_balance'     => $balance,
                ];
            }

            // ──────────────────────────────────────────────────────────────────
            // STEP 3: Abort entirely if ANY row failed — all-or-nothing import
            // ──────────────────────────────────────────────────────────────────
            if (!empty($errors)) {
                return redirect()->route('vendor.finance.index')
                    ->with('error', 'Import cancelled. Fix the errors below and try again.')
                    ->with('import_errors', $errors);
            }

            if (empty($rowsToImport)) {
                return redirect()->route('vendor.finance.index')
                    ->with('error', 'No valid data rows found in the file. Please check your file and try again.');
            }

            // ──────────────────────────────────────────────────────────────────
            // STEP 4: All rows valid — insert into database
            // ──────────────────────────────────────────────────────────────────
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

            return redirect()->route('vendor.finance.index')
                ->with('success', count($rowsToImport) . ' invoice(s) imported successfully.')
                ->with('import_success_data', $summary);

        } catch (\Exception $e) {
            \Log::error('VendorFinance import failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('vendor.finance.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}

