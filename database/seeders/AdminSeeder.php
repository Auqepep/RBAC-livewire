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
                'hierarchy_level' => 100,
                'badge_color' => '#dc2626',
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
                'hierarchy_level' => 10,
                'badge_color' => '#6b7280',
                'is_active' => true
            ]
        );

        // Create staff role for gateway access
        $staffRole = Role::firstOrCreate(
            ['name' => 'staff'],
            [
                'display_name' => 'Staff',
                'description' => 'Staff member with gateway access',
                'hierarchy_level' => 30,
                'badge_color' => '#059669',
                'is_active' => true
            ]
        );

        // Create manager role for gateway access
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Manager with elevated gateway access',
                'hierarchy_level' => 70,
                'badge_color' => '#f59e0b',
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

        // Give staff role more permissions including gateway access
        $staffPermissions = Permission::whereIn('name', [
            'view_users',
            'view_groups',
            'view_content',
            'edit_content',
            'manage_group_roles'
        ])->get();
        $staffRole->permissions()->syncWithoutDetaching($staffPermissions->pluck('id')->toArray());

        // Give manager role even more permissions
        $managerPermissions = Permission::whereIn('name', [
            'view_users',
            'view_groups',
            'view_content',
            'edit_content',
            'manage_group_roles',
            'assign_group_members',
            'edit_own_group',
            'manage_own_group_members'
        ])->get();
        $managerRole->permissions()->syncWithoutDetaching($managerPermissions->pluck('id')->toArray());

        // Create a Gateway Test Group for testing
        $gatewayTestGroup = Group::firstOrCreate(
            ['name' => 'Gateway Test Group'],
            [
                'description' => 'Test group for gateway access functionality',
                'is_active' => true,
                'created_by' => $admin->id
            ]
        );

        // Add admin to gateway test group as administrator
        GroupMember::firstOrCreate([
            'user_id' => $admin->id,
            'group_id' => $gatewayTestGroup->id,
        ], [
            'role_id' => $adminRole->id,
            'joined_at' => now()
        ]);

        // Add test user to gateway test group as member (no gateway access)
        GroupMember::firstOrCreate([
            'user_id' => $testUser->id,
            'group_id' => $gatewayTestGroup->id,
        ], [
            'role_id' => $memberRole->id,
            'joined_at' => now()
        ]);

        // Create additional test users for different roles
        $staffUser = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'email_verified_at' => now(),
            ]
        );

        $managerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'email_verified_at' => now(),
            ]
        );

        // Add staff user to gateway test group as staff
        GroupMember::firstOrCreate([
            'user_id' => $staffUser->id,
            'group_id' => $gatewayTestGroup->id,
        ], [
            'role_id' => $staffRole->id,
            'joined_at' => now()
        ]);

        // Add manager user to gateway test group as manager
        GroupMember::firstOrCreate([
            'user_id' => $managerUser->id,
            'group_id' => $gatewayTestGroup->id,
        ], [
            'role_id' => $managerRole->id,
            'joined_at' => now()
        ]);

        // Add test user to regular users group
        GroupMember::firstOrCreate([
            'user_id' => $testUser->id,
            'group_id' => $userGroup->id,
        ], [
            'role_id' => $memberRole->id,
            'joined_at' => now()
        ]);

        $this->command->info('Admin seeder completed:');
        $this->command->info("- Admin user: {$admin->email} (gateway access)");
        $this->command->info("- Test user: {$testUser->email} (no gateway access)");
        $this->command->info("- Staff user: {$staffUser->email} (gateway access)");
        $this->command->info("- Manager user: {$managerUser->email} (gateway access)");
        $this->command->info("- Administrators group created with {$allPermissions->count()} permissions");
        $this->command->info("- Regular Users group created with {$basicPermissions->count()} permissions");
        $this->command->info("- Gateway Test Group created for testing role-based access");
        $this->command->info("- Gateway route: /my-groups/{group_id}/gateway");
    }
}
