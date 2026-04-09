

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Customer Management</h1>
            <p class="shad-page-description">Manage your customer database</p>
        </div>
        <div class="d-flex gap-2">
            
            <a href="<?php echo e(route('customers.template')); ?>" class="shad-btn shad-btn-ghost">
                <i class="fas fa-file-download mr-2"></i>
                Template
            </a>
            
            <button type="button" class="shad-btn shad-btn-outline" data-toggle="modal" data-target="#importModal">
                <i class="fas fa-file-upload mr-2"></i>
                Import Excel
            </button>
            
            <form action="<?php echo e(route('export.finance-customers')); ?>" method="POST" style="display: inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="shad-btn shad-btn-outline">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export to Excel
                </button>
            </form>
            
            <button type="button" class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#customerModal">
                <i class="fas fa-plus"></i> Add Customer
            </button>
        </div>
    </div>

    <!-- Customer List -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">Customer List</h2>
                <p class="shad-card-description"><?php echo e($customers->total()); ?> total customers</p>
            </div>
            <input type="text" class="shad-input" id="searchCustomer" placeholder="Search customers..." style="width: 250px;">
        </div>
        <div class="shad-card-body p-0">
            <?php if($customers->count() > 0): ?>
                <div class="table-responsive">
                    <table class="shad-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>Customer Name</th>
                                <th class="text-center" style="width: 150px;">Created</th>
                                <th class="text-right" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $displayName = $customer->customerName ?: $customer->name ?: 'N/A'; ?>
                            <tr class="customer-row" data-customer-id="<?php echo e($customer->id); ?>" data-customer-name="<?php echo e(strtolower($displayName)); ?>">
                                <td class="text-center" style="color: var(--slate-500);"><?php echo e($customers->firstItem() + $loop->index); ?></td>
                                <td style="font-weight: 600; color: var(--slate-800);"><?php echo e($displayName); ?></td>
                                <td class="text-center" style="color: var(--slate-500); font-size: 0.8125rem;">
                                    <?php echo e($customer->created_at->format('M d, Y')); ?>

                                </td>
                                <td>
                                    <div class="shad-actions justify-content-end">
                                        <button class="shad-btn shad-btn-ghost shad-btn-sm delete-btn" 
                                                data-id="<?php echo e($customer->id); ?>" 
                                                data-name="<?php echo e($displayName); ?>"
                                                style="color: #dc2626;"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="shad-empty">
                    <div class="shad-empty-icon">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <h3 class="shad-empty-title">No customers found</h3>
                    <p class="shad-empty-description">Add your first customer to get started.</p>
                    <button type="button" class="shad-btn shad-btn-primary mt-3" data-toggle="modal" data-target="#customerModal">
                        <i class="fas fa-plus"></i> Add Customer
                    </button>
                </div>
            <?php endif; ?>
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

<!-- IMPORT MODAL -->
<div class="modal fade shad-modal" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-upload mr-2"></i>Import from Excel</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo e(route('customers.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="shad-alert shad-alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            Please use the <strong>Template</strong> file to fill in your data before importing.
                            Only <strong>.xlsx</strong> files are accepted.
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

<!-- Add/Edit Customer Modal -->
<div class="modal fade shad-modal" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo e(route('customers.store')); ?>" method="POST" id="customerForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="customer_id" id="customerId" value="">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="shad-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customerName" id="customerName" class="shad-input" placeholder="Enter customer name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary" id="saveBtn">
                        <span id="btnText">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade shad-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Customer</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="width: 48px; height: 48px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.25rem;"></i>
                </div>
                <h6 style="font-weight: 600; color: var(--slate-800);">Delete "<span id="deleteCustomerName"></span>"?</h6>
                <p style="font-size: 0.875rem; color: var(--slate-500);">This action cannot be undone.</p>
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


<?php if(session('import_success_data') && count(session('import_success_data')) > 0): ?>
<?php $importedRows = session('import_success_data'); ?>
<div class="modal fade shad-modal" id="importSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 480px;">
        <div class="modal-content">
            <div class="modal-header" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-check-circle" style="color: #16a34a; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" style="margin: 0; color: #15803d;">Import Berjaya!</h5>
                        <small style="color: #16a34a;"><?php echo e(count($importedRows)); ?> customer berjaya diimport ke sistem</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color: #15803d;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 0.5rem 0.75rem; text-align: left; color: #6b7280; font-weight: 600;">#</th>
                                <th style="padding: 0.5rem 0.75rem; text-align: left; color: #6b7280; font-weight: 600;">Customer Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $importedRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="<?php echo e($loop->odd ? 'background:#fff;' : 'background:#f9fafb;'); ?> border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.5rem 0.75rem; color: #9ca3af;"><?php echo e($i + 1); ?></td>
                                <td style="padding: 0.5rem 0.75rem; font-weight: 600; color: #111827;"><?php echo e($row['customerName']); ?></td>
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
        iziToast.error({ title: 'Error', message: <?php echo json_encode(session('error')); ?>, position: 'topRight', timeout: 3000 });
    });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('customerForm');
    const modal = $('#customerModal');

    // Search
    const searchInput = document.getElementById('searchCustomer');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.customer-row').forEach(row => {
                row.style.display = row.dataset.customerName.includes(term) ? '' : 'none';
            });
        });
    }

    // Reset modal
    modal.on('show.bs.modal', function(e) {
        document.getElementById('customerModalLabel').textContent = 'Add New Customer';
        document.getElementById('btnText').textContent = 'Save';
        document.getElementById('customerId').value = '';
        form.action = '<?php echo e(route("customers.store")); ?>';
        document.getElementById('formMethod').value = 'POST';
        form.reset();
    });

    // Delete button
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('deleteCustomerName').textContent = this.dataset.name;
            document.getElementById('deleteForm').action = '<?php echo e(route("customers.destroy", ":id")); ?>'.replace(':id', this.dataset.id);
            $('#deleteModal').modal('show');
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/customers/create.blade.php ENDPATH**/ ?>