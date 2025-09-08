<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use Livewire\Component;

class RecentActivity extends Component
{
    public $recentUsers = [];
    public $recentRoles = [];
    public $recentGroups = [];

    public function mount()
    {
        $this->loadRecentActivity();
    }

    public function loadRecentActivity()
    {
        $this->recentUsers = User::with('roles')->latest()->take(5)->get();
        $this->recentRoles = Role::latest()->take(5)->get();
        $this->recentGroups = Group::latest()->take(5)->get();
    }

    public function refreshActivity()
    {
        $this->loadRecentActivity();
        session()->flash('message', 'Recent activity refreshed.');
    }

    public function render()
    {
        return view('livewire.admin.recent-activity');
    }
}
