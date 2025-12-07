<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ============================================
            // GLOBAL PERMISSIONS (System-wide access)
            // ============================================
            
            // System Management (Admin Only)
            [
                'name' => 'manage_system',
                'display_name' => 'Manage System',
                'description' => 'Full system administration access - highest level permission',
                'category' => 'global_system',
                'is_active' => true,
            ],
            [
                'name' => 'manage_permissions',
                'display_name' => 'Manage Permissions',
                'description' => 'Create, edit, and delete system-wide permissions',
                'category' => 'global_system',
                'is_active' => true,
            ],
            [
                'name' => 'manage_global_roles',
                'display_name' => 'Manage Global Roles',
                'description' => 'Create, edit, and delete roles across all groups',
                'category' => 'global_system',
                'is_active' => true,
            ],
            [
                'name' => 'view_system_logs',
                'display_name' => 'View System Logs',
                'description' => 'Access system audit logs and activity history',
                'category' => 'global_system',
                'is_active' => true,
            ],

            // Global User Management
            [
                'name' => 'view_all_users',
                'display_name' => 'View All Users',
                'description' => 'View all users across the entire system',
                'category' => 'global_users',
                'is_active' => true,
            ],
            [
                'name' => 'manage_all_users',
                'display_name' => 'Manage All Users',
                'description' => 'Create, edit, delete, and manage any user in the system',
                'category' => 'global_users',
                'is_active' => true,
            ],
            [
                'name' => 'edit_any_user_roles',
                'display_name' => 'Edit Any User Roles',
                'description' => 'Assign and remove roles for any user in any group',
                'category' => 'global_users',
                'is_active' => true,
            ],
            [
                'name' => 'impersonate_users',
                'display_name' => 'Impersonate Users',
                'description' => 'Log in as another user for support purposes',
                'category' => 'global_users',
                'is_active' => true,
            ],

            // Global Group Management
            [
                'name' => 'view_all_groups',
                'display_name' => 'View All Groups',
                'description' => 'View all groups and their members across the system',
                'category' => 'global_groups',
                'is_active' => true,
            ],
            [
                'name' => 'create_groups',
                'display_name' => 'Create Groups',
                'description' => 'Create new groups in the system',
                'category' => 'global_groups',
                'is_active' => true,
            ],
            [
                'name' => 'edit_own_group',
                'display_name' => 'Edit Own Group',
                'description' => 'Edit the group where user is a manager',
                'category' => 'groups',
                'is_active' => true,
            ],
            [
                'name' => 'manage_own_group_members',
                'display_name' => 'Manage Own Group Members',
                'description' => 'Add, edit, and remove members in own group',
                'category' => 'groups',
                'is_active' => true,
            ],
            [
                'name' => 'assign_group_members',
                'display_name' => 'Assign Group Members',
                'description' => 'Add and remove users from groups',
                'category' => 'groups',
                'is_active' => true,
            ],
            [
                'name' => 'assign_members_any_group',
                'display_name' => 'Assign Members to Any Group',
                'description' => 'Add and remove users from any group',
                'category' => 'global_groups',
                'is_active' => true,
            ],

            // Global Reports & Analytics
            [
                'name' => 'view_all_reports',
                'display_name' => 'View All Reports',
                'description' => 'Access all system reports and analytics across all groups',
                'category' => 'global_reports',
                'is_active' => true,
            ],
            [
                'name' => 'export_system_data',
                'display_name' => 'Export System Data',
                'description' => 'Export data from entire system to files',
                'category' => 'global_reports',
                'is_active' => true,
            ],

            // ============================================
            // LOCAL PERMISSIONS (Group-specific access)
            // ============================================

            // Group Administration (Group Admins)
            [
                'name' => 'manage_own_group',
                'display_name' => 'Manage Own Group',
                'description' => 'Edit group details (name, description) for groups you admin',
                'category' => 'local_group_admin',
                'is_active' => true,
            ],
            [
                'name' => 'manage_group_members',
                'display_name' => 'Manage Group Members',
                'description' => 'Add and remove members within your group',
                'category' => 'local_group_admin',
                'is_active' => true,
            ],
            [
                'name' => 'edit_member_roles_in_group',
                'display_name' => 'Edit Member Roles in Group',
                'description' => 'Change roles of members within your group',
                'category' => 'local_group_admin',
                'is_active' => true,
            ],
            [
                'name' => 'view_group_members',
                'display_name' => 'View Group Members',
                'description' => 'View all members and their roles in your group',
                'category' => 'local_group_admin',
                'is_active' => true,
            ],
            [
                'name' => 'create_group_roles',
                'display_name' => 'Create Group Roles',
                'description' => 'Create custom roles within your group',
                'category' => 'local_group_admin',
                'is_active' => true,
            ],
            [
                'name' => 'delete_group_members',
                'display_name' => 'Remove Group Members',
                'description' => 'Remove members from your group',
                'category' => 'local_group_admin',
                'is_active' => true,
            ],

            // Manager Permissions
            [
                'name' => 'view_team_members',
                'display_name' => 'View Team Members',
                'description' => 'View members in your team/group',
                'category' => 'local_manager',
                'is_active' => true,
            ],
            [
                'name' => 'approve_team_requests',
                'display_name' => 'Approve Team Requests',
                'description' => 'Approve or reject requests from team members',
                'category' => 'local_manager',
                'is_active' => true,
            ],

            // Staff/Member Permissions
            [
                'name' => 'view_own_group',
                'display_name' => 'View Own Group',
                'description' => 'View basic information about groups you belong to',
                'category' => 'local_member',
                'is_active' => true,
            ],
            // NOTE: Removed content & task related permissions - not required for gateway-only groups

            // ============================================
            // COMMON PERMISSIONS (Available to all)
            // ============================================

            // Profile & Dashboard
            [
                'name' => 'view_own_profile',
                'display_name' => 'View Own Profile',
                'description' => 'View your own profile information',
                'category' => 'common',
                'is_active' => true,
            ],
            [
                'name' => 'edit_own_profile',
                'display_name' => 'Edit Own Profile',
                'description' => 'Modify your own profile information',
                'category' => 'common',
                'is_active' => true,
            ],
            [
                'name' => 'view_dashboard',
                'display_name' => 'View Dashboard',
                'description' => 'Access main dashboard',
                'category' => 'common',
                'is_active' => true,
            ],
            [
                'name' => 'view_notifications',
                'display_name' => 'View Notifications',
                'description' => 'View your notifications',
                'category' => 'common',
                'is_active' => true,
            ],

            // ============================================
            // SPECIAL PERMISSIONS
            // ============================================

            // Gateway Access
            [
                'name' => 'gateway_test',
                'display_name' => 'Gateway Test',
                'description' => 'Access to test gateway functionality',
                'category' => 'special',
                'is_active' => true,
            ],
            [
                'name' => 'access_api',
                'display_name' => 'Access API',
                'description' => 'Access system API endpoints',
                'category' => 'special',
                'is_active' => true,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Permissions seeded successfully!');
        $this->command->info('Total permissions: ' . count($permissions));
        $this->command->info('- Global System: 4');
        $this->command->info('- Global Users: 4');
        $this->command->info('- Global Groups: 4');
        $this->command->info('- Global Reports: 2');
        $this->command->info('- Local Group Admin: 6');
    $this->command->info('- Local Manager: 2');
    $this->command->info('- Local Member: 1');
        $this->command->info('- Common: 4');
        $this->command->info('- Special: 2');
    }
}
