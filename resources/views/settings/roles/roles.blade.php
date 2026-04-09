@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-shield"></i> Role Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('tickets.settings') }}">
                <i class="fas fa-sliders-h"></i> Dropdown Config
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('tickets.settings.roles') }}">
                <i class="fas fa-user-shield"></i> Role Management
            </a>
        </li>
    </ul>

    <div class="row">
        
        <!-- Roles Card -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-id-badge"></i> System Roles</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td><strong>{{ ucfirst($role->name) }}</strong></td>
                                <td>
                                    @foreach($role->permissions as $perm)
                                        <span class="badge badge-info">{{ $perm->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editRoleModal-{{ $role->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(!in_array($role->name, ['admin', 'manager']))
                                    <form action="{{ route('tickets.settings.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Edit Role Modal -->
                            <div class="modal fade" id="editRoleModal-{{ $role->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('tickets.settings.roles.update', $role->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Role: {{ $role->name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <label><strong>Permissions:</strong></label>
                                                <div class="row">
                                                    @foreach($permissions as $perm)
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                                   value="{{ $perm->name }}" id="perm-{{ $role->id }}-{{ $perm->id }}"
                                                                   {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm-{{ $role->id }}-{{ $perm->id }}">
                                                                {{ $perm->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save Permissions</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Add New Role -->
                    <hr>
                    <h6>Create New Role</h6>
                    <form action="{{ route('tickets.settings.roles.store') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Role name..." required>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Role Assignment -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-users-cog"></i> Assign Roles to Users</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Current Role</th>
                                <th>Assign</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name ?? $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $r)
                                        <span class="badge badge-primary">{{ $r->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <form action="{{ route('tickets.settings.roles.assign') }}" method="POST" class="form-inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <select name="role" class="form-control form-control-sm mr-2" required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
