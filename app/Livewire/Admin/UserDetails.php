<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserDetails extends Component
{
    public User $user;
    public $selectedRoles = [];
    public $selectedGroups = [];
    public $showRoleModal = false;
    public $showGroupModal = false;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->selectedRoles = $user->groupMembers()
            ->whereNotNull('role_id')
            ->pluck('role_id')
            ->toArray();
        $this->selectedGroups = $user->groups->pluck('id')->toArray();
    }

    public function toggleRoleModal()
    {
        $this->showRoleModal = !$this->showRoleModal;
    }

    public function toggleGroupModal()
    {
        $this->showGroupModal = !$this->showGroupModal;
    }

    // Note: In our group-based RBAC system, roles are no longer assigned directly to users
    // Users get roles through their group memberships. This method is disabled.
    public function updateRoles()
    {
        session()->flash('error', 'Roles are now managed through groups. Please assign the user to groups that have the desired roles.');
        $this->showRoleModal = false;
    }

    public function updateGroups()
    {
        $syncData = [];
        foreach ($this->selectedGroups as $groupId) {
            $syncData[$groupId] = [
                'added_by' => Auth::id(),
                'joined_at' => now(),
            ];
        }
        
        $this->user->groups()->sync($syncData);
        $this->user->refresh();
        $this->showGroupModal = false;
        session()->flash('message', 'User groups updated successfully.');
    }

    // Note: In our group-based RBAC system, roles are no longer assigned directly to users
    public function removeRole($roleId)
    {
        session()->flash('error', 'Roles are now managed through groups. Please remove the user from groups that have this role.');
    }

    public function removeGroup($groupId)
    {
        $this->user->groups()->detach($groupId);
        $this->user->refresh();
        $this->selectedGroups = $this->user->groups->pluck('id')->toArray();
        session()->flash('message', 'Group removed successfully.');
    }

    public function render()
    {
        $availableRoles = collect(); // Will be replaced with group roles
        $availableGroups = Group::where('is_active', true)->orderBy('name')->get();
        
        return view('livewire.admin.user-details', compact('availableRoles', 'availableGroups'));
    }
}
