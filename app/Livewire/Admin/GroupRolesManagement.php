<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use App\Models\Role;
use App\Models\GroupMember;
use Livewire\Component;

class GroupRolesManagement extends Component
{
    public $selectedGroup = null;
    public $showCreateRole = false;
    public $newRoleName = '';
    public $newRoleDisplayName = '';
    public $newRoleDescription = '';
    public $newRoleColor = '#3B82F6';
    public $newRoleHierarchy = 1;

    protected $rules = [
        'newRoleName' => 'required|string|max:255|unique:roles,name',
        'newRoleDisplayName' => 'required|string|max:255',
        'newRoleDescription' => 'nullable|string|max:1000',
        'newRoleColor' => 'required|string',
        'newRoleHierarchy' => 'required|integer|min:1|max:10',
    ];

    public function selectGroup($groupId)
    {
        $this->selectedGroup = $groupId;
        $this->showCreateRole = false;
        $this->resetRoleForm();
    }

    public function showCreateRoleForm($groupId)
    {
        $this->selectedGroup = $groupId;
        $this->showCreateRole = true;
        $this->resetRoleForm();
    }

    public function createRole()
    {
        $this->validate();

        try {
            Role::create([
                'name' => $this->newRoleName,
                'display_name' => $this->newRoleDisplayName,
                'description' => $this->newRoleDescription,
                'badge_color' => $this->newRoleColor,
                'hierarchy_level' => $this->newRoleHierarchy,
                'is_active' => true,
            ]);

            session()->flash('message', 'Role created successfully.');
            $this->showCreateRole = false;
            $this->resetRoleForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    public function cancelCreateRole()
    {
        $this->showCreateRole = false;
        $this->resetRoleForm();
    }

    private function resetRoleForm()
    {
        $this->newRoleName = '';
        $this->newRoleDisplayName = '';
        $this->newRoleDescription = '';
        $this->newRoleColor = '#3B82F6';
        $this->newRoleHierarchy = 1;
        $this->resetErrorBag();
    }

    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);
        
        if (!$role) {
            session()->flash('error', 'Role not found.');
            return;
        }

        // Check if role is being used
        $usageCount = GroupMember::where('role_id', $role->id)->count();
        if ($usageCount > 0) {
            session()->flash('error', "Cannot delete role that is assigned to {$usageCount} users.");
            return;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
    }

    public function render()
    {
        // Get all groups with their roles (through group_members)
        $groups = Group::with([
            'groupMembers.role.permissions',
            'groupMembers.user'
        ])->where('is_active', true)->get();

        // Transform to get unique roles per group
        $groupsWithRoles = $groups->map(function ($group) {
            $roles = $group->groupMembers->pluck('role')->unique('id')->values();
            $group->unique_roles = $roles;
            
            // Get user count for each role in this group
            foreach ($roles as $role) {
                $role->users_count_in_group = $group->groupMembers
                    ->where('role_id', $role->id)
                    ->count();
            }
            
            return $group;
        });

        // Get all available roles for reference
        $allRoles = Role::with('permissions')->orderBy('hierarchy_level', 'desc')->get();

        return view('livewire.admin.group-roles-management', [
            'groups' => $groupsWithRoles,
            'allRoles' => $allRoles
        ]);
    }
}
