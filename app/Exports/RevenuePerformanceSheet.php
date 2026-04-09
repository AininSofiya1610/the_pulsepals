<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RevenuePerformanceSheet
{
    protected $year;
    protected $revenueData;
    protected $ytdData;

    public function __construct($year, $revenueData, $ytdData)
    {
        $this->year = $year;
        $this->revenueData = $revenueData;
        $this->ytdData = $ytdData;
    }

    /**
     * Populate the given worksheet with revenue performance data
     */
    public function populate(Worksheet $sheet): void
    {
        $sheet->setTitle('Revenue Performance ' . $this->year);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);

        // Write header row
        $headings = ['Month', 'Budget Target (RM)', 'Actual Revenue (RM)', 'Variance (RM)', 'Achievement %'];
        $sheet->fromArray($headings, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Write monthly data
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $row = 2;

        foreach ($months as $index => $month) {
            $budget = $this->revenueData['budgets'][$index] ?? 0;
            $actual = $this->revenueData['actuals'][$index] ?? 0;
            $variance = $actual - $budget;
            $percentAchieved = $budget > 0 ? round(($actual / $budget) * 100, 2) : 0;

            $sheet->fromArray([
                $month,
                number_format($budget, 2),
                number_format($actual, 2),
                number_format($variance, 2),
                $percentAchieved . '%',
            ], null, 'A' . $row);

            $row++;
        }

        // Empty row
        $row++;

        // YTD Summary header
        $ytdHeaderRow = $row;
        $sheet->setCellValue('A' . $row, 'YTD SUMMARY');
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
        ]);
        $row++;

        // YTD data rows
        $sheet->fromArray(['YTD Budget Target', 'RM ' . number_format($this->ytdData['budget'], 2)], null, 'A' . $row);
        $row++;
        $sheet->fromArray(['YTD Actual Revenue', 'RM ' . number_format($this->ytdData['actual'], 2)], null, 'A' . $row);
        $row++;
        $sheet->fromArray(['YTD Variance', 'RM ' . number_format($this->ytdData['actual'] - $this->ytdData['budget'], 2)], null, 'A' . $row);
        $row++;
        $sheet->fromArray(['YTD Achievement', $this->ytdData['percent'] . '%'], null, 'A' . $row);
    }
}
