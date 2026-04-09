<!-- Ticket Detail Body (used in modal AJAX and show page) -->

<style>
    .ticket-detail .info-label {
        font-size: 0.75rem;
        color: #6B7280;
        margin-bottom: 2px;
    }
    .ticket-detail .info-value {
        font-size: 0.875rem;
        font-weight: 500;
        color: #111827 !important;
    }
    .ticket-detail .ts-text {
        font-size: 0.75rem;
        color: #374151 !important;
    }
    .ticket-detail .ts-label {
        font-size: 0.75rem;
        color: #6B7280;
    }
</style>

<div class="ticket-detail">

    <!-- Status & Priority Row -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <?php
            $statusClass = match($ticket['status'] ?? '') {
                'Critical' => 'shad-badge-red',
                'Open' => 'shad-badge-yellow',
                'In Progress' => 'shad-badge-blue',
                'Resolved' => 'shad-badge-green',
                'Closed' => 'shad-badge-gray',
                default => 'shad-badge-gray',
            };
            $priorityClass = match($ticket['priority'] ?? '') {
                'Critical' => 'shad-badge-red',
                'High' => 'shad-badge-orange',
                'Medium' => 'shad-badge-blue',
                'Low' => 'shad-badge-gray',
                default => 'shad-badge-gray',
            };
        ?>
        <span class="shad-badge <?php echo e($statusClass); ?>"><?php echo e($ticket['status']); ?></span>
        <span class="shad-badge <?php echo e($priorityClass); ?>"><?php echo e($ticket['priority']); ?> Priority</span>
    </div>

    <!-- Title -->
    <h5 class="font-weight-bold mb-3" style="color: #111827;"><?php echo e($ticket['title'] ?? 'N/A'); ?></h5>

    <!-- Description -->
    <?php if(!empty($ticket['description'])): ?>
    <div class="mb-4 p-3 rounded" style="background:#f8fafc; border: 1px solid #e2e8f0;">
        <p class="mb-0" style="font-size:0.875rem; color:#1f2937; white-space: pre-wrap;"><?php echo e($ticket['description']); ?></p>
    </div>
    <?php endif; ?>

    <!-- Info Grid -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="info-label">Contact Name</div>
            <div class="info-value"><?php echo e($ticket['full_name'] ?? 'N/A'); ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="info-label">Email</div>
            <div class="info-value"><?php echo e($ticket['email'] ?? 'N/A'); ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="info-label">Phone</div>
            <div class="info-value">
                <?php echo e($ticket['phone'] ?? 'N/A'); ?>

                <?php if(!empty($ticket['phone_ext'])): ?>
                    <span style="color:#4B5563; font-weight:400;">ext. <?php echo e($ticket['phone_ext']); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="info-label">Category</div>
            <div class="info-value"><?php echo e($ticket['category'] ?? 'N/A'); ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="info-label">Ticket Type</div>
            <div class="info-value"><?php echo e($ticket['ticket_type'] ?? 'N/A'); ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="info-label">Unit</div>
            <div class="info-value"><?php echo e($ticket['unit'] ?? 'N/A'); ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="info-label">Assigned To</div>
            <div class="info-value"><?php echo e($ticket['assigned_to'] ?? 'Unassigned'); ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="info-label">Created By</div>
            <div class="info-value"><?php echo e($ticket['created_by'] ?? 'System'); ?></div>
        </div>
    </div>

    <!-- Timestamps -->
    <div class="border-top pt-3 mt-2">
        <div class="row">
            <div class="col-md-6 mb-2">
                <span class="ts-label">Created:</span>
                <span class="ts-text">
                    <?php echo e($ticket['created_at'] ? \Carbon\Carbon::parse($ticket['created_at'])->format('M d, Y H:i') : 'N/A'); ?>

                </span>
            </div>
            <?php if(!empty($ticket['started_at'])): ?>
            <div class="col-md-6 mb-2">
                <span class="ts-label">Started:</span>
                <span class="ts-text"><?php echo e(\Carbon\Carbon::parse($ticket['started_at'])->format('M d, Y H:i')); ?></span>
            </div>
            <?php endif; ?>
            <?php if(!empty($ticket['resolved_at'])): ?>
            <div class="col-md-6 mb-2">
                <span class="ts-label">Resolved:</span>
                <span class="ts-text"><?php echo e(\Carbon\Carbon::parse($ticket['resolved_at'])->format('M d, Y H:i')); ?></span>
            </div>
            <?php endif; ?>
            <?php if(!empty($ticket['closed_at'])): ?>
            <div class="col-md-12 mb-2">
                <div class="d-flex align-items-center p-2 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <span class="shad-badge shad-badge-green mr-2">
                        <i class="fas fa-check-circle mr-1"></i> Closed
                    </span>
                    <span class="ts-label mr-1">Closed At:</span>
                    <span style="font-size:0.75rem; font-weight:600; color:#111827;">
                        <?php echo e(\Carbon\Carbon::parse($ticket['closed_at'])->format('d M Y, h:i A')); ?>

                    </span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/tickets/partials/ticket-detail-body.blade.php ENDPATH**/ ?>