<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use App\Models\GroupMember;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@acs-rbac.com'
        ], [
            'name' => 'System Administrator',
            'password' => bcrypt('admin123'), // Default password
            'email_verified_at' => now()
        ]);

        // Create admin group
        $adminGroup = Group::firstOrCreate([
            'name' => 'System Administrators'
        ], [
            'description' => 'System administrative group with full access',
            'is_active' => true,
            'created_by' => $admin->id
        ]);

        // Get Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($superAdminRole) {
            // Assign admin user to admin group with Super Admin role
            GroupMember::firstOrCreate([
                'group_id' => $adminGroup->id,
                'user_id' => $admin->id
            ], [
                'role_id' => $superAdminRole->id,
                'assigned_by' => $admin->id,
                'joined_at' => now()
            ]);

            $this->command->info("Admin user created and assigned Super Admin role: {$admin->email}");
        } else {
            $this->command->error('Super Admin role not found. Please run RoleSeeder first.');
        }
    }
}
