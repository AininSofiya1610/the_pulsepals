@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">All Users</h1>
            <p class="shad-page-description">Every registered user in the system</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">User List</h2>
                <p class="shad-card-description">All accounts across all roles</p>
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
                            <th>Role</th>
                            <th>Unit</th>
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
                            <td style="color: var(--slate-500);">
                                {{ $user->unit->name ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
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
