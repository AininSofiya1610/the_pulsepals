

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Manage Permissions</h1>
            <p class="shad-page-description">Role: <strong><?php echo e($role->name); ?></strong></p>
        </div>
        <a href="<?php echo e(route('settings.roles')); ?>" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <!-- Info Alert -->
    <div class="shad-alert shad-alert-info mb-4">
        <i class="fas fa-info-circle"></i>
        <div>Select the permissions you want to assign. Users with this role will have access to all checked permissions.</div>
    </div>

    <!-- Permissions Form -->
    <form action="<?php echo e(route('settings.roles.permissions.update', $role)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="shad-card mb-4">
            <div class="shad-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="shad-card-title">Available Permissions</h2>
                    <p class="shad-card-description">Group by module</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="shad-btn shad-btn-secondary shad-btn-sm" onclick="selectAll()">
                        <i class="fas fa-check-square"></i> Select All
                    </button>
                    <button type="button" class="shad-btn shad-btn-ghost shad-btn-sm" onclick="deselectAll()">
                        <i class="fas fa-square"></i> Deselect All
                    </button>
                </div>
            </div>
            <div class="shad-card-body">
                <?php if($permissions->isEmpty()): ?>
                <div class="shad-empty">
                    <div class="shad-empty-icon">
                        <i class="fas fa-key fa-3x"></i>
                    </div>
                    <h3 class="shad-empty-title">No permissions available</h3>
                    <p class="shad-empty-description">Please create permissions first.</p>
                </div>
                <?php else: ?>
                <?php
                    $permissionLabels = [
                        // Access
                        'access-settings'       => 'Access Settings Page',

                        // Manage
                        'manage-roles'          => 'Manage Roles',
                        'manage roles'          => 'Manage Roles',
                        'manage-ticket-options' => 'Manage Ticket Options (Dropdowns)',
                        'manage-vendors'        => 'Manage Vendors',
                        'manage-customers'      => 'Manage Customers',
                        'manage-leads'          => 'Manage Leads',
                        'manage-deals'          => 'Manage Deals',
                        'manage-tasks'          => 'Manage Tasks',
                        'manage-users'          => 'Manage Users',
                        'manage permissions'    => 'Manage Permissions',

                        // View
                        'view-dashboard'        => 'View Dashboard',
                        'view dashboard'        => 'View Dashboard',
                        'view-finance'          => 'View Finance',
                        'view-crm'              => 'View CRM',
                        'view-all-tickets'      => 'View All Tickets',
                        'view-own-tickets'      => 'View Own Tickets Only',
                        'view-vendors'          => 'View Vendors',
                        'view-customers'        => 'View Customers',
                        'view-leads'            => 'View Leads',
                        'view-deals'            => 'View Deals',
                        'view-tasks'            => 'View Tasks',
                        'view-users'            => 'View Users',
                        'view users'            => 'View Users',
                        'view-roles'            => 'View Roles',
                        'view technicians'      => 'View Technicians',
                        'view units'            => 'View Units',

                        // Create
                        'create-ticket'         => 'Create New Ticket',

                        // Assign
                        'assign-ticket'         => 'Assign Ticket to Staff',
                        'assign ticket'         => 'Assign Ticket to Staff',

                        // Update
                        'update-ticket-status'  => 'Update/Edit Ticket',

                        // Delete
                        'delete-ticket'         => 'Delete Ticket',
                    ];

                    $groupOverrides = [
                        'view users'         => 'view',
                        'view dashboard'     => 'view',
                        'view technicians'   => 'view',
                        'view units'         => 'view',
                        'manage roles'       => 'manage',
                        'manage permissions' => 'manage',
                        'assign ticket'      => 'assign',
                    ];

                    // Flatten already-grouped $permissions then regroup correctly
                    $regrouped = [];
                    foreach ($permissions as $groupItems) {
                        foreach ($groupItems as $permission) {
                            $group = $groupOverrides[$permission->name]
                                ?? explode('-', $permission->name)[0];
                            $regrouped[$group][] = $permission;
                        }
                    }

                    // Sort groups in logical order
                    $groupOrder = ['access', 'view', 'create', 'manage', 'assign', 'update', 'delete', 'other'];
                    $sortedRegrouped = [];
                    foreach ($groupOrder as $key) {
                        if (isset($regrouped[$key])) {
                            $sortedRegrouped[$key] = $regrouped[$key];
                        }
                    }
                    // Add any remaining groups not in order list
                    foreach ($regrouped as $key => $value) {
                        if (!isset($sortedRegrouped[$key])) {
                            $sortedRegrouped[$key] = $value;
                        }
                    }

                    $groupLabels = [
                        'access'  => '🔐 Access',
                        'view'    => '👁️ View',
                        'create'  => '➕ Create',
                        'manage'  => '⚙️ Manage',
                        'assign'  => '📋 Assign',
                        'update'  => '✏️ Update',
                        'delete'  => '🗑️ Delete',
                        'other'   => '📌 Other',
                    ];
                ?>
                <div class="row">
                    <?php $__currentLoopData = $sortedRegrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="shad-card h-100" style="border-left: 3px solid var(--info);">
                            <div class="shad-card-header" style="padding: 0.75rem 1rem; background: var(--slate-50);">
                                <h6 style="font-weight: 600; color: var(--slate-700); margin: 0;">
                                    <?php echo e($groupLabels[$group] ?? ucfirst($group)); ?>

                                </h6>
                            </div>
                            <div class="shad-card-body" style="padding: 1rem;">
                                <?php $__currentLoopData = $groupPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex align-items-center mb-2">
                                    <input type="checkbox" 
                                           class="permission-checkbox" 
                                           id="permission-<?php echo e($permission->id); ?>" 
                                           name="permissions[]" 
                                           value="<?php echo e($permission->id); ?>"
                                           style="width: 1rem; height: 1rem; accent-color: var(--primary);"
                                           <?php echo e(in_array($permission->id, $rolePermissions) ? 'checked' : ''); ?>>
                                    <label for="permission-<?php echo e($permission->id); ?>" style="margin-left: 0.5rem; font-size: 0.875rem; color: var(--slate-600); cursor: pointer; margin-bottom: 0;">
                                        <?php echo e($permissionLabels[$permission->name] ?? ucwords(str_replace('-', ' ', $permission->name))); ?>

                                    </label>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="shad-card">
            <div class="shad-card-body d-flex justify-content-between align-items-center">
                <div>
                    <span class="shad-badge shad-badge-info" id="selected-count"><?php echo e(count($rolePermissions)); ?></span>
                    <span style="color: var(--slate-500); font-size: 0.875rem; margin-left: 0.5rem;">permission(s) selected</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('settings.roles')); ?>" class="shad-btn shad-btn-secondary">Cancel</a>
                    <button type="submit" class="shad-btn shad-btn-primary">
                        <i class="fas fa-save"></i> Save Permissions
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function selectAll() {
    $('.permission-checkbox').prop('checked', true);
    updateCount();
}

function deselectAll() {
    $('.permission-checkbox').prop('checked', false);
    updateCount();
}

function updateCount() {
    const count = $('.permission-checkbox:checked').length;
    $('#selected-count').text(count);
}

$(document).ready(function() {
    $('.permission-checkbox').on('change', function() {
        updateCount();
    });
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/settings/roles/permissions.blade.php ENDPATH**/ ?>