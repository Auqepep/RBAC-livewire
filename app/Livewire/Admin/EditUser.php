<?php

namespace App\Livewire\Admin;

use App\Models\Role;
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
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->roles = Role::where('is_active', true)->get();
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

        // Sync roles
        $this->user->roles()->detach();
        if (!empty($this->selectedRoles)) {
            $roles = Role::whereIn('id', $this->selectedRoles)->get();
            foreach ($roles as $role) {
                $this->user->assignRole($role, auth()->user());
            }
        }

        session()->flash('success', 'User updated successfully.');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.edit-user');
    }
}
