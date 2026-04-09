<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProjectDeliverySheet
{
    protected $projectStats;

    public function __construct($projectStats)
    {
        $this->projectStats = $projectStats;
    }

    /**
     * Populate the given worksheet with project delivery data
     */
    public function populate(Worksheet $sheet): void
    {
        $sheet->setTitle('Project Delivery Status');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);

        // Write header row
        $headings = ['Status', 'Count', 'Percentage', 'Description'];
        $sheet->fromArray($headings, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '18181B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $total = $this->projectStats['green'] + $this->projectStats['yellow'] + $this->projectStats['red'];

        // Row 2: On Track (green)
        $sheet->fromArray([
            'On Track',
            $this->projectStats['green'],
            $total > 0 ? round(($this->projectStats['green'] / $total) * 100, 2) . '%' : '0%',
            'On schedule & on budget',
        ], null, 'A2');
        $sheet->getStyle('A2:D2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
        ]);

        // Row 3: At Risk (yellow)
        $sheet->fromArray([
            'At Risk',
            $this->projectStats['yellow'],
            $total > 0 ? round(($this->projectStats['yellow'] / $total) * 100, 2) . '%' : '0%',
            'Requires monitoring',
        ], null, 'A3');
        $sheet->getStyle('A3:D3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
        ]);

        // Row 4: Delayed (red)
        $sheet->fromArray([
            'Delayed',
            $this->projectStats['red'],
            $total > 0 ? round(($this->projectStats['red'] / $total) * 100, 2) . '%' : '0%',
            'Immediate attention needed',
        ], null, 'A4');
        $sheet->getStyle('A4:D4')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
        ]);

        // Row 5: empty

        // Row 6: Total
        $sheet->fromArray(['TOTAL PROJECTS', $total, '100%', ''], null, 'A6');
        $sheet->getStyle('A6:D6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
        ]);
    }
}
