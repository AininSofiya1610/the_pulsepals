

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-6">
    
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-6">
        <div>
            <h1 class="h3 font-weight-bold text-gray-900 mb-1">All Tickets</h1>
            <p class="text-sm text-gray-500 mb-0">Manage and track support tickets</p>
        </div>
        <div class="d-flex gap-2">
            <form action="<?php echo e(route('export.tickets')); ?>" method="POST" style="display: inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="start_date" value="<?php echo e(request('date_from', '')); ?>">
                <input type="hidden" name="end_date" value="<?php echo e(request('date_to', '')); ?>">
                <input type="hidden" name="search" value="<?php echo e(request('search', '')); ?>">
                <input type="hidden" name="status" value="<?php echo e(request('status', '')); ?>">
                <button type="submit" class="shad-btn shad-btn-outline">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export to Excel
                </button>
            </form>
            <button class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#createTicketModal">
                <i class="fas fa-plus mr-2"></i> Create New Ticket
            </button>
        </div>
    </div>

    

    <!-- Dashboard Statistics Row -->
    <div class="row mb-6">
        
        <!-- Total Tickets Card -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="shad-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-sm font-medium text-black mb-1">Total Tickets</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['total'] ?? count($tickets)); ?></div>
                    </div>
                    <div class="bg-primary-50 p-3 rounded-full">
                        <i class="fas fa-ticket-alt text-primary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Tickets Card -->
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="shad-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-sm font-medium text-black mb-1">Open Tickets</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['open'] ?? 0); ?></div>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-full">
                        <i class="fas fa-folder-open text-yellow-600 fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress Tickets Card -->
        <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
            <div class="shad-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-sm font-medium text-black mb-1">In Progress</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['in_progress'] ?? 0); ?></div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-full">
                        <i class="fas fa-spinner text-blue-600 fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved Tickets Card -->
        <div class="col-xl-3 col-md-6">
            <div class="shad-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-sm font-medium text-black mb-1">Resolved</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['resolved'] ?? 0); ?></div>
                    </div>
                    <div class="bg-green-50 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Tickets Table Card -->
    <div class="shad-card">
        <div class="px-6 py-4 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-gray-900">Ticket List</h6>
            <?php if(isset($tickets) && count($tickets) > 0): ?>
                <span class="shad-badge shad-badge-outline"><?php echo e($tickets->count()); ?> Total</span>
            <?php endif; ?>
        </div>
        
        <!-- Status Filter Tabs -->
        <div class="px-6 py-3 border-bottom bg-gray-50">
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('tickets.index', array_merge(request()->except(['status', 'page']), []))); ?>" 
                   class="shad-btn <?php echo e(!request('status') ? 'shad-btn-primary' : 'shad-btn-ghost'); ?> shad-btn-sm">
                    All
                </a>
                <a href="<?php echo e(route('tickets.index', array_merge(request()->except(['status', 'page']), ['status' => 'Open']))); ?>" 
                   class="shad-btn <?php echo e(request('status') == 'Open' ? 'shad-btn-primary' : 'shad-btn-ghost'); ?> shad-btn-sm">
                    Open
                </a>
                <a href="<?php echo e(route('tickets.index', array_merge(request()->except(['status', 'page']), ['status' => 'In Progress']))); ?>" 
                   class="shad-btn <?php echo e(request('status') == 'In Progress' ? 'shad-btn-primary' : 'shad-btn-ghost'); ?> shad-btn-sm">
                    In Progress
                </a>
                <a href="<?php echo e(route('tickets.index', array_merge(request()->except(['status', 'page']), ['status' => 'Closed']))); ?>" 
                   class="shad-btn <?php echo e(request('status') == 'Closed' ? 'shad-btn-primary' : 'shad-btn-ghost'); ?> shad-btn-sm">
                    Closed
                </a>
            </div>
        </div>
        
        <div class="p-6">
            
            <!-- Search and Filter Section -->
            <form method="GET" action="<?php echo e(route('tickets.index')); ?>" class="mb-6">
                <div class="row">
                    <!-- Search Bar -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute text-gray-400" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" 
                                   name="search" 
                                   class="shad-input pl-5" 
                                   placeholder="Search by Ticket ID, Name, Email..." 
                                   value="<?php echo e(request('search')); ?>"
                                   style="padding-left: 2.5rem !important;">
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <input type="date" 
                               name="date_from" 
                               class="shad-input" 
                               placeholder="From Date"
                               value="<?php echo e(request('date_from')); ?>">
                    </div>

                    <!-- Date To -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <input type="date" 
                               name="date_to" 
                               class="shad-input" 
                               placeholder="To Date"
                               value="<?php echo e(request('date_to')); ?>">
                    </div>

                    <!-- Filter Buttons -->
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="shad-btn shad-btn-primary w-100 mr-2">
                            Filters
                        </button>
                        <?php if(request('search') || request('date_from') || request('date_to')): ?>
                            <a href="<?php echo e(route('tickets.index')); ?>" class="shad-btn shad-btn-secondary w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            
            <?php if(isset($tickets) && count($tickets) > 0): ?>
                <!-- Tickets Table -->
                <div class="table-responsive">
                    <table class="shad-table">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>
                                    <a href="<?php echo e(route('tickets.index', array_merge(request()->all(), ['sort_by' => 'status', 'sort_order' => request('sort_by') == 'status' && request('sort_order') == 'desc' ? 'asc' : 'desc']))); ?>" class="text-dark text-decoration-none d-flex align-items-center">
                                        Status
                                        <?php if(request('sort_by') == 'status'): ?>
                                            <i class="fas fa-sort-<?php echo e(request('sort_order') == 'asc' ? 'up' : 'down'); ?> ml-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort ml-1 text-muted opacity-50"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>
                                    <a href="<?php echo e(route('tickets.index', array_merge(request()->all(), ['sort_by' => 'priority', 'sort_order' => request('sort_by') == 'priority' && request('sort_order') == 'desc' ? 'asc' : 'desc']))); ?>" class="text-dark text-decoration-none d-flex align-items-center">
                                        Priority
                                        <?php if(request('sort_by') == 'priority'): ?>
                                            <i class="fas fa-sort-<?php echo e(request('sort_order') == 'asc' ? 'up' : 'down'); ?> ml-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort ml-1 text-muted opacity-50"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?php echo e(route('tickets.index', array_merge(request()->all(), ['sort_by' => 'created_at', 'sort_order' => request('sort_by') == 'created_at' && request('sort_order') == 'asc' ? 'desc' : 'asc']))); ?>" class="text-dark text-decoration-none d-flex align-items-center">
                                        Created
                                        <?php if(request('sort_by', 'created_at') == 'created_at'): ?>
                                            <i class="fas fa-sort-<?php echo e(request('sort_order', 'desc') == 'asc' ? 'up' : 'down'); ?> ml-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort ml-1 text-muted opacity-50"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Closed At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="font-medium text-gray-900"><?php echo e($ticket['ticket_id']); ?></td>
                                <td><?php echo e($ticket['email'] ?? 'N/A'); ?></td>
                                <td><?php echo e($ticket['full_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                        $statusClass = '';
                                        $statusLabel = $ticket['status'];
                                        switch($statusLabel) {
                                            case 'Critical': $statusClass = 'shad-badge-red'; break;
                                            case 'Open': $statusClass = 'shad-badge-yellow'; break;
                                            case 'In Progress': $statusClass = 'shad-badge-blue'; break;
                                            case 'Resolved': $statusClass = 'shad-badge-green'; break;
                                            case 'Closed': $statusClass = 'shad-badge-gray'; break;
                                            default: $statusClass = 'shad-badge-gray';
                                        }
                                    ?>
                                    <span class="shad-badge <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span>
                                </td>
                                <td>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($ticket['title'] ?? 'N/A'); ?></span>
                                </td>
                                <td><?php echo e($ticket['category']); ?></td>
                                <td><?php echo e($ticket['unit']); ?></td>
                                <td>
                                    <?php
                                        $priorityClass = '';
                                        switch($ticket['priority']) {
                                            case 'Critical': $priorityClass = 'shad-badge-red'; break;
                                            case 'High': $priorityClass = 'shad-badge-orange'; break;
                                            case 'Medium': $priorityClass = 'shad-badge-blue'; break;
                                            case 'Low': $priorityClass = 'shad-badge-gray'; break;
                                            default: $priorityClass = 'shad-badge-gray';
                                        }
                                    ?>
                                    <span class="shad-badge <?php echo e($priorityClass); ?>"><?php echo e($ticket['priority']); ?></span>
                                </td>
                                <td><?php echo e(\Carbon\Carbon::parse($ticket['created_at'])->format('M d, Y')); ?></td>
                                <td>
                                    <?php if(!empty($ticket['closed_at'])): ?>
                                        <div class="d-flex align-items-center">
                                            <span class="shad-badge shad-badge-green mr-1" style="font-size: 0.65rem;">
                                                <i class="fas fa-check-circle mr-1"></i>Closed
                                            </span>
                                        </div>
                                        <span class="text-xs" style="color: #4B5563;"><?php echo e(\Carbon\Carbon::parse($ticket['closed_at'])->format('d M Y, h:i A')); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown">
                                        <button class="shad-btn shad-btn-ghost btn-sm" type="button" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                            <a class="dropdown-item view-ticket-btn" href="#" data-ticket-id="<?php echo e($ticket['ticket_id']); ?>">
                                                <i class="fas fa-eye mr-2 text-gray-400"></i> View Details
                                            </a>
                                            <a class="dropdown-item edit-ticket-btn" href="#" data-id="<?php echo e($ticket['ticket_id']); ?>">
                                                <i class="fas fa-edit mr-2 text-gray-400"></i> Edit Ticket
                                            </a>
                                            
                                            <?php if($ticket['status'] !== 'Closed'): ?>
                                            <a class="dropdown-item close-ticket-btn" href="#"
                                               data-id="<?php echo e($ticket['id']); ?>"
                                               data-ticket-id="<?php echo e($ticket['ticket_id']); ?>"
                                               data-priority="<?php echo e($ticket['priority']); ?>"
                                               data-category="<?php echo e($ticket['category']); ?>"
                                               data-unit="<?php echo e($ticket['unit']); ?>"
                                               data-ticket-type="<?php echo e($ticket['ticket_type']); ?>">
                                                <i class="fas fa-times-circle mr-2 text-gray-400"></i> Close Ticket
                                            </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-ticket')): ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger delete-ticket-btn" href="#" 
                                               data-ticket-id="<?php echo e($ticket['ticket_id']); ?>"
                                               data-ticket-title="<?php echo e($ticket['title'] ?? 'this ticket'); ?>">
                                                <i class="fas fa-trash mr-2"></i> Delete Ticket
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-6">
                    <?php echo e($tickets->appends(request()->except('page'))->links()); ?> 
                </div>
            <?php else: ?>
                <!-- No Tickets Yet -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="bg-gray-50 rounded-full d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-inbox fa-3x text-gray-300"></i>
                        </div>
                    </div>
                    <h5 class="text-gray-900 font-medium mb-1">No tickets found</h5>
                    <p class="text-gray-500 mb-4">Get started by creating a new support ticket.</p>
                    <button class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#createTicketModal">
                        <i class="fas fa-plus mr-2"></i> Create Ticket
                    </button>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<!-- ============================================================ -->
<!-- Create Ticket Modal                                           -->
<!-- ============================================================ -->
<div class="modal fade" id="createTicketModal" tabindex="-1" role="dialog" aria-labelledby="createTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shad-modal">
            <div class="modal-header border-bottom px-6 py-4">
                <h5 class="modal-title font-weight-bold text-gray-900" id="createTicketModalLabel">
                    Open a New Ticket
                </h5>
                <button type="button" class="close w-auto h-auto p-0 m-0 text-gray-400 hover:text-gray-600" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="<?php echo e(route('tickets.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="modal-body px-6 py-4">
                    
                    <?php if($errors->any()): ?>
                        <div class="shad-alert shad-alert-danger mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-circle mt-1 mr-2"></i>
                                <div>
                                    <strong>Errors Found:</strong>
                                    <ul class="mb-0 pl-3 mt-1">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Contact Information -->
                    <h6 class="text-gray-900 font-weight-bold mb-3">Contact Information</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="shad-label" for="email">Email Address <span class="text-danger">*</span></label>
                            <input 
                                type="email" 
                                class="shad-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="email" 
                                name="email" 
                                value="<?php echo e(old('email', auth()->user()->email ?? '')); ?>" 
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="shad-label" for="full_name">Full Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="shad-input <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="full_name" 
                                name="full_name" 
                                value="<?php echo e(old('full_name', auth()->user()->name ?? '')); ?>" 
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="shad-label" for="phone">Phone Number</label>
                            <input 
                                type="tel" 
                                class="shad-input" 
                                id="phone" 
                                name="phone" 
                                value="<?php echo e(old('phone')); ?>"
                            >
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="shad-label" for="phone_ext">Extension</label>
                            <input 
                                type="text" 
                                class="shad-input" 
                                id="phone_ext" 
                                name="phone_ext" 
                                value="<?php echo e(old('phone_ext')); ?>"
                            >
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div class="border-top my-4"></div>
                    <h6 class="text-gray-900 font-weight-bold mb-3">Ticket Details</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="shad-label" for="unit">Operational Unit <span class="text-danger">*</span></label>
                            <select class="shad-input <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="unit" name="unit" required>
                                <option value="">Select Unit</option>
                                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($unit->name ?? $unit); ?>" <?php echo e(old('unit') == ($unit->name ?? $unit) ? 'selected' : ''); ?>><?php echo e($unit->name ?? $unit); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="shad-label" for="priority">Priority Level <span class="text-danger">*</span></label>
                            <select class="shad-input <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($priority->value ?? $priority); ?>" <?php echo e(old('priority') == ($priority->value ?? $priority) ? 'selected' : ''); ?>><?php echo e($priority->value ?? $priority); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="shad-label" for="ticket_type">Ticket Type <span class="text-danger">*</span></label>
                            <select class="shad-input <?php $__errorArgs = ['ticket_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ticket_type" name="ticket_type" required>
                                <option value="">Select Type</option>
                                <?php $__currentLoopData = $ticketTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->value ?? $type); ?>" <?php echo e(old('ticket_type') == ($type->value ?? $type) ? 'selected' : ''); ?>><?php echo e($type->value ?? $type); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="shad-label" for="category">Help Topic <span class="text-danger">*</span></label>
                            <select class="shad-input <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="category" name="category" required>
                                <option value="">Select Topic</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->value ?? $category); ?>" <?php echo e(old('category') == ($category->value ?? $category) ? 'selected' : ''); ?>><?php echo e($category->value ?? $category); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="shad-label" for="title">Subject/Title <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="shad-input <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="title" 
                            name="title" 
                            value="<?php echo e(old('title')); ?>" 
                            placeholder="Brief summary of the issue"
                            required
                        >
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="shad-label" for="description">Description <span class="text-danger">*</span></label>
                        <textarea 
                            class="shad-input <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="description" 
                            name="description" 
                            rows="4"
                            placeholder="Please describe your issue in detail (minimum 10 characters)"
                            required
                        ><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger text-sm mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-muted text-xs mt-1">Minimum 10 characters required</p>
                    </div>

                </div>
                
                <div class="modal-footer border-top bg-gray-50 px-6 py-4">
                    <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="shad-btn shad-btn-primary ml-2">
                        Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- Ticket Details Modal                                          -->
