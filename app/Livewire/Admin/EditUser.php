<?php

namespace App\Livewire\Admin;

use App\Models\GroupMember;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;

class EditUser extends Component
{
    public User $user;
    
    #[Validate('required|string|max:255')]
    public $name = '';

    public $email = '';

    #[Validate('array')]
    public $selectedRoles = [];

    public $roles;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        
        // Get role IDs from group memberships
        $this->selectedRoles = $user->groupMembers()
            ->whereNotNull('role_id')
            ->pluck('role_id')
            ->toArray();
        
        $this->roles = \App\Models\Role::all(); // All available roles
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'selectedRoles' => 'array',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // Note: In our group-based RBAC system, roles are managed through groups
        // Direct role assignment to users is no longer supported
        session()->flash('message', 'User updated successfully. Note: Roles are now managed through group memberships.');

        session()->flash('success', 'User updated successfully.');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.edit-user');
    }
}
