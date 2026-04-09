<?php

namespace App\Http\Controllers;

use App\Models\RevenueBudget;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RevenueBudgetController extends Controller
{
    /**
     * Display the revenue budget settings page.
     */
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));
        
        // Get existing budgets for selected year
        $budgets = RevenueBudget::where('year', $selectedYear)
            ->pluck('amount', 'month')
            ->toArray();
        
        // Prepare data for all 12 months
        $monthlyBudgets = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyBudgets[$m] = [
                'month' => $m,
                'name' => date('F', mktime(0, 0, 0, $m, 1)),
                'amount' => $budgets[$m] ?? 0,
            ];
        }
        
        // Available years from revenue_budgets table (distinct years, ordered descending)
        $years = RevenueBudget::distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        // If no years in database, use current year as fallback
        if (empty($years)) {
            $years = [date('Y')];
            $selectedYear = date('Y');
        }
        
        return view('settings.revenue-budgets.index', compact('monthlyBudgets', 'selectedYear', 'years'));
    }

    /**
     * Update the revenue budgets for a specific year.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2050',
            'budgets' => 'required|array',
            'budgets.*' => 'required|numeric|min:0',
        ]);
        
        $year = $validated['year'];
        $budgets = $validated['budgets'];
        
        foreach ($budgets as $month => $amount) {
            RevenueBudget::updateOrCreate(
                ['year' => $year, 'month' => $month],
                ['amount' => $amount]
            );
        }
        
        return redirect()
            ->route('settings.revenue-budgets.index', ['year' => $year])
            ->with('success', 'Revenue budgets for ' . $year . ' updated successfully!');
    }
    
    /**
     * Store a new budget year and auto-create 12 monthly budget records.
     */
    public function storeYear(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2100|unique:revenue_budgets,year',
        ], [
            'year.unique' => 'This year already exists in the system.',
        ]);
        
        $year = $validated['year'];
        
        // Auto-create 12 monthly budgets with amount = 0
        for ($month = 1; $month <= 12; $month++) {
            RevenueBudget::create([
                'year' => $year,
                'month' => $month,
                'amount' => 0
            ]);
        }
        
        return redirect()
            ->route('settings.revenue-budgets.index', ['year' => $year])
            ->with('success', "Year $year added successfully with 12 monthly budgets!");
    }

    /**
     * Export revenue budget targets to Excel.
     */
    public function export(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));

            $budgets = RevenueBudget::where('year', $year)
                ->pluck('amount', 'month')
                ->toArray();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Revenue Budget ' . $year);

            // Column widths
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(18);
            $sheet->getColumnDimension('C')->setWidth(22);

            // Title row with year info
            $sheet->setCellValue('A1', 'Revenue Budget Targets - Year ' . $year);
            $sheet->mergeCells('A1:C1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(28);

            // Header row (row 3, after a blank row 2)
            $headers = ['#', 'Month', 'Budget Target (RM)'];
            $sheet->fromArray($headers, null, 'A3');

            $sheet->getStyle('A3:C3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Monthly data rows (starting at row 4)
            $total = 0;
            for ($m = 1; $m <= 12; $m++) {
                $row = $m + 3;
                $amount = $budgets[$m] ?? 0;
                $total += $amount;

                $sheet->fromArray([
                    $m,
                    date('F', mktime(0, 0, 0, $m, 1)),
                    number_format($amount, 2),
                ], null, 'A' . $row);

                $sheet->getStyle('C' . $row)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            }

            // Total row
            $totalRow = 16;
            $sheet->fromArray(['', 'TOTAL ANNUAL BUDGET', 'RM ' . number_format($total, 2)], null, 'A' . $totalRow);
            $sheet->getStyle('A' . $totalRow . ':C' . $totalRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            ]);
            $sheet->getStyle('C' . $totalRow)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);

            $filename = 'revenue_budget_targets_' . $year . '_' . date('Y-m-d_His') . '.xlsx';

            return new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export: ' . $e->getMessage());
        }
    }
}