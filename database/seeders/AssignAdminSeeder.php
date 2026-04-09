<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'), // Ensure you set a password
                'role' => 'admin', // If using role column
            ]);
        }
        
        // Assign Spatie Role if using permissions
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Admin');
        }
    }
}
