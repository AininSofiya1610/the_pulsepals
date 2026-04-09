

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Technicians</h1>
            <p class="shad-page-description">All staff with technical roles</p>
        </div>
    </div>

    <!-- Technicians Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">Technical Staff</h2>
                <p class="shad-card-description">System Unit · Network & Infrastructure · Technical Support</p>
            </div>
            <span class="shad-badge shad-badge-default"><?php echo e($technicians->count()); ?> staff</span>
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="font-weight: 500; color: var(--slate-500);"><?php echo e($tech->id); ?></td>
                            <td style="font-weight: 600; color: var(--slate-800);"><?php echo e($tech->name); ?></td>
                            <td style="color: var(--slate-500);"><?php echo e($tech->email); ?></td>
                            <td>
                                <?php $__currentLoopData = $tech->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="shad-badge shad-badge-info"><?php echo e($role->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td style="color: var(--slate-500);">
                                <?php echo e($tech->unit->name ?? '—'); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5">
                                <div class="shad-empty">
                                    <div class="shad-empty-icon">
                                        <i class="fas fa-user-cog fa-3x"></i>
                                    </div>
                                    <h3 class="shad-empty-title">No technicians found</h3>
                                    <p class="shad-empty-description">Assign a technical role to a user first.</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/users/technicians.blade.php ENDPATH**/ ?>