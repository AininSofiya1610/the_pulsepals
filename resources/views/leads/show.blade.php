@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header mb-4">
        <div>
            <h1 class="shad-page-title">Lead Details</h1>
            <p class="shad-page-description">View and manage lead information</p>
        </div>
        <a href="{{ route('leads.index') }}" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Leads
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="shad-alert shad-alert-success mb-4">
            <i class="fas fa-check-circle"></i>
            <div>{{ $message }}</div>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="shad-alert shad-alert-danger mb-4">
            <i class="fas fa-exclamation-circle"></i>
            <div>{{ $message }}</div>
        </div>
    @endif

    <div class="row">
        <!-- Lead Information Column -->
        <div class="col-lg-8">
            
            <!-- Lead Information Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-user mr-2" style="color: var(--slate-400);"></i>
                        Lead Information
                    </h2>
                </div>
                <div class="shad-card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="mb-4">
                                <p class="shad-stat-label mb-1">Name</p>
                                <p class="font-weight-semibold" style="font-size: 1.125rem; color: var(--slate-800);">{{ $lead->name }}</p>
                            </div>
                            <div class="mb-4">
                                <p class="shad-stat-label mb-1">Email</p>
                                <p style="color: var(--slate-700);">
                                    @if($lead->email)
                                        <a href="mailto:{{ $lead->email }}" class="text-primary">{{ $lead->email }}</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="shad-stat-label mb-1">Phone</p>
                                <p style="color: var(--slate-700);">{{ $lead->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="mb-4">
                                <p class="shad-stat-label mb-1">Source</p>
                                <span class="shad-badge shad-badge-outline">{{ $lead->source ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-4">
                                <p class="shad-stat-label mb-1">Status</p>
                                @php
                                    $statusStyles = [
                                        'new_lead' => ['class' => 'shad-badge-blue', 'label' => 'New Lead'],
                                        'contacted' => ['class' => 'shad-badge-primary', 'label' => 'Contacted'],
                                        'negotiation' => ['class' => 'shad-badge-yellow', 'label' => 'Negotiation'],
                                        'qualified' => ['class' => 'shad-badge-green', 'label' => 'Qualified'],
                                        'lost' => ['class' => 'shad-badge-red', 'label' => 'Lost'],
                                    ];
                                    $statusData = $statusStyles[$lead->status] ?? ['class' => 'shad-badge-gray', 'label' => $lead->status];
                                @endphp
                                <span class="shad-badge {{ $statusData['class'] }}">{{ $statusData['label'] }}</span>
                            </div>
                            <div>
                                <p class="shad-stat-label mb-1">Assigned To</p>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        {{ substr($lead->assignedTo->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span style="color: var(--slate-700);">{{ $lead->assignedTo->name ?? 'Unassigned' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header d-flex justify-content-between align-items-center">
                    <h2 class="shad-card-title">
                        <i class="fas fa-history mr-2" style="color: var(--slate-400);"></i>
                        Activity Timeline
                    </h2>
                    <button class="shad-btn shad-btn-primary shad-btn-sm" data-toggle="modal" data-target="#addActivityModal">
                        <i class="fas fa-plus mr-1"></i> Add Activity
                    </button>
                </div>
                <div class="shad-card-body">
                    @forelse($lead->activities ?? [] as $activity)
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
                                    <span class="font-weight-medium" style="color: var(--slate-700);">{{ $activity->user->name ?? 'System' }}</span>
                                    <small class="text-muted">{{ $activity->created_at->format('M d, Y h:i A') }}</small>
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
                            <p class="shad-empty-description">Start tracking interactions with this lead</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            
            <!-- Quick Actions Card -->
            <div class="shad-card mb-4">
                <div class="shad-card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                    <h2 class="shad-card-title" style="color: #fff;">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="shad-card-body">
                    @if($lead->status !== 'qualified')
                        <!-- Convert to Customer -->
                        <button type="button" class="shad-btn shad-btn-success mb-3" style="width: 100%;" data-toggle="modal" data-target="#convertModal">
                            <i class="fas fa-user-plus mr-2"></i> Convert to Customer
                        </button>

                        <!-- Update Status Dropdown -->
                        <div class="dropdown mb-3">
                            <button class="shad-btn shad-btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="width: 100%;">
                                <i class="fas fa-sync mr-2"></i> Update Status
                            </button>
                            <div class="dropdown-menu w-100" style="border-radius: var(--radius-md); box-shadow: var(--shadow-lg);">
                                @foreach(['new_lead' => 'New Lead', 'contacted' => 'Contacted', 'negotiation' => 'Negotiation', 'lost' => 'Lost'] as $status => $label)
                                    <form action="{{ route('leads.updateStatus', $lead->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <button type="submit" class="dropdown-item {{ $lead->status == $status ? 'active' : '' }}" style="padding: 0.75rem 1rem;">
                                            @if($status == 'new_lead')
                                                <i class="fas fa-seedling text-info mr-2"></i>
                                            @elseif($status == 'contacted')
                                                <i class="fas fa-phone text-primary mr-2"></i>
                                            @elseif($status == 'negotiation')
                                                <i class="fas fa-handshake text-warning mr-2"></i>
                                            @else
                                                <i class="fas fa-times text-danger mr-2"></i>
                                            @endif
                                            {{ $label }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="shad-alert shad-alert-success mb-3">
                            <i class="fas fa-check-circle"></i>
                            <span>This lead has been converted to a customer.</span>
                        </div>
                    @endif

                    <!-- Assign Lead -->
                    <form action="{{ route('leads.assign', $lead->id) }}" method="POST">
                        @csrf
                        <div class="mb-0">
                            <label class="shad-label">Assign To</label>
                            <select name="assigned_to" class="shad-input" onchange="this.form.submit()">
                                <option value="">-- Select User --</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}" {{ $lead->assigned_to == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Lead Card -->
            <div class="shad-card" style="border: 1px solid #fecaca;">
                <div class="shad-card-body">
                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="shad-btn shad-btn-danger" style="width: 100%;">
                            <i class="fas fa-trash mr-2"></i> Delete Lead
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Add Activity Modal -->
<div class="modal fade" id="addActivityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="shad-card" style="margin: 0;">
            <div class="shad-card-header">
                <h2 class="shad-card-title">Log Activity</h2>
                <button type="button" class="close" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.5;">&times;</button>
            </div>
            <form action="{{ route('activities.store') }}" method="POST">
                @csrf
                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                <div class="shad-card-body">
                    <div class="mb-3">
                        <label class="shad-label">Activity Type</label>
                        <select name="type" class="shad-input" required>
                            <option value="call">📞 Call</option>
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
                    <button type="submit" class="shad-btn shad-btn-primary">Save Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Convert to Customer Modal -->
<div class="modal fade" id="convertModal" tabindex="-1" role="dialog" aria-labelledby="convertModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="shad-card" style="margin: 0; border: none;">
            <div class="shad-card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                <h2 class="shad-card-title mb-0" style="color: #fff;">
                    <i class="fas fa-user-plus mr-2"></i> Convert to Customer
                </h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; opacity: 0.9; padding: 0; margin: 0; background: none; border: none; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('leads.convert', $lead->id) }}" method="POST">
                @csrf
                <div class="shad-card-body">
                    <div class="shad-alert shad-alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <span>You are about to convert <strong>{{ $lead->name }}</strong> to a customer.</span>
                    </div>
                    
                    <div class="mb-4">
                        <label class="shad-label font-weight-bold">Do you want to create a deal for this customer?</label>
                        <div class="mt-2">
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="createDealYes" name="create_deal" value="yes" class="custom-control-input" checked>
                                <label class="custom-control-label" for="createDealYes">
                                    <strong>Yes</strong> - Create a new deal opportunity
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="createDealNo" name="create_deal" value="no" class="custom-control-input">
                                <label class="custom-control-label" for="createDealNo">
                                    <strong>No</strong> - Just create the customer
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="dealFields" class="p-3 rounded-lg" style="background: var(--slate-50);">
                        <div class="mb-3">
                            <label class="shad-label">Deal Title</label>
                            <input type="text" name="deal_title" class="shad-input" value="Opportunity - {{ $lead->name }}" placeholder="e.g. Website Project">
                        </div>
                        <div class="mb-0">
                            <label class="shad-label">Deal Value (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #fff; border-color: var(--border-color);">RM</span>
                                <input type="number" step="0.01" name="deal_value" class="shad-input" value="0" placeholder="0.00" style="border-left: none;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="shad-card-footer d-flex justify-content-end gap-2">
                    <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal" onclick="$('#convertModal').modal('hide');">Cancel</button>
                    <button type="submit" class="shad-btn shad-btn-success">
                        <i class="fas fa-check mr-2"></i> Convert
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dealFields = document.getElementById('dealFields');
    const radios = document.querySelectorAll('input[name="create_deal"]');
    const modal = document.getElementById('convertModal');
    
    // Prevent modal from closing when clicking inside
    if (modal) {
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
    
    // Handle radio button changes
    radios.forEach(radio => {
        radio.addEventListener('change', function(e) {
            e.stopPropagation(); // Prevent event from bubbling
            dealFields.style.display = this.value === 'yes' ? 'block' : 'none';
        });
        
        // Also prevent click event from closing modal
        radio.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Prevent label clicks from closing modal
    const labels = document.querySelectorAll('label[for="createDealYes"], label[for="createDealNo"]');
    labels.forEach(label => {
        label.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>

<style>
.gap-2 { gap: 0.5rem; }
</style>

@endsection
