<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use App\Models\Role;
use App\Models\GroupMember;
use Livewire\Component;

class GroupRolesDisplay extends Component
{
    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);
        
        if (!$role) {
            session()->flash('error', 'Role not found.');
            return;
        }

        // Don't allow deletion of system roles
        if (in_array($role->name, ['Super Admin', 'Admin', 'Manager'])) {
            session()->flash('error', 'Cannot delete system roles.');
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

        // Transform to get unique roles per group with their usage stats
        $groupsWithRoles = $groups->map(function ($group) {
            // Get unique roles used in this group
            $roles = $group->groupMembers->pluck('role')->unique('id')->filter()->values();
            
            // Add usage statistics for each role in this group
            foreach ($roles as $role) {
                $role->users_count_in_group = $group->groupMembers
                    ->where('role_id', $role->id)
                    ->count();
            }
            
            $group->unique_roles = $roles->sortByDesc('hierarchy_level');
            return $group;
        });

        // Also get any roles that aren't assigned to any group yet
        $assignedRoleIds = GroupMember::pluck('role_id')->unique()->toArray();
        $unassignedRoles = Role::with('permissions')
            ->whereNotIn('id', $assignedRoleIds)
            ->orderByDesc('hierarchy_level')
            ->get();

        return view('livewire.admin.group-roles-display', [
            'groups' => $groupsWithRoles,
            'unassignedRoles' => $unassignedRoles
        ]);
    }
}
