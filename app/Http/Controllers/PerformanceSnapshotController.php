<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerFinance;
use App\Models\Project;
use App\Models\RevenueBudget;
use Carbon\Carbon;

use App\Exports\ProjectSnapshotExport;
use Illuminate\Support\Facades\DB;

class PerformanceSnapshotController extends Controller
{
    public function index()
    {
        // --- Part A: Revenue Performance ---
        
        // 1. Calculate Actual Monthly Revenue (Current Year)
        // Group invoices by month for the current year
        $monthlyRevenue = CustomerFinance::selectRaw('MONTH(invoice_date) as month, SUM(invoice) as total')
            ->whereYear('invoice_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // 2. Define Budget (Mock Targets)
        // Let's assume a growing budget
        $budgetData = [
            1 => 80000, 2 => 85000, 3 => 90000, 4 => 95000, 
            5 => 100000, 6 => 110000, 7 => 115000, 8 => 120000, 
            9 => 125000, 10 => 130000, 11 => 135000, 12 => 140000
        ];

        // 3. Helper to ensure all 12 months are present for charts
        $months = range(1, 12);
        $actuals = [];
        $budgets = [];
        
        foreach ($months as $m) {
            $actuals[] = $monthlyRevenue[$m] ?? 0;
            $budgets[] = $budgetData[$m];
        }

        // 4. Calculate YTD Stats
        $currentMonth = Carbon::now()->month;
        $ytdActual = 0;
        $ytdBudget = 0;

        for ($i = 1; $i <= $currentMonth; $i++) {
            $ytdActual += $monthlyRevenue[$i] ?? 0;
            $ytdBudget += $budgetData[$i];
        }

        $ytdPercent = $ytdBudget > 0 ? ($ytdActual / $ytdBudget) * 100 : 0;


        // --- Part B: Project Delivery ---
        
        // Count projects by status
        $projectStats = [
            'green' => Project::where('status', 'green')->count(),
            'yellow' => Project::where('status', 'yellow')->count(),
            'red' => Project::where('status', 'red')->count(),
        ];
        
        $totalProjects = array_sum($projectStats);


        return view('performance.snapshot', compact(
            'actuals', 'budgets', 'ytdActual', 'ytdBudget', 'ytdPercent', 'projectStats', 'totalProjects'
        ));
    }
    
    public function itsm(Request $request)
    {
        // =============================================
        // Get Available Years from Database
        // =============================================
        $years = RevenueBudget::distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        // If no years exist in database, provide a default range
        if ($years->isEmpty()) {
            $years = collect(range(date('Y'), date('Y') + 5));
        }
        
        // =============================================
        // PART A: Revenue Performance
        // =============================================
        
        $selectedYear = $request->input('year', Carbon::now()->year);

        // 1. Calculate Actual Monthly Revenue (Selected Year)
        // Revenue grouped by INVOICE DATE (when invoice was created)
        // This matches Customer Finance page grouping logic
        // Revenue = Initial received_amount + Additional payments from customer_payments
        
        // A) Get initial payments grouped by invoice_date
        $initialPayments = CustomerFinance::selectRaw('MONTH(invoice_date) as month, SUM(received_amount) as total')
            ->whereYear('invoice_date', $selectedYear)
            ->where('received_amount', '>', 0)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        // B) Get additional payments grouped by invoice's invoice_date
        // Join customer_payments with customer_finances to get invoice_date
        $additionalPayments = DB::table('customer_payments')
            ->join('customer_finances', 'customer_payments.customer_finance_id', '=', 'customer_finances.id')
            ->selectRaw('MONTH(customer_finances.invoice_date) as month, SUM(customer_payments.amount) as total')
            ->whereYear('customer_finances.invoice_date', $selectedYear)
            ->where('customer_payments.amount', '>', 0)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        // Combine both sources with zero-fill for all 12 months
        $monthlyReceived = [];
        for ($month = 1; $month <= 12; $month++) {
            $initial = $initialPayments[$month] ?? 0;
            $additional = $additionalPayments[$month] ?? 0;
            $monthlyReceived[$month] = $initial + $additional;
        }

        // 2. Get Budget from Database (or use default fallback)
        $budgetData = RevenueBudget::getMonthlyBudgets($selectedYear);
        
        // Fallback to default if no budget set for this year
        if (empty($budgetData)) {
            $budgetData = [
                1 => 80000, 2 => 85000, 3 => 90000, 4 => 95000, 
                5 => 100000, 6 => 110000, 7 => 115000, 8 => 120000, 
                9 => 125000, 10 => 130000, 11 => 135000, 12 => 140000
            ];
        }

        // 3. Prepare chart data for all 12 months
        $months = range(1, 12);
        $actuals = [];
        $budgets = [];
        
        foreach ($months as $m) {
            $actuals[] = $monthlyReceived[$m];  // Already zero-filled
            $budgets[] = $budgetData[$m];
        }

        // 4. Calculate YTD Stats (Year-To-Date)
        // If selected year is in the past, use full year (12 months)
        // If selected year is current year, use up to current month
        $limitMonth = ($selectedYear < Carbon::now()->year) ? 12 : Carbon::now()->month;
        
        $ytdReceivedTotal = 0;
        $ytdBudgetTotal = 0;

        for ($i = 1; $i <= $limitMonth; $i++) {
            $ytdReceivedTotal += $monthlyReceived[$i];
            $ytdBudgetTotal += $budgetData[$i];
        }

        $ytdPercent = $ytdBudgetTotal > 0 ? round(($ytdReceivedTotal / $ytdBudgetTotal) * 100, 1) : 0;

        // =============================================
        // PART B: Project Delivery Performance
        // =============================================
        
        $projectStats = [
            'green' => Project::where('status', 'green')->count(),
            'yellow' => Project::where('status', 'yellow')->count(),
            'red' => Project::where('status', 'red')->count(),
        ];
        
        $totalProjects = array_sum($projectStats);

        return view('performance.itsm_snapshot', compact(
            'years',  // ADD THIS
            'months', 'actuals', 'budgets', 
            'ytdReceivedTotal', 'ytdBudgetTotal', 'ytdPercent',
            'projectStats', 'totalProjects', 'selectedYear'
        ));
    }

    /**
     * Export Project Snapshot report to Excel
     * 
     * Route: POST /performance/itsm/export
     * Name: performance.itsm.export
     */
    public function export(Request $request)
    {
        try {
            $selectedYear = $request->input('year', Carbon::now()->year);
            
            // =============================================
            // PART A: Revenue Performance Data
            // =============================================
            
            // 1. Calculate Actual Monthly Revenue (same logic as itsm method)
            
            // A) Get initial payments grouped by invoice_date
            $initialPayments = CustomerFinance::selectRaw('MONTH(invoice_date) as month, SUM(received_amount) as total')
                ->whereYear('invoice_date', $selectedYear)
                ->where('received_amount', '>', 0)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
            
            // B) Get additional payments grouped by invoice's invoice_date
            $additionalPayments = DB::table('customer_payments')
                ->join('customer_finances', 'customer_payments.customer_finance_id', '=', 'customer_finances.id')
                ->selectRaw('MONTH(customer_finances.invoice_date) as month, SUM(customer_payments.amount) as total')
                ->whereYear('customer_finances.invoice_date', $selectedYear)
                ->where('customer_payments.amount', '>', 0)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
            
            // Combine both sources with zero-fill for all 12 months
            $monthlyReceived = [];
            for ($month = 1; $month <= 12; $month++) {
                $initial = $initialPayments[$month] ?? 0;
                $additional = $additionalPayments[$month] ?? 0;
                $monthlyReceived[$month] = $initial + $additional;
            }

            // 2. Get Budget from Database
            $budgetData = RevenueBudget::getMonthlyBudgets($selectedYear);
            
            // Fallback to default if no budget set for this year
            if (empty($budgetData)) {
                $budgetData = [
                    1 => 80000, 2 => 85000, 3 => 90000, 4 => 95000, 
                    5 => 100000, 6 => 110000, 7 => 115000, 8 => 120000, 
                    9 => 125000, 10 => 130000, 11 => 135000, 12 => 140000
                ];
            }

            // 3. Prepare arrays for export (0-indexed for consistency)
            $actuals = [];
            $budgets = [];
            
            for ($m = 1; $m <= 12; $m++) {
                $actuals[] = $monthlyReceived[$m];
                $budgets[] = $budgetData[$m];
            }

            // 4. Calculate YTD Stats
            $limitMonth = ($selectedYear < Carbon::now()->year) ? 12 : Carbon::now()->month;
            
            $ytdReceivedTotal = 0;
            $ytdBudgetTotal = 0;

            for ($i = 1; $i <= $limitMonth; $i++) {
                $ytdReceivedTotal += $monthlyReceived[$i];
                $ytdBudgetTotal += $budgetData[$i];
            }

            $ytdPercent = $ytdBudgetTotal > 0 ? round(($ytdReceivedTotal / $ytdBudgetTotal) * 100, 1) : 0;
            
            // =============================================
            // PART B: Project Delivery Performance
            // =============================================
            
            $projectStats = [
                'green' => Project::where('status', 'green')->count(),
                'yellow' => Project::where('status', 'yellow')->count(),
                'red' => Project::where('status', 'red')->count(),
            ];
            
            // =============================================
            // Prepare data for export
            // =============================================
            
            $revenueData = [
                'budgets' => $budgets,
                'actuals' => $actuals,
            ];
            
            $ytdData = [
                'budget' => $ytdBudgetTotal,
                'actual' => $ytdReceivedTotal,
                'percent' => $ytdPercent,
            ];
            
            $filename = 'project_snapshot_' . $selectedYear . '_' . date('Y-m-d_His') . '.xlsx';
            
            $export = new ProjectSnapshotExport($selectedYear, $revenueData, $projectStats, $ytdData);
            return $export->download($filename);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}