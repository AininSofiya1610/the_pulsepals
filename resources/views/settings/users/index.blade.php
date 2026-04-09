@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">User Management</h1>
            <p class="shad-page-description">Manage users and assign roles</p>
        </div>
        <a href="{{ route('settings.roles') }}" class="shad-btn shad-btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <!-- Users Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">All Users</h2>
                <p class="shad-card-description">Assign roles to control user permissions</p>
            </div>
            <span class="shad-badge shad-badge-default">{{ $users->count() }} users</span>
        </div>
        <div class="shad-card-body p-0">
            <div class="table-responsive">
                <table class="shad-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th>Assign Role</th>
                            <th class="text-right" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td style="font-weight: 500; color: var(--slate-500);">{{ $user->id }}</td>
                            <td>
                                <span style="font-weight: 600; color: var(--slate-800);">{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span class="shad-badge shad-badge-info ml-2">You</span>
                                @endif
                            </td>
                            <td style="color: var(--slate-500);">{{ $user->email }}</td>
                            <td>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="shad-badge shad-badge-success">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="shad-badge shad-badge-warning">No Role</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('settings.users.updateRole', $user) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="shad-select" style="width: 160px;">
                                        {{-- No Role option — auto-selected if user has no role --}}
                                        <option value="no_role" {{ $user->roles->count() === 0 ? 'selected' : '' }}>
                                            No Role
                                        </option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="shad-btn shad-btn-primary shad-btn-sm">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="shad-actions justify-content-end">
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('settings.users.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="shad-btn shad-btn-ghost shad-btn-sm" style="color: #dc2626;" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" class="shad-btn shad-btn-ghost shad-btn-sm" disabled style="opacity: 0.3;" title="Cannot delete yourself">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="shad-empty">
                                    <div class="shad-empty-icon">
                                        <i class="fas fa-users fa-3x"></i>
                                    </div>
                                    <h3 class="shad-empty-title">No users found</h3>
                                    <p class="shad-empty-description">Users will appear here after registration.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
