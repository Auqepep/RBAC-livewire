<?php

namespace App\Livewire\User;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupMember;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Mary\Traits\Toast;

class ManageGroup extends Component
{
    use Toast;

    public Group $group;
    public $name;
    public $description;
    public $is_active;
    
    // Member management
    public $showAddMemberModal = false;
    public $selectedUserId;
    public $selectedRoleId;
    public $searchTerm = '';
    
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:groups,name,' . $this->group->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function mount(Group $group)
    {
        // Check if user can manage this group
        if (!Gate::allows('manage-group', $group)) {
            abort(403, 'You do not have permission to manage this group.');
        }

        $this->group = $group;
        $this->name = $group->name;
        $this->description = $group->description;
        $this->is_active = $group->is_active;
    }

    public function updateGroupDetails()
    {
        // Check permission again
        if (!Gate::allows('edit-group-description', $this->group)) {
            $this->error('You do not have permission to edit this group.');
            return;
        }

        $this->validate();

        $this->group->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->success('Group details updated successfully!');
    }

    public function openAddMemberModal()
    {
        $this->showAddMemberModal = true;
        $this->selectedUserId = null;
        $this->selectedRoleId = null;
        $this->searchTerm = '';
    }

    public function closeAddMemberModal()
    {
        $this->showAddMemberModal = false;
    }

    public function addMember()
    {
        // Check permission
        if (!Gate::allows('manage-group-members', $this->group)) {
            $this->error('You do not have permission to add members.');
            return;
        }

        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
            'selectedRoleId' => 'required|exists:roles,id',
        ]);

        // Check if user is already a member
        $existingMember = GroupMember::where('group_id', $this->group->id)
            ->where('user_id', $this->selectedUserId)
            ->first();

        if ($existingMember) {
            $this->error('This user is already a member of the group.');
            return;
        }

        GroupMember::create([
            'group_id' => $this->group->id,
            'user_id' => $this->selectedUserId,
            'role_id' => $this->selectedRoleId,
            'assigned_by' => auth()->id(),
            'joined_at' => now(),
        ]);

        $this->success('Member added successfully!');
        $this->closeAddMemberModal();
        $this->group->refresh();
    }

    public function removeMember($userId)
    {
        // Check permission
        if (!Gate::allows('manage-group-members', $this->group)) {
            $this->error('You do not have permission to remove members.');
            return;
        }

        // Prevent removing yourself if you're the only admin/manager
        if ($userId == auth()->id()) {
            $adminCount = GroupMember::where('group_id', $this->group->id)
                ->whereHas('role', function($query) {
                    $query->whereIn('name', ['admin', 'manager'])
                        ->where('hierarchy_level', '>=', 50);
                })
                ->count();

            if ($adminCount <= 1) {
                $this->error('You cannot remove yourself as you are the only manager/admin.');
                return;
            }
        }

        GroupMember::where('group_id', $this->group->id)
            ->where('user_id', $userId)
            ->delete();

        $this->success('Member removed successfully!');
        $this->group->refresh();
    }

    public function updateMemberRole($userId, $roleId)
    {
        // Check permission
        if (!Gate::allows('edit-member-roles-in-group', $this->group)) {
            $this->error('You do not have permission to edit member roles.');
            return;
        }

        $member = GroupMember::where('group_id', $this->group->id)
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            $this->error('Member not found.');
            return;
        }

        // Get the target role
        $targetRole = \App\Models\Role::find($roleId);
        
        // Get current user's role
        $currentUserRole = GroupMember::where('group_id', $this->group->id)
            ->where('user_id', auth()->id())
            ->first()?->role;

        // Prevent managers from assigning admin roles (only admins can do that)
        if ($targetRole && $targetRole->hierarchy_level > ($currentUserRole?->hierarchy_level ?? 0)) {
            $this->error('You cannot assign a role higher than your own.');
            return;
        }

        $member->update([
            'role_id' => $roleId,
            'assigned_by' => auth()->id(),
        ]);

        $this->success('Member role updated successfully!');
        $this->group->refresh();
    }

    public function getAvailableUsersProperty()
    {
        return User::whereNotNull('email_verified_at')
            ->whereNotIn('id', $this->group->groupMembers->pluck('user_id'))
            ->when($this->searchTerm, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->limit(10)
            ->get();
    }

    public function getAvailableRolesProperty()
    {
        return $this->group->roles()
            ->where('is_active', true)
            ->orderBy('hierarchy_level', 'desc')
            ->get();
    }

    public function render()
    {
        $this->group->load(['groupMembers.user', 'groupMembers.role', 'roles']);
        
        return view('livewire.user.manage-group', [
            'members' => $this->group->groupMembers,
            'availableUsers' => $this->availableUsers,
            'availableRoles' => $this->availableRoles,
        ]);
    }
}
