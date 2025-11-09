<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Models\Role;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GroupManagementController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the form for editing the group (for managers)
     */
    public function edit(Group $group)
    {
        // Check if user can update this group
        $this->authorize('update', $group);

        $users = User::whereNotNull('email_verified_at')->get();
        $roles = Role::where('is_active', true)->get();
        $groupMembers = $group->groupMembers()->with(['user', 'role'])->get();

        return view('users.group-edit', compact('group', 'users', 'roles', 'groupMembers'));
    }

    /**
     * Update the group details (for managers)
     */
    public function update(Request $request, Group $group)
    {
        // Check if user can update this group
        $this->authorize('update', $group);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('groups')->ignore($group->id)],
            'description' => 'nullable|string',
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('groups.edit', $group->id)
            ->with('success', 'Group updated successfully!');
    }

    /**
     * Add a member to the group
     */
    public function addMember(Request $request, Group $group)
    {
        // Check if user can manage members in this group
        $this->authorize('manageMembers', $group);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Check if user is already a member
        $existing = GroupMember::where('group_id', $group->id)
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($existing) {
            return back()->with('error', 'User is already a member of this group.');
        }

        // Get the manager's role to check hierarchy
        $managerMembership = GroupMember::where('user_id', auth()->id())
            ->where('group_id', $group->id)
            ->with('role')
            ->first();

        $newRole = Role::find($validated['role_id']);

        // Managers can't assign roles higher than their own
        if ($managerMembership && $newRole->hierarchy_level >= $managerMembership->role->hierarchy_level) {
            return back()->with('error', 'You cannot assign a role equal to or higher than your own.');
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $validated['user_id'],
            'role_id' => $validated['role_id'],
            'assigned_by' => auth()->id(),
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Member added successfully!');
    }

    /**
     * Update a member's role
     */
    public function updateMember(Request $request, Group $group, GroupMember $member)
    {
        // Check if user can manage members in this group
        $this->authorize('manageMembers', $group);

        // Ensure the member belongs to this group
        if ($member->group_id !== $group->id) {
            abort(404);
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Get the manager's role to check hierarchy
        $managerMembership = GroupMember::where('user_id', auth()->id())
            ->where('group_id', $group->id)
            ->with('role')
            ->first();

        $newRole = Role::find($validated['role_id']);
        $currentRole = $member->role;

        // Managers can't modify members with equal or higher hierarchy
        if ($managerMembership && $currentRole->hierarchy_level >= $managerMembership->role->hierarchy_level) {
            return back()->with('error', 'You cannot modify members with equal or higher roles.');
        }

        // Managers can't assign roles higher than their own
        if ($managerMembership && $newRole->hierarchy_level >= $managerMembership->role->hierarchy_level) {
            return back()->with('error', 'You cannot assign a role equal to or higher than your own.');
        }

        $member->update([
            'role_id' => $validated['role_id'],
        ]);

        return back()->with('success', 'Member role updated successfully!');
    }

    /**
     * Remove a member from the group
     */
    public function removeMember(Group $group, GroupMember $member)
    {
        // Check if user can manage members in this group
        $this->authorize('manageMembers', $group);

        // Ensure the member belongs to this group
        if ($member->group_id !== $group->id) {
            abort(404);
        }

        // Prevent removing yourself
        if ($member->user_id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself from the group.');
        }

        // Get the manager's role to check hierarchy
        $managerMembership = GroupMember::where('user_id', auth()->id())
            ->where('group_id', $group->id)
            ->with('role')
            ->first();

        $memberRole = $member->role;

        // Managers can't remove members with equal or higher hierarchy
        if ($managerMembership && $memberRole->hierarchy_level >= $managerMembership->role->hierarchy_level) {
            return back()->with('error', 'You cannot remove members with equal or higher roles.');
        }

        $member->delete();

        return back()->with('success', 'Member removed successfully!');
    }
}
