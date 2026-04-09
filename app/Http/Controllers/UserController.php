<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::all();
        
        return view('settings.users.index', compact('users', 'roles'));
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        if ($request->role === 'no_role') {
            // Remove all roles — user becomes roleless
            $user->syncRoles([]);

            return redirect()->route('settings.users')
                ->with('success', "Role for {$user->name} has been removed.");
        }

        // Validate that the selected role actually exists
        if (!Role::where('name', $request->role)->exists()) {
            return redirect()->back()->with('error', 'Invalid role selected.');
        }

        // Remove all existing roles and assign new one
        $user->syncRoles([$request->role]);

        return redirect()->route('settings.users')
            ->with('success', "Role for {$user->name} updated to {$request->role}!");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('settings.users')
            ->with('success', 'User deleted successfully!');
    }
}
