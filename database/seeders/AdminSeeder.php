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
                'is_super_admin' => true,
            ]
        );
        
        // Ensure is_super_admin is set for existing admin
        if (!$admin->is_super_admin) {
            $admin->update(['is_super_admin' => true]);
        }

        // Create Administrators group
        $adminGroup = Group::firstOrCreate(
            ['name' => 'Administrators'],
            [
                'description' => 'System administrators with full access',
                'is_active' => true,
                'created_by' => $admin->id
            ]
        );

        // Create administrator role for Administrators group
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrator', 'group_id' => $adminGroup->id],
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

        // Create member role with limited permissions for Regular Users group
        $memberRole = Role::firstOrCreate(
            ['name' => 'member', 'group_id' => $userGroup->id],
            [
                'display_name' => 'Member',
                'description' => 'Regular member access',
                'hierarchy_level' => 10,
                'badge_color' => '#6b7280',
                'is_active' => true
            ]
        );

        // Create staff role for gateway access in Regular Users group
        $staffRole = Role::firstOrCreate(
            ['name' => 'staff', 'group_id' => $userGroup->id],
            [
                'display_name' => 'Staff',
                'description' => 'Staff member with gateway access',
                'hierarchy_level' => 30,
                'badge_color' => '#059669',
                'is_active' => true
            ]
        );

        // Create manager role for gateway access in Regular Users group
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager', 'group_id' => $userGroup->id],
            [
                'display_name' => 'Manager',
                'description' => 'Manager with elevated gateway access',
                'hierarchy_level' => 70,
                'badge_color' => '#f59e0b',
                'is_active' => true
            ]
        );

        // Give member role some basic permissions (Staff/Member level)
        $basicPermissions = Permission::whereIn('name', [
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications'
        ])->get();

        // Assign basic permissions to member role using the relationship
        $memberRole->permissions()->syncWithoutDetaching($basicPermissions->pluck('id')->toArray());

        // Give staff role more permissions including local group admin capabilities
        $staffPermissions = Permission::whereIn('name', [
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications',
            'view_group_members',
            'gateway_test'
        ])->get();
        $staffRole->permissions()->syncWithoutDetaching($staffPermissions->pluck('id')->toArray());

        // Give manager role supervisor/manager permissions
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

        // Create group-specific roles for Gateway Test Group
        $gatewayAdminRole = Role::firstOrCreate(
            ['name' => 'administrator', 'group_id' => $gatewayTestGroup->id],
            [
                'display_name' => 'Gateway Administrator',
                'description' => 'Administrator for Gateway Test Group',
                'badge_color' => '#ef4444',
                'hierarchy_level' => 100,
                'is_active' => true,
                'group_id' => $gatewayTestGroup->id
            ]
        );

        $gatewayStaffRole = Role::firstOrCreate(
            ['name' => 'staff', 'group_id' => $gatewayTestGroup->id],
            [
                'display_name' => 'Gateway Staff',
                'description' => 'Staff for Gateway Test Group',
                'badge_color' => '#3b82f6',
                'hierarchy_level' => 10,
                'is_active' => true,
                'group_id' => $gatewayTestGroup->id
            ]
        );

        $gatewayManagerRole = Role::firstOrCreate(
            ['name' => 'manager', 'group_id' => $gatewayTestGroup->id],
            [
                'display_name' => 'Gateway Manager',
                'description' => 'Manager for Gateway Test Group',
                'badge_color' => '#f59e0b',
                'hierarchy_level' => 70, // Changed from 50 to 70
                'is_active' => true,
                'group_id' => $gatewayTestGroup->id
            ]
        );

        $gatewayMemberRole = Role::firstOrCreate(
            ['name' => 'member', 'group_id' => $gatewayTestGroup->id],
            [
                'display_name' => 'Gateway Member',
                'description' => 'Regular member for Gateway Test Group (no gateway access)',
                'badge_color' => '#6b7280',
                'hierarchy_level' => 5,
                'is_active' => true,
                'group_id' => $gatewayTestGroup->id
            ]
        );

        // Assign permissions to Gateway Test Group roles
        $gatewayAdminPermissions = Permission::whereIn('name', [
            'manage_own_group',
            'manage_group_members',
            'edit_member_roles_in_group',
            'view_group_members',
            'create_group_roles',
            'delete_group_members',
            'view_team_members',
            'approve_team_requests',
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications',
            'gateway_test'
        ])->get();
        $gatewayAdminRole->permissions()->syncWithoutDetaching($gatewayAdminPermissions->pluck('id')->toArray());

        $gatewayManagerPermissions = Permission::whereIn('name', [
            'edit_member_roles_in_group',
            'view_group_members',
            'view_team_members',
            'approve_team_requests',
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications',
            'gateway_test'
        ])->get();
        $gatewayManagerRole->permissions()->syncWithoutDetaching($gatewayManagerPermissions->pluck('id')->toArray());

        $gatewayStaffPermissions = Permission::whereIn('name', [
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications',
            'view_group_members',
            'gateway_test'
        ])->get();
        $gatewayStaffRole->permissions()->syncWithoutDetaching($gatewayStaffPermissions->pluck('id')->toArray());

        $gatewayMemberPermissions = Permission::whereIn('name', [
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications'
        ])->get();
        $gatewayMemberRole->permissions()->syncWithoutDetaching($gatewayMemberPermissions->pluck('id')->toArray());

        // Add admin to gateway test group as administrator
        GroupMember::firstOrCreate([
            'user_id' => $admin->id,
            'group_id' => $gatewayTestGroup->id,
        ], [
            'role_id' => $gatewayAdminRole->id,
            'joined_at' => now()
        ]);

        // Add test user to gateway test group as member (no gateway access)
        GroupMember::firstOrCreate([
            'user_id' => $testUser->id,
            'group_id' => $gatewayTestGroup->id,
        ], [
            'role_id' => $gatewayMemberRole->id,
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
            'role_id' => $gatewayStaffRole->id,
            'joined_at' => now()
        ]);

        // Add manager user to gateway test group as manager
        GroupMember::firstOrCreate([
            'user_id' => $managerUser->id,
            'group_id' => $gatewayTestGroup->id,
        ], [
            'role_id' => $gatewayManagerRole->id,
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

        // Create a second test group to demonstrate group-specific roles
        $hrGroup = Group::firstOrCreate(
            ['name' => 'HR Department'],
            [
                'description' => 'Human Resources Department',
                'is_active' => true,
                'created_by' => $admin->id
            ]
        );

        // Create HR-specific roles
        $hrManagerRole = Role::firstOrCreate(
            ['name' => 'manager', 'group_id' => $hrGroup->id],
            [
                'display_name' => 'HR Manager',
                'description' => 'Manager for HR Department',
                'badge_color' => '#8b5cf6',
                'hierarchy_level' => 70, // Changed from 50 to 70
                'is_active' => true,
                'group_id' => $hrGroup->id
            ]
        );

        $hrStaffRole = Role::firstOrCreate(
            ['name' => 'staff', 'group_id' => $hrGroup->id],
            [
                'display_name' => 'HR Staff',
                'description' => 'Staff for HR Department',
                'badge_color' => '#3b82f6',
                'hierarchy_level' => 10,
                'is_active' => true,
                'group_id' => $hrGroup->id
            ]
        );

        // Assign permissions to HR Department roles
        $hrManagerPermissions = Permission::whereIn('name', [
            'edit_member_roles_in_group',
            'manage_group_members',
            'view_group_members',
            'view_team_members',
            'approve_team_requests',
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications'
        ])->get();
        $hrManagerRole->permissions()->syncWithoutDetaching($hrManagerPermissions->pluck('id')->toArray());

        $hrStaffPermissions = Permission::whereIn('name', [
            'view_own_group',
            'view_own_profile',
            'edit_own_profile',
            'view_dashboard',
            'view_notifications',
            'view_group_members'
        ])->get();
        $hrStaffRole->permissions()->syncWithoutDetaching($hrStaffPermissions->pluck('id')->toArray());

        // Add staff user as manager in HR (different role than in Gateway Test Group)
        GroupMember::firstOrCreate([
            'user_id' => $staffUser->id,
            'group_id' => $hrGroup->id,
        ], [
            'role_id' => $hrManagerRole->id,
            'joined_at' => now()
        ]);

        // Add manager user as staff in HR (different role than in Gateway Test Group)
        GroupMember::firstOrCreate([
            'user_id' => $managerUser->id,
            'group_id' => $hrGroup->id,
        ], [
            'role_id' => $hrStaffRole->id,
            'joined_at' => now()
        ]);

        $this->command->info('Admin seeder completed:');
        $this->command->info("- Admin user: {$admin->email} (gateway access)");
        $this->command->info("- Test user: {$testUser->email} (no gateway access)");
        $this->command->info("- Staff user: {$staffUser->email} (gateway access in Gateway Test Group, manager in HR)");
        $this->command->info("- Manager user: {$managerUser->email} (gateway access in Gateway Test Group, staff in HR)");
        $this->command->info("- Administrators group created with {$allPermissions->count()} permissions");
        $this->command->info("- Regular Users group created with {$basicPermissions->count()} permissions");
        $this->command->info("- Gateway Test Group created for testing role-based access");
        $this->command->info("- HR Department created to demonstrate group-specific roles");
        $this->command->info("- Gateway route: /my-groups/{group_id}/gateway");
        $this->command->info("- Users now have DIFFERENT roles in DIFFERENT groups!");
        $this->command->info("- Role access is GROUP-SPECIFIC - same user, different roles per group");
    }
}
