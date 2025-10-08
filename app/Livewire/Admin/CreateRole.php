<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Group;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CreateRole extends Component
{
    public $group_id = '';
    public $name = '';
    public $display_name = '';
    public $description = '';
    public $badge_color = '#3B82F6';
    public $hierarchy_level = 1;
    public $is_active = true;
    public $selectedPermissions = [];

    protected function rules()
    {
        return [
            'group_id' => ['nullable', 'exists:groups,id'],
            'name' => ['required', 'string', 'max:255', 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'badge_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'hierarchy_level' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['boolean'],
            'selectedPermissions' => ['array'],
        ];
    }

    protected $messages = [
        'name.alpha_dash' => 'The role name may only contain letters, numbers, dashes and underscores.',
        'badge_color.regex' => 'The badge color must be a valid hex color code.',
        'hierarchy_level.min' => 'Hierarchy level must be at least 1.',
        'hierarchy_level.max' => 'Hierarchy level cannot exceed 10.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            $role = Role::create([
                'name' => $this->name,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'badge_color' => $this->badge_color,
                'hierarchy_level' => $this->hierarchy_level,
                'is_active' => $this->is_active,
            ]);

            // Attach selected permissions
            if (!empty($this->selectedPermissions)) {
                $role->permissions()->sync($this->selectedPermissions);
            }

            session()->flash('message', 'Role created successfully.');
            
            return $this->redirect(route('admin.roles.index'));
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while creating the role: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('admin.roles.index'));
    }

    public function render()
    {
        $permissions = Permission::all();
        $groups = Group::where('is_active', true)->orderBy('name')->get();
        
        return view('livewire.admin.create-role', compact('permissions', 'groups'));
    }
}