<!-- ============================================================ -->
<?php echo $__env->make('tickets.partials.ticket-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- ============================================================ -->
<!-- Close Ticket Modal                                            -->
<!-- ============================================================ -->
<div class="modal fade" id="closeTicketModal" tabindex="-1" role="dialog" aria-labelledby="closeTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content shad-modal">

            <div class="modal-header border-bottom px-4 py-3">
                <div>
                    <h5 class="modal-title font-weight-bold text-gray-900 mb-0" id="closeTicketModalLabel">
                        Close Ticket
                    </h5>
                    <p class="text-xs text-gray-500 mb-0 mt-1" id="closeTicketModalSubtitle">
                        Pilih tarikh dan masa ticket ditutup
                    </p>
                </div>
                <button type="button" class="close w-auto h-auto p-0 m-0 text-gray-400" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="closeTicketForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                
                <input type="hidden" name="status" value="Closed">

                
                <input type="hidden" name="priority"    id="ctm_priority">
                <input type="hidden" name="category"    id="ctm_category">
                <input type="hidden" name="unit"        id="ctm_unit">
                <input type="hidden" name="ticket_type" id="ctm_ticket_type">

                <div class="modal-body px-4 py-4">

                    <div class="mb-3">
                        <label class="shad-label" for="closedAtInput">
                            Tarikh &amp; Masa Ditutup <span class="text-danger">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            id="closedAtInput"
                            name="closed_at"
                            class="shad-input"
                            required
                        >
                        <p class="text-xs text-gray-400 mt-1">Default: masa semasa. Boleh diubah.</p>
                    </div>

                    <div class="p-3 rounded" style="background:#fffbeb; border:1px solid #fcd34d;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1" style="font-size:0.75rem;"></i>
                            <p class="text-xs text-yellow-800 mb-0">
                                Pastikan tarikh yang dipilih adalah betul sebelum menutup ticket.
                            </p>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top bg-gray-50 px-4 py-3">
                    <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="shad-btn shad-btn-primary ml-2">
                        <i class="fas fa-check mr-1"></i> Tutup Ticket
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// If there are validation errors, reopen the modal
<?php if($errors->any()): ?>
    $(document).ready(function() {
        $('#createTicketModal').modal('show');
    });
<?php endif; ?>

// Auto-dismiss success alert
setTimeout(function() {
    $('.shad-alert-success').fadeOut('slow');
}, 5000);

$(document).ready(function() {

    // =============================================
    // Helper: Open Edit Modal via AJAX
    // =============================================
    function openEditModal(ticketId) {
        const modal = $('#editTicketModal');
        const modalBody = $('#editTicketModalBody');
        const modalTitle = $('#editModalTicketId');

        modalTitle.text(ticketId);

        modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3 text-gray-500">Loading edit form...</p>
            </div>
        `);

        modal.modal('show');

        $.ajax({
            url: `/tickets/${ticketId}/edit`,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                modalBody.html(response);
            },
            error: function(xhr) {
                modalBody.html(`
                    <div class="shad-alert shad-alert-danger text-center">
                        <i class="fas fa-exclamation-triangle mb-2"></i><br>
                        <strong>Error!</strong> Failed to load edit form.
                    </div>
                `);
            }
        });
    }

    // =============================================
    // View Ticket Modal Handler
    // =============================================
    $('.view-ticket-btn').on('click', function(e) {
        e.preventDefault();

        const ticketId = $(this).data('ticket-id');
        const modal = $('#ticketModal');
        const modalBody = $('#ticketModalBody');
        const modalTitle = $('#modalTicketId');

        $('#editTicketBtn').data('ticket-id', ticketId);
        modalTitle.text(ticketId);

        modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3 text-gray-500">Loading ticket details...</p>
            </div>
        `);

        modal.modal('show');

        $.ajax({
            url: `/tickets/${ticketId}`,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                modalBody.html(response);
            },
            error: function(xhr) {
                modalBody.html(`
                    <div class="shad-alert shad-alert-danger text-center">
                        <i class="fas fa-exclamation-triangle mb-2"></i><br>
                        <strong>Error!</strong> Failed to load ticket details.
                    </div>
                `);
            }
        });
    });

    // =============================================
    // Edit Button in View Modal → Opens Edit Modal
    // =============================================
    $(document).on('click', '#editTicketBtn', function(e) {
        e.preventDefault();
        const ticketId = $(this).data('ticket-id');

        $('#ticketModal').modal('hide');
        $('#ticketModal').one('hidden.bs.modal', function() {
            openEditModal(ticketId);
        });
    });

    // =============================================
    // Edit Button in Table Dropdown
    // =============================================
    $('.edit-ticket-btn').on('click', function(e) {
        e.preventDefault();
        const ticketId = $(this).data('id');
        openEditModal(ticketId);
    });

    // =============================================
    // Save Button in Edit Modal
    // =============================================
    $(document).on('click', '#saveEditTicketBtn', function() {
        const form = $('#editTicketForm');
        if (form.length) {
            form.submit();
        }
    });

    // =============================================
    // Close Ticket Button → Opens Close Modal
    // =============================================
    $('.close-ticket-btn').on('click', function(e) {
        e.preventDefault();

        const id         = $(this).data('id');          // numeric DB id
        const ticketId   = $(this).data('ticket-id');   // e.g. TKT-20260406-3309
        const priority   = $(this).data('priority');
        const category   = $(this).data('category');
        const unit       = $(this).data('unit');
        const ticketType = $(this).data('ticket-type');

        // Set form action to the update route using numeric id
        $('#closeTicketForm').attr('action', `/tickets/${id}`);

        // Update subtitle
        $('#closeTicketModalSubtitle').text('Ticket: ' + ticketId + ' — pilih tarikh ditutup');

        // Fill hidden required fields
        $('#ctm_priority').val(priority);
        $('#ctm_category').val(category);
        $('#ctm_unit').val(unit);
        $('#ctm_ticket_type').val(ticketType);

        // Default closed_at to current local datetime
        const now = new Date();
        const localISO = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 16);
        $('#closedAtInput').val(localISO);

        // Open Bootstrap modal
        $('#closeTicketModal').modal('show');
    });

    // =============================================
    // Clean up modals on close
    // =============================================
    $('#ticketModal').on('hidden.bs.modal', function() {
        $('#ticketModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
    });

    $('#editTicketModal').on('hidden.bs.modal', function() {
        $('#editTicketModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
    });

    // =============================================
    // Delete Ticket Handler
    // =============================================
    $('.delete-ticket-btn').on('click', function(e) {
        e.preventDefault();

        const ticketId   = $(this).data('ticket-id');
        const ticketTitle = $(this).data('ticket-title');

        if (confirm(`Are you sure you want to delete ticket "${ticketId}"?\n\nTitle: ${ticketTitle}\n\nThis action cannot be undone.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/tickets/${ticketId}`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfToken);

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    });

    // =============================================
    // Submit Activity Handler
    // =============================================
    $(document).on('click', '#submitActivityBtn', function(e) {
        e.preventDefault();

        const message  = $('#activity_message').val().trim();
        const ticketId = $('#editTicketForm').find('input[name="ticket_id"]').val();

        if (!message) {
            alert('Please enter an activity message');
            return;
        }

        $.ajax({
            url: `/tickets/${ticketId}/activity`,
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                message: message
            },
            success: function(response) {
                $('#activity_message').val('');
                openEditModal(ticketId);
                alert('Activity added successfully!');
            },
            error: function(xhr) {
                alert('Error adding activity. Please try again.');
            }
        });
    });

});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/tickets/index.blade.php ENDPATH**/ ?>