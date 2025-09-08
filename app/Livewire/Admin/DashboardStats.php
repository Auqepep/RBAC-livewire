<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\Permission;
use App\Models\GroupJoinRequest;
use Livewire\Component;

class DashboardStats extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'groups' => Group::count(),
            'permissions' => Permission::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'active_groups' => Group::where('is_active', true)->count(),
            'role_distribution' => Role::withCount('users')->get(),
            'pending_join_requests' => GroupJoinRequest::where('status', 'pending')->count(),
        ];
    }

    public function refreshStats()
    {
        $this->loadStats();
        session()->flash('message', 'Statistics refreshed successfully.');
    }

    public function render()
    {
        return view('livewire.admin.dashboard-stats');
    }
}
