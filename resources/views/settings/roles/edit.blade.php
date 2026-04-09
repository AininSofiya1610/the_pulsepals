@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Edit Role</h1>
            <p class="shad-page-description">{{ $role->name }}</p>
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
                    <p class="shad-card-description">Update role details</p>
                </div>
                <div class="shad-card-body">
                    <form action="{{ route('settings.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="shad-label" for="name">Role Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="shad-input @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $role->name) }}"
                                   required
                                   {{ in_array($role->name, ['Admin', 'super-admin']) ? 'readonly' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(in_array($role->name, ['Admin', 'super-admin']))
                                <p style="font-size: 0.8125rem; color: #f59e0b; margin-top: 0.5rem;">
                                    <i class="fas fa-lock"></i> System roles cannot be renamed.
                                </p>
                            @endif
                        </div>

                        <div class="shad-alert shad-alert-info mb-4">
                            <i class="fas fa-users"></i>
                            <div>This role is currently assigned to <strong>{{ $role->users->count() }}</strong> user(s).</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3" style="border-top: 1px solid var(--border-color);">
                            <a href="{{ route('settings.roles') }}" class="shad-btn shad-btn-secondary">Cancel</a>
                            <button type="submit" class="shad-btn shad-btn-primary">
                                <i class="fas fa-save"></i> Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="shad-card mb-4">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-bolt mr-2" style="color: var(--slate-400);"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="shad-card-body">
                    <a href="{{ route('settings.roles.permissions', $role) }}" class="shad-btn shad-btn-secondary d-block mb-2" style="width: 100%;">
                        <i class="fas fa-key"></i> Manage Permissions
                    </a>
                    
                    @if(!in_array($role->name, ['Admin', 'super-admin']) && $role->users->count() == 0)
                    <form action="{{ route('settings.roles.destroy', $role) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this role?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="shad-btn d-block" style="width: 100%; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;">
                            <i class="fas fa-trash"></i> Delete Role
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Role Stats -->
            <div class="shad-card">
                <div class="shad-card-header">
                    <h2 class="shad-card-title">
                        <i class="fas fa-chart-bar mr-2" style="color: var(--slate-400);"></i>
                        Statistics
                    </h2>
                </div>
                <div class="shad-card-body">
                    <div class="mb-3">
                        <label style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Assigned Users</label>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--info); margin: 0;">{{ $role->users->count() }}</p>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Permissions</label>
                        <p style="font-size: 1.5rem; font-weight: 700; color: var(--success); margin: 0;">{{ $role->permissions->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection