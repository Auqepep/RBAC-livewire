<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use App\Models\Permission;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Hash;

class GroupCentricRbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'category' => 'general'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'category' => 'user_management'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'category' => 'user_management'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'category' => 'user_management'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'category' => 'user_management'],
            ['name' => 'manage_groups', 'display_name' => 'Manage Groups', 'category' => 'group_management'],
            ['name' => 'create_groups', 'display_name' => 'Create Groups', 'category' => 'group_management'],
            ['name' => 'edit_groups', 'display_name' => 'Edit Groups', 'category' => 'group_management'],
            ['name' => 'delete_groups', 'display_name' => 'Delete Groups', 'category' => 'group_management'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'category' => 'role_management'],
            ['name' => 'create_roles', 'display_name' => 'Create Roles', 'category' => 'role_management'],
            ['name' => 'edit_roles', 'display_name' => 'Edit Roles', 'category' => 'role_management'],
            ['name' => 'delete_roles', 'display_name' => 'Delete Roles', 'category' => 'role_management'],
            ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'category' => 'permission_management'],
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'category' => 'reporting'],
            ['name' => 'generate_reports', 'display_name' => 'Generate Reports', 'category' => 'reporting'],
            ['name' => 'approve_requests', 'display_name' => 'Approve Requests', 'category' => 'approval'],
            ['name' => 'assign_tasks', 'display_name' => 'Assign Tasks', 'category' => 'task_management'],
            ['name' => 'view_finances', 'display_name' => 'View Finances', 'category' => 'finance'],
            ['name' => 'manage_finances', 'display_name' => 'Manage Finances', 'category' => 'finance'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create admin user first
        $adminUser = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@acs-rbac.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Create organizational groups
        $groups = [
            [
                'name' => 'IT Support',
                'description' => 'Information Technology Support Department',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Human Resources Department',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance Department',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Administration',
                'description' => 'System Administration',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Sales',
                'description' => 'Sales Department',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ],
        ];

        foreach ($groups as $groupData) {
            Group::create($groupData);
        }

        // Create group-specific roles
        $this->createGroupRoles();

        // Create sample users for different departments
        $this->createSampleUsers($adminUser->id);
    }

    private function createGroupRoles()
    {
        $groups = Group::all();

        foreach ($groups as $group) {
            if ($group->name === 'Administration') {
                // Administration group roles
                $adminRole = $group->createRole([
                    'name' => 'administrator',
                    'display_name' => 'System Administrator',
                    'description' => 'Full system access and control',
                    'badge_color' => '#7c2d12',
                    'hierarchy_level' => 10,
                    'is_active' => true,
                ]);

                $group->createRole([
                    'name' => 'admin',
                    'display_name' => 'Administrator',
                    'description' => 'Administrative access within the system',
                    'badge_color' => '#991b1b',
                    'hierarchy_level' => 8,
                    'is_active' => true,
                ]);

                // Assign all permissions to administrator role
                $adminRole->permissions()->attach(Permission::all());
            } else {
                // Standard departmental roles
                $supervisorRole = $group->createRole([
                    'name' => 'supervisor',
                    'display_name' => $group->name . ' Supervisor',
                    'description' => 'Supervisory role for ' . $group->name,
                    'badge_color' => '#dc2626',
                    'hierarchy_level' => 6,
                    'is_active' => true,
                ]);

                $managerRole = $group->createRole([
                    'name' => 'manager',
                    'display_name' => $group->name . ' Manager',
                    'description' => 'Management role for ' . $group->name,
                    'badge_color' => '#ea580c',
                    'hierarchy_level' => 4,
                    'is_active' => true,
                ]);

                $staffRole = $group->createRole([
                    'name' => 'staff',
                    'display_name' => $group->name . ' Staff',
                    'description' => 'Staff member in ' . $group->name,
                    'badge_color' => '#16a34a',
                    'hierarchy_level' => 1,
                    'is_active' => true,
                ]);

                // Assign permissions based on role hierarchy
                $supervisorRole->permissions()->attach(Permission::whereIn('category', [
                    'general', 'user_management', 'group_management', 'reporting', 'approval', 'task_management'
                ])->get());

                $managerRole->permissions()->attach(Permission::whereIn('category', [
                    'general', 'reporting', 'approval', 'task_management'
                ])->get());

                $staffRole->permissions()->attach(Permission::whereIn('category', [
                    'general'
                ])->get());

                // Add department-specific roles
                if ($group->name === 'Finance') {
                    $group->createRole([
                        'name' => 'accountant',
                        'display_name' => 'Accountant',
                        'description' => 'Accounting specialist',
                        'badge_color' => '#059669',
                        'hierarchy_level' => 2,
                        'is_active' => true,
                    ]);
                }

                if ($group->name === 'Sales') {
                    $group->createRole([
                        'name' => 'team_lead',
                        'display_name' => 'Sales Team Lead',
                        'description' => 'Team leadership role',
                        'badge_color' => '#d97706',
                        'hierarchy_level' => 3,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Add admin user to Administration group with administrator role
        $adminUser = User::where('email', 'admin@acs-rbac.com')->first();
        $adminGroup = Group::where('name', 'Administration')->first();
        $adminRole = $adminGroup->roles()->where('name', 'administrator')->first();
        
        GroupMember::create([
            'group_id' => $adminGroup->id,
            'user_id' => $adminUser->id,
            'role_id' => $adminRole->id,
            'assigned_by' => $adminUser->id,
            'joined_at' => now(),
        ]);
    }

    private function createSampleUsers($adminId)
    {
        $itGroup = Group::where('name', 'IT Support')->first();
        $hrGroup = Group::where('name', 'Human Resources')->first();
        $financeGroup = Group::where('name', 'Finance')->first();
        $salesGroup = Group::where('name', 'Sales')->first();

        // IT Department
        $itSupervisor = User::create([
            'name' => 'John Smith',
            'email' => 'john.smith@acs-rbac.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $supervisorRole = $itGroup->roles()->where('name', 'supervisor')->first();
        GroupMember::create([
            'group_id' => $itGroup->id,
            'user_id' => $itSupervisor->id,
            'role_id' => $supervisorRole->id,
            'assigned_by' => $adminId,
            'joined_at' => now(),
        ]);

        $itStaff = User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@acs-rbac.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $staffRole = $itGroup->roles()->where('name', 'staff')->first();
        GroupMember::create([
            'group_id' => $itGroup->id,
            'user_id' => $itStaff->id,
            'role_id' => $staffRole->id,
            'assigned_by' => $itSupervisor->id,
            'joined_at' => now(),
        ]);

        // HR Department
        $hrManager = User::create([
            'name' => 'Sarah Wilson',
            'email' => 'sarah.wilson@acs-rbac.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $managerRole = $hrGroup->roles()->where('name', 'manager')->first();
        GroupMember::create([
            'group_id' => $hrGroup->id,
            'user_id' => $hrManager->id,
            'role_id' => $managerRole->id,
            'assigned_by' => $adminId,
            'joined_at' => now(),
        ]);

        // Finance Department
        $financeManager = User::create([
            'name' => 'Michael Brown',
            'email' => 'michael.brown@acs-rbac.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $financeManagerRole = $financeGroup->roles()->where('name', 'manager')->first();
        GroupMember::create([
            'group_id' => $financeGroup->id,
            'user_id' => $financeManager->id,
            'role_id' => $financeManagerRole->id,
            'assigned_by' => $adminId,
            'joined_at' => now(),
        ]);

        // Sales Department
        $salesLead = User::create([
            'name' => 'Emma Davis',
            'email' => 'emma.davis@acs-rbac.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $leadRole = $salesGroup->roles()->where('name', 'team_lead')->first();
        GroupMember::create([
            'group_id' => $salesGroup->id,
            'user_id' => $salesLead->id,
            'role_id' => $leadRole->id,
            'assigned_by' => $adminId,
            'joined_at' => now(),
        ]);

        $salesStaff = User::create([
            'name' => 'Robert Miller',
            'email' => 'robert.miller@acs-rbac.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $salesStaffRole = $salesGroup->roles()->where('name', 'staff')->first();
        GroupMember::create([
            'group_id' => $salesGroup->id,
            'user_id' => $salesStaff->id,
            'role_id' => $salesStaffRole->id,
            'assigned_by' => $salesLead->id,
            'joined_at' => now(),
        ]);
    }
}
