

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">User Management</h1>
            <p class="shad-page-description">Manage users and assign roles</p>
        </div>
        <a href="<?php echo e(route('settings.roles')); ?>" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <!-- Users Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">All Users</h2>
                <p class="shad-card-description">Assign roles to control user permissions</p>
            </div>
            <span class="shad-badge shad-badge-default"><?php echo e($users->count()); ?> users</span>
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th>Assign Role</th>
                            <th class="text-right" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="font-weight: 500; color: var(--slate-500);"><?php echo e($user->id); ?></td>
                            <td>
                                <span style="font-weight: 600; color: var(--slate-800);"><?php echo e($user->name); ?></span>
                                <?php if($user->id === auth()->id()): ?>
                                    <span class="shad-badge shad-badge-info ml-2">You</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--slate-500);"><?php echo e($user->email); ?></td>
                            <td>
                                <?php if($user->roles->count() > 0): ?>
                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="shad-badge shad-badge-success"><?php echo e($role->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="shad-badge shad-badge-warning">No Role</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?php echo e(route('settings.users.updateRole', $user)); ?>" method="POST" class="d-flex gap-2">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <select name="role" class="shad-select" style="width: 160px;">
                                        
                                        <option value="no_role" <?php echo e($user->roles->count() === 0 ? 'selected' : ''); ?>>
                                            No Role
                                        </option>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($role->name); ?>" <?php echo e($user->hasRole($role->name) ? 'selected' : ''); ?>>
                                                <?php echo e($role->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <button type="submit" class="shad-btn shad-btn-primary shad-btn-sm">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="shad-actions justify-content-end">
                                    <?php if($user->id !== auth()->id()): ?>
                                    <form action="<?php echo e(route('settings.users.destroy', $user)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <button type="button" class="shad-btn shad-btn-ghost shad-btn-sm" disabled style="opacity: 0.3;" title="Cannot delete yourself">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6">
                                <div class="shad-empty">
                                    <div class="shad-empty-icon">
                                        <i class="fas fa-users fa-3x"></i>
                                    </div>
                                    <h3 class="shad-empty-title">No users found</h3>
                                    <p class="shad-empty-description">Users will appear here after registration.</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/settings/users/index.blade.php ENDPATH**/ ?>