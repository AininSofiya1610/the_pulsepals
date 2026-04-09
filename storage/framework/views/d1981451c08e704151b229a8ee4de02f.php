

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Leads Management</h1>
            <p class="shad-page-description">Track and manage your sales leads</p>
        </div>
        <div class="d-flex gap-2">
            <form action="<?php echo e(route('export.leads')); ?>" method="POST" style="display: inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="start_date" value="<?php echo e($startDate ?? ''); ?>">
                <input type="hidden" name="end_date" value="<?php echo e($endDate ?? ''); ?>">
                <button type="submit" class="shad-btn shad-btn-outline">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export to Excel
                </button>
            </form>
            <a href="<?php echo e(route('leads.create')); ?>" class="shad-btn shad-btn-primary">
                <i class="fas fa-plus"></i> Add Lead
            </a>
        </div>
    </div>

    

    <!-- Filter Bar -->
    <div class="shad-card mb-4">
        <div class="shad-card-body py-3">
            <form action="<?php echo e(route('leads.index')); ?>" method="GET" class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="shad-label mb-0" for="start_date" style="white-space: nowrap;">From:</label>
                        <input type="date" name="start_date" id="start_date" class="shad-input" style="width: auto;" value="<?php echo e($startDate); ?>">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="shad-label mb-0" for="end_date" style="white-space: nowrap;">To:</label>
                        <input type="date" name="end_date" id="end_date" class="shad-input" style="width: auto;" value="<?php echo e($endDate); ?>">
                    </div>
                    <div class="position-relative" id="searchWrapper">
                        <input type="text" name="search" class="shad-input" style="width: 200px;" placeholder="Search leads..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <button type="submit" class="shad-btn shad-btn-primary shad-btn-sm">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?php echo e(route('leads.index')); ?>" class="shad-btn shad-btn-ghost shad-btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">All Leads</h2>
                <p class="shad-card-description"><?php echo e($leads->total()); ?> total leads</p>
            </div>
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--slate-800);"><?php echo e($lead->name); ?></td>
                            <td><?php echo e($lead->email ?? '-'); ?></td>
                            <td><?php echo e($lead->phone ?? '-'); ?></td>
                            <td><?php echo e($lead->source ?? '-'); ?></td>
                            <td>
                                <?php if($lead->status == 'new_lead'): ?>
                                    <span class="shad-badge shad-badge-info">New Lead</span>
                                <?php elseif($lead->status == 'contacted'): ?>
                                    <span class="shad-badge shad-badge-warning">Contacted</span>
                                <?php elseif($lead->status == 'qualified'): ?>
                                    <span class="shad-badge shad-badge-success">Qualified</span>
                                <?php else: ?>
                                    <span class="shad-badge shad-badge-default"><?php echo e($lead->status); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($lead->assignedTo->name ?? 'Unassigned'); ?></td>
                            <td><?php echo e($lead->created_at->format('M d, Y')); ?></td>
                            <td>
                                <div class="shad-actions justify-content-end">
                                    <a href="<?php echo e(route('leads.show', $lead->id)); ?>" class="shad-btn shad-btn-ghost shad-btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="<?php echo e(route('leads.destroy', $lead->id)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this lead?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8">
                                <div class="shad-empty">
                                    <div class="shad-empty-icon">
                                        <i class="fas fa-user-plus fa-3x"></i>
                                    </div>
                                    <h3 class="shad-empty-title">No leads found</h3>
                                    <p class="shad-empty-description">Start by adding your first lead.</p>
                                    <a href="<?php echo e(route('leads.create')); ?>" class="shad-btn shad-btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Add Lead
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($leads->hasPages()): ?>
        <div class="shad-card-footer">
            <div class="shad-pagination">
                <?php echo e($leads->appends([
                    'search' => request('search'), 
                    'timeline' => $timeline,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ])->links('pagination::bootstrap-4')); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<style>
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--border-color);
        border-top: none;
        border-radius: 0 0 var(--radius-sm) var(--radius-sm);
        box-shadow: var(--shadow-md);
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }
    .suggestion-item {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
    }
    .suggestion-item:last-child { border-bottom: none; }
    .suggestion-item:hover { background: var(--slate-50); }
    .suggestion-name { font-weight: 600; color: var(--slate-800); }
    .suggestion-email { font-size: 0.75rem; color: var(--slate-500); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;
    
    const wrapper = document.getElementById('searchWrapper');
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'search-suggestions';
    wrapper.appendChild(suggestionsContainer);

    let timeoutId;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeoutId);
        const query = this.value;
        
        if (query.length < 2) {
            suggestionsContainer.style.display = 'none';
            return;
        }

        timeoutId = setTimeout(() => {
            fetch(`<?php echo e(route('leads.suggestions')); ?>?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsContainer.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(lead => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.innerHTML = `
                                <div class="suggestion-name">${lead.name}</div>
                                ${lead.email ? `<div class="suggestion-email">${lead.email}</div>` : ''}
                            `;
                            div.addEventListener('click', function() {
                                searchInput.value = lead.name;
                                suggestionsContainer.style.display = 'none';
                                searchInput.form.submit();
                            });
                            suggestionsContainer.appendChild(div);
                        });
                        suggestionsContainer.style.display = 'block';
                    } else {
                        suggestionsContainer.style.display = 'none';
                    }
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/leads/index.blade.php ENDPATH**/ ?>