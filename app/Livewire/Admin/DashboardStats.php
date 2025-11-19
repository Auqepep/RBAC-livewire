<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Role;
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
            'total_assignments' => GroupMember::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'active_assignments' => GroupMember::count(),
            'active_groups' => Group::where('is_active', true)->count(),
            'role_distribution' => Role::withCount(['groupMembers as users_count'])
                ->get()
                ->map(function($role) {
                    return (object) [
                        'name' => $role->name,
                        'display_name' => $role->display_name ?: $role->name,
                        'color' => $role->badge_color ?: '#3B82F6',
                        'users_count' => $role->users_count
                    ];
                }),
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
