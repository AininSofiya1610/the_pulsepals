

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Customers</h1>
            <p class="shad-page-description">Manage your CRM customer relationships</p>
        </div>
        <form action="<?php echo e(route('export.crm-customers')); ?>" method="POST" style="display: inline;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="shad-btn shad-btn-outline">
                <i class="fas fa-file-excel mr-2"></i>
                Export to Excel
            </button>
        </form>
    </div>

    <?php if($message = Session::get('success')): ?>
        <div class="shad-alert shad-alert-success mb-4">
            <i class="fas fa-check-circle"></i>
            <div><?php echo e($message); ?></div>
        </div>
    <?php endif; ?>

    <!-- Customers Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">All Customers</h2>
                <p class="shad-card-description"><?php echo e($customers->total()); ?> total customers</p>
            </div>
            <input type="text" class="shad-input" id="searchCustomer" placeholder="Search customers..." style="width: 250px;">
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th class="text-center">Deals</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $displayName = $customer->name ?: $customer->customerName ?: 'N/A';
                            ?>
                            <tr class="customer-row" data-search="<?php echo e(strtolower($displayName . ' ' . ($customer->email ?? '') . ' ' . ($customer->company ?? ''))); ?>">
                                <td style="font-weight: 600; color: var(--slate-800);"><?php echo e($displayName); ?></td>
                                <td><?php echo e($customer->email ?: $customer->customerEmail ?: '-'); ?></td>
                                <td><?php echo e($customer->phone ?: $customer->customerPhone ?: '-'); ?></td>
                                <td><?php echo e($customer->company ?: '-'); ?></td>
                                <td>
                                    <?php if($customer->status == 'active'): ?>
                                        <span class="shad-badge shad-badge-success">Active</span>
                                    <?php elseif($customer->status == 'inactive'): ?>
                                        <span class="shad-badge shad-badge-default">Inactive</span>
                                    <?php elseif($customer->status == 'pending'): ?>
                                        <span class="shad-badge shad-badge-warning">Pending</span>
                                    <?php else: ?>
                                        <span class="shad-badge shad-badge-default"><?php echo e($customer->status ?: 'N/A'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="shad-badge shad-badge-info"><?php echo e($customer->deals->count()); ?></span>
                                </td>
                                <td>
                                    <div class="shad-actions justify-content-end">
                                        <a href="<?php echo e(route('crm.customers.show', $customer->id)); ?>" class="shad-btn shad-btn-ghost shad-btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="shad-empty">
                                        <div class="shad-empty-icon">
                                            <i class="fas fa-users fa-3x"></i>
                                        </div>
                                        <h3 class="shad-empty-title">No customers found</h3>
                                        <p class="shad-empty-description">Customers will appear here once leads are converted.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($customers->hasPages()): ?>
        <div class="shad-card-footer d-flex justify-content-between align-items-center">
            <small style="color: var(--slate-500);">Showing <?php echo e($customers->firstItem()); ?> to <?php echo e($customers->lastItem()); ?> of <?php echo e($customers->total()); ?></small>
            <div class="shad-pagination">
                <?php echo e($customers->links('pagination::bootstrap-4')); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchCustomer');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.customer-row').forEach(row => {
                const searchData = row.dataset.search || '';
                row.style.display = searchData.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/crm/customers/index.blade.php ENDPATH**/ ?>