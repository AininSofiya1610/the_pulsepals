

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Customer Finance</h1>
            <p class="shad-page-description">Manage customer invoices and payments received</p>
        </div>
        <div class="d-flex gap-2">
            
            <a href="<?php echo e(route('customers.finance.template')); ?>" class="shad-btn shad-btn-ghost">
                <i class="fas fa-file-download mr-2"></i>
                Template
            </a>
            
            <button type="button" class="shad-btn shad-btn-outline" data-toggle="modal" data-target="#importModal">
                <i class="fas fa-file-upload mr-2"></i>
                Import Excel
            </button>
            
            <form action="<?php echo e(route('export.customer-finance')); ?>" method="POST" style="display: inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="start_date" value="<?php echo e($startDate ?? ''); ?>">
                <input type="hidden" name="end_date" value="<?php echo e($endDate ?? ''); ?>">
                <button type="submit" class="shad-btn shad-btn-outline">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export to Excel
                </button>
            </form>
            
            <button type="button" class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#invoiceModal">
                <i class="fas fa-plus"></i> Create Invoice
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="shad-card mb-4">
        <div class="shad-card-body py-3">
            <form action="<?php echo e(route('customers.finance')); ?>" method="GET" class="d-flex align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <label class="shad-label mb-0">Search:</label>
                    <input type="text" name="search" class="shad-input" style="width: 180px;" value="<?php echo e($search); ?>" placeholder="Invoice, Customer...">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="shad-label mb-0">From:</label>
                    <input type="date" name="start_date" class="shad-input date-dmy" style="width: auto;" value="<?php echo e($startDate); ?>">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="shad-label mb-0">To:</label>
                    <input type="date" name="end_date" class="shad-input date-dmy" style="width: auto;" value="<?php echo e($endDate); ?>">
                </div>
                <button type="submit" class="shad-btn shad-btn-primary shad-btn-sm">Filter</button>
                <a href="<?php echo e(route('customers.finance')); ?>" class="shad-btn shad-btn-ghost shad-btn-sm">Clear</a>
            </form>
        </div>
    </div>

    <!-- Invoice List -->
    <div class="shad-card">
        <div class="shad-card-header">
            <h2 class="shad-card-title">Invoice List</h2>
            <p class="shad-card-description">
                <?php
                    $totalCount = collect($groupedInvoices)->sum(function($months) {
                        return collect($months)->sum('count');
                    });
                ?>
                <?php echo e($totalCount); ?> invoices
            </p>
        </div>
        <div class="shad-card-body p-0">
            <?php if(count($groupedInvoices) > 0): ?>
                <?php $__currentLoopData = $groupedInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $months): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!-- Year Section -->
                    <div class="year-section" style="border-bottom: 1px solid var(--border-color);">
                        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1rem 1.5rem; border-bottom: 2px solid #3b82f6;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">
                                    <i class="fas fa-calendar-alt mr-2" style="color: #3b82f6;"></i><?php echo e($year); ?>

                                </h3>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-right mr-4">
                                        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Received</div>
                                        <div style="font-size: 1.1rem; font-weight: 700; color: #10b981;">
                                            RM <?php echo e(number_format($yearlyTotals[$year] ?? 0, 2)); ?>

                                        </div>
                                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">
                                            <i class="fas fa-info-circle mr-1"></i>Must match Project Snapshot YTD
                                        </div>
                                    </div>
                                    <span class="shad-badge shad-badge-primary" style="font-size: 0.875rem;">
                                        <?php echo e(collect($months)->sum('count')); ?> invoices
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Month Sections -->
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthNum => $monthData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $monthName = \Carbon\Carbon::create()->month((int)$monthNum)->format('F');
                                $isCurrentMonth = ($year == date('Y') && $monthNum == date('m'));
                            ?>
                            <div class="month-section" style="border-bottom: 1px solid var(--border-color);">
                                <div class="month-header" 
                                     style="background: #fafafa; padding: 0.75rem 1.5rem; cursor: pointer; transition: background 0.2s;" 
                                     data-toggle="collapse" 
                                     data-target="#month-<?php echo e($year); ?>-<?php echo e($monthNum); ?>"
                                     onmouseover="this.style.background='#f1f5f9'"
                                     onmouseout="this.style.background='#fafafa'">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fas fa-chevron-right" id="icon-<?php echo e($year); ?>-<?php echo e($monthNum); ?>" style="color: var(--slate-400); font-size: 0.75rem; transition: transform 0.2s;"></i>
                                            <h4 style="font-size: 1rem; font-weight: 600; color: var(--slate-700); margin: 0;"><?php echo e($monthName); ?></h4>
                                            <span class="shad-badge shad-badge-default" style="font-size: 0.75rem;"><?php echo e($monthData['count']); ?></span>
                                        </div>
                                        <div class="d-flex gap-4" style="font-size: 0.875rem;">
                                            <span style="color: var(--slate-600);">
                                                <strong style="color: var(--slate-800);">RM <?php echo e(number_format($monthData['total_invoice'], 2)); ?></strong>
                                            </span>
                                            <span style="color: #22c55e;">
                                                <i class="fas fa-arrow-down" style="font-size: 0.7rem;"></i>
                                                RM <?php echo e(number_format($monthData['total_received'], 2)); ?>

                                            </span>
                                            <span style="color: <?php echo e($monthData['balance'] > 0 ? '#ef4444' : '#22c55e'); ?>; font-weight: 600;">
                                                Balance: RM <?php echo e(number_format($monthData['balance'], 2)); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div id="month-<?php echo e($year); ?>-<?php echo e($monthNum); ?>" class="collapse <?php echo e($isCurrentMonth ? 'show' : ''); ?>">
                                    <div class="table-responsive">
                                        <table class="shad-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">#</th>
                                                    <th>Invoice No.</th>
                                                    <th>Customer</th>
                                                    <th>Invoice Date</th>
                                                    <th>Due Date</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-right">Invoice</th>
                                                    <th class="text-right">Received</th>
                                                    <th class="text-right">Balance</th>
                                                    <th class="text-right" style="width: 100px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $monthData['invoices']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $finance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $initialReceived = $finance->received_amount ?? 0;
                                                    $additionalReceived = $finance->payments->sum('amount');
                                                    $totalPaid = $initialReceived + $additionalReceived;
                                                    $balance = $finance->amount - $totalPaid;
                                                    $dueDate = \Carbon\Carbon::parse($finance->due_date);
                                                    $today = \Carbon\Carbon::today();
                                                    $overdueDays = $today->diffInDays($dueDate, false);
                                                    if ($balance <= 0) { $status = 'Paid'; $statusClass = 'success'; }
                                                    elseif ($totalPaid > 0) { $status = 'Partial'; $statusClass = 'warning'; }
                                                    elseif (\Carbon\Carbon::parse($finance->due_date)->isPast()) { $status = 'Overdue'; $statusClass = 'danger'; }
                                                    else { $status = 'Pending'; $statusClass = 'default'; }
                                                ?>
                                                <tr class="finance-row" style="<?php echo e(($balance > 0 && \Carbon\Carbon::parse($finance->due_date)->isPast()) ? 'background-color: #fff5f5;' : ''); ?>">
                                                    <td class="text-center" style="color: var(--slate-500);"><?php echo e($loop->iteration); ?></td>
                                                    <td style="font-weight: 600; color: var(--slate-800);"><?php echo e($finance->invoice_no); ?></td>
                                                    <td><?php echo e($finance->customer_name); ?></td>
                                                    <td style="font-size: 0.8125rem;">
                                                        <?php echo e(\Carbon\Carbon::parse($finance->invoice_date)->format('d M Y')); ?>

                                                    </td>
                                                    <td style="font-size: 0.8125rem;">
                                                        <?php echo e($dueDate->format('d M Y')); ?>

                                                        <?php if($balance > 0 && $overdueDays < 0): ?>
                                                            <br><span class="shad-badge shad-badge-danger" style="font-size: 0.65rem;"><?php echo e(abs($overdueDays)); ?>d overdue</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="shad-badge shad-badge-<?php echo e($statusClass); ?>"><?php echo e($status); ?></span>
                                                    </td>
                                                    <td class="text-right">RM <?php echo e(number_format($finance->amount, 2)); ?></td>
                                                    <td class="text-right" style="color: #22c55e;">RM <?php echo e(number_format($totalPaid, 2)); ?></td>
                                                    <td class="text-right" style="font-weight: 600;">RM <?php echo e(number_format($balance, 2)); ?></td>
                                                    <td>
                                                        <div class="shad-actions justify-content-end">
                                                            <button class="shad-btn shad-btn-ghost shad-btn-sm view-btn" data-id="<?php echo e($finance->id); ?>" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="shad-btn shad-btn-ghost shad-btn-sm delete-btn" data-id="<?php echo e($finance->id); ?>" data-invoice="<?php echo e($finance->invoice_no); ?>" style="color: #dc2626;" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="shad-empty">
                    <div class="shad-empty-icon">
                        <i class="fas fa-file-invoice fa-3x"></i>
                    </div>
                    <h3 class="shad-empty-title">No invoices found</h3>
                    <p class="shad-empty-description">Create your first customer invoice to get started.</p>
                    <button type="button" class="shad-btn shad-btn-primary mt-3" data-toggle="modal" data-target="#invoiceModal">
                        <i class="fas fa-plus"></i> Create Invoice
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- IMPORT MODAL -->
<div class="modal fade shad-modal" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-upload mr-2"></i>Import from Excel</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo e(route('customers.finance.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="shad-alert shad-alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            Please use the <strong>Template</strong> file to fill in your data before importing.
                            Only <strong>.xlsx</strong> files are accepted. Date format: <strong>DD-MM-YYYY</strong> (contoh: 25-02-2026).
                        </div>
                    </div>
                    <div class="shad-alert shad-alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            Semua rows akan disemak dahulu. Jika ada <strong>sebarang error atau duplicate</strong>, import akan dibatalkan sepenuhnya.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Select Excel File <span class="text-danger">*</span></label>
                        <input type="file" name="import_file" class="shad-input" accept=".xlsx,.xls" required>
                        <small style="color: var(--slate-500);">Max file size: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary">
                        <i class="fas fa-upload mr-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CREATE INVOICE MODAL -->
