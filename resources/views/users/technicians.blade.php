@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="shad-page-header">
        <div>
            <h1 class="shad-page-title">Technicians</h1>
            <p class="shad-page-description">All staff with technical roles</p>
        </div>
    </div>

    <!-- Technicians Table -->
    <div class="shad-card">
        <div class="shad-card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="shad-card-title">Technical Staff</h2>
                <p class="shad-card-description">System Unit · Network & Infrastructure · Technical Support</p>
            </div>
            <span class="shad-badge shad-badge-default">{{ $technicians->count() }} staff</span>
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
                        @forelse($technicians as $tech)
                        <tr>
                            <td style="font-weight: 500; color: var(--slate-500);">{{ $tech->id }}</td>
                            <td style="font-weight: 600; color: var(--slate-800);">{{ $tech->name }}</td>
                            <td style="color: var(--slate-500);">{{ $tech->email }}</td>
                            <td>
                                @foreach($tech->roles as $role)
                                    <span class="shad-badge shad-badge-info">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td style="color: var(--slate-500);">
                                {{ $tech->unit->name ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="shad-empty">
                                    <div class="shad-empty-icon">
                                        <i class="fas fa-user-cog fa-3x"></i>
                                    </div>
                                    <h3 class="shad-empty-title">No technicians found</h3>
                                    <p class="shad-empty-description">Assign a technical role to a user first.</p>
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
