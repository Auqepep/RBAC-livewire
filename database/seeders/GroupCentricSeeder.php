<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use App\Models\Permission;
use App\Models\GroupMember;

class GroupCentricSeeder extends Seeder
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

        // Create basic permissions
        $permissions = [
            // User management
            ['name' => 'view_users', 'display_name' => 'View Users', 'category' => 'user_management'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'category' => 'user_management'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'category' => 'user_management'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'category' => 'user_management'],
            
            // Group management
            ['name' => 'view_groups', 'display_name' => 'View Groups', 'category' => 'group_management'],
            ['name' => 'create_groups', 'display_name' => 'Create Groups', 'category' => 'group_management'],
            ['name' => 'edit_groups', 'display_name' => 'Edit Groups', 'category' => 'group_management'],
            ['name' => 'delete_groups', 'display_name' => 'Delete Groups', 'category' => 'group_management'],
            
            // Content management
            ['name' => 'view_content', 'display_name' => 'View Content', 'category' => 'content'],
            ['name' => 'create_content', 'display_name' => 'Create Content', 'category' => 'content'],
            ['name' => 'edit_content', 'display_name' => 'Edit Content', 'category' => 'content'],
            ['name' => 'delete_content', 'display_name' => 'Delete Content', 'category' => 'content'],
            ['name' => 'publish_content', 'display_name' => 'Publish Content', 'category' => 'content'],
            
            // Reports
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'category' => 'reports'],
            ['name' => 'generate_reports', 'display_name' => 'Generate Reports', 'category' => 'reports'],
            
            // System
            ['name' => 'system_admin', 'display_name' => 'System Administration', 'category' => 'system'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create generic roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full administrative access within a group',
                'hierarchy_level' => 90,
                'badge_color' => '#dc2626',
                'permissions' => ['view_users', 'create_users', 'edit_users', 'delete_users', 'view_groups', 'edit_groups', 'view_content', 'create_content', 'edit_content', 'delete_content', 'publish_content', 'view_reports', 'generate_reports']
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Management role with oversight responsibilities',
                'hierarchy_level' => 70,
                'badge_color' => '#2563eb',
                'permissions' => ['view_users', 'view_content', 'create_content', 'edit_content', 'publish_content', 'view_reports', 'generate_reports']
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Supervisory role with limited management capabilities',
                'hierarchy_level' => 50,
                'badge_color' => '#7c3aed',
                'permissions' => ['view_users', 'view_content', 'create_content', 'edit_content', 'view_reports']
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff Member',
                'description' => 'Regular staff with basic access',
                'hierarchy_level' => 30,
                'badge_color' => '#059669',
                'permissions' => ['view_content', 'create_content', 'edit_content']
            ],
            [
                'name' => 'intern',
                'display_name' => 'Intern',
                'description' => 'Intern with limited access',
                'hierarchy_level' => 10,
                'badge_color' => '#d97706',
                'permissions' => ['view_content']
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'hierarchy_level' => $roleData['hierarchy_level'],
                    'badge_color' => $roleData['badge_color'],
                ]
            );

            // Attach permissions
            $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
            $role->permissions()->sync($permissions->pluck('id'));
        }

        // Create sample groups
        $groups = [
            [
                'name' => 'IT Support',
                'description' => 'Information Technology Support Department',
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Human Resources Department',
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing and Communications Department',
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance and Accounting Department',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($groups as $groupData) {
            Group::firstOrCreate(
                ['name' => $groupData['name']],
                $groupData
            );
        }

        // Create sample users
        $users = [
            ['name' => 'John Manager', 'email' => 'john.manager@example.com'],
            ['name' => 'Jane Staff', 'email' => 'jane.staff@example.com'],
            ['name' => 'Bob Supervisor', 'email' => 'bob.supervisor@example.com'],
            ['name' => 'Alice Intern', 'email' => 'alice.intern@example.com'],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['email_verified_at' => now()])
            );
        }

        // Get created entities
        $itGroup = Group::where('name', 'IT Support')->first();
        $hrGroup = Group::where('name', 'Human Resources')->first();
        $marketingGroup = Group::where('name', 'Marketing')->first();
        
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $supervisorRole = Role::where('name', 'supervisor')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $internRole = Role::where('name', 'intern')->first();
        
        $systemAdminPermission = Permission::where('name', 'system_admin')->first();
        
        // Assign system admin permission to admin user directly (not through group)
        // This could be done through a special "System" group or direct assignment
        
        // Assign users to groups with roles
        $assignments = [
            // IT Support Group
            ['user_email' => 'john.manager@example.com', 'group' => $itGroup, 'role' => $managerRole],
            ['user_email' => 'bob.supervisor@example.com', 'group' => $itGroup, 'role' => $supervisorRole],
            ['user_email' => 'jane.staff@example.com', 'group' => $itGroup, 'role' => $staffRole],
            
            // HR Group  
            ['user_email' => 'jane.staff@example.com', 'group' => $hrGroup, 'role' => $supervisorRole], // Jane is staff in IT but supervisor in HR
            ['user_email' => 'alice.intern@example.com', 'group' => $hrGroup, 'role' => $internRole],
            
            // Marketing Group
            ['user_email' => 'bob.supervisor@example.com', 'group' => $marketingGroup, 'role' => $managerRole], // Bob is supervisor in IT but manager in Marketing
            ['user_email' => 'alice.intern@example.com', 'group' => $marketingGroup, 'role' => $staffRole], // Alice is intern in HR but staff in Marketing
        ];

        foreach ($assignments as $assignment) {
            $user = User::where('email', $assignment['user_email'])->first();
            
            GroupMember::firstOrCreate(
                [
                    'group_id' => $assignment['group']->id,
                    'user_id' => $user->id,
                ],
                [
                    'role_id' => $assignment['role']->id,
                    'assigned_by' => $admin->id,
                    'joined_at' => now(),
                ]
            );
        }

        $this->command->info('Group-centric RBAC system seeded successfully!');
        $this->command->info('Example users:');
        $this->command->info('- admin@example.com (System Admin)');
        $this->command->info('- john.manager@example.com (Manager in IT Support)');
        $this->command->info('- jane.staff@example.com (Staff in IT Support, Supervisor in HR)');
        $this->command->info('- bob.supervisor@example.com (Supervisor in IT Support, Manager in Marketing)');
        $this->command->info('- alice.intern@example.com (Intern in HR, Staff in Marketing)');
    }
}
