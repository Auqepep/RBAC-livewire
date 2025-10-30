<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use App\Models\Permission;
use App\Models\GroupMember;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'email_verified_at' => now(),
            ]
        );

        // Create Administrators group
        $adminGroup = Group::firstOrCreate(
            ['name' => 'Administrators'],
            [
                'description' => 'System administrators with full access',
                'is_active' => true,
                'created_by' => $admin->id
            ]
        );

        // Create administrator role
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrator'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system administrator access',
                'is_active' => true
            ]
        );

        // Get all permissions
        $allPermissions = Permission::where('is_active', true)->get();

        // Assign all permissions to admin role using the relationship
        $adminRole->permissions()->syncWithoutDetaching($allPermissions->pluck('id')->toArray());

        // Add admin user to admin group with admin role
        GroupMember::firstOrCreate([
            'user_id' => $admin->id,
            'group_id' => $adminGroup->id,
        ], [
            'role_id' => $adminRole->id,
            'joined_at' => now()
        ]);

        // Create a test user for permission testing
        $testUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
            ]
        );

        // Create Regular Users group
        $userGroup = Group::firstOrCreate(
            ['name' => 'Regular Users'],
            [
                'description' => 'Regular users with limited access',
                'is_active' => true,
                'created_by' => $admin->id
            ]
        );

        // Create member role with limited permissions
        $memberRole = Role::firstOrCreate(
            ['name' => 'member'],
            [
                'display_name' => 'Member',
                'description' => 'Regular member access',
                'is_active' => true
            ]
        );

        // Give member role some basic permissions
        $basicPermissions = Permission::whereIn('name', [
            'view_users',
            'view_groups',
            'view_content'
        ])->get();

        // Assign basic permissions to member role using the relationship
        $memberRole->permissions()->syncWithoutDetaching($basicPermissions->pluck('id')->toArray());

        // Add test user to regular users group
        GroupMember::firstOrCreate([
            'user_id' => $testUser->id,
            'group_id' => $userGroup->id,
        ], [
            'role_id' => $memberRole->id,
            'joined_at' => now()
        ]);

        $this->command->info('Admin seeder completed:');
        $this->command->info("- Admin user: {$admin->email}");
        $this->command->info("- Test user: {$testUser->email}");
        $this->command->info("- Administrators group created with {$allPermissions->count()} permissions");
        $this->command->info("- Regular Users group created with {$basicPermissions->count()} permissions");
    }
}