<div class="modal fade shad-modal" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Invoice</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo e(route('customers.finance.store')); ?>" method="POST" id="invoiceForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="shad-label">Invoice No. <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_no" class="shad-input" placeholder="INV-001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="shad-label">Customer <span class="text-danger">*</span></label>
                            <select name="customer_name" class="shad-select" required>
                                <option value="">Select customer...</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->name ?: $customer->customerName); ?>"><?php echo e($customer->name ?: $customer->customerName); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="shad-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" class="shad-input date-dmy" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="shad-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="shad-input date-dmy" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="shad-label">Invoice Amount (RM) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="shad-input" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="shad-label">Initial Payment (RM)</label>
                            <input type="number" step="0.01" name="received_amount" class="shad-input" value="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Description</label>
                        <textarea name="description" class="shad-input" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary">Create Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW / PAYMENT MODAL -->
<div class="modal fade shad-modal" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="shad-card h-100">
                            <div class="shad-card-body">
                                <h6 style="font-weight: 600; color: var(--slate-800); margin-bottom: 1rem;"><i class="fas fa-file-invoice"></i> Invoice Info</h6>
                                <div style="display: grid; gap: 0.5rem; font-size: 0.875rem;">
                                    <div class="d-flex"><span style="width: 120px; color: var(--slate-500);">Invoice No:</span> <strong id="viewInvoiceNo"></strong></div>
                                    <div class="d-flex"><span style="width: 120px; color: var(--slate-500);">Customer:</span> <span id="viewCustomer"></span></div>
                                    <div class="d-flex"><span style="width: 120px; color: var(--slate-500);">Invoice Date:</span> <span id="viewInvoiceDate"></span></div>
                                    <div class="d-flex"><span style="width: 120px; color: var(--slate-500);">Due Date:</span> <span id="viewDueDate"></span></div>
                                    <div class="d-flex"><span style="width: 120px; color: var(--slate-500);">Initial Rcvd:</span> <span id="viewReceivedAmount"></span></div>
                                    <div class="d-flex"><span style="width: 120px; color: var(--slate-500);">Description:</span> <span id="viewDescription"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="shad-card h-100">
                            <div class="shad-card-body">
                                <h6 style="font-weight: 600; color: var(--slate-800); margin-bottom: 1rem;"><i class="fas fa-calculator"></i> Payment Summary</h6>
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <p style="font-size: 0.75rem; color: var(--slate-500); margin-bottom: 0.25rem;">Invoice</p>
                                        <p style="font-size: 1.25rem; font-weight: 700; color: var(--slate-800); margin: 0;" id="viewInvoiceAmount">RM 0</p>
                                    </div>
                                    <div class="col-4">
                                        <p style="font-size: 0.75rem; color: var(--slate-500); margin-bottom: 0.25rem;">Received</p>
                                        <p style="font-size: 1.25rem; font-weight: 700; color: #22c55e; margin: 0;" id="viewTotalPaid">RM 0</p>
                                    </div>
                                    <div class="col-4">
                                        <p style="font-size: 0.75rem; color: var(--slate-500); margin-bottom: 0.25rem;">Balance</p>
                                        <p style="font-size: 1.25rem; font-weight: 700; color: #dc2626; margin: 0;" id="viewBalance">RM 0</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <span class="shad-badge" id="viewStatus" style="font-size: 0.875rem; padding: 0.5rem 1rem;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 style="font-weight: 600; color: var(--slate-800); margin: 0;"><i class="fas fa-history"></i> Payment History</h6>
                    <button type="button" class="shad-btn shad-btn-primary shad-btn-sm" id="addPaymentBtn">
                        <i class="fas fa-plus"></i> Add Payment
                    </button>
                </div>

                <div id="paymentFormSection" style="display: none;">
                    <div class="shad-card mb-3" style="border-left: 3px solid #22c55e;">
                        <div class="shad-card-body">
                            <form action="<?php echo e(route('customers.finance.payment.store')); ?>" method="POST" id="paymentForm">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="customer_finance_id" id="paymentFinanceId">
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label class="shad-label">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" id="paymentDate" class="shad-input date-dmy" required>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="shad-label">Amount (RM) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="amount" id="paymentAmount" class="shad-input" required>
                                        <small style="color: var(--slate-500);">Balance: <span id="remainingBalance"></span></small>
                                    </div>
                                    <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                        <button type="submit" class="shad-btn shad-btn-primary shad-btn-sm"><i class="fas fa-check"></i></button>
                                        <button type="button" class="shad-btn shad-btn-ghost shad-btn-sm" id="cancelPaymentBtn"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="shad-card">
                    <div class="shad-card-body p-0">
                        <table class="shad-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Balance After</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentHistoryBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade shad-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Invoice</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="width: 48px; height: 48px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.25rem;"></i>
                </div>
                <h6 style="font-weight: 600; color: var(--slate-800);">Delete invoice "<span id="deleteInvoiceNo"></span>"?</h6>
                <p style="font-size: 0.875rem; color: var(--slate-500);">This will also delete all payment records.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="shad-btn" style="background: #dc2626; color: #fff;">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    iziToast.success({ title: 'Success', message: <?php echo json_encode(session('success')); ?>, position: 'topRight', timeout: 3000 });
});
</script>
<?php endif; ?>

