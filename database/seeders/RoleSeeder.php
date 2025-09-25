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
                    'manage_system', 'view_system_logs', 'manage_settings',
                    'manage_users', 'create_users', 'edit_users', 'delete_users', 'view_users',
                    'manage_groups', 'create_groups', 'edit_groups', 'delete_groups', 'view_groups',
                    'manage_roles', 'create_roles', 'edit_roles', 'delete_roles', 'assign_roles',
                    'manage_permissions', 'view_reports', 'generate_reports', 'export_data',
                    'view_dashboard', 'access_admin'
                ]
            ],
            [
                'name' => 'Admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access with most permissions',
                'badge_color' => '#dc2626',
                'hierarchy_level' => 90,
                'permissions' => [
                    'manage_users', 'create_users', 'edit_users', 'view_users',
                    'manage_groups', 'edit_groups', 'view_groups',
                    'assign_roles', 'view_reports', 'generate_reports',
                    'view_dashboard', 'access_admin'
                ]
            ],
            [
                'name' => 'Manager',
                'display_name' => 'Manager',
                'description' => 'Management role with group oversight',
                'badge_color' => '#059669',
                'hierarchy_level' => 70,
                'permissions' => [
                    'view_users', 'view_groups', 'assign_roles',
                    'view_reports', 'view_dashboard'
                ]
            ],
            [
                'name' => 'Staff',
                'display_name' => 'Staff',
                'description' => 'Regular staff member with basic access',
                'badge_color' => '#2563eb',
                'hierarchy_level' => 50,
                'permissions' => [
                    'view_dashboard'
                ]
            ],
            [
                'name' => 'Member',
                'display_name' => 'Member',
                'description' => 'Basic member access',
                'badge_color' => '#6b7280',
                'hierarchy_level' => 10,
                'permissions' => [
                    'view_dashboard'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            // Assign permissions to role
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
