@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Sales Pipeline</h1>
            <p class="shad-page-description">Track deals through the sales process</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('export.deals') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="shad-btn shad-btn-outline">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export to Excel
                </button>
            </form>
            <button type="button" class="shad-btn shad-btn-primary" data-toggle="modal" data-target="#newDealModal">
                <i class="fas fa-plus"></i> New Deal
            </button>
        </div>
    </div>

    {{-- Success/Error messages now handled globally by iziToast in app.blade.php --}}

    <!-- Pipeline Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Total Deals</p>
                        <p class="shad-stat-value">{{ $totalDeals }}</p>
                    </div>
                    <div class="shad-stat-icon info">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Pipeline Value</p>
                        <p class="shad-stat-value">RM {{ number_format($pipelineValue, 0) }}</p>
                    </div>
                    <div class="shad-stat-icon primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card" style="border-left: 3px solid #22c55e;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Won</p>
                        <p class="shad-stat-value" style="color: #15803d;">RM {{ number_format($wonValue, 0) }}</p>
                    </div>
                    <div class="shad-stat-icon success">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="shad-stat-card" style="border-left: 3px solid #ef4444;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="shad-stat-label">Lost</p>
                        <p class="shad-stat-value" style="color: #dc2626;">RM {{ number_format($lostValue, 0) }}</p>
                    </div>
                    <div class="shad-stat-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-wrapper">
        <div class="kanban-board">
            @php
                $stageConfig = [
                    'new_opportunity' => ['label' => 'New', 'icon' => 'fa-star', 'color' => '#3b82f6'],
                    'qualified' => ['label' => 'Qualified', 'icon' => 'fa-check-circle', 'color' => '#8b5cf6'],
                    'proposal' => ['label' => 'Proposal', 'icon' => 'fa-file-alt', 'color' => '#f59e0b'],
                    'negotiation' => ['label' => 'Negotiation', 'icon' => 'fa-handshake', 'color' => '#71717a'],
                    'closed_won' => ['label' => 'Won', 'icon' => 'fa-trophy', 'color' => '#22c55e'],
                    'closed_lost' => ['label' => 'Lost', 'icon' => 'fa-times', 'color' => '#ef4444'],
                ];
            @endphp

            @foreach($stageConfig as $stageKey => $config)
                <div class="kanban-column" data-stage="{{ $stageKey }}">
                    <div class="kanban-header" style="border-top: 3px solid {{ $config['color'] }};">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="kanban-title">
                                <i class="fas {{ $config['icon'] }}" style="color: {{ $config['color'] }};"></i>
                                {{ $config['label'] }}
                            </span>
                            <span class="shad-badge shad-badge-default">{{ count($stages[$stageKey] ?? []) }}</span>
                        </div>
                        <p class="kanban-value">RM {{ number_format(collect($stages[$stageKey] ?? [])->sum('value'), 0) }}</p>
                    </div>
                    <div class="kanban-body" data-stage="{{ $stageKey }}">
                        @foreach($stages[$stageKey] ?? [] as $deal)
                            <div class="kanban-card" draggable="true" data-deal-id="{{ $deal->id }}" data-stage="{{ $stageKey }}">
                                <h4 class="card-title">{{ Str::limit($deal->title, 30) }}</h4>
                                <p class="card-customer"><i class="fas fa-user"></i> {{ $deal->customer->name ?? 'N/A' }}</p>
                                <p class="card-value">RM {{ number_format($deal->value, 0) }}</p>
                                <a href="{{ route('deals.show', $deal->id) }}" class="shad-btn shad-btn-ghost shad-btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        @endforeach

                        @if(count($stages[$stageKey] ?? []) == 0)
                            <div class="kanban-empty">
                                <i class="fas fa-inbox"></i>
                                <p>No deals</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="shad-alert shad-alert-info mt-4">
        <i class="fas fa-lightbulb"></i>
        <div><strong>Tip:</strong> Drag deals between columns to change their stage.</div>
    </div>

</div>

<!-- New Deal Modal -->
<div class="modal fade shad-modal" id="newDealModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Deal</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('deals.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="shad-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="shad-select" required>
                            <option value="">Select customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Deal Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="shad-input" placeholder="e.g. Website Redesign" required>
                    </div>
                    <div class="mb-3">
                        <label class="shad-label">Deal Value</label>
                        <div class="d-flex align-items-center">
                            <span style="padding: 0.5rem 0.75rem; background: var(--slate-100); border: 1px solid var(--border-color); border-right: none; border-radius: var(--radius-sm) 0 0 var(--radius-sm); color: var(--slate-500);">RM</span>
                            <input type="number" step="0.01" name="value" class="shad-input" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0;" placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="shad-btn shad-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-primary">Create Deal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.kanban-wrapper { overflow-x: auto; padding-bottom: 1rem; }
.kanban-board { display: flex; gap: 1rem; min-width: max-content; }
.kanban-column { width: 240px; flex-shrink: 0; background: var(--slate-50); border-radius: var(--radius-lg); border: 1px solid var(--border-color); }
.kanban-header { padding: 1rem; background: #fff; border-radius: var(--radius-lg) var(--radius-lg) 0 0; border-bottom: 1px solid var(--border-color); }
.kanban-title { font-weight: 600; font-size: 0.875rem; color: var(--slate-800); display: flex; align-items: center; gap: 0.5rem; }
.kanban-value { font-size: 0.75rem; color: var(--slate-500); margin: 0.25rem 0 0 0; }
.kanban-body { padding: 0.75rem; min-height: 300px; max-height: 450px; overflow-y: auto; transition: background 0.2s; }
.kanban-body.drag-over { background: #dcfce7; border: 2px dashed #22c55e; }
.kanban-card { background: #fff; border-radius: var(--radius); padding: 0.875rem; margin-bottom: 0.5rem; border: 1px solid var(--border-color); cursor: grab; transition: all 0.15s; }
.kanban-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
.kanban-card.dragging { opacity: 0.5; transform: rotate(2deg); }
.kanban-card .card-title { font-size: 0.875rem; font-weight: 600; color: var(--slate-800); margin: 0 0 0.375rem 0; }
.kanban-card .card-customer { font-size: 0.75rem; color: var(--slate-500); margin: 0 0 0.25rem 0; }
.kanban-card .card-value { font-size: 0.875rem; font-weight: 600; color: #22c55e; margin: 0 0 0.5rem 0; }
.kanban-empty { text-align: center; padding: 2rem 1rem; color: var(--slate-400); }
.kanban-empty i { font-size: 1.5rem; margin-bottom: 0.5rem; display: block; }
.kanban-empty p { margin: 0; font-size: 0.8125rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-body');

    cards.forEach(card => {
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('dealId', this.dataset.dealId);
            this.classList.add('dragging');
        });
        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            columns.forEach(col => col.classList.remove('drag-over'));
        });
    });

    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        column.addEventListener('dragleave', function(e) {
            if (!this.contains(e.relatedTarget)) this.classList.remove('drag-over');
        });
        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const dealId = e.dataTransfer.getData('dealId');
            const newStage = this.dataset.stage;
            if (!dealId || !newStage) return;

            const token = document.querySelector('meta[name="csrf-token"]');
            fetch(`/deals/${dealId}/stage`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token.getAttribute('content'), 'Accept': 'application/json' },
                body: JSON.stringify({ stage: newStage })
            })
            .then(r => r.json())
            .then(data => { if (data.success) setTimeout(() => location.reload(), 300); else alert('Failed to update'); })
            .catch(() => alert('Error updating deal stage'));
        });
    });
});
</script>
@endsection
