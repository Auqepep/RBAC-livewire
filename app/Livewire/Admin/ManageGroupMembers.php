<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Models\GroupMember;
use Livewire\Component;
use Livewire\WithPagination;

class ManageGroupMembers extends Component
{
    use WithPagination;

    public Group $group;
    public $showAddMemberModal = false;
    public $showChangeRoleModal = false;
    public $selectedUserId = null;
    public $selectedRoleId = null;
    public $selectedMemberId = null;
    public $search = '';

    protected $rules = [
        'selectedUserId' => 'required|exists:users,id',
        'selectedRoleId' => 'required|exists:roles,id',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function openAddMemberModal()
    {
        $this->selectedUserId = null;
        $this->selectedRoleId = null;
        $this->showAddMemberModal = true;
    }

    public function openChangeRoleModal($memberId)
    {
        $member = GroupMember::find($memberId);
        if ($member && $member->group_id === $this->group->id) {
            $this->selectedMemberId = $memberId;
            $this->selectedRoleId = $member->role_id;
            $this->showChangeRoleModal = true;
        }
    }

    public function addMember()
    {
        $this->validate();

        // Check if user is already a member
        if ($this->group->hasMember($this->selectedUserId)) {
            session()->flash('error', 'User is already a member of this group.');
            return;
        }

        // Check permissions
        if (!auth()->user()->canAssignRolesInGroup($this->group->id)) {
            session()->flash('error', 'You do not have permission to add members to this group.');
            return;
        }

        try {
            $this->group->addMember(
                $this->selectedUserId,
                $this->selectedRoleId,
                auth()->id()
            );

            $user = User::find($this->selectedUserId);
            $role = Role::find($this->selectedRoleId);
            
            session()->flash('message', "Successfully added {$user->name} to {$this->group->name} as {$role->display_name}.");
            
            $this->showAddMemberModal = false;
            $this->resetForm();
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while adding the member: ' . $e->getMessage());
        }
    }

    public function changeRole()
    {
        $this->validate(['selectedRoleId' => 'required|exists:roles,id']);

        $member = GroupMember::find($this->selectedMemberId);
        if (!$member || $member->group_id !== $this->group->id) {
            session()->flash('error', 'Invalid member selected.');
            return;
        }

        // Check permissions
        if (!auth()->user()->canAssignRolesInGroup($this->group->id)) {
            session()->flash('error', 'You do not have permission to change roles in this group.');
            return;
        }

        try {
            $this->group->changeUserRole(
                $member->user_id,
                $this->selectedRoleId,
                auth()->id()
            );

            $role = Role::find($this->selectedRoleId);
            session()->flash('message', "Successfully changed {$member->user->name}'s role to {$role->display_name}.");
            
            $this->showChangeRoleModal = false;
            $this->resetForm();
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while changing the role: ' . $e->getMessage());
        }
    }

    public function removeMember($memberId)
    {
        $member = GroupMember::find($memberId);
        if (!$member || $member->group_id !== $this->group->id) {
            session()->flash('error', 'Invalid member selected.');
            return;
        }

        // Check permissions
        if (!auth()->user()->canAssignRolesInGroup($this->group->id)) {
            session()->flash('error', 'You do not have permission to remove members from this group.');
            return;
        }

        // Prevent self-removal
        if ($member->user_id === auth()->id()) {
            session()->flash('error', 'You cannot remove yourself from the group.');
            return;
        }

        try {
            $userName = $member->user->name;
            $this->group->removeMember($member->user_id);
            
            session()->flash('message', "Successfully removed {$userName} from {$this->group->name}.");
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while removing the member: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->selectedUserId = null;
        $this->selectedRoleId = null;
        $this->selectedMemberId = null;
    }

    public function closeModals()
    {
        $this->showAddMemberModal = false;
        $this->showChangeRoleModal = false;
        $this->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $members = $this->group->groupMembers()
            ->with(['user', 'role'])
            ->when($this->search, function($query) {
                $query->whereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get users not in this group for adding
        $availableUsers = User::whereDoesntHave('groupMemberships', function($query) {
            $query->where('group_id', $this->group->id);
        })->orderBy('name')->get();

        // Get available roles for this specific group
        $availableRoles = $this->group->roles()->where('is_active', true)->get();

        return view('livewire.admin.manage-group-members', compact('members', 'availableUsers', 'availableRoles'));
    }
}
