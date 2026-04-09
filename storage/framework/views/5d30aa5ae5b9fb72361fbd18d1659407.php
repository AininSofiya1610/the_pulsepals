

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Role Management</h1>
            <p class="shad-page-description">Manage user roles and permissions</p>
        </div>
        <a href="<?php echo e(route('settings.roles.create')); ?>" class="shad-btn shad-btn-primary">
            <i class="fas fa-plus"></i> Add Role
        </a>
    </div>

    

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Total Roles</p>
                        <p class="shad-stat-value"><?php echo e($roles->count()); ?></p>
                    </div>
                    <div class="shad-stat-icon primary">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Total Permissions</p>
                        <p class="shad-stat-value"><?php echo e($roles->sum(fn($r) => $r->permissions->count())); ?></p>
                    </div>
                    <div class="shad-stat-icon success">
                        <i class="fas fa-key"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">All Roles</h2>
                <p class="shad-card-description">Configure roles and their permissions</p>
            </div>
            <span class="shad-badge shad-badge-default"><?php echo e($roles->count()); ?> roles</span>
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Role Name</th>
                            <th class="text-center" style="width: 100px;">Users</th>
                            <th class="text-center" style="width: 120px;">Permissions</th>
                            <th class="text-right" style="width: 280px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="font-weight: 500; color: var(--slate-500);"><?php echo e($role->id); ?></td>
                            <td>
                                <span style="font-weight: 600; color: var(--slate-800);"><?php echo e($role->name); ?></span>
                                <?php if(in_array($role->name, ['Admin', 'super-admin'])): ?>
                                    <span class="shad-badge shad-badge-warning ml-2">System</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="shad-badge shad-badge-info"><?php echo e($role->users->count()); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="shad-badge shad-badge-success"><?php echo e($role->permissions->count()); ?></span>
                            </td>
                            <td>
                                <div class="shad-actions justify-content-end">
                                    <a href="<?php echo e(route('settings.roles.permissions', $role)); ?>" 
                                       class="shad-btn shad-btn-secondary shad-btn-sm" title="Manage Permissions">
                                        <i class="fas fa-key"></i> Permissions
                                    </a>
                                    <a href="<?php echo e(route('settings.roles.edit', $role)); ?>" 
                                       class="shad-btn shad-btn-ghost shad-btn-sm" title="Edit Role">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <?php if(!in_array($role->name, ['Admin', 'super-admin']) && $role->users->count() == 0): ?>
                                    <form action="<?php echo e(route('settings.roles.destroy', $role)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this role?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" title="Delete Role">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <button type="button" class="shad-btn shad-btn-ghost shad-btn-sm" disabled style="opacity: 0.3;" title="Cannot delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5">
                                <div class="shad-empty">
                                    <div class="shad-empty-icon">
                                        <i class="fas fa-user-shield fa-3x"></i>
                                    </div>
                                    <h3 class="shad-empty-title">No roles found</h3>
                                    <p class="shad-empty-description">Create your first role to get started.</p>
                                    <a href="<?php echo e(route('settings.roles.create')); ?>" class="shad-btn shad-btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Add Role
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/settings/roles/index.blade.php ENDPATH**/ ?>