@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Edit Ticket</h1>
            <p class="shad-page-description">
                <span class="font-mono" style="color: var(--slate-500);">{{ $ticket['ticket_id'] }}</span>
            </p>
        </div>
        <a href="{{ route('tickets.index') }}" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Tickets
        </a>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">Ticket Details</h2>
                    <p class="shad-card-description">Update ticket information below</p>
                </div>
                <div class="shad-card-body">
                    <form action="{{ route('tickets.update', $ticket['ticket_id']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="shad-label" for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" class="shad-input" id="title" name="title" value="{{ $ticket['title'] }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="shad-label" for="unit">Operational Unit <span class="text-danger">*</span></label>
                                <select class="shad-select" id="unit" name="unit" required>
                                    <option value="">Select unit...</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->name }}" {{ $ticket['unit'] == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="shad-label" for="ticket_type">Ticket Type <span class="text-danger">*</span></label>
                                <select class="shad-select" id="ticket_type" name="ticket_type" required>
                                    <option value="">Select type...</option>
                                    @foreach($ticketTypes as $type)
                                        <option value="{{ $type->value }}" {{ ($ticket['ticket_type'] ?? '') == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="shad-label" for="priority">Priority <span class="text-danger">*</span></label>
                                <select class="shad-select" id="priority" name="priority" required>
                                    <option value="">Select priority...</option>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->value }}" {{ $ticket['priority'] == $priority->value ? 'selected' : '' }}>{{ $priority->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="shad-label" for="category">Help Topic <span class="text-danger">*</span></label>
                                <select class="shad-select" id="category" name="category" required>
                                    <option value="">Select topic...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->value }}" {{ $ticket['category'] == $category->value ? 'selected' : '' }}>{{ $category->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="shad-label" for="description">Description</label>
                            <textarea class="shad-input" id="description" name="description" rows="5" style="resize: vertical;">{{ $ticket['description'] ?? '' }}</textarea>
                        </div>

                        @can('assign ticket')
                        <div class="mb-4">
                            <label class="shad-label" for="assigned_to">Assign To</label>
                            <select class="shad-select" id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $staff)
                                    <option value="{{ $staff->id }}" {{ ($ticket['assigned_to_id'] ?? null) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endcan

                        <div class="d-flex justify-content-end gap-2 pt-3" style="border-top: 1px solid var(--border-color);">
                            <a href="{{ route('tickets.index') }}" class="shad-btn shad-btn-secondary">Cancel</a>
                            <button type="submit" class="shad-btn shad-btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-user mr-2" style="color: var(--slate-400);"></i>
                        Requestor Info
                    </h2>
                </div>
                <div class="shad-card-body">
                    <div class="mb-3">
                        <label style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.05em;">Name</label>
                        <p style="font-weight: 500; color: var(--slate-800); margin: 0;">{{ $ticket['full_name'] }}</p>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.05em;">Email</label>
                        <p style="font-weight: 500; color: var(--slate-800); margin: 0;">{{ $ticket['email'] }}</p>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.05em;">Unit</label>
                        <p style="font-weight: 500; color: var(--slate-800); margin: 0;">{{ $ticket['unit'] }}</p>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.05em;">Created</label>
                        <p style="font-weight: 500; color: var(--slate-800); margin: 0;">{{ $ticket['created_at']->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
