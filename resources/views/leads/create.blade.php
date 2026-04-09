@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background: var(--slate-50, #f8fafc); min-height: 100vh;">

    <!-- Page Header -->
    <div class="shad-page-header mb-6">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('leads.index') }}" class="shad-btn shad-btn-ghost shad-btn-sm" style="padding: 0.5rem;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="shad-page-title">Add New Lead</h1>
                <p class="shad-page-description">Create a new lead entry for your sales pipeline</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Form Card -->
        <div class="col-lg-8">
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-user-plus mr-2" style="color: var(--slate-400);"></i>
                        Lead Information
                    </h2>
                    <p class="shad-card-description">Enter the details for the new lead</p>
                </div>
                <div class="shad-card-body">
                    <form action="{{ route('leads.store') }}" method="POST">
                        @csrf

                        <!-- Name Field -->
                        <div class="mb-4">
                            <label for="name" class="shad-label">
                                Name <span style="color: #ef4444;">*</span>
                            </label>
                            <input
                                type="text"
                                class="shad-input @error('name') border-danger @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter lead's full name"
                                required
                            >
                            <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                The full name of the potential customer
                            </p>
                            @error('name')
                                <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Contact Information -->
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 1rem;">
                            <i class="fas fa-address-book mr-2" style="color: var(--slate-400);"></i>
                            Contact Information
                        </h3>

                        <div class="row g-4">
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="shad-label">Email</label>
                                <div style="position: relative;">
                                    <i class="fas fa-envelope" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--slate-400); font-size: 0.875rem;"></i>
                                    <input
                                        type="email"
                                        class="shad-input @error('email') border-danger @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="email@example.com"
                                        style="padding-left: 2.5rem;"
                                    >
                                </div>
                                <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                    Primary email for communication
                                </p>
                                @error('email')
                                    <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="shad-label">Phone</label>
                                <div style="position: relative;">
                                    <i class="fas fa-phone" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--slate-400); font-size: 0.875rem;"></i>
                                    <input
                                        type="text"
                                        class="shad-input @error('phone') border-danger @enderror"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="+60 12-345 6789"
                                        style="padding-left: 2.5rem;"
                                    >
                                </div>
                                <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                    Contact phone number
                                </p>
                                @error('phone')
                                    <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Lead Source — DYNAMIC from DB -->
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 1rem;">
                            <i class="fas fa-bullseye mr-2" style="color: var(--slate-400);"></i>
                            Lead Source
                        </h3>

                        <div class="mb-4">
                            <label for="source" class="shad-label">Source</label>
                            <select
                                class="shad-select @error('source') border-danger @enderror"
                                id="source"
                                name="source"
                            >
                                <option value="">Select how the lead found you...</option>
                                {{-- Dynamic options from lead_sources table --}}
                                @forelse($leadSources as $source)
                                    <option value="{{ $source->name }}" {{ old('source') == $source->name ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @empty
                                    <option value="" disabled>No lead sources configured — add them in Settings</option>
                                @endforelse
                            </select>
                            <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                Understanding lead sources helps optimize your marketing.
                                <a href="{{ route('settings.dropdown') }}" style="color: #3b82f6;" target="_blank">
                                    Manage sources →
                                </a>
                            </p>
                            @error('source')
                                <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-4" style="border-color: var(--border-color);">

                        <!-- Assignment -->
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 1rem;">
                            <i class="fas fa-user-check mr-2" style="color: var(--slate-400);"></i>
                            Assignment
                        </h3>

                        <div class="mb-4">
                            <label for="assigned_to" class="shad-label">Assigned To</label>
                            <select
                                class="shad-select @error('assigned_to') border-danger @enderror"
                                id="assigned_to"
                                name="assigned_to"
                            >
                                <option value="">Select team member...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                        👤 {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1" style="font-size: 0.75rem; color: var(--slate-500);">
                                Assign this lead to a sales team member for follow-up
                            </p>
                            @error('assigned_to')
                                <p class="mt-1" style="font-size: 0.75rem; color: #ef4444;">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <hr class="my-4" style="border-color: var(--border-color);">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('leads.index') }}" class="shad-btn shad-btn-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="shad-btn shad-btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                Create Lead
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-3 { gap: 0.75rem; }
.gap-4 { gap: 1rem; }
@media (max-width: 991px) {
    .col-lg-4 { margin-top: 1.5rem; }
}
</style>
@endsection
