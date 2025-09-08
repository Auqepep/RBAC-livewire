<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('email', 'admin@acs-rbac.com')->first();
        
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run RbacSeeder first.');
            return;
        }

        // Create groups
        $groups = [
            [
                'name' => 'Administrators',
                'description' => 'System administrators with full access to all features',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Managers',
                'description' => 'Department managers with access to their team management',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Staff',
                'description' => 'Regular staff members with limited access',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Contractors',
                'description' => 'External contractors with temporary access',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($groups as $groupData) {
            $group = Group::firstOrCreate(
                ['name' => $groupData['name']],
                $groupData
            );
            
            $this->command->info("Created/Updated group: {$group->name}");
        }

        // Add admin to administrators group
        $adminGroup = Group::where('name', 'Administrators')->first();
        if ($adminGroup && !$adminGroup->hasMember($admin->id)) {
            $adminGroup->addMember($admin->id, $admin->id);
            $this->command->info("Added admin to Administrators group");
        }
    }
}
