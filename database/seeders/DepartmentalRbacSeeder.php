<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Models\GroupMember;
use Illuminate\Database\Seeder;

class DepartmentalRbacSeeder extends Seeder
{
    public function run(): void
    {
        echo "Creating departmental RBAC structure...\n";

        // Create department groups
        $itGroup = Group::firstOrCreate(
            ['name' => 'IT Support'],
            [
                'description' => 'Information Technology Support Department',
                'is_active' => true,
                'created_by' => 1, // Admin user
            ]
        );

        $marketingGroup = Group::firstOrCreate(
            ['name' => 'Marketing'],
            [
                'description' => 'Marketing and Communications Department',
                'is_active' => true,
                'created_by' => 1, // Admin user
            ]
        );

        $hrGroup = Group::firstOrCreate(
            ['name' => 'Human Resources'],
            [
                'description' => 'Human Resources Department',
                'is_active' => true,
                'created_by' => 1, // Admin user
            ]
        );

        // Create specific roles for departments
        $itManager = Role::firstOrCreate(
            ['name' => 'IT Manager'],
            [
                'display_name' => 'IT Manager',
                'description' => 'Manages the IT department and technical operations',
                'hierarchy_level' => 80,
                'is_active' => true,
            ]
        );

        $itSupervisor = Role::firstOrCreate(
            ['name' => 'IT Supervisor'],
            [
                'display_name' => 'IT Supervisor',
                'description' => 'Supervises IT staff and day-to-day operations',
                'hierarchy_level' => 60,
                'is_active' => true,
            ]
        );

        $itStaff = Role::firstOrCreate(
            ['name' => 'IT Staff'],
            [
                'display_name' => 'IT Staff',
                'description' => 'Technical support staff member',
                'hierarchy_level' => 40,
                'is_active' => true,
            ]
        );

        $marketingManager = Role::firstOrCreate(
            ['name' => 'Marketing Manager'],
            [
                'display_name' => 'Marketing Manager',
                'description' => 'Manages marketing campaigns and strategy',
                'hierarchy_level' => 80,
                'is_active' => true,
            ]
        );

        $marketingSpecialist = Role::firstOrCreate(
            ['name' => 'Marketing Specialist'],
            [
                'display_name' => 'Marketing Specialist',
                'description' => 'Executes marketing campaigns and content creation',
                'hierarchy_level' => 50,
                'is_active' => true,
            ]
        );

        $hrManager = Role::firstOrCreate(
            ['name' => 'HR Manager'],
            [
                'display_name' => 'HR Manager',
                'description' => 'Manages human resources and employee relations',
                'hierarchy_level' => 80,
                'is_active' => true,
            ]
        );

        echo "✅ Departmental RBAC structure created successfully!\n";
        echo "Groups: IT Support, Marketing, Human Resources\n";
        echo "Roles: IT Manager, IT Supervisor, IT Staff, Marketing Manager, Marketing Specialist, HR Manager\n";
        echo "\nYou can now assign users to these roles within specific groups.\n";
    }
}
