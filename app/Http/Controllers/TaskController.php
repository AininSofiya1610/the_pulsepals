<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Deal;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * Pagination: 10 per page
     * Sorting: Newest first
     */
    public function index(Request $request)
    {
        $query = Task::with(['user', 'deal']);

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by status using model scopes
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'overdue':
                    $query->overdue();
                    break;
                case 'done':
                    $query->done();
                    break;
                case 'open':
                    // Open = not done AND (no due date OR due date in future)
                    $query->where('status', 'open');
                    break;
            }
        }

        // Filter by due date range
        if ($request->filled('due_from')) {
            $query->whereDate('due_date', '>=', $request->due_from);
        }
        if ($request->filled('due_to')) {
            $query->whereDate('due_date', '<=', $request->due_to);
        }

        // Sort: Newest first (by created_at)
        $query->orderBy('created_at', 'desc')
              ->orderBy('id', 'desc');

        // Paginate with 10 per page
        $tasks = $query->paginate(10);
        
        // Preserve filters in pagination links
        $tasks->appends($request->only(['assigned_to', 'status', 'due_from', 'due_to']));
        
        $users = User::all();
        $deals = Deal::with('customer')->get();

        return view('tasks.index', compact('tasks', 'users', 'deals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'assigned_to' => 'required|exists:users,id',
            'related_to_deal' => 'nullable|exists:deals,id',
            'due_date' => 'nullable|date',
        ]);

        Task::create([
            'title' => $request->title,
            'assigned_to' => $request->assigned_to,
            'related_to_deal' => $request->related_to_deal,
            'due_date' => $request->due_date,
            'status' => 'open',
        ]);

        return redirect()->route('tasks.index')
                        ->with('success','Task created successfully.');
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,done',
        ]);

        $task = Task::findOrFail($id);
        $task->status = $request->status;
        $task->save();

        return redirect()->back()->with('success', 'Task status updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
            'assigned_to' => 'required|exists:users,id',
            'related_to_deal' => 'nullable|exists:deals,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:open,done',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->all());

        return redirect()->route('tasks.index')
                        ->with('success','Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')
                        ->with('success','Task deleted successfully.');
    }

    /**
     * Export tasks to Excel.
     * Respects current filters from the index page.
     */
    public function export(Request $request)
    {
        // Debug: Log what filters we received
        \Log::info('Export filters received:', [
            'assigned_to' => $request->input('assigned_to'),
            'status' => $request->input('status'),
            'due_from' => $request->input('due_from'),
            'due_to' => $request->input('due_to'),
            'all_input' => $request->all()
        ]);

        $query = Task::with(['user', 'deal']);

        // Apply the same filters as index page
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by status using model scopes (same as index)
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'overdue':
                    $query->overdue();
                    break;
                case 'done':
                    $query->done();
                    break;
                case 'open':
                    $query->where('status', 'open');
                    break;
            }
        }

        if ($request->filled('due_from')) {
            $query->whereDate('due_date', '>=', $request->due_from);
        }
        if ($request->filled('due_to')) {
            $query->whereDate('due_date', '<=', $request->due_to);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        // Debug: Log how many tasks we're exporting
        \Log::info('Export task count:', ['count' => $tasks->count()]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tasks');

        // Title Row
        $sheet->setCellValue('A1', 'TASKS MANAGEMENT REPORT');
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

        $openCount = $tasks->where('status', 'open')->count();
        $doneCount = $tasks->where('status', 'done')->count();

        $sheet->setCellValue('A3', 'Total Tasks:');
        $sheet->setCellValue('B3', $tasks->count() . '  |  Open: ' . $openCount . '  |  Done: ' . $doneCount);
        $sheet->mergeCells('B3:F3');
        $sheet->getStyle('A3:F3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);

        // Empty row
        $sheet->getRowDimension(4)->setRowHeight(10);

        // Table Headers
        $row = 5;
        $headers = ['#', 'Task Title', 'Assigned To', 'Related Deal', 'Due Date', 'Status'];
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
        foreach ($tasks as $task) {
            $statusLabel = $task->computed_status;
            $statusLabel = match($statusLabel) {
                'done' => 'Done',
                'overdue' => 'Overdue',
                default => 'Open',
            };

            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", $task->title);
            $sheet->setCellValue("C{$row}", $task->user->name ?? 'N/A');
            $sheet->setCellValue("D{$row}", $task->deal->title ?? 'N/A');
            $sheet->setCellValue("E{$row}", $task->due_date ? Carbon::parse($task->due_date)->format('M d, Y') : 'No due date');
            $sheet->setCellValue("F{$row}", $statusLabel);

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Color-code status
            if ($statusLabel === 'Overdue') {
                $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('DC2626');
                $sheet->getStyle("F{$row}")->getFont()->setBold(true);
            } elseif ($statusLabel === 'Done') {
                $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('16A34A');
            }

            $row++;
            $num++;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(14);

        // Generate filename
        $filename = 'Tasks_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        // Save and download
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'tasks');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}



