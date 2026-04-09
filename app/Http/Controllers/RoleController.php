<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{


    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('settings.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('settings.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name'
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('settings.roles')
            ->with('success', 'Role created successfully!');
    }

    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id
        ]);

        $role->update(['name' => $request->name]);

        return redirect()->route('settings.roles')
            ->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        // Prevent deletion of critical roles
        if (in_array($role->name, ['Admin', 'super-admin'])) {
            return redirect()->route('settings.roles')
                ->with('error', 'Cannot delete system role: ' . $role->name);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('settings.roles')
                ->with('error', 'Cannot delete role with assigned users!');
        }

        $role->delete();

        return redirect()->route('settings.roles')
            ->with('success', 'Role deleted successfully!');
    }

    public function permissions(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            // Group permissions by prefix (e.g., 'view-', 'create-', 'edit-')
            $parts = explode('-', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('settings.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        // Get permission objects from IDs - Spatie requires permission names or objects, not IDs
        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()->route('settings.roles')
            ->with('success', 'Role permissions updated successfully!');
    }
}