<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            // System Administration (Super Admin only)
            [
                'name' => 'manage_system',
                'display_name' => 'Manage System',
                'description' => 'Full system administration access',
                'category' => 'system',
            ],
            [
                'name' => 'manage_permissions',
                'display_name' => 'Manage Permissions',
                'description' => 'Create, edit, and delete permissions',
                'category' => 'system',
            ],
            [
                'name' => 'manage_roles',
                'display_name' => 'Manage Roles',
                'description' => 'Create, edit, and delete roles',
                'category' => 'system',
            ],

            // User Management (Admin and above)
            [
                'name' => 'manage_users',
                'display_name' => 'Manage Users',
                'description' => 'Create, edit, and delete users',
                'category' => 'users',
            ],
            [
                'name' => 'view_users',
                'display_name' => 'View Users',
                'description' => 'View user profiles and lists',
                'category' => 'users',
            ],
            [
                'name' => 'edit_user_roles',
                'display_name' => 'Edit User Roles',
                'description' => 'Assign and remove user roles',
                'category' => 'users',
            ],

            // Group Management (Manager and above)
            [
                'name' => 'manage_groups',
                'display_name' => 'Manage Groups',
                'description' => 'Create, edit, and delete groups',
                'category' => 'groups',
            ],
            [
                'name' => 'view_groups',
                'display_name' => 'View Groups',
                'description' => 'View group information and members',
                'category' => 'groups',
            ],
            [
                'name' => 'assign_group_members',
                'display_name' => 'Assign Group Members',
                'description' => 'Add and remove group members',
                'category' => 'groups',
            ],
            [
                'name' => 'manage_group_roles',
                'display_name' => 'Manage Group Roles',
                'description' => 'Assign roles within groups',
                'category' => 'groups',
            ],

            // Content Management (Staff and above)
            [
                'name' => 'create_content',
                'display_name' => 'Create Content',
                'description' => 'Create new content and posts',
                'category' => 'content',
            ],
            [
                'name' => 'edit_content',
                'display_name' => 'Edit Content',
                'description' => 'Edit existing content',
                'category' => 'content',
            ],
            [
                'name' => 'delete_content',
                'display_name' => 'Delete Content',
                'description' => 'Delete content and posts',
                'category' => 'content',
            ],
            [
                'name' => 'publish_content',
                'display_name' => 'Publish Content',
                'description' => 'Publish and unpublish content',
                'category' => 'content',
            ],

            // Reports and Analytics (Supervisor and above)
            [
                'name' => 'view_reports',
                'display_name' => 'View Reports',
                'description' => 'Access reports and analytics',
                'category' => 'reports',
            ],
            [
                'name' => 'export_data',
                'display_name' => 'Export Data',
                'description' => 'Export data and generate reports',
                'category' => 'reports',
            ],

            // Basic Permissions (All authenticated users)
            [
                'name' => 'view_dashboard',
                'display_name' => 'View Dashboard',
                'description' => 'Access user dashboard',
                'category' => 'dashboard',
            ],
            [
                'name' => 'view_profile',
                'display_name' => 'View Profile',
                'description' => 'View own profile',
                'category' => 'profile',
            ],
            [
                'name' => 'edit_profile',
                'display_name' => 'Edit Profile',
                'description' => 'Edit own profile information',
                'category' => 'profile',
            ],

            // Team/Department Specific (Varies by group)
            [
                'name' => 'manage_department',
                'display_name' => 'Manage Department',
                'description' => 'Manage departmental activities and resources',
                'category' => 'department',
            ],
            [
                'name' => 'view_team_data',
                'display_name' => 'View Team Data',
                'description' => 'Access team-specific data and information',
                'category' => 'team',
            ],
            [
                'name' => 'approve_requests',
                'display_name' => 'Approve Requests',
                'description' => 'Approve or reject various requests',
                'category' => 'approvals',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                array_merge($permission, ['is_active' => true])
            );
        }
    }
}
