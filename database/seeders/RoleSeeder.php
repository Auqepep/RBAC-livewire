<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'badge_color' => '#dc2626',
                'hierarchy_level' => 100,
                'permissions' => [
                    'manage_system', 'manage_permissions', 'manage_roles',
                    'manage_users', 'view_users', 'edit_user_roles',
                    'manage_groups', 'view_groups', 'assign_group_members', 'manage_group_roles',
                    'create_content', 'edit_content', 'delete_content', 'publish_content',
                    'view_reports', 'export_data',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'manage_department', 'view_team_data', 'approve_requests'
                ]
            ],
            [
                'name' => 'Admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access with most permissions',
                'badge_color' => '#dc2626',
                'hierarchy_level' => 90,
                'permissions' => [
                    'manage_users', 'view_users', 'edit_user_roles',
                    'manage_groups', 'view_groups', 'assign_group_members', 'manage_group_roles',
                    'create_content', 'edit_content', 'delete_content', 'publish_content',
                    'view_reports', 'export_data',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'manage_department', 'view_team_data', 'approve_requests'
                ]
            ],
            [
                'name' => 'IT Manager',
                'display_name' => 'IT Manager',
                'description' => 'IT department management role',
                'badge_color' => '#7c3aed',
                'hierarchy_level' => 80,
                'permissions' => [
                    'view_users', 'manage_groups', 'view_groups', 'assign_group_members', 'manage_group_roles',
                    'create_content', 'edit_content', 'publish_content',
                    'view_reports', 'export_data',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'manage_department', 'view_team_data', 'approve_requests'
                ]
            ],
            [
                'name' => 'Marketing Manager',
                'display_name' => 'Marketing Manager',
                'description' => 'Marketing department management role',
                'badge_color' => '#059669',
                'hierarchy_level' => 80,
                'permissions' => [
                    'view_users', 'manage_groups', 'view_groups', 'assign_group_members', 'manage_group_roles',
                    'create_content', 'edit_content', 'publish_content',
                    'view_reports', 'export_data',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'manage_department', 'view_team_data', 'approve_requests'
                ]
            ],
            [
                'name' => 'HR Manager',
                'display_name' => 'HR Manager',
                'description' => 'Human Resources management role',
                'badge_color' => '#dc2626',
                'hierarchy_level' => 80,
                'permissions' => [
                    'manage_users', 'view_users', 'edit_user_roles',
                    'manage_groups', 'view_groups', 'assign_group_members', 'manage_group_roles',
                    'view_reports', 'export_data',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'manage_department', 'view_team_data', 'approve_requests'
                ]
            ],
            [
                'name' => 'Supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Team supervisor with reporting access',
                'badge_color' => '#0891b2',
                'hierarchy_level' => 60,
                'permissions' => [
                    'view_users', 'view_groups',
                    'create_content', 'edit_content',
                    'view_reports',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'view_team_data', 'approve_requests'
                ]
            ],
            [
                'name' => 'Senior Developer',
                'display_name' => 'Senior Developer',
                'description' => 'Senior technical role with content permissions',
                'badge_color' => '#7c3aed',
                'hierarchy_level' => 55,
                'permissions' => [
                    'create_content', 'edit_content', 'publish_content',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'view_team_data'
                ]
            ],
            [
                'name' => 'Manager',
                'display_name' => 'Manager',
                'description' => 'General management role',
                'badge_color' => '#059669',
                'hierarchy_level' => 70,
                'permissions' => [
                    'view_users', 'view_groups', 'assign_group_members',
                    'create_content', 'edit_content',
                    'view_reports',
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'view_team_data', 'approve_requests'
                ]
            ],
            [
                'name' => 'Staff',
                'display_name' => 'Staff',
                'description' => 'Regular staff member with basic access',
                'badge_color' => '#2563eb',
                'hierarchy_level' => 30,
                'permissions' => [
                    'create_content', 'edit_content',
                    'view_dashboard', 'view_profile', 'edit_profile'
                ]
            ],
            [
                'name' => 'IT Support',
                'display_name' => 'IT Support Staff',
                'description' => 'IT support team member',
                'badge_color' => '#6366f1',
                'hierarchy_level' => 25,
                'permissions' => [
                    'view_dashboard', 'view_profile', 'edit_profile',
                    'view_team_data'
                ]
            ],
            [
                'name' => 'Member',
                'display_name' => 'Member',
                'description' => 'Basic member access',
                'badge_color' => '#6b7280',
                'hierarchy_level' => 10,
                'permissions' => [
                    'view_dashboard', 'view_profile', 'edit_profile'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                array_merge($roleData, ['is_active' => true])
            );

            // Assign permissions to role (only existing permissions)
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            if ($permissionIds->isNotEmpty()) {
                $role->permissions()->sync($permissionIds);
            }
        }

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
