@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header with Breadcrumb -->
    <div class="shad-page-header mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb bg-transparent mb-0 p-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('deals.index') }}" class="text-primary">Pipeline</a></li>
                    <li class="breadcrumb-item text-muted">/</li>
                    <li class="breadcrumb-item text-muted">{{ $deal->customer->name ?? 'Customer' }}</li>
                </ol>
            </nav>
            <h1 class="shad-page-title">{{ $deal->title }}</h1>
        </div>
        <a href="{{ route('deals.index') }}" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Pipeline
        </a>
    </div>

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            
            <!-- Deal Information Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <h2 class="shad-card-title">
                        <i class="fas fa-info-circle mr-2" style="color: var(--slate-400);"></i>
                        Deal Information
                    </h2>
                    <button class="shad-btn shad-btn-ghost shad-btn-sm" data-toggle="modal" data-target="#editDealModal">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
                <div class="shad-card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="mb-4">
                                <p class="shad-stat-label mb-1">Customer</p>
                                <a href="{{ route('crm.customers.show', $deal->customer->id ?? 0) }}" class="text-primary font-weight-medium" style="font-size: 1rem;">
                                    {{ $deal->customer->name ?? 'N/A' }}
                                </a>
                            </div>
                            <div>
                                <p class="shad-stat-label mb-1">Value</p>
                                <p class="shad-stat-value" style="color: #22c55e; font-size: 1.5rem;">RM {{ number_format($deal->value, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="mb-4">
                                <p class="shad-stat-label mb-1">Stage</p>
                                @php
                                    $stageStyles = [
                                        'new_opportunity' => ['class' => 'shad-badge-blue', 'label' => 'New'],
                                        'qualified' => ['class' => 'shad-badge-primary', 'label' => 'Qualified'],
                                        'proposal' => ['class' => 'shad-badge-yellow', 'label' => 'Proposal'],
                                        'negotiation' => ['class' => 'shad-badge-gray', 'label' => 'Negotiation'],
                                        'closed_won' => ['class' => 'shad-badge-green', 'label' => 'Won'],
                                        'closed_lost' => ['class' => 'shad-badge-red', 'label' => 'Lost'],
                                    ];
                                    $stageData = $stageStyles[$deal->stage] ?? ['class' => 'shad-badge-gray', 'label' => $deal->stage];
                                @endphp
                                <span class="shad-badge {{ $stageData['class'] }}" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                    {{ $stageData['label'] }}
                                </span>
                            </div>
                            <div>
                                <p class="shad-stat-label mb-1">Created</p>
                                <p class="font-weight-medium" style="font-size: 1rem; color: var(--slate-700);">{{ $deal->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($deal->closed_reason)
                        <div class="shad-alert shad-alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div><strong>Close Reason:</strong> {{ $deal->closed_reason }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pipeline Progress Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-chart-line mr-2" style="color: var(--slate-400);"></i>
                        Pipeline Progress
                    </h2>
                </div>
                <div class="shad-card-body">
                    @php
                        $stageOrder = ['new_opportunity', 'qualified', 'proposal', 'negotiation', 'closed_won'];
                        $currentIndex = array_search($deal->stage, $stageOrder);
                        if ($deal->stage == 'closed_lost') $currentIndex = -1;
                        $stageLabels = ['New', 'Qualified', 'Proposal', 'Negotiation', 'Won'];
                    @endphp
                    
                    <div class="d-flex justify-content-between align-items-center position-relative" style="padding: 0 1rem;">
                        <!-- Progress Line Background -->
                        <div style="position: absolute; top: 20px; left: 40px; right: 40px; height: 4px; background: var(--slate-200); border-radius: 2px; z-index: 0;"></div>
                        <!-- Progress Line Active -->
                        @if($currentIndex >= 0)
                        <div style="position: absolute; top: 20px; left: 40px; width: {{ min(100, ($currentIndex / 4) * 100) }}%; height: 4px; background: linear-gradient(90deg, #22c55e, #16a34a); border-radius: 2px; z-index: 1; transition: width 0.5s ease;"></div>
                        @endif
                        
                        @foreach($stageLabels as $index => $stageName)
                            <div class="text-center" style="z-index: 2; flex: 1;">
                                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-sm
                                    {{ $currentIndex >= $index ? 'text-white' : 'text-muted' }}"
                                    style="width: 44px; height: 44px; font-weight: 600; font-size: 0.875rem;
                                    background: {{ $currentIndex >= $index ? 'linear-gradient(135deg, #22c55e, #16a34a)' : '#fff' }};
                                    border: {{ $currentIndex >= $index ? 'none' : '2px solid var(--slate-200)' }};">
                                    @if($currentIndex >= $index)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <small class="d-block mt-2 {{ $currentIndex >= $index ? 'font-weight-semibold text-success' : 'text-muted' }}">{{ $stageName }}</small>
                            </div>
                        @endforeach
                    </div>

                    @if($deal->stage == 'closed_lost')
                        <div class="shad-alert shad-alert-danger mt-4 text-center">
                            <i class="fas fa-times-circle"></i>
                            <span>This deal was marked as lost</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tasks Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <h2 class="shad-card-title">
                        <i class="fas fa-tasks mr-2" style="color: var(--slate-400);"></i>
                        Tasks
                    </h2>
                    <button class="shad-btn shad-btn-primary shad-btn-sm" data-toggle="modal" data-target="#newTaskModal">
                        <i class="fas fa-plus mr-1"></i> Add Task
                    </button>
                </div>
                <div class="shad-card-body">
                    @forelse($deal->tasks as $task)
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-lg {{ $task->status == 'done' ? 'bg-success-subtle' : 'bg-warning-subtle' }}" 
                             style="border-left: 4px solid {{ $task->status == 'done' ? '#22c55e' : '#f59e0b' }}; background: {{ $task->status == 'done' ? 'rgba(34, 197, 94, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                            <div>
                                <span class="{{ $task->status == 'done' ? 'text-decoration-line-through text-muted' : 'font-weight-medium' }}">
                                    {{ $task->title }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $task->user->name ?? 'Unassigned' }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($task->due_date)
                                    <span class="shad-badge shad-badge-outline mr-2">{{ $task->due_date->format('M d') }}</span>
                                @endif
                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $task->status == 'open' ? 'done' : 'open' }}">
                                    <button type="submit" class="shad-btn {{ $task->status == 'done' ? 'shad-btn-ghost' : 'shad-btn-success' }} shad-btn-sm" style="width: 36px; height: 36px; padding: 0;">
                                        <i class="fas {{ $task->status == 'done' ? 'fa-undo' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="shad-empty py-4">
                            <div class="shad-empty-icon">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                            <h3 class="shad-empty-title">No tasks yet</h3>
                            <p class="shad-empty-description">Add your first task to track progress</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            
            <!-- Move to Stage Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                    <h2 class="shad-card-title" style="color: #fff;">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Move to Stage
                    </h2>
                </div>
                <div class="shad-card-body">
                    <div class="d-flex flex-column gap-2">
                        @foreach(['new_opportunity' => 'New', 'qualified' => 'Qualified', 'proposal' => 'Proposal', 'negotiation' => 'Negotiation'] as $stage => $label)
                            <form action="{{ route('deals.updateStage', $deal->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="stage" value="{{ $stage }}">
                                <button type="submit" class="shad-btn {{ $deal->stage == $stage ? 'shad-btn-primary' : 'shad-btn-ghost' }} mb-2" style="width: 100%; justify-content: flex-start;">
                                    <i class="fas fa-arrow-right mr-2"></i> {{ $label }}
                                </button>
                            </form>
                        @endforeach
                        
                        <hr class="my-2" style="border-color: var(--border-color);">
                        
                        <form action="{{ route('deals.updateStage', $deal->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="stage" value="closed_won">
                            <button type="submit" class="shad-btn shad-btn-success mb-2" style="width: 100%; justify-content: flex-start;">
                                <i class="fas fa-trophy mr-2"></i> Mark as Won
                            </button>
                        </form>
                        
                        <form action="{{ route('deals.updateStage', $deal->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="stage" value="closed_lost">
                            <button type="submit" class="shad-btn shad-btn-danger" style="width: 100%; justify-content: flex-start;">
                                <i class="fas fa-times mr-2"></i> Mark as Lost
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Deal Card -->
            <div class="shad-card" style="border: 1px solid #fecaca;">
                <div class="shad-card-body">
                    <form action="{{ route('deals.destroy', $deal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this deal? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="shad-btn shad-btn-danger" style="width: 100%;">
                            <i class="fas fa-trash mr-2"></i> Delete Deal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Edit Deal Modal -->
<div class="modal fade" id="editDealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="shad-card" style="margin: 0;">
            <div class="shad-card-header">
                <h2 class="shad-card-title">Edit Deal</h2>
                <button type="button" class="close" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.5;">&times;</button>
            </div>
            <form action="{{ route('deals.update', $deal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="shad-card-body">
                    <div class="mb-3">
                        <label class="shad-label">Deal Title</label>
                        <input type="text" name="title" class="shad-input" value="{{ $deal->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Deal Value (RM)</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: var(--slate-100); border-color: var(--border-color);">RM</span>
                            <input type="number" step="0.01" name="value" class="shad-input" style="border-left: none;" value="{{ $deal->value }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Close Reason (if lost)</label>
                        <textarea name="closed_reason" class="shad-input" rows="2" style="resize: vertical;">{{ $deal->closed_reason }}</textarea>
                    </div>
                </div>
                <div class="shad-card-footer d-flex justify-content-end gap-2">
                    <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Task Modal -->
<div class="modal fade" id="newTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="shad-card" style="margin: 0;">
            <div class="shad-card-header">
                <h2 class="shad-card-title">Add Task</h2>
                <button type="button" class="close" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.5;">&times;</button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="related_to_deal" value="{{ $deal->id }}">
                <div class="shad-card-body">
                    <div class="mb-3">
                        <label class="shad-label">Task Title</label>
                        <input type="text" name="title" class="shad-input" placeholder="Enter task title..." required>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Assign To</label>
                        <select name="assigned_to" class="shad-input" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Due Date</label>
                        <input type="datetime-local" name="due_date" class="shad-input">
                    </div>
                </div>
                <div class="shad-card-footer d-flex justify-content-end gap-2">
                    <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.text-decoration-line-through { text-decoration: line-through; }
.bg-success-subtle { background-color: rgba(34, 197, 94, 0.1); }
.bg-warning-subtle { background-color: rgba(245, 158, 11, 0.1); }
.gap-2 { gap: 0.5rem; }
</style>

@endsection
