<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Group;
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
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
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

    public function updateRoles()
    {
        $this->user->roles()->sync($this->selectedRoles);
        $this->user->refresh();
        $this->showRoleModal = false;
        session()->flash('message', 'User roles updated successfully.');
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

    public function removeRole($roleId)
    {
        $this->user->roles()->detach($roleId);
        $this->user->refresh();
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
        session()->flash('message', 'Role removed successfully.');
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
        $availableRoles = Role::where('is_active', true)->orderBy('display_name')->get();
        $availableGroups = Group::where('is_active', true)->orderBy('name')->get();
        
        return view('livewire.admin.user-details', compact('availableRoles', 'availableGroups'));
    }
}
