@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Tasks Management</h1>
            <p class="shad-page-description">Track and complete your team's tasks</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('export.tasks', request()->only(['assigned_to', 'status', 'due_from', 'due_to'])) }}" class="shad-btn shad-btn-outline">
                <i class="fas fa-file-excel mr-2"></i>
                Export to Excel
            </a>
            <button type="button" class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#taskModal">
                <i class="fas fa-plus"></i> Add Task
            </button>
        </div>
    </div>

    {{-- Success/Error messages now handled globally by iziToast in app.blade.php --}}

    <!-- Debug Info - Remove after testing -->
    @if(request()->hasAny(['assigned_to', 'status', 'due_from', 'due_to']))
    <div class="alert alert-info mb-3">
        <strong>Active Filters:</strong>
        @if(request('assigned_to')) User ID: {{ request('assigned_to') }} | @endif
        @if(request('status')) Status: {{ request('status') }} | @endif
        @if(request('due_from')) From: {{ request('due_from') }} | @endif
        @if(request('due_to')) To: {{ request('due_to') }} @endif
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="shad-card mb-4">
        <div class="shad-card-body py-3">
            <form method="GET" action="{{ route('tasks.index') }}" id="filterForm" class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <label class="shad-label mb-0">Assigned:</label>
                    <select name="assigned_to" id="assignedToSelect" class="shad-select" style="width: auto;">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="shad-label mb-0">Status:</label>
                    <select name="status" id="statusSelect" class="shad-select" style="width: auto;">
                        <option value="">All</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="shad-label mb-0">Due:</label>
                    <input type="date" name="due_from" id="dueFromInput" class="shad-input" style="width: auto;" value="{{ request('due_from') }}" title="From date">
                    <span class="text-muted">to</span>
                    <input type="date" name="due_to" id="dueToInput" class="shad-input" style="width: auto;" value="{{ request('due_to') }}" title="To date">
                </div>
                <button type="submit" class="shad-btn shad-btn-primary shad-btn-sm">Filter</button>
                <a href="{{ route('tasks.index') }}" class="shad-btn shad-btn-ghost shad-btn-sm">Clear</a>
            </form>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">All Tasks</h2>
                <p class="shad-card-description">
                    {{ $tasks->total() }} total tasks • 
                    Showing {{ $tasks->count() }} on this page • 
                    Page {{ $tasks->currentPage() }} of {{ $tasks->lastPage() }}
                </p>
            </div>
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned To</th>
                            <th>Related Deal</th>
                            <th>Due Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr style="{{ $task->status == 'done' ? 'background: #f0fdf4;' : '' }}">
                            <td>
                                <span style="font-weight: 600; color: var(--slate-800);">{{ $task->title }}</span>
                                @if($task->due_date && $task->due_date->isPast() && $task->status == 'open')
                                    <span class="shad-badge shad-badge-danger ml-2">Overdue</span>
                                @endif
                            </td>
                            <td>{{ $task->user->name ?? 'N/A' }}</td>
                            <td>{{ $task->deal->title ?? 'N/A' }}</td>
                            <td>
                                @if($task->due_date)
                                    <span>{{ $task->due_date->format('M d, Y') }}</span>
                                    <br><small style="color: var(--slate-500);">{{ $task->due_date->diffForHumans() }}</small>
                                @else
                                    <span style="color: var(--slate-400);">No due date</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($task->status == 'done')
                                    <span class="shad-badge shad-badge-success">Done</span>
                                @elseif($task->due_date && $task->due_date->isPast())
                                    <span class="shad-badge shad-badge-danger">Overdue</span>
                                @else
                                    <span class="shad-badge shad-badge-info">Open</span>
                                @endif
                            </td>
                            <td>
                                <div class="shad-actions justify-content-end">
                                    <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if($task->status == 'open')
                                            <input type="hidden" name="status" value="done">
                                            <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #22c55e;" title="Mark as Done">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="open">
                                            <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #f59e0b;" title="Reopen">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif
                                    </form>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="shad-empty">
                                    <div class="shad-empty-icon">
                                        <i class="fas fa-tasks fa-3x"></i>
                                    </div>
                                    <h3 class="shad-empty-title">No tasks found</h3>
                                    <p class="shad-empty-description">
                                        @if(request()->hasAny(['assigned_to', 'status', 'due_from', 'due_to']))
                                            No tasks match your current filters. Try adjusting the filters above.
                                        @else
                                            Create your first task to get started.
                                        @endif
                                    </p>
                                    <button type="button" class="shad-btn shad-btn-primary mt-3" data-toggle="modal" data-target="#taskModal">
                                        <i class="fas fa-plus"></i> Add Task
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tasks->hasPages())
        <div class="shad-card-footer d-flex justify-content-between align-items-center">
            <small style="color: var(--slate-500);">Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }}</small>
            <div class="shad-pagination">
                {{ $tasks->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

</div>

<!-- Task Modal -->
<div class="modal fade shad-modal" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Task</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="shad-label">Task Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="shad-input" placeholder="Enter task title" required>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Assign To <span class="text-danger">*</span></label>
                        <select name="assigned_to" class="shad-select" required>
                            <option value="">Select user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Related Deal (Optional)</label>
                        <select name="related_to_deal" class="shad-select">
                            <option value="">Select deal...</option>
                            @foreach($deals as $deal)
                                <option value="{{ $deal->id }}">{{ $deal->title }} ({{ $deal->customer->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Due Date</label>
                        <input type="datetime-local" name="due_date" class="shad-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const assignedToSelect = document.getElementById('assignedToSelect');
    const statusSelect = document.getElementById('statusSelect');
    const dueFromInput = document.getElementById('dueFromInput');
    const dueToInput = document.getElementById('dueToInput');
    
    // Debug: Log when filters change
    function logFilterChange(filterName, value) {
        console.log('Filter changed:', filterName, '=', value);
    }
    
    // Auto-submit when Assigned To changes
    if (assignedToSelect) {
        assignedToSelect.addEventListener('change', function() {
            logFilterChange('assigned_to', this.value);
            filterForm.submit();
        });
    }
    
    // Auto-submit when Status changes
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            logFilterChange('status', this.value);
            filterForm.submit();
        });
    }
    
    // Auto-submit when date inputs change
    if (dueFromInput) {
        dueFromInput.addEventListener('change', function() {
            logFilterChange('due_from', this.value);
            filterForm.submit();
        });
    }
    
    if (dueToInput) {
        dueToInput.addEventListener('change', function() {
            logFilterChange('due_to', this.value);
            filterForm.submit();
        });
    }
});
</script>
@endpush
@endsection



