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
    public $showEditUserModal = false;
    public $selectedUserId = null;
    public $selectedRoleId = null;
    public $selectedUserIds = [];
    public $search = '';
    
    // Edit user fields
    public $editUserId = null;
    public $editUserName = '';
    public $editUserEmail = '';

    protected $rules = [
        'selectedRoleId' => 'required|exists:roles,id',
        'selectedUserIds' => 'required|array|min:1',
        'selectedUserIds.*' => 'exists:users,id',
        'editUserName' => 'required|string|max:255',
        'editUserEmail' => 'required|email|max:255',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function openAddMemberModal()
    {
        $this->selectedUserId = null;
        $this->selectedRoleId = null;
        $this->selectedUserIds = [];
        $this->showAddMemberModal = true;
    }

    public function addMember()
    {
        // If multiple users selected, use bulk add
        if (count($selectedUserIds) > 0) {
            return $this->bulkAddMembers();
        }

        // Single user add (legacy, not used anymore)
        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
            'selectedRoleId' => 'required|exists:roles,id',
        ]);

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

    public function bulkAddMembers()
    {
        $this->validate([
            'selectedRoleId' => 'required|exists:roles,id',
            'selectedUserIds' => 'required|array|min:1',
            'selectedUserIds.*' => 'exists:users,id',
        ]);

        // Check permissions
        if (!auth()->user()->canAssignRolesInGroup($this->group->id)) {
            session()->flash('error', 'You do not have permission to add members to this group.');
            return;
        }

        try {
            $addedCount = 0;
            $skippedCount = 0;
            
            foreach ($this->selectedUserIds as $userId) {
                // Skip if already a member
                if ($this->group->hasMember($userId)) {
                    $skippedCount++;
                    continue;
                }
                
                $this->group->addMember(
                    $userId,
                    $this->selectedRoleId,
                    auth()->id()
                );
                $addedCount++;
            }

            $role = Role::find($this->selectedRoleId);
            $message = "Successfully added {$addedCount} member(s) as {$role->display_name}.";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} user(s) were already members)";
            }
            
            session()->flash('message', $message);
            
            $this->showAddMemberModal = false;
            $this->resetForm();
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while adding members: ' . $e->getMessage());
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
        $this->selectedUserIds = [];
    }

    public function openEditUserModal($userId)
    {
        // Debug logging
        logger()->info('openEditUserModal called with userId: ' . $userId);
        
        $user = User::find($userId);
        if ($user) {
            logger()->info('User found: ' . $user->name);
            $this->editUserId = $user->id;
            $this->editUserName = $user->name;
            $this->editUserEmail = $user->email;
            $this->showEditUserModal = true;
            logger()->info('showEditUserModal set to: ' . ($this->showEditUserModal ? 'true' : 'false'));
        } else {
            logger()->error('User not found with ID: ' . $userId);
        }
    }

    public function updateUser()
    {
        $this->validate([
            'editUserName' => 'required|string|max:255',
            'editUserEmail' => 'required|email|max:255|unique:users,email,' . $this->editUserId,
        ]);

        try {
            $user = User::find($this->editUserId);
            if ($user) {
                $user->name = $this->editUserName;
                $user->email = $this->editUserEmail;
                $user->save();

                session()->flash('message', "Successfully updated {$user->name}'s information.");
                $this->showEditUserModal = false;
                $this->resetEditForm();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the user: ' . $e->getMessage());
        }
    }

    public function resetEditForm()
    {
        $this->editUserId = null;
        $this->editUserName = '';
        $this->editUserEmail = '';
        $this->resetValidation(['editUserName', 'editUserEmail']);
    }

    public function closeModals()
    {
        $this->showAddMemberModal = false;
        $this->showEditUserModal = false;
        $this->resetForm();
        $this->resetEditForm();
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

        // Get all active roles (roles are global, not group-specific)
        $availableRoles = Role::where('is_active', true)
                              ->orderBy('hierarchy_level', 'desc')
                              ->get();

        return view('livewire.admin.manage-group-members', compact('members', 'availableUsers', 'availableRoles'));
    }
}
