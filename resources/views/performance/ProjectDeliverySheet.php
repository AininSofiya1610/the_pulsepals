<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProjectDeliverySheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $projectStats;

    public function __construct($projectStats)
    {
        $this->projectStats = $projectStats;
    }

    public function array(): array
    {
        $total = $this->projectStats['green'] + $this->projectStats['yellow'] + $this->projectStats['red'];
        
        return [
            [
                'On Track',
                $this->projectStats['green'],
                $total > 0 ? round(($this->projectStats['green'] / $total) * 100, 2) . '%' : '0%',
                'On schedule & on budget'
            ],
            [
                'At Risk',
                $this->projectStats['yellow'],
                $total > 0 ? round(($this->projectStats['yellow'] / $total) * 100, 2) . '%' : '0%',
                'Requires monitoring'
            ],
            [
                'Delayed',
                $this->projectStats['red'],
                $total > 0 ? round(($this->projectStats['red'] / $total) * 100, 2) . '%' : '0%',
                'Immediate attention needed'
            ],
            [
                '',
                '',
                '',
                ''
            ],
            [
                'TOTAL PROJECTS',
                $total,
                '100%',
                ''
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Status',
            'Count',
            'Percentage',
            'Description'
        ];
    }

    public function title(): string
    {
        return 'Project Delivery Status';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '18181B']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
            // On Track row (green)
            2 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D1FAE5']
                ]
            ],
            // At Risk row (yellow)
            3 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7']
                ]
            ],
            // Delayed row (red)
            4 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEE2E2']
                ]
            ],
            // Total row
            6 => [
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
            'A' => 18,
            'B' => 12,
            'C' => 15,
            'D' => 30,
        ];
    }
}
