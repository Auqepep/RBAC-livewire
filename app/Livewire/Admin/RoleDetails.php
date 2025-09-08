<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class RoleDetails extends Component
{
    use WithPagination;

    public Role $role;
    public $showUsersList = true;
    public $usersPerPage = 10;

    protected $paginationTheme = 'tailwind';

    public function mount(Role $role)
    {
        $this->role = $role;
    }

    public function toggleUsersList()
    {
        $this->showUsersList = !$this->showUsersList;
    }

    public function removeUserFromRole($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            session()->flash('error', 'User not found.');
            return;
        }

        // Don't allow removal if this is the last administrator
        if ($this->role->name === 'administrator' && $this->role->users()->count() <= 1) {
            session()->flash('error', 'Cannot remove the last administrator.');
            return;
        }

        $this->role->users()->detach($userId);
        
        session()->flash('message', "User {$user->name} removed from role {$this->role->display_name}.");
        
        // Refresh the role to update user count
        $this->role = $this->role->fresh();
    }

    public function render()
    {
        $users = $this->showUsersList 
            ? $this->role->users()->paginate($this->usersPerPage) 
            : collect();

        return view('livewire.admin.role-details', compact('users'));
    }
}
