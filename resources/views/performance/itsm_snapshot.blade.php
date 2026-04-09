@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Project Snapshot</h1>
            <p class="shad-page-description">Revenue performance and project delivery health.</p>
        </div>
        
        <!-- Generate Report Button -->
        <form action="{{ route('performance.itsm.export') }}" method="POST" class="m-0">
            @csrf
            <input type="hidden" name="year" value="{{ $selectedYear ?? date('Y') }}">
            <button type="submit" class="shad-btn shad-btn-primary">
                <i class="fas fa-file-excel mr-2"></i>
                Generate Report
            </button>
        </form>
    </div>

    <!-- ============================================ -->
    <!-- PART A: Revenue Performance -->
    <!-- ============================================ -->
    <div class="shad-card mb-4">
        <div class="shad-card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #000000ff 100%); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="shad-card-title" style="color: #fff;">
                        <i class="fas fa-chart-line mr-2"></i> Revenue Performance
                    </h2>
                    <p style="color: rgba(255,255,255,0.8); font-size: 0.8125rem; margin: 0;">Actual vs Budget comparison for selected year</p>
                </div>
                <!-- Year Filter Form -->
                <form action="{{ route('performance.itsm') }}" method="GET" class="m-0">
                    <div class="d-flex align-items-center" style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 8px;">
                        <span class="mr-2" style="color: rgba(255,255,255,0.8); font-size: 0.875rem;"><i class="fas fa-calendar-alt"></i></span>
                        <select name="year" onchange="this.form.submit()" class="shad-input border-0 p-0" style="background: transparent; color: white; font-weight: 600; width: auto; cursor: pointer; height: auto;">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ ($selectedYear ?? date('Y')) == $y ? 'selected' : '' }} style="color: #1e293b;">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="shad-card-body">
            <div class="row">
                <!-- YTD Summary Cards -->
                <div class="col-lg-4 mb-4">
                    <div class="row">
                        <!-- YTD Actual -->
                        <div class="col-12 mb-3">
                            <div class="shad-stat-card" style="border-left: 4px solid #f59e0b;">
                                <p class="shad-stat-label">YTD Actual Revenue</p>
                                <p class="shad-stat-value" style="color: #f59e0b;">RM {{ number_format($ytdReceivedTotal, 0) }}</p>
                            </div>
                        </div>
                        <!-- YTD Budget -->
                        <div class="col-12 mb-3">
                            <div class="shad-stat-card" style="border-left: 4px solid #3b82f6;">
                                <p class="shad-stat-label">YTD Budget Target</p>
                                <p class="shad-stat-value" style="color: #3b82f6;">RM {{ number_format($ytdBudgetTotal, 0) }}</p>
                            </div>
                        </div>
                        <!-- YTD Percentage Gauge -->
                        <div class="col-12">
                            <div class="shad-stat-card text-center" style="border-left: 4px solid #22c55e;">
                                <p class="shad-stat-label">YTD Achievement</p>
                                <p class="shad-stat-value" style="font-size: 2.5rem; color: #22c55e;">
                                    {{ $ytdPercent }}%
                                </p>
                                <div class="progress mt-2" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ min($ytdPercent, 100) }}%; background: #22c55e;"
                                         aria-valuenow="{{ $ytdPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="col-lg-8">
                    <div style="height: 320px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <p class="text-center mt-2" style="font-size: 0.8125rem; color: var(--slate-500);">
                        <i class="fas fa-square mr-1" style="color: #3b82f6;"></i> Budget Target
                        <span class="mx-2">|</span>
                        <i class="fas fa-minus mr-1" style="color: #f59e0b;"></i> Actual Revenue
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- PART B: Project Delivery Performance -->
    <!-- ============================================ -->
    <div class="shad-card mb-4">
        <div class="shad-card-header" style="background: #18181b; border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
            <h2 class="shad-card-title" style="color: #fff;">
                <i class="fas fa-project-diagram mr-2"></i>
                Project Delivery Status
            </h2>
            <p style="color: rgba(255,255,255,0.6); font-size: 0.8125rem; margin: 0;">{{ $totalProjects }} active projects</p>
        </div>
        <div class="shad-card-body">
            <div class="row">
                <!-- On Track (Green) -->
                <div class="col-md-4 mb-4">
                    <div class="shad-stat-card text-center" style="border: 2px solid #22c55e; background: rgba(34, 197, 94, 0.05);">
                        <div style="width: 64px; height: 64px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="fas fa-check fa-2x" style="color: #fff;"></i>
                        </div>
                        <p class="shad-stat-value" style="font-size: 3rem; color: #22c55e; margin: 0;">{{ $projectStats['green'] }}</p>
                        <p style="font-size: 1rem; font-weight: 600; color: #22c55e; margin: 0.5rem 0;">On Track</p>
                        <p style="font-size: 0.75rem; color: var(--slate-500);">On schedule & on budget</p>
                    </div>
                </div>

                <!-- At Risk (Yellow) -->
                <div class="col-md-4 mb-4">
                    <div class="shad-stat-card text-center" style="border: 2px solid #f59e0b; background: rgba(245, 158, 11, 0.05);">
                        <div style="width: 64px; height: 64px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="fas fa-exclamation fa-2x" style="color: #fff;"></i>
                        </div>
                        <p class="shad-stat-value" style="font-size: 3rem; color: #f59e0b; margin: 0;">{{ $projectStats['yellow'] }}</p>
                        <p style="font-size: 1rem; font-weight: 600; color: #f59e0b; margin: 0.5rem 0;">At Risk</p>
                        <p style="font-size: 0.75rem; color: var(--slate-500);">Requires monitoring</p>
                    </div>
                </div>

                <!-- Delayed (Red) -->
                <div class="col-md-4 mb-4">
                    <div class="shad-stat-card text-center" style="border: 2px solid #ef4444; background: rgba(239, 68, 68, 0.05);">
                        <div style="width: 64px; height: 64px; background: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="fas fa-times fa-2x" style="color: #fff;"></i>
                        </div>
                        <p class="shad-stat-value" style="font-size: 3rem; color: #ef4444; margin: 0;">{{ $projectStats['red'] }}</p>
                        <p style="font-size: 1rem; font-weight: 600; color: #ef4444; margin: 0.5rem 0;">Delayed</p>
                        <p style="font-size: 0.75rem; color: var(--slate-500);">Immediate attention needed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart (Bar + Line)
    var ctxRevenue = document.getElementById("revenueChart");
    var revenueChart = new Chart(ctxRevenue, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                type: 'bar',
                label: 'Budget Target',
                data: @json($budgets),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: '#3b82f6',
                borderWidth: 1,
                borderRadius: 4,
                order: 2
            }, {
                type: 'line',
                label: 'Actual Revenue',
                data: @json($actuals),
                borderColor: '#f59e0b',
                backgroundColor: 'transparent',
                borderWidth: 3,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 8,           // Bigger point (was 4)
                pointHoverRadius: 12,     // Even bigger on hover
                pointHoverBorderWidth: 3,
                tension: 0.3,
                order: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RM ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });


</script>
@endpush
@endsection
