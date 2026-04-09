<?php

// Add this method to your VendorController.php class

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;


// Fetch all vendors (or you can add filters/pagination if needed)
$vendors = \App\Models\Vendor::orderBy('vendorName', 'asc')->get();

// Create Excel file
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Vendor List');

// Title Row
$sheet->setCellValue('A1', 'VENDOR LIST');
$sheet->mergeCells('A1:C1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(30);

// Generated date
$sheet->setCellValue('A2', 'Generated On:');
$sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
$sheet->mergeCells('B2:C2');
$sheet->getStyle('A2:C2')->applyFromArray([
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
]);

$sheet->setCellValue('A3', 'Total Vendors:');
$sheet->setCellValue('B3', $vendors->count());
$sheet->mergeCells('B3:C3');
$sheet->getStyle('A3:C3')->applyFromArray([
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
    'font' => ['bold' => true],
]);

// Empty row
$sheet->getRowDimension(4)->setRowHeight(10);

// Table Headers
$row = 5;
$sheet->setCellValue("A{$row}", '#');
$sheet->setCellValue("B{$row}", 'Vendor Name');
$sheet->setCellValue("C{$row}", 'Created Date');

$sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);
$row++;

// Data rows
$num = 1;
foreach ($vendors as $vendor) {
    $sheet->setCellValue("A{$row}", $num);
    $sheet->setCellValue("B{$row}", $vendor->vendorName);
    $sheet->setCellValue("C{$row}", $vendor->created_at->format('M d, Y'));

    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
    ]);

    // Center align the number column
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $row++;
    $num++;
}

// Auto-size columns
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setWidth(20);

// Generate filename
$filename = 'Vendor_List_' . Carbon::now()->format('Ymd_His') . '.xlsx';

// Save and download
$writer = new Xlsx($spreadsheet);
$temp_file = tempnam(sys_get_temp_dir(), 'vendors');
$writer->save($temp_file);

return response()->download($temp_file, $filename)->deleteFileAfterSend(true);

