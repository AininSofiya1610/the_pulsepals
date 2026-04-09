<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Activity;
use App\Models\LeadSource;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LeadController extends Controller
{
    public function searchSuggestions(Request $request)
    {
        $query = $request->input('query');

        $leads = Lead::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(5)
            ->get(['name', 'email']);

        return response()->json($leads);
    }

    /**
     * Display a listing of active leads.
     * Excludes qualified/converted leads.
     */
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $timeline  = $request->input('timeline', 'all_time');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $query = Lead::with('assignedTo')
            ->whereNotIn('status', ['qualified', 'converted']);

        $query->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $timeline = 'custom';
        } elseif ($timeline == 'this_month') {
            $query->where('created_at', '>=', Carbon::now()->startOfMonth());
        } elseif ($timeline == 'last_month') {
            $query->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ]);
        } elseif ($timeline == 'this_quarter') {
            $query->where('created_at', '>=', Carbon::now()->startOfQuarter());
        } elseif ($timeline == 'this_year') {
            $query->where('created_at', '>=', Carbon::now()->startOfYear());
        }

        $leads = $query->latest('id')->paginate(10);

        $leads->appends([
            'search'     => $search,
            'timeline'   => $timeline,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        return view('leads.index', compact('leads', 'startDate', 'endDate', 'timeline'));
    }

    /**
     * Show the form for creating a new lead.
     * Passes dynamic lead sources from DB.
     */
    public function create()
    {
        $users       = User::all();
        $leadSources = LeadSource::active()->get();
        return view('leads.create', compact('users', 'leadSources'));
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'email'       => 'nullable|email',
            'phone'       => 'nullable',
            'source'      => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Lead::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'source'      => $request->source,
            'status'      => 'new_lead',
            'assigned_to' => $request->assigned_to,
        ]);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead.
     */
    public function show(string $id)
    {
        $lead  = Lead::with(['assignedTo', 'activities.user'])->findOrFail($id);
        $users = User::all();
        return view('leads.show', compact('lead', 'users'));
    }

    /**
     * Assign lead to a user.
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $lead              = Lead::findOrFail($id);
        $lead->assigned_to = $request->assigned_to;
        $lead->save();

        return redirect()->back()->with('success', 'Lead assigned successfully.');
    }

    /**
     * Update lead status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new_lead,new,contacted,lost',
        ]);

        $lead         = Lead::findOrFail($id);
        $lead->status = $request->status === 'new' ? 'new_lead' : $request->status;
        $lead->save();

        return redirect()->back()->with('success', 'Lead status updated successfully.');
    }

    /**
     * Convert Lead → CRM Customer ONLY.
     * ✅ Saves into: customers table
     * ❌ Never touches: finance_customers table
     */
    public function convert(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        if (in_array($lead->status, ['qualified', 'converted'])) {
            $existingCustomer = Customer::where('created_from_lead', $lead->id)->first();
            if ($existingCustomer) {
                return redirect()->route('crm.customers.show', $existingCustomer->id)
                    ->with('info', 'This lead has already been converted to a customer.');
            }
            return redirect()->back()->with('error', 'This lead has already been converted.');
        }

        if ($lead->email) {
            $existingByEmail = Customer::where('email', $lead->email)->first();
            if ($existingByEmail) {
                $lead->status = 'qualified';
                $lead->save();
                return redirect()->route('crm.customers.show', $existingByEmail->id)
                    ->with('info', 'A customer with this email already exists. Lead marked as qualified.');
            }
        }

        $customer = Customer::create([
            'name'              => $lead->name,
            'email'             => $lead->email,
            'phone'             => $lead->phone,
            'company'           => '',
            'status'            => 'active',
            'created_from_lead' => $lead->id,
        ]);

        if ($request->input('create_deal') === 'yes') {
            Deal::create([
                'customer_id' => $customer->id,
                'title'       => $request->input('deal_title', 'Opportunity - ' . $customer->name),
                'value'       => $request->input('deal_value', 0),
                'stage'       => 'new_opportunity',
            ]);
            $successMessage = 'Lead converted to CRM customer with a new deal!';
        } else {
            $successMessage = 'Lead converted to CRM customer successfully.';
        }

        $lead->status = 'qualified';
        $lead->save();

        return redirect()->route('crm.customers.show', $customer->id)
            ->with('success', $successMessage);
    }

    /**
     * Update a lead.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'   => 'required',
            'email'  => 'nullable|email',
            'phone'  => 'nullable',
            'source' => 'nullable',
        ]);

        Lead::findOrFail($id)->update($request->all());

        return redirect()->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Delete a lead.
     */
    public function destroy(string $id)
    {
        Lead::findOrFail($id)->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    /**
     * Export leads to Excel.
     */
    public function export()
    {
        $leads = Lead::with('assignedTo')
            ->whereNotIn('status', ['qualified', 'converted'])
            ->latest('created_at')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads');

        $sheet->setCellValue('A1', 'LEADS MANAGEMENT REPORT');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'Generated On:');
        $sheet->setCellValue('B2', Carbon::now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('B2:G2');
        $sheet->getStyle('A2:G2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $sheet->setCellValue('A3', 'Total Leads:');
        $sheet->setCellValue('B3', $leads->count());
        $sheet->mergeCells('B3:G3');
        $sheet->getStyle('A3:G3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'font' => ['bold' => true],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(10);

        $row     = 5;
        $headers = ['#', 'Name', 'Email', 'Phone', 'Source', 'Status', 'Assigned To'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . $row, $header);
        }
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        $num = 1;
        foreach ($leads as $lead) {
            $statusLabel = match ($lead->status) {
                'new_lead'  => 'New Lead',
                'new'       => 'New Lead',
                'contacted' => 'Contacted',
                'qualified' => 'Qualified',
                default     => ucfirst(str_replace('_', ' ', $lead->status)),
            };

            $sheet->setCellValue("A{$row}", $num);
            $sheet->setCellValue("B{$row}", $lead->name);
            $sheet->setCellValue("C{$row}", $lead->email ?? '-');
            $sheet->setCellValue("D{$row}", $lead->phone ?? '-');
            $sheet->setCellValue("E{$row}", $lead->source ?? '-');
            $sheet->setCellValue("F{$row}", $statusLabel);
            $sheet->setCellValue("G{$row}", $lead->assignedTo->name ?? 'Unassigned');

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $num++;
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        $filename  = 'Leads_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $writer    = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'leads');
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
