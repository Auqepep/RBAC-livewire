<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use App\Models\Permission;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Hash;

class PureGroupBasedRbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions first
        $this->createPermissions();

        // Create the Administrators group first (without a creator)
        $adminGroup = Group::create([
            'name' => 'Administrators',
            'description' => 'System Administrators - Manage the entire system',
            'is_active' => true,
            'created_by' => null, // No creator needed for the initial admin group
        ]);

        // Create other organizational groups
        $groups = $this->createOrganizationalGroups();

        // Create group-specific roles
        $this->createGroupRoles($adminGroup, $groups);

        // Create initial system administrator user
        $this->createInitialSystemAdmin($adminGroup);

        // Create sample users for different departments
        $this->createSampleUsers($groups);
    }

    private function createPermissions(): void
    {
        $permissions = [
            // System-level permissions (for Administrators group)
            ['name' => 'manage_system', 'display_name' => 'Manage System', 'category' => 'system'],
            ['name' => 'manage_all_groups', 'display_name' => 'Manage All Groups', 'category' => 'system'],
            ['name' => 'manage_all_users', 'display_name' => 'Manage All Users', 'category' => 'system'],
            ['name' => 'manage_all_roles', 'display_name' => 'Manage All Roles', 'category' => 'system'],
            ['name' => 'view_system_logs', 'display_name' => 'View System Logs', 'category' => 'system'],
            
            // General permissions
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
    }

    private function createOrganizationalGroups(): array
    {
        $groupsData = [
            [
                'name' => 'IT Support',
                'description' => 'Information Technology Support Department',
                'is_active' => true,
                'created_by' => null, // Will be set later
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Human Resources Department',
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance Department',
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'name' => 'Sales',
                'description' => 'Sales Department',
                'is_active' => true,
                'created_by' => null,
            ],
            [
                'name' => 'Operations',
                'description' => 'Operations Department',
                'is_active' => true,
                'created_by' => null,
            ],
        ];

        $groups = [];
        foreach ($groupsData as $groupData) {
            $groups[] = Group::create($groupData);
        }

        return $groups;
    }

    private function createGroupRoles(Group $adminGroup, array $organizationalGroups): void
    {
        // Create roles for Administrators group
        $adminRoles = [
            [
                'name' => 'Super Admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'badge_color' => '#dc2626',
                'hierarchy_level' => 100,
                'group_id' => $adminGroup->id,
            ],
            [
                'name' => 'System Admin',
                'display_name' => 'System Administrator',
                'description' => 'System administration with most permissions',
                'badge_color' => '#ea580c',
                'hierarchy_level' => 90,
                'group_id' => $adminGroup->id,
            ],
            [
                'name' => 'Group Admin',
                'display_name' => 'Group Administrator',
                'description' => 'Can manage specific groups and users',
                'badge_color' => '#f59e0b',
                'hierarchy_level' => 80,
                'group_id' => $adminGroup->id,
            ],
        ];

        foreach ($adminRoles as $roleData) {
            Role::create($roleData);
        }

        // Create standard roles for organizational groups
        $standardRoles = [
            [
                'name' => 'Manager',
                'display_name' => 'Manager',
                'description' => 'Department manager with full department access',
                'badge_color' => '#2563eb',
                'hierarchy_level' => 70,
            ],
            [
                'name' => 'Supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Team supervisor with limited management access',
                'badge_color' => '#7c3aed',
                'hierarchy_level' => 50,
            ],
            [
                'name' => 'Senior Staff',
                'display_name' => 'Senior Staff',
                'description' => 'Senior staff member with additional responsibilities',
                'badge_color' => '#059669',
                'hierarchy_level' => 30,
            ],
            [
                'name' => 'Staff',
                'display_name' => 'Staff',
                'description' => 'Regular staff member',
                'badge_color' => '#6366f1',
                'hierarchy_level' => 10,
            ],
        ];

        // Create these roles for each organizational group
        foreach ($organizationalGroups as $group) {
            foreach ($standardRoles as $roleData) {
                $roleData['group_id'] = $group->id;
                Role::create($roleData);
            }
        }
    }

    private function createInitialSystemAdmin(Group $adminGroup): User
    {
        $systemAdmin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@acs-rbac.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Assign Super Admin role in Administrators group
        $superAdminRole = Role::where('group_id', $adminGroup->id)
                             ->where('name', 'Super Admin')
                             ->first();

        GroupMember::create([
            'group_id' => $adminGroup->id,
            'user_id' => $systemAdmin->id,
            'role_id' => $superAdminRole->id,
            'assigned_by' => null, // Self-assigned initially
            'joined_at' => now(),
        ]);

        // Update group creators now that we have a system admin
        Group::where('id', '!=', $adminGroup->id)->update(['created_by' => $systemAdmin->id]);
        $adminGroup->update(['created_by' => $systemAdmin->id]);

        return $systemAdmin;
    }

    private function createSampleUsers(array $groups): void
    {
        $users = [
            ['name' => 'John Doe', 'email' => 'john@acs-rbac.com', 'group' => 'IT Support', 'role' => 'Manager'],
            ['name' => 'Jane Smith', 'email' => 'jane@acs-rbac.com', 'group' => 'Human Resources', 'role' => 'Manager'],
            ['name' => 'Bob Johnson', 'email' => 'bob@acs-rbac.com', 'group' => 'Finance', 'role' => 'Manager'],
            ['name' => 'Alice Brown', 'email' => 'alice@acs-rbac.com', 'group' => 'IT Support', 'role' => 'Supervisor'],
            ['name' => 'Charlie Wilson', 'email' => 'charlie@acs-rbac.com', 'group' => 'IT Support', 'role' => 'Senior Staff'],
            ['name' => 'Diana Davis', 'email' => 'diana@acs-rbac.com', 'group' => 'Human Resources', 'role' => 'Staff'],
            ['name' => 'Eva Martinez', 'email' => 'eva@acs-rbac.com', 'group' => 'Finance', 'role' => 'Senior Staff'],
            ['name' => 'Frank Miller', 'email' => 'frank@acs-rbac.com', 'group' => 'Sales', 'role' => 'Manager'],
            ['name' => 'Grace Taylor', 'email' => 'grace@acs-rbac.com', 'group' => 'Sales', 'role' => 'Staff'],
            ['name' => 'Henry Anderson', 'email' => 'henry@acs-rbac.com', 'group' => 'Operations', 'role' => 'Supervisor'],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Find the group and role
            $group = collect($groups)->firstWhere('name', $userData['group']);
            if ($group) {
                $role = Role::where('group_id', $group->id)
                          ->where('name', $userData['role'])
                          ->first();

                if ($role) {
                    GroupMember::create([
                        'group_id' => $group->id,
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                        'assigned_by' => 1, // Assigned by system admin
                        'joined_at' => now(),
                    ]);
                }
            }
        }
    }
}
