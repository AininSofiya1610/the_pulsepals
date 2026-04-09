<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RevenuePerformanceSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths
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

    public function array(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];

        // Monthly data
        foreach ($months as $index => $month) {
            $budget = $this->revenueData['budgets'][$index] ?? 0;
            $actual = $this->revenueData['actuals'][$index] ?? 0;
            $variance = $actual - $budget;
            $percentAchieved = $budget > 0 ? round(($actual / $budget) * 100, 2) : 0;

            $data[] = [
                $month,
                number_format($budget, 2),
                number_format($actual, 2),
                number_format($variance, 2),
                $percentAchieved . '%'
            ];
        }

        // Add empty row
        $data[] = ['', '', '', '', ''];

        // YTD Summary
        $data[] = [
            'YTD SUMMARY',
            '',
            '',
            '',
            ''
        ];
        $data[] = [
            'YTD Budget Target',
            'RM ' . number_format($this->ytdData['budget'], 2),
            '',
            '',
            ''
        ];
        $data[] = [
            'YTD Actual Revenue',
            'RM ' . number_format($this->ytdData['actual'], 2),
            '',
            '',
            ''
        ];
        $data[] = [
            'YTD Variance',
            'RM ' . number_format($this->ytdData['actual'] - $this->ytdData['budget'], 2),
            '',
            '',
            ''
        ];
        $data[] = [
            'YTD Achievement',
            $this->ytdData['percent'] . '%',
            '',
            '',
            ''
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'Month',
            'Budget Target (RM)',
            'Actual Revenue (RM)',
            'Variance (RM)',
            'Achievement %'
        ];
    }

    public function title(): string
    {
        return 'Revenue Performance ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
            // YTD Summary header (row 14)
            14 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 22,
            'D' => 18,
            'E' => 18,
        ];
    }
}
