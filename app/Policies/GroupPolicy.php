<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;

class GroupPolicy
{
    /**
     * Determine if the user can view any groups.
     */
    public function viewAny(User $user): bool
    {
        // System admins can view all groups
        if ($user->isSystemAdmin()) {
            return true;
        }
        
        return $user->hasPermission('view_groups') || $user->hasPermission('manage_groups');
    }

    /**
     * Determine if the user can view the group.
     */
    public function view(User $user, Group $group): bool
    {
        // System admins can view any group
        if ($user->isSystemAdmin() || $user->hasPermission('manage_groups')) {
            return true;
        }

        // Members can view their own groups
        return $user->belongsToGroup($group->id);
    }

    /**
     * Determine if the user can create groups.
     */
    public function create(User $user): bool
    {
        return $user->isSystemAdmin() || $user->hasPermission('manage_groups');
    }

    /**
     * Determine if the user can update the group.
     */
    public function update(User $user, Group $group): bool
    {
        // System admins can update any group
        if ($user->isSystemAdmin() || $user->hasPermission('manage_groups')) {
            return true;
        }

        // Group managers can update their own group
        return $this->isManagerOfGroup($user, $group);
    }

    /**
     * Determine if the user can delete the group.
     */
    public function delete(User $user, Group $group): bool
    {
        // Only system admins can delete groups
        return $user->isSystemAdmin() || $user->hasPermission('manage_groups');
    }

    /**
     * Determine if the user can manage members in the group.
     */
    public function manageMembers(User $user, Group $group): bool
    {
        // System admins can manage members in any group
        if ($user->isSystemAdmin() || $user->hasPermission('assign_group_members')) {
            return true;
        }

        // Group managers can manage members in their own group
        return $this->isManagerOfGroup($user, $group);
    }

    /**
     * Determine if the user can assign roles in the group.
     */
    public function assignRoles(User $user, Group $group): bool
    {
        // System admins can assign roles in any group
        if ($user->isSystemAdmin() || $user->hasPermission('manage_group_roles')) {
            return true;
        }

        // Group managers can assign roles in their own group
        return $this->isManagerOfGroup($user, $group);
    }

    /**
     * Check if user is a manager (or higher) in the specified group.
     */
    protected function isManagerOfGroup(User $user, Group $group): bool
    {
        $membership = GroupMember::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->with('role')
            ->first();

        if (!$membership || !$membership->role) {
            return false;
        }

        // Hierarchy levels: admin (90), manager (70), staff (30)
        // Only managers and admins can perform management actions
        return $membership->role->hierarchy_level >= 70;
    }
}
