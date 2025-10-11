<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyGroups extends Component
{
    public $user;
    public $groups;

    public function mount()
    {
        $this->user = Auth::user()->load('roles');
        $this->loadGroups();
    }

    public function loadGroups()
    {
        $this->groups = $this->user->groups()
            ->with(['groupMembers.user', 'groupMembers.role'])
            ->get();
    }

    public function render()
    {
        return view('livewire.user.my-groups');
    }
}
