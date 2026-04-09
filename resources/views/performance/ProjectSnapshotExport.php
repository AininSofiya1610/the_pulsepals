<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProjectSnapshotExport implements WithMultipleSheets
{
    protected $year;
    protected $revenueData;
    protected $projectStats;
    protected $ytdData;

    public function __construct($year, $revenueData, $projectStats, $ytdData)
    {
        $this->year = $year;
        $this->revenueData = $revenueData;
        $this->projectStats = $projectStats;
        $this->ytdData = $ytdData;
    }

    /**
     * Return multiple sheets for the export
     */
    public function sheets(): array
    {
        return [
            new RevenuePerformanceSheet($this->year, $this->revenueData, $this->ytdData),
            new ProjectDeliverySheet($this->projectStats),
        ];
    }
}
