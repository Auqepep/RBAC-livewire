<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\Permission;
use Livewire\Component;
use Illuminate\Validation\Rule;

class EditRole extends Component
{
    public Role $role;
    public $name = '';
    public $display_name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $is_active = true;
    public $selectedPermissions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->name = $role->name;
        $this->display_name = $role->display_name;
        $this->description = $role->description ?? '';
        $this->color = $role->badge_color ?? '#3B82F6';
        $this->is_active = $role->is_active;
        $this->selectedPermissions = $role->permissions ?? [];
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('roles', 'name')->ignore($this->role->id)],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'selectedPermissions' => ['array'],
        ];
    }

    protected $messages = [
        'name.alpha_dash' => 'The role name may only contain letters, numbers, dashes and underscores.',
        'color.regex' => 'The color must be a valid hex color code.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            $this->role->update([
                'name' => $this->name,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'badge_color' => $this->color,
                'is_active' => $this->is_active,
                'permissions' => $this->selectedPermissions,
            ]);

            session()->flash('message', 'Role template updated successfully.');
            
            return $this->redirect(route('admin.roles.index'));
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the role template: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('admin.roles.index'));
    }

    public function render()
    {
        $permissions = Permission::all();
        
        return view('livewire.admin.edit-role', compact('permissions'));
    }
}
