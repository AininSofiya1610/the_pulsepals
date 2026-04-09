<!-- Edit Ticket Body (AJAX-loaded into edit modal) -->
<form id="editTicketForm" action="{{ route('tickets.update', $ticket['id']) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Title -->
    <div class="mb-3">
        <label class="shad-label" for="edit_title">Title <span class="text-danger">*</span></label>
        <input type="text" class="shad-input" id="edit_title" name="title" value="{{ $ticket['title'] }}" required>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_unit">Operational Unit <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_unit" name="unit" required>
                <option value="">Select unit...</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->name }}" {{ $ticket['unit'] == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_ticket_type">Ticket Type <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_ticket_type" name="ticket_type" required>
                <option value="">Select type...</option>
                @foreach($ticketTypes as $type)
                    <option value="{{ $type->value }}" {{ ($ticket['ticket_type'] ?? '') == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_priority">Priority <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_priority" name="priority" required>
                <option value="">Select priority...</option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->value }}" {{ $ticket['priority'] == $priority->value ? 'selected' : '' }}>{{ $priority->value }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="shad-label" for="edit_category">Help Topic <span class="text-danger">*</span></label>
            <select class="shad-select" id="edit_category" name="category" required>
                <option value="">Select topic...</option>
                @foreach($categories as $category)
                    <option value="{{ $category->value }}" {{ $ticket['category'] == $category->value ? 'selected' : '' }}>{{ $category->value }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="shad-label" for="edit_status">Status <span class="text-danger">*</span></label>
        <select class="shad-select" id="edit_status" name="status" required>
            <option value="Open"        {{ ($ticket['status'] ?? 'Open') === 'Open'        ? 'selected' : '' }}>Open</option>
            <option value="In Progress" {{ ($ticket['status'] ?? 'Open') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
            <option value="Closed"      {{ ($ticket['status'] ?? 'Open') === 'Closed'      ? 'selected' : '' }} style="color:#6b7280;font-weight:bold;">Closed</option>
        </select>
    </div>

    {{-- Show Closed At info if ticket is already closed --}}
    @if(($ticket['status'] ?? '') === 'Closed' && !empty($ticket['closed_at']))
    <div class="mb-3 p-3 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
        <div class="d-flex align-items-center">
            <span class="shad-badge shad-badge-green mr-2">
                <i class="fas fa-check-circle mr-1"></i> Closed
            </span>
            <span class="text-sm text-gray-700">
                Closed At: <strong>{{ \Carbon\Carbon::parse($ticket['closed_at'])->format('d M Y, h:i A') }}</strong>
            </span>
        </div>
    </div>
    @endif


    <div class="mb-3">
        <label class="shad-label" for="edit_description">Description</label>
        <textarea class="shad-input" id="edit_description" name="description" rows="4" style="resize: vertical;">{{ $ticket['description'] ?? '' }}</textarea>
    </div>

    @can('assign ticket')
    <div class="mb-3">
        <label class="shad-label" for="edit_assigned_to">Assign To</label>
        <select class="shad-select" id="edit_assigned_to" name="assigned_to">
            <option value="">Unassigned</option>
            @foreach(\App\Models\User::orderBy('name')->get() as $staff)
                <option value="{{ $staff->id }}" {{ ($ticket['assigned_to_id'] ?? null) == $staff->id ? 'selected' : '' }}>
                    {{ $staff->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endcan

    <!-- Activity Log Section -->
    @if(isset($logs) && count($logs) > 0)
    <div class="border-top pt-3 mt-3">
        <h6 class="text-gray-900 font-weight-bold mb-3">
            <i class="fas fa-history mr-1 text-gray-400"></i> Activity Log
        </h6>
        <div style="max-height: 200px; overflow-y: auto;">
            @foreach($logs as $log)
            <div class="d-flex align-items-start mb-2 pb-2 border-bottom">
                <div class="bg-gray-100 rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 28px; height: 28px; min-width: 28px;">
                    <i class="fas fa-user text-gray-400" style="font-size: 0.65rem;"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-800">{{ $log->message }}</div>
                    <div class="text-xs text-gray-400">
                        {{ $log->user->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Add Activity -->
    <div class="border-top pt-3 mt-3">
        <h6 class="text-gray-900 font-weight-bold mb-2">
            <i class="fas fa-comment mr-1 text-gray-400"></i> Add Activity
        </h6>
        <div class="d-flex gap-2">
            <input type="hidden" name="ticket_id" value="{{ $ticket['ticket_id'] }}">
            <textarea class="shad-input" id="activity_message" rows="2" placeholder="Add a note or activity..." style="resize: none;"></textarea>
        </div>
        <div class="text-right mt-2">
            <button type="button" class="shad-btn shad-btn-outline shad-btn-sm" id="submitActivityBtn">
                <i class="fas fa-paper-plane mr-1"></i> Add Activity
            </button>
        </div>
    </div>
</form>

