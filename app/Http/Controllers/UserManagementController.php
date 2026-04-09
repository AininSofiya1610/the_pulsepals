<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Show all technician users (non-admin roles).
     */
    public function technicians()
    {
        $technicians = User::with(['roles', 'unit'])
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['System Unit', 'Network & Infrastructure', 'Technical Support']);
            })
            ->orderBy('name')
            ->get();

        return view('users.technicians', compact('technicians'));
    }

    /**
     * Show all users.
     */
    public function index()
    {
        $users = User::with(['roles', 'unit'])->orderBy('name')->get();

        return view('users.index', compact('users'));
    }
}
