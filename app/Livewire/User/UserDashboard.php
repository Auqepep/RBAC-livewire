<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserDashboard extends Component
{
    public $user;
    public $stats = [];

    public function mount()
    {
        $this->user = Auth::user()->load('roles', 'groups');
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_roles' => $this->user->roles()->count(),
            'total_groups' => $this->user->groups()->count(),
            'active_groups' => $this->user->groups()->where('is_active', true)->count(),
            'recent_groups' => $this->user->groups()->latest()->take(3)->get(),
        ];
    }

    public function refreshStats()
    {
        $this->loadStats();
        session()->flash('message', 'Dashboard refreshed successfully.');
    }

    public function render()
    {
        return view('livewire.user.user-dashboard');
    }
}
