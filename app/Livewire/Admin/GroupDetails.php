<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GroupDetails extends Component
{
    public Group $group;
    public $selectedUsers = [];
    public $showUserModal = false;

    public function mount(Group $group)
    {
        $this->group = $group;
        $this->selectedUsers = $group->users->pluck('id')->toArray();
    }

    public function toggleUserModal()
    {
        $this->showUserModal = !$this->showUserModal;
    }

    public function updateUsers()
    {
        try {
            $syncData = [];
            foreach ($this->selectedUsers as $userId) {
                $syncData[$userId] = [
                    'added_by' => Auth::id(),
                    'joined_at' => now(),
                ];
            }
            
            $this->group->users()->sync($syncData);
            $this->group->refresh();
            
            session()->flash('message', 'Group members updated successfully.');
            $this->showUserModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating members.');
        }
    }

    public function removeUser($userId)
    {
        try {
            $this->group->users()->detach($userId);
            $this->group->refresh();
            $this->selectedUsers = $this->group->users->pluck('id')->toArray();
            
            session()->flash('message', 'Member removed successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while removing the member.');
        }
    }

    public function render()
    {
        $availableUsers = User::orderBy('name')->get();
        
        return view('livewire.admin.group-details', compact('availableUsers'));
    }
}
