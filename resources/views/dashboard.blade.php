@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Dashboard</h1>
            <p class="shad-page-description">Financial overview as of {{ $reportDate }}</p>
        </div>
        <div>
            <button type="button" class="shad-btn shad-btn-primary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#reportModal"
                    data-toggle="modal"
                    data-target="#reportModal"
                    onclick="openReportModal()">
                <i class="fas fa-file-excel mr-2"></i>
                Generate Report
            </button>
        </div>
    </div>

    <!-- Cash Balance Section -->
    <div class="row mb-4">
        <!-- Total Cash Available -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Total Cash Available</p>
                        <p class="shad-stat-value" style="color: #22c55e;">RM {{ number_format($totalCashAvailable, 2) }}</p>
                    </div>
                    <div class="shad-stat-icon success">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between" style="font-size: 0.8125rem; color: var(--slate-500);">
                        <span>MBB: RM {{ number_format($cashAvailableMBB, 2) }}</span>
                        <span>RHB: RM {{ number_format($cashAvailableRHB, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- To Pay (Overdue) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">To Pay (Overdue)</p>
                        <p class="shad-stat-value" style="color: #ef4444;">RM {{ number_format($toPay, 2) }}</p>
                    </div>
                    <div class="shad-stat-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- To Collect (Overdue) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">To Collect (Overdue)</p>
                        <p class="shad-stat-value" style="color: #f59e0b;">RM {{ number_format($toCollect, 2) }}</p>
                    </div>
                    <div class="shad-stat-icon warning">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Pay -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Net Pay</p>
                        <p class="shad-stat-value" style="color: {{ $netPay >= 0 ? '#22c55e' : '#ef4444' }};">
                            RM {{ number_format($netPay, 2) }}
                        </p>
                    </div>
                    <div class="shad-stat-icon info">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Summary — Vendor LEFT, Customer RIGHT -->
    <div class="row mb-4">
        <!-- Vendor Outstanding (LEFT) -->
        <div class="col-md-6 mb-4">
            <div class="shad-stat-card" style="border-left: 3px solid #f59e0b;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="shad-stat-label">Total Vendor Outstanding</p>
                        <p class="shad-stat-value">RM {{ number_format($totalVendorOutstanding, 2) }}</p>
                    </div>
                    <a href="{{ route('vendor.finance.index') }}" class="shad-btn shad-btn-ghost shad-btn-sm">
                        View <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Outstanding (RIGHT) -->
        <div class="col-md-6 mb-4">
            <div class="shad-stat-card" style="border-left: 3px solid #3b82f6;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="shad-stat-label">Total Customer Outstanding</p>
                        <p class="shad-stat-value">RM {{ number_format($totalCustomerOutstanding, 2) }}</p>
                    </div>
                    <a href="{{ route('customers.finance') }}" class="shad-btn shad-btn-ghost shad-btn-sm">
                        View <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Tables — Vendors LEFT, Customers RIGHT -->
    <div class="row">
        <!-- Overdue Vendors (To Pay) — sorted latest due date first -->
        <div class="col-lg-6 mb-4">
            <div class="shad-card h-100">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="shad-card-title">
                            <i class="fas fa-building mr-2" style="color: var(--slate-400);"></i>
                            Overdue Vendors
                        </h2>
                        <p class="shad-card-description">Amounts to pay</p>
                    </div>
                    <span class="shad-badge shad-badge-danger">{{ $overdueVendors->total() }}</span>
                </div>
                <div class="shad-card-body p-0">
                    @if($overdueVendors->total() > 0)
                        <div class="table-responsive">
                            <table class="shad-table">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueVendors as $vendor)
                                    <tr>
                                        <td style="font-weight: 500; color: var(--slate-800);">
                                            {{ $vendor['vendor_name'] }}
                                        </td>
                                        <td class="text-center" style="font-size: 0.8125rem; color: var(--slate-500);">
                                            {{ isset($vendor['due_date']) ? \Carbon\Carbon::parse($vendor['due_date'])->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-right" style="color: #dc2626; font-weight: 500;">
                                            RM {{ number_format($vendor['total'] ?? $vendor['amount'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top d-flex justify-content-end">
                            {{ $overdueVendors->appends(['customers_page' => request('customers_page')])->links() }}
                        </div>
                    @else
                        <div class="shad-empty" style="padding: 2rem;">
                            <i class="fas fa-check-circle fa-2x" style="color: #22c55e;"></i>
                            <p class="shad-empty-title mt-2">No overdue payments</p>
                            <p class="shad-empty-description">All vendor invoices are up to date.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Overdue Customers (To Collect) — sorted latest due date first -->
        <div class="col-lg-6 mb-4">
            <div class="shad-card h-100">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="shad-card-title">
                            <i class="fas fa-users mr-2" style="color: var(--slate-400);"></i>
                            Overdue Customers
                        </h2>
                        <p class="shad-card-description">Amounts to collect</p>
                    </div>
                    <span class="shad-badge shad-badge-warning">{{ $overdueCustomers->total() }}</span>
                </div>
                <div class="shad-card-body p-0">
                    @if($overdueCustomers->total() > 0)
                        <div class="table-responsive">
                            <table class="shad-table">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueCustomers as $customer)
                                    <tr>
                                        <td style="font-weight: 500; color: var(--slate-800);">
                                            {{ $customer['customer_name'] }}
                                        </td>
                                        <td class="text-center" style="font-size: 0.8125rem; color: var(--slate-500);">
                                            {{ isset($customer['due_date']) ? \Carbon\Carbon::parse($customer['due_date'])->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-right" style="color: #f59e0b; font-weight: 500;">
                                            RM {{ number_format($customer['total'] ?? $customer['amount'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top d-flex justify-content-end">
                            {{ $overdueCustomers->appends(['vendors_page' => request('vendors_page')])->links() }}
                        </div>
                    @else
                        <div class="shad-empty" style="padding: 2rem;">
                            <i class="fas fa-check-circle fa-2x" style="color: #22c55e;"></i>
                            <p class="shad-empty-title mt-2">No overdue collections</p>
                            <p class="shad-empty-description">All customer invoices are up to date.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Balance Summary Card -->
    <div class="shad-card">
        <div class="shad-card-header" style="background: #18181b; border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
            <h2 class="shad-card-title" style="color: #fafafa;">
                <i class="fas fa-coins mr-2"></i>
                Projected Cash Balance
            </h2>
        </div>
        <div class="shad-card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div style="font-size: 3rem; font-weight: 700; color: {{ $cashBalance >= 0 ? '#22c55e' : '#ef4444' }};">
                        RM {{ number_format($cashBalance, 2) }}
                    </div>
                    <p style="color: var(--slate-500); margin-top: 0.5rem;">
                        = Cash Available - To Pay + Net Pay
                    </p>
                </div>
                <div class="col-md-6">
                    <div style="background: var(--slate-50); border-radius: var(--radius); padding: 1rem;">
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.875rem;">
                            <span style="color: var(--slate-500);">Cash Available</span>
                            <span style="font-weight: 500;">RM {{ number_format($totalCashAvailable, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.875rem;">
                            <span style="color: var(--slate-500);">Less: To Pay (Overdue)</span>
                            <span style="font-weight: 500; color: #ef4444;">- RM {{ number_format($toPay, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size: 0.875rem;">
                            <span style="color: var(--slate-500);">Add: Net Pay</span>
                            <span style="font-weight: 500; color: {{ $netPay >= 0 ? '#22c55e' : '#ef4444' }};">
                                {{ $netPay >= 0 ? '+' : '' }} RM {{ number_format($netPay, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Include Report Modal -->
@include('report-modal')

<script>
function openReportModal() {
    if (typeof bootstrap !== 'undefined') {
        var myModal = new bootstrap.Modal(document.getElementById('reportModal'));
        myModal.show();
    } else if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#reportModal').modal('show');
    } else {
        document.getElementById('reportModal').style.display = 'block';
        document.getElementById('reportModal').classList.add('show');
        document.body.classList.add('modal-open');
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modalBackdrop';
        document.body.appendChild(backdrop);
    }
}

function closeReportModal() {
    var modal = document.getElementById('reportModal');
    if (typeof bootstrap !== 'undefined') {
        try {
            var modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) modalInstance.hide();
        } catch (e) {}
    }
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        try { jQuery('#reportModal').modal('hide'); } catch (e) {}
    }
    setTimeout(function() {
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show', 'fade');
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.removeAttribute('data-bs-overflow');
        document.body.removeAttribute('data-bs-padding-right');
        document.querySelectorAll('.modal-backdrop, #modalBackdrop').forEach(function(el) {
            el.parentNode.removeChild(el);
        });
    }, 100);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeReportModal();
        });
    });

    var modal = document.getElementById('reportModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeReportModal();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            var modal = document.getElementById('reportModal');
            if (modal && (modal.classList.contains('show') || modal.style.display === 'block')) {
                closeReportModal();
            }
        }
    });
});
</script>

@endsection
