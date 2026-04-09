<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TicketController extends Controller
{
    public function dashboard()
    {
        // Temporary fake data
        $ticketSummary = [
            'total' => 150,
            'open' => 25,
            'in_progress' => 30,
            'resolved' => 70,
            'closed' => 25
        ];

        $unitStats = [
            'System Unit' => [
                'avg_response' => 15,
                'avg_resolution' => 4.5,
                'sla_compliance' => 92,
                'backlog' => 3
            ],
            'Network & Infrastructure' => [
                'avg_response' => 20,
                'avg_resolution' => 6.2,
                'sla_compliance' => 85,
                'backlog' => 8
            ],
            'Technical Support' => [
                'avg_response' => 12,
                'avg_resolution' => 3.8,
                'sla_compliance' => 95,
                'backlog' => 1
            ]
        ];

        $priorityStats = [
            'Critical' => 15,
            'High' => 35,
            'Medium' => 60,
            'Low' => 40
        ];

        $comparisonIds = [
            'System' => 45,
            'Network' => 38,
            'Technical Support' => 52
        ];

        return view('tickets.dashboard', compact('ticketSummary', 'unitStats', 'priorityStats', 'comparisonIds'));
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Permission-based access control — show empty page if no ticket permissions
        if (!$user->can('view-all-tickets') && !$user->can('view-own-tickets')) {
            $emptyStats = ['total' => 0, 'open' => 0, 'in_progress' => 0, 'resolved' => 0, 'closed' => 0];
            $tickets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            return view('tickets.index', array_merge(
                compact('tickets'),
                ['stats' => $emptyStats],
                app(\App\Services\DropdownService::class)->getAllDropdowns()
            ))->with('info', 'Your account is pending role assignment. Please contact an administrator.');
        }

        // Build query from database
        $query = \App\Models\Ticket::with(['unit']);

        // Filter tickets based on permission
        if (!$user->can('view-all-tickets')) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        // Apply Search Filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('ticket_id', 'like', "%{$searchTerm}%")
                    ->orWhere('full_name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('title', 'like', "%{$searchTerm}%")
                    ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        // Apply Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Calculate Dashboard Statistics (respects permission filtering)
        $statsQuery = \App\Models\Ticket::query();
        if (!$user->can('view-all-tickets')) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }
        $allTickets = $statsQuery->get();
        $stats = [
            'total'       => $allTickets->count(),
            'open'        => $allTickets->where('status', 'Open')->count(),
            'in_progress' => $allTickets->where('status', 'In Progress')->count(),
            'resolved'    => $allTickets->where('status', 'Resolved')->count(),
            'closed'      => $allTickets->where('status', 'Closed')->count(),
        ];

        // Sorting
        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'priority') {
            $query->orderByRaw("FIELD(priority, 'Critical', 'High', 'Medium', 'Low') " . ($sortOrder === 'asc' ? 'ASC' : 'DESC'));
        } elseif ($sortBy === 'status') {
            $query->orderByRaw("FIELD(status, 'Critical', 'Open', 'In Progress', 'Resolved', 'Closed') " . ($sortOrder === 'asc' ? 'ASC' : 'DESC'));
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $tickets = $query->paginate(10)->appends($request->query());

        // Transform tickets to include unit name for view compatibility
        $tickets->getCollection()->transform(function ($ticket) {
            return [
                'id'          => $ticket->id,
                'ticket_id'   => $ticket->ticket_id,
                'status'      => $ticket->status,
                'full_name'   => $ticket->full_name,
                'email'       => $ticket->email,
                'category'    => $ticket->category,
                'unit'        => $ticket->unit->name ?? 'N/A',
                'priority'    => $ticket->priority,
                'created_at'  => $ticket->created_at,
                'closed_at'   => $ticket->closed_at,
                'title'       => $ticket->title,
                'ticket_type' => $ticket->ticket_type,
            ];
        });

        return view('tickets.index', array_merge(
            compact('tickets', 'stats'),
            app(\App\Services\DropdownService::class)->getAllDropdowns()
        ));
    }

    public function create(\App\Services\DropdownService $dropdowns)
    {
        return view('tickets.create', $dropdowns->getAllDropdowns());
    }

    public function store(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'email'       => 'required|email',
            'full_name'   => 'required|string|max:255',
            'phone'       => 'nullable|string',
            'phone_ext'   => 'nullable|string',
            'unit'        => 'required|string',
            'priority'    => 'required|string',
            'ticket_type' => 'required|string',
            'category'    => 'required|string',
            'title'       => 'required|string|max:200',
            'description' => 'required|string|min:10',
        ]);

        // Generate ticket ID
        $ticketId = 'TKT-' . date('Ymd') . '-' . rand(1000, 9999);

        // Find unit by name to get unit_id
        $unit = \App\Models\Unit::where('name', $validated['unit'])->first();

        $ticket = \App\Models\Ticket::create([
            'ticket_id'   => $ticketId,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'full_name'   => $validated['full_name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'] ?? null,
            'phone_ext'   => $validated['phone_ext'] ?? null,
            'unit_id'     => $unit->id ?? null,
            'priority'    => $validated['priority'],
            'ticket_type' => $validated['ticket_type'],
            'category'    => $validated['category'],
            'status'      => 'Open',
            'created_by'  => auth()->id(),
        ]);

        // Send email to ticket requester
        \App\Jobs\SendTicketCreatedEmailJob::dispatch($ticket);

        return redirect()
            ->route('tickets.index')
            ->with('success', "✅ Ticket {$ticketId} created successfully!");
    }

    public function myTickets(Request $request)
    {
        // Get current user info
        $user = auth()->user();

        // 1. Get all dummy tickets (Source of Truth)
        $allTickets = collect($this->getDummyTickets());

        // For demo purposes, we'll assign ALL tickets to "Me"
        $assignedTickets = $allTickets;

        // 2. Calculate Summary Stats (Always based on FULL dataset)
        $summary = [
            'open'           => $assignedTickets->whereIn('status', ['Open', 'Critical'])->count(),
            'in_progress'    => $assignedTickets->where('status', 'In Progress')->count(),
            'resolved_today' => $assignedTickets->where('status', 'Resolved')->count(),
            'total_assigned' => $assignedTickets->count()
        ];

        // 3. Handle Tab Filtering
        $currentTab = $request->get('tab', 'open');

        if ($currentTab === 'open') {
            $filtered = $assignedTickets->whereIn('status', ['Open', 'Critical']);
        } elseif ($currentTab === 'progress') {
            $filtered = $assignedTickets->where('status', 'In Progress');
        } elseif ($currentTab === 'resolved') {
            $filtered = $assignedTickets->whereIn('status', ['Resolved', 'Closed']);
        } else {
            $filtered = $assignedTickets;
        }

        // 4. Handle Sorting
        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'priority') {
            $priorityOrder = ['Critical' => 4, 'High' => 3, 'Medium' => 2, 'Low' => 1];
            $callback      = function ($item) use ($priorityOrder) {
                return $priorityOrder[$item['priority']] ?? 0;
            };
            $filtered = $sortOrder === 'asc' ? $filtered->sortBy($callback) : $filtered->sortByDesc($callback);
        } else {
            $filtered = $sortOrder === 'asc' ? $filtered->sortBy($sortBy) : $filtered->sortByDesc($sortBy);
        }

        // 5. Pagination
        $perPage = 10;
        $page    = $request->get('page', 1);
        $items   = $filtered->values()->slice(($page - 1) * $perPage, $perPage);

        $tickets = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('tickets.my-tickets', compact('user', 'summary', 'tickets', 'currentTab', 'assignedTickets'));
    }

    public function show($id)
    {
        $ticket = \App\Models\Ticket::with(['unit', 'assignedTechnician', 'creator'])
            ->where('id', $id)
            ->orWhere('ticket_id', $id)
            ->first();

        if (!$ticket) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Ticket not found'], 404);
            }
            return redirect()->route('tickets.index')->with('error', 'Ticket not found.');
        }

        $ticketData = [
            'id'           => $ticket->id,
            'ticket_id'    => $ticket->ticket_id,
            'title'        => $ticket->title,
            'description'  => $ticket->description,
            'status'       => $ticket->status,
            'priority'     => $ticket->priority,
            'category'     => $ticket->category,
            'ticket_type'  => $ticket->ticket_type,
            'full_name'    => $ticket->full_name,
            'email'        => $ticket->email,
            'phone'        => $ticket->phone,
            'phone_ext'    => $ticket->phone_ext,
            'unit'         => $ticket->unit->name ?? 'N/A',
            'unit_id'      => $ticket->unit_id,
            'assigned_to'  => $ticket->assignedTechnician->name ?? 'Unassigned',
            'assigned_to_id' => $ticket->assigned_to,
            'created_by'   => $ticket->creator->name ?? 'System',
            'created_at'   => $ticket->created_at,
            'started_at'   => $ticket->started_at,
            'resolved_at'  => $ticket->resolved_at,
            'closed_at'    => $ticket->closed_at,
        ];

        if (request()->ajax()) {
            return view('tickets.partials.ticket-detail-body', ['ticket' => $ticketData]);
        }

        return view('tickets.show', ['ticket' => $ticketData]);
    }

    public function edit($id, \App\Services\DropdownService $dropdowns)
    {
        $ticket = \App\Models\Ticket::with(['unit', 'logs.user'])
            ->where('id', $id)
            ->orWhere('ticket_id', $id)
            ->first();

        if (!$ticket) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Ticket not found.'], 404);
            }
            return redirect()->route('tickets.index')->with('error', 'Ticket not found.');
        }

        $ticketData = [
            'id'            => $ticket->id,
            'ticket_id'     => $ticket->ticket_id,
            'title'         => $ticket->title,
            'description'   => $ticket->description,
            'status'        => $ticket->status,
            'priority'      => $ticket->priority,
            'category'      => $ticket->category,
            'ticket_type'   => $ticket->ticket_type,
            'full_name'     => $ticket->full_name,
            'email'         => $ticket->email,
            'phone'         => $ticket->phone,
            'phone_ext'     => $ticket->phone_ext,
            'unit'          => $ticket->unit->name ?? 'N/A',
            'unit_id'       => $ticket->unit_id,
            'assigned_to_id' => $ticket->assigned_to,
            'created_at'    => $ticket->created_at,
            'closed_at'     => $ticket->closed_at,
        ];

        $logs = $ticket->logs;
        $data = array_merge(['ticket' => $ticketData, 'logs' => $logs], $dropdowns->getAllDropdowns());

        if (request()->ajax()) {
            return view('tickets.partials.ticket-edit-body', $data);
        }

        return view('tickets.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'priority'    => 'required',
            'category'    => 'required',
            'unit'        => 'required|string',
            'ticket_type' => 'required|string',
        ]);

        $ticket = \App\Models\Ticket::where('id', $id)
            ->orWhere('ticket_id', $id)
            ->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')->with('error', 'Ticket not found.');
        }

        // Store old values for logging
        $oldValues = [
            'status'      => $ticket->status,
            'priority'    => $ticket->priority,
            'category'    => $ticket->category,
            'unit'        => $ticket->unit->name ?? null,
            'ticket_type' => $ticket->ticket_type,
            'title'       => $ticket->title,
        ];

        // Find unit by name
        $unit = \App\Models\Unit::where('name', $request->unit)->first();

        // Update ticket
        $updateData = [
            'full_name'   => $request->full_name ?? $ticket->full_name,
            'email'       => $request->email ?? $ticket->email,
            'title'       => $request->title ?? $ticket->title,
            'description' => $request->description ?? $ticket->description,
            'priority'    => $request->priority,
            'category'    => $request->category,
            'unit_id'     => $unit->id ?? $ticket->unit_id,
            'ticket_type' => $request->ticket_type,
            'status'      => $request->status ?? $ticket->status,
        ];

        // ─── Handle closed_at ────────────────────────────────────────
        $newStatus = $request->status ?? $ticket->status;
        if ($newStatus === 'Closed' && $oldValues['status'] !== 'Closed') {
            // Guna tarikh yang user pilih, fallback ke now() kalau kosong
            $updateData['closed_at'] = $request->filled('closed_at')
                ? Carbon::parse($request->closed_at)
                : now();
        } elseif ($newStatus !== 'Closed' && $oldValues['status'] === 'Closed') {
            $updateData['closed_at'] = null;
        }

        // Only update assigned_to if user has permission
        if (auth()->user()->can('assign ticket') && $request->has('assigned_to')) {
            $updateData['assigned_to'] = $request->assigned_to ?: null;
            $updateData['assigned_at'] = $request->assigned_to ? now() : null;
        }

        $ticket->update($updateData);

        $user    = auth()->user();

        // ─── Status Change ────────────────────────────────────────────
        if ($oldValues['status'] !== $ticket->status) {
            $statusLog = \App\Models\TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'action'    => 'status_changed',
                'field'     => 'status',
                'old_value' => $oldValues['status'],
                'new_value' => $ticket->status,
                'message'   => "{$user->name} changed status from {$oldValues['status']} to {$ticket->status}",
            ]);

            // ✅ Queue email for ALL status changes (Open, In Progress, Resolved, Closed)
            // Email to ticket requester
            if ($ticket->email) {
                \Illuminate\Support\Facades\Mail::to($ticket->email)
                    ->queue(new \App\Mail\TicketActivityNotification($ticket, $statusLog));
            }

            // Email to the staff who made the change (only if different from requester)
            if ($user->email && $user->email !== $ticket->email) {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->queue(new \App\Mail\TicketActivityNotification($ticket, $statusLog));
            }
        }

        // ─── Priority Change ──────────────────────────────────────────
        if ($oldValues['priority'] !== $ticket->priority) {
            \App\Models\TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'action'    => 'priority_changed',
                'field'     => 'priority',
                'old_value' => $oldValues['priority'],
                'new_value' => $ticket->priority,
                'message'   => "{$user->name} changed priority from {$oldValues['priority']} to {$ticket->priority}",
            ]);
        }

        // ─── Category Change ──────────────────────────────────────────
        if ($oldValues['category'] !== $ticket->category) {
            \App\Models\TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'action'    => 'category_changed',
                'field'     => 'category',
                'old_value' => $oldValues['category'],
                'new_value' => $ticket->category,
                'message'   => "{$user->name} changed category from {$oldValues['category']} to {$ticket->category}",
            ]);
        }

        // ─── General Update Log ───────────────────────────────────────
        if (
            $oldValues['title'] !== ($request->title ?? $ticket->title) ||
            $oldValues['unit'] !== $request->unit ||
            $oldValues['ticket_type'] !== $request->ticket_type
        ) {
            \App\Models\TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'action'    => 'updated',
                'field'     => null,
                'old_value' => null,
                'new_value' => null,
                'message'   => "{$user->name} updated ticket details",
            ]);
        }

        return redirect()->route('tickets.index')
            ->with('success', "Ticket {$ticket->ticket_id} updated successfully!");
    }

    public function addActivity(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = \App\Models\Ticket::where('id', $id)
            ->orWhere('ticket_id', $id)
            ->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found.'], 404);
        }

        $user = auth()->user();

        \App\Models\TicketLog::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'guest_email' => null,
            'is_staff'    => true,
            'action'      => 'activity_added',
            'field'       => null,
            'old_value'   => null,
            'new_value'   => null,
            'message'     => $request->message,
        ]);

        $log = \App\Models\TicketLog::where('ticket_id', $ticket->id)
            ->where('message', $request->message)
            ->latest()
            ->first();

        if ($ticket->email && $log) {
            \App\Jobs\SendTicketEmailJob::dispatch($ticket, $log);
        }

        return response()->json([
            'success' => true,
            'message' => 'Activity added successfully!'
        ]);
    }

    public function destroy($id)
    {
        $ticket = \App\Models\Ticket::where('ticket_id', $id)->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')->with('error', 'Ticket not found!');
        }

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully!');
    }

    public function assignTicket(Request $request, $id)
    {
        $ticket = \App\Models\Ticket::where('id', $id)
            ->orWhere('ticket_id', $id)
            ->first();

        if (!$ticket) {
            return redirect()->route('tickets.index')->with('error', 'Ticket not found.');
        }

        $oldAssignee = $ticket->assignedTechnician->name ?? 'Unassigned';

        $ticket->update([
            'assigned_to' => $request->assigned_to ?: null,
            'assigned_at' => $request->assigned_to ? now() : null,
        ]);

        $ticket->refresh();
        $newAssignee = $ticket->assignedTechnician->name ?? 'Unassigned';

        \App\Models\TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'action'    => 'assigned',
            'field'     => 'assigned_to',
            'old_value' => $oldAssignee,
            'new_value' => $newAssignee,
            'message'   => auth()->user()->name . " assigned ticket to {$newAssignee}",
        ]);

        return redirect()->back()->with('success', "Ticket assigned to {$newAssignee} successfully!");
    }

    public function export()
    {
        $tickets = \App\Models\Ticket::with('unit')
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('All Tickets');

        // Title Row
        $sheet->setCellValue('A1', 'ALL TICKETS REPORT');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Generated date
        $sheet->setCellValue('A2', 'Generated On:');
        $sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B2:J2');
        $sheet->getStyle('A2:J2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        // Summary stats
        $openCount       = $tickets->where('status', 'Open')->count();
        $inProgressCount = $tickets->where('status', 'In Progress')->count();
        $resolvedCount   = $tickets->where('status', 'Resolved')->count();
        $closedCount     = $tickets->where('status', 'Closed')->count();

        $sheet->setCellValue('A3', 'Total Tickets:');
        $sheet->setCellValue('B3', $tickets->count() . '  |  Open: ' . $openCount . '  |  In Progress: ' . $inProgressCount . '  |  Resolved: ' . $resolvedCount . '  |  Closed: ' . $closedCount);
        $sheet->mergeCells('B3:J3');
        $sheet->getStyle('A3:J3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);

        // Empty row
        $sheet->getRowDimension(4)->setRowHeight(10);

        // Table Headers
        $row     = 5;
        $headers = ['#', 'Ticket ID', 'Title', 'Name', 'Email', 'Status', 'Priority', 'Category', 'Unit', 'Created'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . $row, $header);
        }

        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        // Data rows
        $num = 1;
        foreach ($tickets as $ticket) {
            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", $ticket->ticket_id);
            $sheet->setCellValue("C{$row}", $ticket->title);
            $sheet->setCellValue("D{$row}", $ticket->full_name ?? 'N/A');
            $sheet->setCellValue("E{$row}", $ticket->email ?? 'N/A');
            $sheet->setCellValue("F{$row}", $ticket->status);
            $sheet->setCellValue("G{$row}", $ticket->priority);
            $sheet->setCellValue("H{$row}", $ticket->category);
            $sheet->setCellValue("I{$row}", $ticket->unit->name ?? 'N/A');
            $sheet->setCellValue("J{$row}", Carbon::parse($ticket->created_at)->format('M d, Y'));

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Color-code status
            $statusColor = match ($ticket->status) {
                'Open'        => 'F59E0B',
                'In Progress' => '3B82F6',
                'Resolved'    => '22C55E',
                'Closed'      => '6B7280',
                'Critical'    => 'DC2626',
                default       => '000000',
            };
            $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB($statusColor);
            $sheet->getStyle("F{$row}")->getFont()->setBold(true);

            // Color-code priority
            $priorityColor = match ($ticket->priority) {
                'Critical' => 'DC2626',
                'High'     => 'F97316',
                'Medium'   => '3B82F6',
                'Low'      => '6B7280',
                default    => '000000',
            };
            $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB($priorityColor);
            $sheet->getStyle("G{$row}")->getFont()->setBold(true);

            $row++;
            $num++;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setWidth(16);

        $filename  = 'All_Tickets_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer    = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'tickets');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    private function getDummyTickets()
    {
        $tickets    = [];
        $statuses   = ['Open', 'In Progress', 'Resolved', 'Closed', 'Critical'];
        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $units      = ['Network & Infrastructure', 'System Unit', 'Technical Support'];
        $categories = ['Server Issue', 'Hardware Issue', 'Software Issue', 'Access Request', 'Maintenance Request', 'Network Problem', 'Other'];
        $types      = ['CM', 'PM'];

        for ($i = 1; $i <= 50; $i++) {
            $created_at = now()->subDays(rand(0, 30))->subHours(rand(1, 23));
            $status     = $statuses[array_rand($statuses)];
            $priority   = $priorities[array_rand($priorities)];

            if ($status === 'Critical') {
                $priority = 'Critical';
            }

            $started_at  = ($status === 'In Progress' || $status === 'Resolved' || $status === 'Closed')
                ? (clone $created_at)->addHours(rand(1, 4))
                : null;

            $resolved_at = ($status === 'Resolved' || $status === 'Closed')
                ? (clone $started_at ?? $created_at)->addHours(rand(2, 48))
                : null;

            $tickets[] = [
                'ticket_id'   => 'TKT-2024-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status'      => $status,
                'full_name'   => 'User ' . $i,
                'email'       => 'user' . $i . '@company.com',
                'category'    => $categories[array_rand($categories)],
                'unit'        => $units[array_rand($units)],
                'priority'    => $priority,
                'created_at'  => $created_at,
                'started_at'  => $started_at,
                'resolved_at' => $resolved_at,
                'title'       => 'Sample Ticket ' . $i . ' - ' . $categories[array_rand($categories)],
                'ticket_type' => $types[array_rand($types)]
            ];
        }

        usort($tickets, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return $tickets;
    }

    private function calculateDashboardStats($tickets)
    {
        return [
            'total'           => $tickets->count(),
            'open'            => $tickets->where('status', 'Open')->count(),
            'in_progress'     => $tickets->where('status', 'In Progress')->count(),
            'resolved'        => $tickets->where('status', 'Resolved')->count(),
            'closed'          => $tickets->where('status', 'Closed')->count(),
            'critical_status' => $tickets->where('status', 'Critical')->count(),
            'priority'        => [
                'critical' => $tickets->where('priority', 'Critical')->count(),
                'high'     => $tickets->where('priority', 'High')->count(),
                'medium'   => $tickets->where('priority', 'Medium')->count(),
                'low'      => $tickets->where('priority', 'Low')->count(),
            ],
            'categories' => $tickets->groupBy('category')
                ->map(fn($group) => $group->count())
                ->sortDesc()
                ->take(5)
                ->toArray(),
            'units' => $tickets->groupBy('unit')
                ->map(fn($group) => $group->count())
                ->sortDesc()
                ->toArray(),
        ];
    }

    public function publicView($token)
    {
        $ticket = \App\Models\Ticket::where('public_token', $token)
            ->with(['logs' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'logs.user'])
            ->first();

        if (!$ticket) {
            abort(404, 'Ticket not found.');
        }

        $logs = $ticket->logs;

        return view('tickets.public', compact('ticket', 'logs'));
    }

    public function publicReply(Request $request, $token)
    {
        $request->validate([
            'email'   => 'required|email',
            'message' => 'required|string',
        ]);

        $ticket = \App\Models\Ticket::where('public_token', $token)->first();

        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        \App\Models\TicketLog::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => null,
            'guest_email' => $request->email,
            'is_staff'    => false,
            'action'      => 'reply',
            'field'       => null,
            'old_value'   => null,
            'new_value'   => null,
            'message'     => $request->message,
        ]);

        return redirect()->back()->with('success', 'Your reply has been submitted successfully!');
    }
}
