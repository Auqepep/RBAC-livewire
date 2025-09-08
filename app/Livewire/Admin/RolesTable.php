<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\Permission;
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

        // Don't allow deletion of administrator role
        if ($role->name === 'administrator') {
            session()->flash('error', 'Cannot delete the administrator role.');
            return;
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            session()->flash('error', 'Cannot delete role that has assigned users.');
            return;
        }

        $role->permissions()->detach();
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
        $query = Role::with(['permissions', 'users'])
            ->withCount('users');

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

        return view('livewire.admin.roles-table', compact('roles'));
    }
}
