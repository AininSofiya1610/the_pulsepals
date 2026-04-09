<?php

namespace App\Http\Controllers;

use App\Models\CompanyFinance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CompanyFinanceController extends Controller
{
    /**
     * Display the company finance index page with history records.
     */
    public function index(Request $request)
    {
        $timeline = $request->input('timeline');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = CompanyFinance::orderBy('record_date', 'desc')->orderBy('id', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('record_date', [$startDate, $endDate]);
        }

        $history = $query->paginate(15);
        $latestRecord = CompanyFinance::orderBy('record_date', 'desc')->first();

        return view('finance.company.index', compact('history', 'latestRecord', 'startDate', 'endDate', 'timeline'));
    }

    /**
     * Store a new company finance record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'record_date' => 'required|date',
            'mbb_balance' => 'required|numeric',
            'rhb_balance' => 'required|numeric',
            'net_pay' => 'required|numeric',
        ]);

        CompanyFinance::create([
            'record_date' => $request->record_date,
            'mbb_balance' => $request->mbb_balance,
            'rhb_balance' => $request->rhb_balance,
            'net_pay' => $request->net_pay,
        ]);

        return redirect()->route('finance.company.index')->with('success', 'Record saved successfully');
    }

    /**
     * Export company finance records to Excel.
     */
    public function export()
    {
        $records = CompanyFinance::orderBy('record_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Company Finance');

        // Title Row
        $sheet->setCellValue('A1', 'COMPANY FINANCE RECORDS');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Generated date
        $sheet->setCellValue('A2', 'Generated On:');
        $sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B2:F2');
        $sheet->getStyle('A2:F2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $sheet->setCellValue('A3', 'Total Records:');
        $sheet->setCellValue('B3', $records->count());
        $sheet->mergeCells('B3:F3');
        $sheet->getStyle('A3:F3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);

        // Empty row
        $sheet->getRowDimension(4)->setRowHeight(10);

        // Table Headers
        $row = 5;
        $headers = ['#', 'Record Date', 'MBB Balance (RM)', 'RHB Balance (RM)', 'Total Cash (RM)', 'Net Pay (RM)'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . $row, $header);
        }

        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // Data rows
        $num = 1;
        foreach ($records as $record) {
            $totalCash = $record->mbb_balance + $record->rhb_balance;

            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", Carbon::parse($record->record_date)->format('M d, Y'));
            $sheet->setCellValue("C{$row}", $record->mbb_balance);
            $sheet->setCellValue("D{$row}", $record->rhb_balance);
            $sheet->setCellValue("E{$row}", $totalCash);
            $sheet->setCellValue("F{$row}", $record->net_pay);

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);

            // Center align number column
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Format numbers with 2 decimal places
            $sheet->getStyle("C{$row}:F{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $row++;
            $num++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Generate filename
        $filename = 'Company_Finance_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        // Save and download
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'company_finance');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
