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
            // System Management
            [
                'name' => 'manage_system',
                'display_name' => 'Manage System',
                'description' => 'Full system administration access',
                'category' => 'system',
                'is_active' => true,
            ],
            [
                'name' => 'manage_permissions',
                'display_name' => 'Manage Permissions',
                'description' => 'Create, edit, and delete permissions',
                'category' => 'system',
                'is_active' => true,
            ],
            [
                'name' => 'manage_roles',
                'display_name' => 'Manage Roles',
                'description' => 'Create, edit, and delete roles',
                'category' => 'system',
                'is_active' => true,
            ],

            // User Management
            [
                'name' => 'manage_users',
                'display_name' => 'Manage Users',
                'description' => 'Create, edit, and delete users',
                'category' => 'users',
                'is_active' => true,
            ],
            [
                'name' => 'view_users',
                'display_name' => 'View Users',
                'description' => 'View user profiles and information',
                'category' => 'users',
                'is_active' => true,
            ],
            [
                'name' => 'edit_user_roles',
                'display_name' => 'Edit User Roles',
                'description' => 'Assign and remove user roles',
                'category' => 'users',
                'is_active' => true,
            ],

            // Group Management
            [
                'name' => 'manage_groups',
                'display_name' => 'Manage Groups',
                'description' => 'Create, edit, and delete groups',
                'category' => 'groups',
                'is_active' => true,
            ],
            [
                'name' => 'view_groups',
                'display_name' => 'View Groups',
                'description' => 'View group information and members',
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
                'name' => 'manage_group_roles',
                'display_name' => 'Manage Group Roles',
                'description' => 'Assign roles within groups',
                'category' => 'groups',
                'is_active' => true,
            ],

            // Content Management
            [
                'name' => 'create_content',
                'display_name' => 'Create Content',
                'description' => 'Create new content items',
                'category' => 'content',
                'is_active' => true,
            ],
            [
                'name' => 'edit_content',
                'display_name' => 'Edit Content',
                'description' => 'Modify existing content',
                'category' => 'content',
                'is_active' => true,
            ],
            [
                'name' => 'delete_content',
                'display_name' => 'Delete Content',
                'description' => 'Remove content items',
                'category' => 'content',
                'is_active' => true,
            ],
            [
                'name' => 'publish_content',
                'display_name' => 'Publish Content',
                'description' => 'Make content publicly visible',
                'category' => 'content',
                'is_active' => true,
            ],

            // Reports & Analytics
            [
                'name' => 'view_reports',
                'display_name' => 'View Reports',
                'description' => 'Access system reports and analytics',
                'category' => 'reports',
                'is_active' => true,
            ],
            [
                'name' => 'export_data',
                'display_name' => 'Export Data',
                'description' => 'Export system data to files',
                'category' => 'reports',
                'is_active' => true,
            ],

            // Profile Management
            [
                'name' => 'view_profile',
                'display_name' => 'View Profile',
                'description' => 'View user profile information',
                'category' => 'profile',
                'is_active' => true,
            ],
            [
                'name' => 'edit_profile',
                'display_name' => 'Edit Profile',
                'description' => 'Modify profile information',
                'category' => 'profile',
                'is_active' => true,
            ],
            [
                'name' => 'view_dashboard',
                'display_name' => 'View Dashboard',
                'description' => 'Access main dashboard',
                'category' => 'profile',
                'is_active' => true,
            ],

            // Department Management
            [
                'name' => 'manage_department',
                'display_name' => 'Manage Department',
                'description' => 'Oversee department operations',
                'category' => 'department',
                'is_active' => true,
            ],

            // Team Management  
            [
                'name' => 'view_team_data',
                'display_name' => 'View Team Data',
                'description' => 'Access team information and metrics',
                'category' => 'team',
                'is_active' => true,
            ],

            // Approvals
            [
                'name' => 'approve_requests',
                'display_name' => 'Approve Requests',
                'description' => 'Approve or reject system requests',
                'category' => 'approvals',
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
    }
}
