

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Company Finance</h1>
            <p class="shad-page-description">Track bank balances and net pay records</p>
        </div>
        <form action="<?php echo e(route('export.company-finance')); ?>" method="POST" style="display: inline;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="start_date" value="<?php echo e($startDate ?? ''); ?>">
            <input type="hidden" name="end_date" value="<?php echo e($endDate ?? ''); ?>">
            <button type="submit" class="shad-btn shad-btn-outline">
                <i class="fas fa-file-excel mr-2"></i>
                Export to Excel
            </button>
        </form>
    </div>

    <?php if(session('success')): ?>
        <div class="shad-alert shad-alert-success mb-4">
            <i class="fas fa-check-circle"></i>
            <div><?php echo e(session('success')); ?></div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="shad-alert shad-alert-danger mb-4">
            <i class="fas fa-exclamation-circle"></i>
            <div><?php echo e(session('error')); ?></div>
        </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="shad-card mb-4">
        <div class="shad-card-body py-3">
            <form action="<?php echo e(route('finance.company.index')); ?>" method="GET" class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="shad-label mb-0">From:</label>
                        <input type="date" name="start_date" class="shad-input" style="width: auto;" value="<?php echo e($startDate); ?>">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="shad-label mb-0">To:</label>
                        <input type="date" name="end_date" class="shad-input" style="width: auto;" value="<?php echo e($endDate); ?>">
                    </div>
                    <button type="submit" class="shad-btn shad-btn-primary shad-btn-sm">Filter</button>
                    <a href="<?php echo e(route('finance.company.index')); ?>" class="shad-btn shad-btn-ghost shad-btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Input Form -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="shad-card h-100">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">Update Records</h2>
                    <p class="shad-card-description">Enter bank balances and net pay</p>
                </div>
                <div class="shad-card-body">
                    <form action="<?php echo e(route('finance.company.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="shad-label">Date</label>
                            <input type="date" name="record_date" class="shad-input" value="<?php echo e(old('record_date', date('Y-m-d'))); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">MBB Bank Balance (RM)</label>
                            <input type="number" step="0.01" name="mbb_balance" class="shad-input" value="<?php echo e(old('mbb_balance', $latestRecord->mbb_balance ?? 0)); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">RHB Bank Balance (RM)</label>
                            <input type="number" step="0.01" name="rhb_balance" class="shad-input" value="<?php echo e(old('rhb_balance', $latestRecord->rhb_balance ?? 0)); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Staff Net Pay (RM)</label>
                            <input type="number" step="0.01" name="net_pay" class="shad-input" value="<?php echo e(old('net_pay', $latestRecord->net_pay ?? 0)); ?>" required>
                        </div>
                        <button type="submit" class="shad-btn shad-btn-primary" style="width: 100%;">
                            <i class="fas fa-save"></i> Save Records
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">Finance History</h2>
                    <p class="shad-card-description"><?php echo e($history->total()); ?> records</p>
                </div>
                <div class="shad-card-body p-0">
                    <div class="table-responsive">
                        <table class="shad-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-right">MBB (RM)</th>
                                    <th class="text-right">RHB (RM)</th>
                                    <th class="text-right">Net Pay</th>
                                    <th class="text-right" style="border-left: 2px solid var(--border-color);">Total Cash</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td style="font-weight: 500;"><?php echo e($record->record_date->format('d M Y')); ?></td>
                                    <td class="text-right"><?php echo e(number_format($record->mbb_balance, 2)); ?></td>
                                    <td class="text-right"><?php echo e(number_format($record->rhb_balance, 2)); ?></td>
                                    <td class="text-right" style="color: #dc2626;"><?php echo e(number_format($record->net_pay, 2)); ?></td>
                                    <td class="text-right" style="font-weight: 600; color: #22c55e; border-left: 2px solid var(--border-color);">
                                        <?php echo e(number_format($record->mbb_balance + $record->rhb_balance, 2)); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="shad-empty">
                                            <div class="shad-empty-icon">
                                                <i class="fas fa-chart-line fa-3x"></i>
                                            </div>
                                            <h3 class="shad-empty-title">No records found</h3>
                                            <p class="shad-empty-description">Start by adding your first finance record.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($history->hasPages()): ?>
                <div class="shad-card-footer d-flex justify-content-between align-items-center">
                    <small style="color: var(--slate-500);">Showing <?php echo e($history->firstItem()); ?> to <?php echo e($history->lastItem()); ?> of <?php echo e($history->total()); ?></small>
                    <div class="shad-pagination">
                        <?php echo e($history->appends(['timeline' => $timeline, 'start_date' => $startDate, 'end_date' => $endDate])->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/finance/company/index.blade.php ENDPATH**/ ?>