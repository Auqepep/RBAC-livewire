<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class RolesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
        $this->resetPage();
    }

    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);
        
        if (!$role) {
            session()->flash('error', 'Role not found.');
            return;
        }

        // Don't allow deletion of system roles
        if (in_array($role->name, ['Super Admin', 'Admin', 'Manager'])) {
            session()->flash('error', 'Cannot delete system roles.');
            return;
        }

        // Check if role is being used
        $usageCount = \App\Models\GroupMember::where('role_id', $role->id)->count();
        if ($usageCount > 0) {
            session()->flash('error', "Cannot delete role that is assigned to {$usageCount} users.");
            return;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
    }

    public function toggleStatus($roleId)
    {
        $role = Role::find($roleId);
        
        if (!$role) {
            session()->flash('error', 'Role not found.');
            return;
        }

        $role->update(['is_active' => !$role->is_active]);
        
        $status = $role->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Role {$status} successfully.");
    }

    public function render()
    {
        $query = Role::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('display_name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status !== '') {
            $query->where('is_active', $this->status);
        }

        $roles = $query->orderBy($this->sortField, $this->sortDirection)
                      ->paginate($this->perPage);

        // Add usage count for each role
        $roles->getCollection()->transform(function ($role) {
            $role->users_count = \App\Models\GroupMember::where('role_id', $role->id)
                                                        ->count();
            return $role;
        });

        return view('livewire.admin.roles-table', compact('roles'));
    }
}
