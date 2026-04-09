<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectSnapshotExport
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
     * Download the export as an Excel file with multiple sheets
     */
    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Revenue Performance
        $revenueSheet = new RevenuePerformanceSheet($this->year, $this->revenueData, $this->ytdData);
        $revenueSheet->populate($spreadsheet->getActiveSheet());

        // Sheet 2: Project Delivery
        $deliverySheet = new ProjectDeliverySheet($this->projectStats);
        $newSheet = $spreadsheet->createSheet();
        $deliverySheet->populate($newSheet);

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
