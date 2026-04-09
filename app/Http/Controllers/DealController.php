<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Customer;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DealController extends Controller
{
    /**
     * Display the Sales Pipeline (Kanban view).
     */
    public function index(Request $request)
    {
        $timeline = $request->input('timeline', 'all_time');
        
        $query = Deal::with(['customer', 'tasks'])->withCount('tasks');

        // Apply Date Filtering
        if ($timeline == 'this_month') {
            $query->where('created_at', '>=', Carbon::now()->startOfMonth());
        } elseif ($timeline == 'last_month') {
            $query->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(), 
                Carbon::now()->subMonth()->endOfMonth()
            ]);
        } elseif ($timeline == 'this_quarter') {
            $query->where('created_at', '>=', Carbon::now()->startOfQuarter());
        } elseif ($timeline == 'this_year') {
            $query->where('created_at', '>=', Carbon::now()->startOfYear());
        }
        
        $deals = $query->orderBy('updated_at', 'desc')->get();
        
        // Group deals by stage
        $stages = [
            'new_opportunity' => [],
            'qualified' => [],
            'proposal' => [],
            'negotiation' => [],
            'closed_won' => [],
            'closed_lost' => []
        ];
        
        foreach ($deals as $deal) {
            if (isset($stages[$deal->stage])) {
                $stages[$deal->stage][] = $deal;
            }
        }
        
        // Calculate metrics based on the FILTERED set
        $totalDeals = $deals->count();
        $pipelineValue = $deals->whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value');
        $wonValue = $deals->where('stage', 'closed_won')->sum('value');
        $lostValue = $deals->where('stage', 'closed_lost')->sum('value');
        
        // Get customers for new deal modal
        $customers = Customer::orderBy('name')->get();
        
        return view('deals.index', compact(
            'stages', 
            'totalDeals', 
            'pipelineValue', 
            'wonValue', 
            'lostValue',
            'customers',
            'timeline'
        ));
    }

    /**
     * Store a newly created deal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'value' => 'nullable|numeric|min:0',
        ]);

        $deal = Deal::create([
            'customer_id' => $request->customer_id,
            'title' => $request->title,
            'value' => $request->value ?? 0,
            'stage' => 'new_opportunity',
        ]);

        // If coming from customer page, redirect back there
        if ($request->has('customer_id')) {
            return redirect()->route('crm.customers.show', $request->customer_id)
                            ->with('success', 'Deal created successfully!');
        }

        return redirect()->route('deals.index')
                        ->with('success', 'Deal created successfully!');
    }

    /**
     * Display deal details.
     */
    public function show(string $id)
    {
        $deal = Deal::with(['customer', 'tasks.user'])->findOrFail($id);
        $users = \App\Models\User::all();
        
        return view('deals.show', compact('deal', 'users'));
    }

    /**
     * Update deal stage (for Kanban drag-drop).
     */
    public function updateStage(Request $request, $id)
    {
        $request->validate([
            'stage' => 'required|in:new_opportunity,qualified,proposal,negotiation,closed_won,closed_lost',
        ]);

        $deal = Deal::findOrFail($id);
        $deal->stage = $request->stage;
        $deal->save();

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Deal moved to ' . ucfirst(str_replace('_', ' ', $request->stage))
            ]);
        }

        // Regular form submission - redirect back
        return redirect()->back()->with('success', 'Deal moved to ' . Deal::STAGES[$request->stage] . '!');
    }

    /**
     * Update deal details.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'stage' => 'nullable|in:new_opportunity,qualified,proposal,negotiation,closed_won,closed_lost',
            'closed_reason' => 'nullable|string',
        ]);

        $deal = Deal::findOrFail($id);
        $deal->update($request->only(['title', 'value', 'stage', 'closed_reason']));

        return redirect()->route('deals.index')
                        ->with('success', 'Deal updated successfully!');
    }

    /**
     * Delete a deal.
     */
    public function destroy(string $id)
    {
        $deal = Deal::findOrFail($id);
        $deal->delete();

        return redirect()->route('deals.index')
                        ->with('success', 'Deal deleted successfully!');
    }

    /**
     * Export deals to Excel.
     */
    public function export()
    {
        $deals = Deal::with('customer')
            ->orderBy('updated_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Pipeline');

        // Title Row
        $sheet->setCellValue('A1', 'SALES PIPELINE REPORT');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Generated date
        $sheet->setCellValue('A2', 'Generated On:');
        $sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B2:G2');
        $sheet->getStyle('A2:G2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        // Summary stats
        $pipelineValue = $deals->whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value');
        $wonValue = $deals->where('stage', 'closed_won')->sum('value');

        $sheet->setCellValue('A3', 'Total Deals:');
        $sheet->setCellValue('B3', $deals->count() . '  |  Pipeline Value: RM ' . number_format($pipelineValue, 2) . '  |  Won: RM ' . number_format($wonValue, 2));
        $sheet->mergeCells('B3:G3');
        $sheet->getStyle('A3:G3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);

        // Empty row
        $sheet->getRowDimension(4)->setRowHeight(10);

        // Table Headers
        $row = 5;
        $headers = ['#', 'Deal Title', 'Customer', 'Value (RM)', 'Stage', 'Closed Reason', 'Created'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . $row, $header);
        }

        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // Data rows
        $num = 1;
        foreach ($deals as $deal) {
            $stageLabel = Deal::STAGES[$deal->stage] ?? ucfirst(str_replace('_', ' ', $deal->stage));

            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", $deal->title);
            $sheet->setCellValue("C{$row}", $deal->customer->name ?? 'N/A');
            $sheet->setCellValue("D{$row}", $deal->value);
            $sheet->setCellValue("E{$row}", $stageLabel);
            $sheet->setCellValue("F{$row}", $deal->closed_reason ?? '-');
            $sheet->setCellValue("G{$row}", Carbon::parse($deal->created_at)->format('M d, Y'));

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $num++;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setWidth(16);

        // Generate filename
        $filename = 'Sales_Pipeline_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        // Save and download
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'deals');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
