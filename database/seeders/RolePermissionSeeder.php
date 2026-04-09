<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // CREATE ALL PERMISSIONS
        // ==========================================

        $permissions = [
            // Dashboard
            'view dashboard',

            // Finance (Admin only)
            'view-finance',
            'manage-vendors',
            'manage-customers',

            // Mini CRM (All staff)
            'view-crm',
            'manage-leads',
            'manage-deals',
            'manage-tasks',

            // Ticketing
            'create-ticket',
            'view-all-tickets',
            'view-own-tickets',
            'assign-ticket',
            'assign ticket',
            'update-ticket-status',
            'delete-ticket',

            // Settings & User Management (Admin only)
            'access-settings',
            'manage-roles',
            'manage-users',

            // Users Menu Permissions
            'view users',
            'manage roles',
            'manage permissions',
            'view technicians',
            'view units',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ==========================================
        // CREATE ROLES & ASSIGN PERMISSIONS
        // ==========================================

        // ----- ADMIN: Full Access (all 17 permissions) -----
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // ----- SYSTEM UNIT (9 permissions) -----
        $systemUnit = Role::firstOrCreate(['name' => 'System Unit', 'guard_name' => 'web']);
        $systemUnit->syncPermissions([
            'view dashboard',
            'view-crm',
            'manage-leads',
            'manage-deals',
            'manage-tasks',
            'create-ticket',
            'view-own-tickets',
            'assign-ticket',
            'update-ticket-status',
        ]);

        // ----- NETWORK & INFRASTRUCTURE (9 permissions) -----
        $network = Role::firstOrCreate(['name' => 'Network & Infrastructure', 'guard_name' => 'web']);
        $network->syncPermissions([
            'view dashboard',
            'view-crm',
            'manage-leads',
            'manage-deals',
            'manage-tasks',
            'create-ticket',
            'view-own-tickets',
            'assign-ticket',
            'update-ticket-status',
        ]);

        // ----- TECHNICAL SUPPORT (9 permissions) -----
        $techSupport = Role::firstOrCreate(['name' => 'Technical Support', 'guard_name' => 'web']);
        $techSupport->syncPermissions([
            'view dashboard',
            'view-crm',
            'manage-leads',
            'manage-deals',
            'manage-tasks',
            'create-ticket',
            'view-own-tickets',
            'assign-ticket',
            'update-ticket-status',
        ]);

        // ==========================================
        // ASSIGN ADMIN ROLE TO FIRST USER
        // ==========================================
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->assignRole('Admin');
            $this->command->info("✅ Assigned Admin role to user: {$firstUser->email}");
        }

        $this->command->info('✅ All roles and permissions seeded successfully!');
    }
}