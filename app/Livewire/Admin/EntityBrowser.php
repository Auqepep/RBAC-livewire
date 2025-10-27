<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use Livewire\Component;

class EntityBrowser extends Component
{
    public $activeTab = 'users';
    public $sortBy = 'date'; // 'date' or 'alphabetical'
    public $sortDirection = 'desc'; // 'asc' or 'desc'

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function setSortBy($sortBy)
    {
        if ($this->sortBy === $sortBy) {
            // Toggle direction if same sort field
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $sortBy;
            $this->sortDirection = $sortBy === 'date' ? 'desc' : 'asc';
        }
    }

    public function getUsers()
    {
        $query = User::query();
        
        if ($this->sortBy === 'date') {
            $query->orderBy('created_at', $this->sortDirection);
        } else {
            $query->orderBy('name', $this->sortDirection);
        }
        
        return $query->take(10)->get();
    }

    public function getGroups()
    {
        $query = Group::query();
        
        if ($this->sortBy === 'date') {
            $query->orderBy('created_at', $this->sortDirection);
        } else {
            $query->orderBy('name', $this->sortDirection);
        }
        
        return $query->take(10)->get();
    }

    public function getRoles()
    {
        $query = Role::query();
        
        if ($this->sortBy === 'date') {
            $query->orderBy('created_at', $this->sortDirection);
        } else {
            $query->orderBy('name', $this->sortDirection);
        }
        
        return $query->take(10)->get();
    }

    public function render()
    {
        return view('livewire.admin.entity-browser', [
            'users' => $this->getUsers(),
            'groups' => $this->getGroups(),
            'roles' => $this->getRoles()
        ]);
    }
}
