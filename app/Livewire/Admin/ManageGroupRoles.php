<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Group;
use App\Models\Role;
use App\Models\Permission;

class ManageGroupRoles extends Component
{
    public Group $group;
    public $roleName = '';
    public $roleDisplayName = '';
    public $roleDescription = '';
    public $hierarchyLevel = 1;
    public $isActive = true;
    public $selectedPermissions = [];
    public $showAddRoleModal = false;

    protected $rules = [
        'roleName' => 'required|string|max:255|regex:/^[a-z_]+$/',
        'roleDisplayName' => 'required|string|max:255',
        'roleDescription' => 'nullable|string|max:1000',
        'hierarchyLevel' => 'required|integer|min:1|max:10',
        'isActive' => 'boolean',
        'selectedPermissions' => 'array',
        'selectedPermissions.*' => 'exists:permissions,id',
    ];

    protected $messages = [
        'roleName.regex' => 'Role name must only contain lowercase letters and underscores.',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function openAddRoleModal()
    {
        $this->resetForm();
        $this->showAddRoleModal = true;
    }

    public function createRole()
    {
        $this->validate();

        // Check if role name already exists in this group
        if ($this->group->roles()->where('name', $this->roleName)->exists()) {
            session()->flash('error', 'A role with this name already exists in this group.');
            return;
        }

        try {
            $role = $this->group->roles()->create([
                'name' => $this->roleName,
                'display_name' => $this->roleDisplayName,
                'description' => $this->roleDescription,
                'hierarchy_level' => $this->hierarchyLevel,
                'is_active' => $this->isActive,
                'created_by' => auth()->id(),
            ]);

            // Attach permissions
            if (!empty($this->selectedPermissions)) {
                $role->permissions()->attach($this->selectedPermissions);
            }

            session()->flash('success', 'Role created successfully.');
            $this->showAddRoleModal = false;
            $this->resetForm();

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    public function deleteRole($roleId)
    {
        $role = $this->group->roles()->find($roleId);
        
        if (!$role) {
            session()->flash('error', 'Role not found.');
            return;
        }

        // Check if role is in use
        if ($role->groupMembers()->count() > 0) {
            session()->flash('error', 'Cannot delete role that is assigned to members.');
            return;
        }

        try {
            $role->delete();
            session()->flash('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    public function toggleRoleStatus($roleId)
    {
        $role = $this->group->roles()->find($roleId);
        
        if (!$role) {
            session()->flash('error', 'Role not found.');
            return;
        }

        $role->update(['is_active' => !$role->is_active]);
        
        $status = $role->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Role {$status} successfully.");
    }

    public function resetForm()
    {
        $this->roleName = '';
        $this->roleDisplayName = '';
        $this->roleDescription = '';
        $this->hierarchyLevel = 1;
        $this->isActive = true;
        $this->selectedPermissions = [];
    }

    public function closeModal()
    {
        $this->showAddRoleModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $roles = $this->group->roles()->with(['permissions'])->orderBy('hierarchy_level', 'desc')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('livewire.admin.manage-group-roles', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}
