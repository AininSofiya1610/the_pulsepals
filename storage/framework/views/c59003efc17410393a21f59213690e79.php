<!-- Edit Ticket Body (AJAX-loaded into edit modal) -->
<form id="editTicketForm" action="<?php echo e(route('tickets.update', $ticket['id'])); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <!-- Title -->
    <div class="mb-3">
        <label class="shad-label" for="edit_title">Title <span class="text-danger">*</span></label>
        <input type="text" class="shad-input" id="edit_title" name="title" value="<?php echo e($ticket['title']); ?>" required>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_unit">Operational Unit <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_unit" name="unit" required>
                <option value="">Select unit...</option>
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($unit->name); ?>" <?php echo e($ticket['unit'] == $unit->name ? 'selected' : ''); ?>><?php echo e($unit->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_ticket_type">Ticket Type <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_ticket_type" name="ticket_type" required>
                <option value="">Select type...</option>
                <?php $__currentLoopData = $ticketTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->value); ?>" <?php echo e(($ticket['ticket_type'] ?? '') == $type->value ? 'selected' : ''); ?>><?php echo e($type->value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_priority">Priority <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_priority" name="priority" required>
                <option value="">Select priority...</option>
                <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($priority->value); ?>" <?php echo e($ticket['priority'] == $priority->value ? 'selected' : ''); ?>><?php echo e($priority->value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_category">Help Topic <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_category" name="category" required>
                <option value="">Select topic...</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->value); ?>" <?php echo e($ticket['category'] == $category->value ? 'selected' : ''); ?>><?php echo e($category->value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="shad-label" for="edit_status">Status <span class="text-danger">*</span></label>
        <select class="shad-select" id="edit_status" name="status" required>
            <option value="Open"        <?php echo e(($ticket['status'] ?? 'Open') === 'Open'        ? 'selected' : ''); ?>>Open</option>
            <option value="In Progress" <?php echo e(($ticket['status'] ?? 'Open') === 'In Progress' ? 'selected' : ''); ?>>In Progress</option>
            <option value="Closed"      <?php echo e(($ticket['status'] ?? 'Open') === 'Closed'      ? 'selected' : ''); ?> style="color:#6b7280;font-weight:bold;">Closed</option>
        </select>
    </div>

    
    <?php if(($ticket['status'] ?? '') === 'Closed' && !empty($ticket['closed_at'])): ?>
    <div class="mb-3 p-3 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
        <div class="d-flex align-items-center">
            <span class="shad-badge shad-badge-green mr-2">
                <i class="fas fa-check-circle mr-1"></i> Closed
            </span>
            <span class="text-sm text-gray-700">
                Closed At: <strong><?php echo e(\Carbon\Carbon::parse($ticket['closed_at'])->format('d M Y, h:i A')); ?></strong>
            </span>
        </div>
    </div>
    <?php endif; ?>


    <div class="mb-3">
        <label class="shad-label" for="edit_description">Description</label>
        <textarea class="shad-input" id="edit_description" name="description" rows="4" style="resize: vertical;"><?php echo e($ticket['description'] ?? ''); ?></textarea>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign ticket')): ?>
    <div class="mb-3">
        <label class="shad-label" for="edit_assigned_to">Assign To</label>
        <select class="shad-select" id="edit_assigned_to" name="assigned_to">
            <option value="">Unassigned</option>
            <?php $__currentLoopData = \App\Models\User::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($staff->id); ?>" <?php echo e(($ticket['assigned_to_id'] ?? null) == $staff->id ? 'selected' : ''); ?>>
                    <?php echo e($staff->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>

    <!-- Activity Log Section -->
    <?php if(isset($logs) && count($logs) > 0): ?>
    <div class="border-top pt-3 mt-3">
        <h6 class="text-gray-900 font-weight-bold mb-3">
            <i class="fas fa-history mr-1 text-gray-400"></i> Activity Log
        </h6>
        <div style="max-height: 200px; overflow-y: auto;">
            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="d-flex align-items-start mb-2 pb-2 border-bottom">
                <div class="bg-gray-100 rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 28px; height: 28px; min-width: 28px;">
                    <i class="fas fa-user text-gray-400" style="font-size: 0.65rem;"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-800"><?php echo e($log->message); ?></div>
                    <div class="text-xs text-gray-400">
                        <?php echo e($log->user->name ?? 'System'); ?> · <?php echo e($log->created_at->diffForHumans()); ?>

                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Add Activity -->
    <div class="border-top pt-3 mt-3">
        <h6 class="text-gray-900 font-weight-bold mb-2">
            <i class="fas fa-comment mr-1 text-gray-400"></i> Add Activity
        </h6>
        <div class="d-flex gap-2">
            <input type="hidden" name="ticket_id" value="<?php echo e($ticket['ticket_id']); ?>">
            <textarea class="shad-input" id="activity_message" rows="2" placeholder="Add a note or activity..." style="resize: none;"></textarea>
        </div>
        <div class="text-right mt-2">
            <button type="button" class="shad-btn shad-btn-outline shad-btn-sm" id="submitActivityBtn">
                <i class="fas fa-paper-plane mr-1"></i> Add Activity
            </button>
        </div>
    </div>
</form>

<?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/tickets/partials/ticket-edit-body.blade.php ENDPATH**/ ?>