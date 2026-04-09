

<?php $__env->startSection('title', 'Ticket ' . $ticket->ticket_id); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Ticket Info Card -->
            <div class="shad-card mb-4">
                <div class="px-6 py-4 border-bottom">
                    <h5 class="m-0 font-weight-bold text-gray-900">
                        <i class="fas fa-ticket-alt mr-2"></i> <?php echo e($ticket->ticket_id); ?>

                    </h5>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-2"><?php echo e($ticket->title); ?></h6>
                        <p class="text-muted mb-0"><?php echo e($ticket->description); ?></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Status</small>
                            <div>
                                <?php if($ticket->status == 'Open'): ?>
                                    <span class="shad-badge shad-badge-yellow"><?php echo e($ticket->status); ?></span>
                                <?php elseif($ticket->status == 'In Progress'): ?>
                                    <span class="shad-badge shad-badge-blue"><?php echo e($ticket->status); ?></span>
                                <?php elseif($ticket->status == 'Closed'): ?>
                                    <span class="shad-badge shad-badge-green"><?php echo e($ticket->status); ?></span>
                                <?php else: ?>
                                    <span class="shad-badge shad-badge-outline"><?php echo e($ticket->status); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Priority</small>
                            <div><strong><?php echo e($ticket->priority); ?></strong></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Category</small>
                            <div><?php echo e($ticket->category); ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Created</small>
                            <div><?php echo e($ticket->created_at->format('d M Y, h:i A')); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="shad-card mb-4">
                <div class="px-6 py-4 border-bottom">
                    <h6 class="m-0 font-weight-bold text-gray-900">Add Reply</h6>
                </div>
                <div class="p-6">
                    <form action="<?php echo e(route('tickets.public.reply', $ticket->public_token)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="shad-label">Your Email</label>
                            <input type="email" name="email" class="shad-input" value="<?php echo e($ticket->email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Your Message</label>
                            <textarea name="message" class="shad-input" rows="4" placeholder="Type your reply here..." required></textarea>
                        </div>
                        <button type="submit" class="shad-btn shad-btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i> Send Reply
                        </button>
                    </form>
                    
                    <?php if(session('success')): ?>
                        <div class="alert alert-success mt-3"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger mt-3"><?php echo e(session('error')); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="shad-card">
                <div class="px-6 py-4 border-bottom">
                    <h6 class="m-0 font-weight-bold text-gray-900">Conversation History</h6>
                </div>
                <div class="p-6">
                    <?php if($logs->count() > 0): ?>
                        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="shad-card mb-3 p-3" style="background-color: #f8f9fa;">
                                <div class="mb-2">
                                    <strong class="text-dark"><?php echo e($log->user->email ?? $log->guest_email ?? 'System'); ?></strong>
                                    <?php if($log->is_staff): ?>
                                        <span class="shad-badge shad-badge-blue ml-2">Staff</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></small>
                                </div>
                                <div class="text-dark">
                                    <?php echo e($log->message); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">No messages yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/tickets/public.blade.php ENDPATH**/ ?>