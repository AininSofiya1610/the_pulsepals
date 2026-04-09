

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Revenue Budget Targets</h1>
            <p class="shad-page-description">Set monthly budget targets for revenue performance tracking</p>
        </div>
        <form action="<?php echo e(route('settings.revenue-budgets.export')); ?>" method="POST" class="m-0">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="year" value="<?php echo e($selectedYear); ?>">
            <button type="submit" class="shad-btn shad-btn-primary" style="background: #18181B; border-color: #18181B;">
                <i class="fas fa-file-excel mr-2"></i>
                Export to Excel
            </button>
        </form>
    </div>

    <!-- Year Selector Card -->
    <div class="shad-card mb-4">
        <div class="shad-card-header">
            <h2 class="shad-card-title">
                <i class="fas fa-calendar-alt mr-2" style="color: var(--slate-400);"></i>
                Select Year
            </h2>
        </div>
        <div class="shad-card-body">
            <form action="<?php echo e(route('settings.revenue-budgets.index')); ?>" method="GET" class="d-flex align-items-center gap-3">
                <select name="year" class="shad-input" style="width: auto;" onchange="this.form.submit()">
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($year); ?>" <?php echo e($selectedYear == $year ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="button" class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#addYearModal">
                    <i class="fas fa-plus"></i> Add Year
                </button>
                <span class="text-muted">← Select a year or add a new one</span>
            </form>
        </div>
    </div>

    <!-- Monthly Budget Form -->
    <div class="shad-card">
        <div class="shad-card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
            <h2 class="shad-card-title" style="color: #fff;">
                <i class="fas fa-coins mr-2"></i>
                Monthly Budget Targets for <?php echo e($selectedYear); ?>

            </h2>
            <p style="color: rgba(255,255,255,0.8); font-size: 0.8125rem; margin: 0;">
                Enter the budget target (RM) for each month
            </p>
        </div>
        <div class="shad-card-body">
            <form action="<?php echo e(route('settings.revenue-budgets.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="year" value="<?php echo e($selectedYear); ?>">
                
                <div class="row">
                    <?php $__currentLoopData = $monthlyBudgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="shad-stat-card" style="border-left: 4px solid #3b82f6;">
                            <label class="shad-label" style="font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; display: block;">
                                <?php echo e($data['name']); ?>

                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #f1f5f9; border-color: #e2e8f0; font-weight: 500;">RM</span>
                                <input 
                                    type="number" 
                                    name="budgets[<?php echo e($month); ?>]" 
                                    value="<?php echo e(number_format($data['amount'], 0, '', '')); ?>"
                                    class="shad-input"
                                    style="border-left: none;"
                                    placeholder="0"
                                    min="0"
                                    step="1"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Summary Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="shad-stat-card" style="background: #f8fafc; border: 2px dashed #cbd5e1;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="shad-stat-label" style="color: #64748b;">Total Annual Budget</p>
                                    <p class="shad-stat-value" id="totalBudget" style="color: #3b82f6;">
                                        RM <?php echo e(number_format(array_sum(array_column($monthlyBudgets, 'amount')), 0)); ?>

                                    </p>
                                </div>
                                <button type="submit" class="shad-btn shad-btn-primary">
                                    <i class="fas fa-save mr-2"></i>
                                    Save Budget Targets
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>

    <!-- Add Year Modal -->
    <div class="modal fade shad-modal" id="addYearModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Budget Year</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="<?php echo e(route('settings.revenue-budgets.year.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="shad-label">Year <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                name="year" 
                                class="shad-input <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                placeholder="e.g., 2030" 
                                min="2020" 
                                max="2100" 
                                value="<?php echo e(old('year')); ?>"
                                required>
                            <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="alert" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 0.75rem;">
                            <p style="font-size: 0.875rem; color: #1e40af; margin: 0;">
                                <i class="fas fa-info-circle"></i> 
                                <strong>What happens when you add a year:</strong>
                            </p>
                            <ul style="font-size: 0.8125rem; color: #1e40af; margin: 0.5rem 0 0 1.5rem; padding: 0;">
                                <li>Year will be added to the system</li>
                                <li>12 monthly budget records will be created automatically</li>
                                <li>All amounts will be initialized to RM 0</li>
                                <li>You can then set individual monthly targets</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="shad-btn shad-btn-primary">
                            <i class="fas fa-check"></i> Add Year
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Live update total when input changes
    document.querySelectorAll('input[name^="budgets"]').forEach(input => {
        input.addEventListener('input', updateTotal);
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('input[name^="budgets"]').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('totalBudget').textContent = 'RM ' + total.toLocaleString('en-MY');
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/settings/revenue-budgets/index.blade.php ENDPATH**/ ?>