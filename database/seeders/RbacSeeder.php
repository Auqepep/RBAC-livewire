<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'Can view users list', 'category' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Can create new users', 'category' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'description' => 'Can edit existing users', 'category' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Can delete users', 'category' => 'users'],
            
            // Role Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'description' => 'Can view roles list', 'category' => 'roles'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'description' => 'Can create new roles', 'category' => 'roles'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'description' => 'Can edit existing roles', 'category' => 'roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'description' => 'Can delete roles', 'category' => 'roles'],
            
            // Permission Management
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'description' => 'Can view permissions list', 'category' => 'permissions'],
            ['name' => 'permissions.create', 'display_name' => 'Create Permissions', 'description' => 'Can create new permissions', 'category' => 'permissions'],
            ['name' => 'permissions.edit', 'display_name' => 'Edit Permissions', 'description' => 'Can edit existing permissions', 'category' => 'permissions'],
            ['name' => 'permissions.delete', 'display_name' => 'Delete Permissions', 'description' => 'Can delete permissions', 'category' => 'permissions'],
            
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Can access dashboard', 'category' => 'dashboard'],
            
            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'description' => 'Can view reports', 'category' => 'reports'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'description' => 'Can export reports', 'category' => 'reports'],
            
            // System Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'description' => 'Can view system settings', 'category' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'description' => 'Can edit system settings', 'category' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create Roles
        $roles = [
            [
                'name' => 'administrator',
                'display_name' => 'Administrator',
                'description' => 'Has full access to all system features and can manage all users, roles, and permissions',
                'badge_color' => 'background-color: #DC2626' // Red
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Can manage users and view reports, but cannot modify system settings or roles',
                'badge_color' => 'background-color: #7C3AED' // Purple
            ],
            [
                'name' => 'editor',
                'display_name' => 'Editor',
                'description' => 'Can create and edit content, view users, but cannot delete or manage roles',
                'badge_color' => 'background-color: #059669' // Green
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Can only view content and basic dashboard, no editing capabilities',
                'badge_color' => 'background-color: #3B82F6' // Blue
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(['name' => $roleData['name']], $roleData);
            
            // Assign permissions to roles
            switch ($roleData['name']) {
                case 'administrator':
                    // Administrator gets all permissions
                    $role->permissions()->sync(Permission::all()->pluck('id'));
                    break;
                    
                case 'manager':
                    // Manager gets user and report permissions
                    $managerPermissions = Permission::whereIn('name', [
                        'users.view', 'users.create', 'users.edit',
                        'dashboard.view', 'reports.view', 'reports.export'
                    ])->pluck('id');
                    $role->permissions()->sync($managerPermissions);
                    break;
                    
                case 'editor':
                    // Editor gets basic permissions
                    $editorPermissions = Permission::whereIn('name', [
                        'users.view', 'dashboard.view', 'reports.view'
                    ])->pluck('id');
                    $role->permissions()->sync($editorPermissions);
                    break;
                    
                case 'viewer':
                    // Viewer gets only view permissions
                    $viewerPermissions = Permission::whereIn('name', [
                        'dashboard.view'
                    ])->pluck('id');
                    $role->permissions()->sync($viewerPermissions);
                    break;
            }
        }

        // Create default admin user if not exists
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@acs-rbac.com'],
            [
                'name' => 'System Administrator',
                'email_verified_at' => now(),
                'password' => bcrypt('admin123'), // Default password
            ]
        );

        // Assign administrator role to admin user
        $adminRole = Role::where('name', 'administrator')->first();
        if ($adminRole && !$adminUser->hasRole('administrator')) {
            $adminUser->assignRole($adminRole);
        }

        $this->command->info('RBAC system seeded successfully!');
        $this->command->info('Default admin user: admin@acs-rbac.com');
        $this->command->info('Roles created: ' . Role::count());
        $this->command->info('Permissions created: ' . Permission::count());
    }
}
