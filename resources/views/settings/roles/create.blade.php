@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Create New Role</h1>
            <p class="shad-page-description">Add a new role to the system</p>
        </div>
        <a href="{{ route('settings.roles') }}" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <div class="row">
        <!-- Form Card -->
        <div class="col-lg-8">
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">Role Information</h2>
                    <p class="shad-card-description">Enter the details for the new role</p>
                </div>
                <div class="shad-card-body">
                    <form action="{{ route('settings.roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="shad-label" for="name">Role Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="shad-input @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="e.g., tech-support, manager, viewer"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p style="font-size: 0.8125rem; color: var(--slate-500); margin-top: 0.5rem;">
                                <i class="fas fa-info-circle"></i> Use lowercase with hyphens (e.g., tech-support)
                            </p>
                        </div>

                        <div class="shad-alert shad-alert-info mb-4">
                            <i class="fas fa-lightbulb"></i>
                            <div>After creating the role, you can assign specific permissions from the roles list.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3" style="border-top: 1px solid var(--border-color);">
                            <a href="{{ route('settings.roles') }}" class="shad-btn shad-btn-secondary">Cancel</a>
                            <button type="submit" class="shad-btn shad-btn-primary">
                                <i class="fas fa-plus"></i> Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-lg-4">
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-question-circle mr-2" style="color: var(--slate-400);"></i>
                        Help
                    </h2>
                </div>
                <div class="shad-card-body">
                    <h6 style="font-weight: 600; color: var(--slate-800); margin-bottom: 0.5rem;">Naming Conventions</h6>
                    <ul style="font-size: 0.875rem; color: var(--slate-600); padding-left: 1.25rem; margin-bottom: 1rem;">
                        <li>Use lowercase letters</li>
                        <li>Separate words with hyphens</li>
                        <li>Be descriptive and clear</li>
                    </ul>

                    <h6 style="font-weight: 600; color: var(--slate-800); margin-bottom: 0.5rem;">Examples</h6>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                        <code style="background: var(--slate-100); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8125rem;">tech-support</code>
                        <code style="background: var(--slate-100); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8125rem;">customer-service</code>
                        <code style="background: var(--slate-100); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8125rem;">data-analyst</code>
                    </div>

                    <div class="shad-alert shad-alert-warning" style="font-size: 0.8125rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>Role names cannot be changed after creation.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection