@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @php $displayName = $customer->name ?: $customer->customerName ?: 'N/A'; @endphp

    <!-- Page Header -->
    <div class="shad-page-header mb-4">
        <div>
            <h1 class="shad-page-title">{{ $displayName }}</h1>
            <p class="shad-page-description">Customer Profile</p>
        </div>
        <a href="{{ route('crm.customers.index') }}" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Customers
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="shad-alert shad-alert-success mb-4">
            <i class="fas fa-check-circle"></i>
            <div>{{ $message }}</div>
        </div>
    @endif

    <div class="row">
        <!-- Customer Information Column -->
        <div class="col-lg-4">
            
            <!-- Customer Information Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <h2 class="shad-card-title">
                        <i class="fas fa-user mr-2" style="color: var(--slate-400);"></i>
                        Customer Information
                    </h2>
                    <button class="shad-btn shad-btn-ghost shad-btn-sm" data-toggle="modal" data-target="#editCustomerModal">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
                <div class="shad-card-body">
                    <div class="mb-4">
                        <p class="shad-stat-label mb-1">Name</p>
                        <p class="font-weight-semibold" style="font-size: 1rem; color: var(--slate-800);">{{ $displayName }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="shad-stat-label mb-1">Email</p>
                        <p style="color: var(--slate-700);">
                            @if($customer->email ?: $customer->customerEmail)
                                <a href="mailto:{{ $customer->email ?: $customer->customerEmail }}" class="text-primary">{{ $customer->email ?: $customer->customerEmail }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-4">
                        <p class="shad-stat-label mb-1">Phone</p>
                        <p style="color: var(--slate-700);">{{ $customer->phone ?: $customer->customerPhone ?: 'N/A' }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="shad-stat-label mb-1">Company</p>
                        <p style="color: var(--slate-700);">{{ $customer->company ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="shad-stat-label mb-1">Status</p>
                        @if($customer->status == 'active')
                            <span class="shad-badge shad-badge-green">Active</span>
                        @else
                            <span class="shad-badge shad-badge-gray">{{ $customer->status ?: 'N/A' }}</span>
                        @endif
                    </div>
                    @if($customer->lead)
                        <div>
                            <p class="shad-stat-label mb-1">Created from Lead</p>
                            <p style="font-size: 0.875rem; color: var(--slate-600);">
                                {{ $customer->lead->name }} ({{ $customer->created_at->format('M d, Y') }})
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                    <h2 class="shad-card-title" style="color: #fff;">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="shad-card-body">
                    <button type="button" class="shad-btn shad-btn-primary mb-3" style="width: 100%;" data-toggle="modal" data-target="#activityModal">
                        <i class="fas fa-comment mr-2"></i> Log Activity
                    </button>
                    <button type="button" class="shad-btn shad-btn-success mb-3" style="width: 100%;" data-toggle="modal" data-target="#dealModal">
                        <i class="fas fa-plus mr-2"></i> Create Deal
                    </button>
                    <hr style="border-color: var(--border-color); margin: 1rem 0;">
                    <form action="{{ route('crm.customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer? This will also delete all associated deals and activities.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="shad-btn shad-btn-ghost" style="width: 100%; color: #dc2626; border: 1px solid #fecaca;">
                            <i class="fas fa-trash mr-2"></i> Delete Customer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Deals & Activities Column -->
        <div class="col-lg-8">
            
            <!-- Deals Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <h2 class="shad-card-title">
                        <i class="fas fa-handshake mr-2" style="color: var(--slate-400);"></i>
                        Deals
                    </h2>
                    <button type="button" class="shad-btn shad-btn-primary shad-btn-sm" data-toggle="modal" data-target="#dealModal">
                        <i class="fas fa-plus mr-1"></i> New Deal
                    </button>
                </div>
                <div class="shad-card-body">
                    @forelse($customer->deals as $deal)
                        <div class="p-3 mb-3 rounded-lg" style="background: var(--slate-50); border: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <a href="{{ route('deals.show', $deal->id) }}" class="font-weight-semibold text-primary mb-1 d-block" style="font-size: 1rem;">
                                        {{ $deal->title }}
                                    </a>
                                    <p class="mb-1" style="font-size: 0.875rem; color: var(--slate-600);">
                                        Value: <span style="color: #22c55e; font-weight: 600;">RM {{ number_format($deal->value, 2) }}</span>
                                    </p>
                                    <small style="color: var(--slate-400);">Created {{ $deal->created_at->diffForHumans() }}</small>
                                </div>
                                <div>
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
                                    <span class="shad-badge {{ $stageData['class'] }}">{{ $stageData['label'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="shad-empty py-4">
                            <div class="shad-empty-icon">
                                <i class="fas fa-handshake fa-2x"></i>
                            </div>
                            <h3 class="shad-empty-title">No deals yet</h3>
                            <p class="shad-empty-description">Create your first deal for this customer</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Activity Timeline Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <h2 class="shad-card-title">
                        <i class="fas fa-history mr-2" style="color: var(--slate-400);"></i>
                        Activity Timeline
                    </h2>
                    <button type="button" class="shad-btn shad-btn-primary shad-btn-sm" data-toggle="modal" data-target="#activityModal">
                        <i class="fas fa-plus mr-1"></i> Log Activity
                    </button>
                </div>
                <div class="shad-card-body">
                    @forelse($customer->activities as $activity)
                        <div class="d-flex p-3 mb-3 rounded-lg" style="background: var(--slate-50); border-left: 4px solid {{ $activity->type == 'call' ? '#22c55e' : ($activity->type == 'email' ? '#3b82f6' : ($activity->type == 'whatsapp' ? '#25d366' : '#f59e0b')) }};">
                            <div class="mr-3">
                                @php
                                    $iconStyles = [
                                        'call' => ['icon' => 'fa-phone-alt', 'color' => '#22c55e', 'bg' => 'rgba(34, 197, 94, 0.1)'],
                                        'email' => ['icon' => 'fa-envelope', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.1)'],
                                        'whatsapp' => ['icon' => 'fab fa-whatsapp', 'color' => '#25d366', 'bg' => 'rgba(37, 211, 102, 0.1)'],
                                        'note' => ['icon' => 'fa-sticky-note', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.1)'],
                                    ];
                                    $iconData = $iconStyles[$activity->type] ?? $iconStyles['note'];
                                @endphp
                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; background: {{ $iconData['bg'] }};">
                                    <i class="{{ str_starts_with($iconData['icon'], 'fab') ? $iconData['icon'] : 'fas ' . $iconData['icon'] }}" 
                                       style="color: {{ $iconData['color'] }};"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="font-weight-medium" style="color: var(--slate-700);">
                                        <strong>{{ ucfirst($activity->type) }}</strong> by {{ $activity->user->name ?? 'System' }}
                                    </span>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0" style="color: var(--slate-600);">{{ $activity->description }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="shad-empty py-4">
                            <div class="shad-empty-icon">
                                <i class="fas fa-comments fa-2x"></i>
                            </div>
                            <h3 class="shad-empty-title">No activities yet</h3>
                            <p class="shad-empty-description">Log your first interaction with this customer</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="shad-card" style="margin: 0;">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">Log New Activity</h2>
                    <button type="button" class="close" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.5;">&times;</button>
                </div>
                <form action="{{ route('activities.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div class="shad-card-body">
                        <div class="mb-3">
                            <label class="shad-label">Activity Type</label>
                            <select name="type" class="shad-input" required>
                                <option value="call">📞 Phone Call</option>
                                <option value="email">📧 Email</option>
                                <option value="whatsapp">💬 WhatsApp</option>
                                <option value="note">📝 Note</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Description</label>
                            <textarea name="description" class="shad-input" rows="3" placeholder="Describe the activity..." required style="resize: vertical;"></textarea>
                        </div>
                    </div>
                    <div class="shad-card-footer d-flex justify-content-end gap-2">
                        <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="shad-btn shad-btn-primary">Log Activity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Deal Modal -->
<div class="modal fade" id="dealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="shad-card" style="margin: 0;">
                <div class="shad-card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                    <h2 class="shad-card-title mb-0" style="color: #fff;">
                        <i class="fas fa-plus mr-2"></i> Create New Deal
                    </h2>
                    <button type="button" class="close text-white" onclick="$('#dealModal').modal('hide');" style="font-size: 1.5rem; opacity: 0.9; padding: 0; margin: 0; background: none; border: none; line-height: 1;">×</button>
                </div>
                <form action="{{ route('deals.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div class="shad-card-body">
                        <div class="mb-3">
                            <label class="shad-label">Deal Title</label>
                            <input type="text" name="title" class="shad-input" placeholder="e.g. Website Redesign Project" required>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Deal Value (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #fff; border-color: var(--border-color);">RM</span>
                                <input type="number" step="0.01" name="value" class="shad-input" placeholder="0.00" style="border-left: none;">
                            </div>
                        </div>
                    </div>
                    <div class="shad-card-footer d-flex justify-content-end gap-2">
                        <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal" onclick="$('#dealModal').modal('hide');">Cancel</button>
                        <button type="submit" class="shad-btn shad-btn-success">Create Deal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="shad-card" style="margin: 0;">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">Edit Customer</h2>
                    <button type="button" class="close" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.5;">&times;</button>
                </div>
                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="shad-card-body">
                        <div class="mb-3">
                            <label class="shad-label">Name</label>
                            <input type="text" name="name" class="shad-input" value="{{ $customer->name ?: $customer->customerName }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Email</label>
                            <input type="email" name="email" class="shad-input" value="{{ $customer->email ?: $customer->customerEmail }}">
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Phone</label>
                            <input type="text" name="phone" class="shad-input" value="{{ $customer->phone ?: $customer->customerPhone }}">
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Company</label>
                            <input type="text" name="company" class="shad-input" value="{{ $customer->company }}">
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Status</label>
                            <select name="status" class="shad-input" required>
                                <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
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
</div>

<style>
.gap-2 { gap: 0.5rem; }
</style>

@endsection
