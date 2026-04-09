<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::orderBy('created_at', 'desc')->get();
        
        // Stats for RAG cards
        $stats = [
            'total' => $projects->count(),
            'green' => $projects->where('status', 'green')->count(),
            'yellow' => $projects->where('status', 'yellow')->count(),
            'red' => $projects->where('status', 'red')->count(),
        ];

        return view('projects.index', compact('projects', 'stats'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Status will be auto-calculated by the model's saving event
        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Return project data as JSON for detail modal
     */
    public function show(Project $project)
    {
        return response()->json([
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'status' => $project->status,
            'created_at' => $project->created_at?->format('Y-m-d'),
            'created_formatted' => $project->created_at?->format('M d, Y'),
            'deadline' => $project->deadline?->format('Y-m-d'),
            'deadline_formatted' => $project->deadline?->format('M d, Y'),
            'progress' => $project->getProgressPercentage(),
            'current_stage' => $project->getCurrentStage(),
            'order_date' => $project->order_date?->format('M d, Y'),
            'vendor_name' => $project->vendor_name,
            'po_number' => $project->po_number,
            'delivery_date' => $project->delivery_date?->format('M d, Y'),
            'received_by' => $project->received_by,
            'installation_date' => $project->installation_date?->format('M d, Y'),
            'installed_by' => $project->installed_by,
            'closing_date' => $project->closing_date?->format('M d, Y'),
            'closing_notes' => $project->closing_notes,
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Status will be auto-calculated by the model's saving event
        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Update a specific stage for a project.
     */
    public function updateStage(Request $request, Project $project)
    {
        $validated = $request->validate([
            'stage' => 'required|in:order,delivery,installation,closing',
            'date' => 'required|date',
            'person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'vendor_name' => 'nullable|string|max:255',
            'po_number' => 'nullable|string|max:255',
        ]);

        // Update based on stage
        switch ($validated['stage']) {
            case 'order':
                $project->order_date = $validated['date'];
                $project->vendor_name = $validated['vendor_name'] ?? null;
                $project->po_number = $validated['po_number'] ?? null;
                break;
                
            case 'delivery':
                $project->delivery_date = $validated['date'];
                $project->received_by = $validated['person'] ?? null;
                break;
                
            case 'installation':
                $project->installation_date = $validated['date'];
                $project->installed_by = $validated['person'] ?? null;
                break;
                
            case 'closing':
                $project->closing_date = $validated['date'];
                $project->closing_notes = $validated['notes'] ?? null;
                break;
        }

        $project->save(); // This will trigger auto-status calculation

        return response()->json([
            'success' => true,
            'message' => ucfirst($validated['stage']) . ' stage updated successfully!',
            'status' => $project->status,
            'status_label' => $project->status_label,
            'current_stage' => $project->getCurrentStage(),
            'progress' => $project->getProgressPercentage()
        ]);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }

    /**
     * Export all projects to Excel report.
     */
    public function export()
    {
        try {
            $projects = Project::orderBy('created_at', 'desc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Project Management Report');

            // Column widths
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(35);
            $sheet->getColumnDimension('D')->setWidth(14);
            $sheet->getColumnDimension('E')->setWidth(14);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(16);
            $sheet->getColumnDimension('H')->setWidth(16);
            $sheet->getColumnDimension('I')->setWidth(16);
            $sheet->getColumnDimension('J')->setWidth(16);

            // Header row
            $headers = ['#', 'Project Name', 'Description', 'Status', 'Deadline', 'Progress', 'Order Date', 'Delivery Date', 'Installation Date', 'Closing Date'];
            $sheet->fromArray($headers, null, 'A1');

            $sheet->getStyle('A1:J1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
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

            // Data rows
            $row = 2;
            foreach ($projects as $index => $project) {
                $statusLabel = match ($project->status) {
                    'green' => 'On Track',
                    'yellow' => 'At Risk',
                    'red' => 'Delayed',
                    default => ucfirst($project->status),
                };

                $sheet->fromArray([
                    $index + 1,
                    $project->name,
                    $project->description ?? '',
                    $statusLabel,
                    $project->deadline ? $project->deadline->format('M d, Y') : '—',
                    $project->getProgressPercentage() . '%',
                    $project->order_date ? $project->order_date->format('M d, Y') : '—',
                    $project->delivery_date ? $project->delivery_date->format('M d, Y') : '—',
                    $project->installation_date ? $project->installation_date->format('M d, Y') : '—',
                    $project->closing_date ? $project->closing_date->format('M d, Y') : '—',
                ], null, 'A' . $row);

                // Color code status cell
                $statusColor = match ($project->status) {
                    'green' => 'D1FAE5',
                    'yellow' => 'FEF3C7',
                    'red' => 'FEE2E2',
                    default => 'F1F5F9',
                };
                $sheet->getStyle('D' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $row++;
            }

            // Summary row
            $row++;
            $totalProjects = $projects->count();
            $sheet->setCellValue('A' . $row, 'SUMMARY');
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            ]);
            $row++;
            $sheet->fromArray(['', 'Total Projects', $totalProjects], null, 'A' . $row);
            $row++;
            $sheet->fromArray(['', 'On Track', $projects->where('status', 'green')->count()], null, 'A' . $row);
            $sheet->getStyle('C' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
            ]);
            $row++;
            $sheet->fromArray(['', 'At Risk', $projects->where('status', 'yellow')->count()], null, 'A' . $row);
            $sheet->getStyle('C' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            ]);
            $row++;
            $sheet->fromArray(['', 'Delayed', $projects->where('status', 'red')->count()], null, 'A' . $row);
            $sheet->getStyle('C' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
            ]);

            $filename = 'project_management_report_' . date('Y-m-d_His') . '.xlsx';

            return new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}
