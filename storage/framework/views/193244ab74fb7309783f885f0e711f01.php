

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background: var(--slate-50, #f8fafc); min-height: 100vh;">

    <!-- Page Header -->
    <div class="shad-page-header mb-6">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo e(route('leads.index')); ?>" class="shad-btn shad-btn-ghost shad-btn-sm" style="padding: 0.5rem;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="shad-page-title">Add New Lead</h1>
                <p class="shad-page-description">Create a new lead entry for your sales pipeline</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Form Card -->
        <div class="col-lg-8">
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-user-plus mr-2" style="color: var(--slate-400);"></i>
                        Lead Information
                    </h2>
                    <p class="shad-card-description">Enter the details for the new lead</p>
                </div>
                <div class="shad-card-body">
                    <form action="<?php echo e(route('leads.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <!-- Name Field -->
                        <div class="mb-4">
                            <label for="name" class="shad-label">
                                Name <span style="color: #ef4444;">*</span>
                            </label>
                            <input
                                type="text"
                                class="shad-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="name"
                                name="name"
                                value="<?php echo e(old('name')); ?>"
                                placeholder="Enter lead's full name"
                                required
                            >
                            <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                The full name of the potential customer
                            </p>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Contact Information -->
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 1rem;">
                            <i class="fas fa-address-book mr-2" style="color: var(--slate-400);"></i>
                            Contact Information
                        </h3>

                        <div class="row g-4">
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="shad-label">Email</label>
                                <div style="position: relative;">
                                    <i class="fas fa-envelope" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--slate-400); font-size: 0.875rem;"></i>
                                    <input
                                        type="email"
                                        class="shad-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="email"
                                        name="email"
                                        value="<?php echo e(old('email')); ?>"
                                        placeholder="email@example.com"
                                        style="padding-left: 2.5rem;"
                                    >
                                </div>
                                <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                    Primary email for communication
                                </p>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="shad-label">Phone</label>
                                <div style="position: relative;">
                                    <i class="fas fa-phone" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--slate-400); font-size: 0.875rem;"></i>
                                    <input
                                        type="text"
                                        class="shad-input <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="phone"
                                        name="phone"
                                        value="<?php echo e(old('phone')); ?>"
                                        placeholder="+60 12-345 6789"
                                        style="padding-left: 2.5rem;"
                                    >
                                </div>
                                <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                    Contact phone number
                                </p>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Lead Source — DYNAMIC from DB -->
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 1rem;">
                            <i class="fas fa-bullseye mr-2" style="color: var(--slate-400);"></i>
                            Lead Source
                        </h3>

                        <div class="mb-4">
                            <label for="source" class="shad-label">Source</label>
                            <select
                                class="shad-select <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="source"
                                name="source"
                            >
                                <option value="">Select how the lead found you...</option>
                                
                                <?php $__empty_1 = true; $__currentLoopData = $leadSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <option value="<?php echo e($source->name); ?>" <?php echo e(old('source') == $source->name ? 'selected' : ''); ?>>
                                        <?php echo e($source->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <option value="" disabled>No lead sources configured — add them in Settings</option>
                                <?php endif; ?>
                            </select>
                            <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                Understanding lead sources helps optimize your marketing.
                                <a href="<?php echo e(route('settings.dropdown')); ?>" style="color: #3b82f6;" target="_blank">
                                    Manage sources →
                                </a>
                            </p>
                            <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Assignment -->
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 1rem;">
                            <i class="fas fa-user-check mr-2" style="color: var(--slate-400);"></i>
                            Assignment
                        </h3>

                        <div class="mb-4">
                            <label for="assigned_to" class="shad-label">Assigned To</label>
                            <select
                                class="shad-select <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="assigned_to"
                                name="assigned_to"
                            >
                                <option value="">Select team member...</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(old('assigned_to') == $user->id ? 'selected' : ''); ?>>
                                        👤 <?php echo e($user->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                Assign this lead to a sales team member for follow-up
                            </p>
                            <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Form Actions -->
                        <hr class="my-4" style="border-color: var(--border-color);">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="<?php echo e(route('leads.index')); ?>" class="shad-btn shad-btn-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="shad-btn shad-btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                Create Lead
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-3 { gap: 0.75rem; }
.gap-4 { gap: 1rem; }
@media (max-width: 991px) {
    .col-lg-4 { margin-top: 1.5rem; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/leads/create.blade.php ENDPATH**/ ?>