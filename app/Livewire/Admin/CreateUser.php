<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CreateUser extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|email|max:255|unique:users')]
    public $email = '';

    #[Validate('array')]
    public $selectedRoles = [];

    public $roles;

    public function mount()
    {
        $this->roles = Role::where('is_active', true)->get();
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => now(),
        ]);

        if (!empty($this->selectedRoles)) {
            $roles = Role::whereIn('id', $this->selectedRoles)->get();
            foreach ($roles as $role) {
                $user->assignRole($role, auth()->user());
            }
        }

        session()->flash('success', 'User created successfully.');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.create-user');
    }
}
