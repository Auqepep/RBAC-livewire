<?php

namespace App\Livewire\Admin;

use App\Models\Group;
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
    public $selectedGroups = [];

    public $groups;

    public function mount()
    {
        $this->groups = Group::with('roles')->where('is_active', true)->get();
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => now(),
        ]);

        // In our group-based RBAC system, users are assigned to groups
        // For now, we just create the user and let admins assign groups separately
        // This could be enhanced to allow group selection during user creation

        session()->flash('success', 'User created successfully. Assign user to groups to give them roles.');
        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.create-user');
    }
}