<?php if(session('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    iziToast.error({ title: 'Error', message: <?php echo json_encode(session('error')); ?>, position: 'topRight', timeout: 5000 });
});
</script>
<?php endif; ?>

<script>
function formatDMY(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;
    const day   = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year  = d.getFullYear();
    return `${day}-${month}-${year}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        input[type="date"].date-dmy::-webkit-datetime-edit-day-field   { order: 1; }
        input[type="date"].date-dmy::-webkit-datetime-edit-text:nth-of-type(1) { order: 2; }
        input[type="date"].date-dmy::-webkit-datetime-edit-month-field { order: 3; }
        input[type="date"].date-dmy::-webkit-datetime-edit-text:nth-of-type(2) { order: 4; }
        input[type="date"].date-dmy::-webkit-datetime-edit-year-field  { order: 5; }
        input[type="date"].date-dmy::-webkit-datetime-edit { display: flex; }
    `;
    document.head.appendChild(style);
});

document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = $('#paymentModal');
    const paymentFormSection = document.getElementById('paymentFormSection');
    const addPaymentBtn = document.getElementById('addPaymentBtn');
    const cancelPaymentBtn = document.getElementById('cancelPaymentBtn');

    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`/customers/finance/${id}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('viewInvoiceNo').textContent = data.invoice_no;
                    document.getElementById('viewCustomer').textContent = data.customer_name;
                    document.getElementById('viewInvoiceDate').textContent = formatDMY(data.invoice_date);
                    document.getElementById('viewDueDate').textContent = formatDMY(data.due_date);
                    document.getElementById('viewReceivedAmount').textContent = 'RM ' + parseFloat(data.received_amount || 0).toFixed(2);
                    document.getElementById('viewDescription').textContent = data.description || '-';
                    document.getElementById('viewInvoiceAmount').textContent = 'RM ' + parseFloat(data.amount).toFixed(2);

                    const initialReceived = parseFloat(data.received_amount || 0);
                    const additionalReceived = data.payments.reduce((sum, p) => sum + parseFloat(p.amount), 0);
                    const totalPaid = initialReceived + additionalReceived;
                    const balance = parseFloat(data.amount) - totalPaid;

                    document.getElementById('viewTotalPaid').textContent = 'RM ' + totalPaid.toFixed(2);
                    document.getElementById('viewBalance').textContent = 'RM ' + balance.toFixed(2);
                    document.getElementById('remainingBalance').textContent = 'RM ' + balance.toFixed(2);
                    document.getElementById('paymentFinanceId').value = id;

                    const statusEl = document.getElementById('viewStatus');
                    if (balance <= 0) { statusEl.textContent = 'Fully Paid'; statusEl.className = 'shad-badge shad-badge-success'; }
                    else if (totalPaid > 0) { statusEl.textContent = 'Partially Paid'; statusEl.className = 'shad-badge shad-badge-warning'; }
                    else { statusEl.textContent = 'Pending'; statusEl.className = 'shad-badge shad-badge-default'; }

                    const tbody = document.getElementById('paymentHistoryBody');
                    tbody.innerHTML = '';
                    let runningBalance = parseFloat(data.amount);
                    let rowCount = 0;

                    if (initialReceived > 0) {
                        rowCount++;
                        runningBalance -= initialReceived;
                        tbody.innerHTML += `
                            <tr>
                                <td>${rowCount}</td>
                                <td>${formatDMY(data.invoice_date)}</td>
                                <td class="text-right" style="color: #22c55e;">RM ${initialReceived.toFixed(2)}</td>
                                <td class="text-right" style="font-weight: 600;">RM ${runningBalance.toFixed(2)}</td>
                                <td class="text-right" style="color: var(--slate-400);">Initial</td>
                            </tr>
                        `;
                    }

                    if (data.payments.length > 0) {
                        data.payments.forEach((payment, index) => {
                            rowCount++;
                            runningBalance -= parseFloat(payment.amount);
                            tbody.innerHTML += `
                                <tr>
                                    <td>${rowCount}</td>
                                    <td>${formatDMY(payment.payment_date)}</td>
                                    <td class="text-right" style="color: #22c55e;">RM ${parseFloat(payment.amount).toFixed(2)}</td>
                                    <td class="text-right" style="font-weight: 600;">RM ${runningBalance.toFixed(2)}</td>
                                    <td class="text-right">
                                        <form action="/customers/finance/payment/${payment.id}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this payment?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" type="submit"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            `;
                        });
                    }

                    if (rowCount === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center" style="color: var(--slate-400); padding: 2rem;">No payments recorded</td></tr>';
                    }
                    paymentModal.modal('show');
                });
        });
    });

    addPaymentBtn.addEventListener('click', () => { paymentFormSection.style.display = 'block'; addPaymentBtn.style.display = 'none'; });
    cancelPaymentBtn.addEventListener('click', () => { paymentFormSection.style.display = 'none'; addPaymentBtn.style.display = 'inline-flex'; document.getElementById('paymentForm').reset(); });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('deleteInvoiceNo').textContent = this.dataset.invoice;
            document.getElementById('deleteForm').action = `/customers/finance/${this.dataset.id}`;
            $('#deleteModal').modal('show');
        });
    });

    $('.month-header').on('click', function() {
        const target = $(this).data('target');
        const iconId = target.replace('#month-', 'icon-');
        const icon = document.getElementById(iconId);
        if ($(target).hasClass('show')) {
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.style.transform = 'rotate(90deg)';
        }
    });

    $('.collapse.show').each(function() {
        const monthId = $(this).attr('id');
        const iconId = monthId.replace('month-', 'icon-');
        const icon = document.getElementById(iconId);
        if (icon) icon.style.transform = '';
    });
});
</script>

<!-- ============================================
     IMPORT SUCCESS MODAL
============================================ -->
<?php if(session('import_success_data') && count(session('import_success_data')) > 0): ?>
<?php $importedRows = session('import_success_data'); ?>
<div class="modal fade shad-modal" id="importSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 640px;">
        <div class="modal-content">
            <div class="modal-header" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-check-circle" style="color: #16a34a; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" style="margin: 0; color: #15803d;">Import Berjaya!</h5>
                        <small style="color: #16a34a;"><?php echo e(count($importedRows)); ?> invoice berjaya diimport ke sistem</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color: #15803d;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                    Berikut adalah senarai invoice yang telah berjaya diimport:
                </p>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.5rem 0.75rem; text-align: left; color: #6b7280; font-weight: 600;">#</th>
                                <th style="padding: 0.5rem 0.75rem; text-align: left; color: #6b7280; font-weight: 600;">Invoice No</th>
                                <th style="padding: 0.5rem 0.75rem; text-align: left; color: #6b7280; font-weight: 600;">Customer</th>
                                <th style="padding: 0.5rem 0.75rem; text-align: left; color: #6b7280; font-weight: 600;">Tarikh</th>
                                <th style="padding: 0.5rem 0.75rem; text-align: right; color: #6b7280; font-weight: 600;">Amaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $importedRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="<?php echo e($loop->odd ? 'background:#fff;' : 'background:#f9fafb;'); ?> border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.5rem 0.75rem; color: #9ca3af;"><?php echo e($i + 1); ?></td>
                                <td style="padding: 0.5rem 0.75rem; font-weight: 600; color: #111827;"><?php echo e($row['invoice_no']); ?></td>
                                <td style="padding: 0.5rem 0.75rem; color: #374151;"><?php echo e($row['customer_name']); ?></td>
                                <td style="padding: 0.5rem 0.75rem; color: #374151;"><?php echo e($row['invoice_date']); ?></td>
                                <td style="padding: 0.5rem 0.75rem; text-align: right; color: #111827; font-weight: 600;">RM <?php echo e($row['amount']); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background: #f0fdf4; border-top: 1px solid #bbf7d0;">
                <button type="button" class="shad-btn shad-btn-primary" data-dismiss="modal">
                    <i class="fas fa-check mr-1"></i> OK, Faham
                </button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#importSuccessModal').modal('show');
});
</script>
<?php endif; ?>

<!-- ============================================
     IMPORT ERROR MODAL
============================================ -->
<?php if(session('import_errors') && count(session('import_errors')) > 0): ?>
<div class="modal fade shad-modal" id="importErrorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 560px;">
        <div class="modal-content">
            <div class="modal-header" style="background: #fef2f2; border-bottom: 1px solid #fecaca;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-times-circle" style="color: #dc2626; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" style="margin: 0; color: #991b1b;">Import Gagal</h5>
                        <small style="color: #b91c1c;">Tiada data diimport &mdash; <?php echo e(count(session('import_errors'))); ?> error ditemui</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color: #991b1b;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                    Sila betulkan semua error berikut dalam fail Excel anda dan cuba import semula:
                </p>
                <div style="background: #fafafa; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                    <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.65rem 1rem; <?php echo e(!$loop->last ? 'border-bottom: 1px solid #f3f4f6;' : ''); ?> <?php echo e($loop->odd ? 'background: #fff;' : 'background: #fafafa;'); ?>">
                        <span style="flex-shrink: 0; width: 22px; height: 22px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; color: #dc2626;"><?php echo e($i + 1); ?></span>
                        <span style="font-size: 0.8125rem; color: #374151; line-height: 1.5;"><?php echo e($err); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="modal-footer" style="background: #fef2f2; border-top: 1px solid #fecaca;">
                <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="shad-btn shad-btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-redo mr-1"></i> Cuba Semula
                </button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#importErrorModal').modal('show');
});
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/customers/finance.blade.php ENDPATH**/ ?>