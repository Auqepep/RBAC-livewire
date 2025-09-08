<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\Permission;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CreateRole extends Component
{
    public $name = '';
    public $display_name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $is_active = true;
    public $selectedPermissions = [];

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:roles,name'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['exists:permissions,id'],
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
            $role = Role::create([
                'name' => $this->name,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'color' => $this->color,
                'is_active' => $this->is_active,
            ]);

            // Attach selected permissions
            if (!empty($this->selectedPermissions)) {
                $role->permissions()->attach($this->selectedPermissions);
            }

            session()->flash('message', 'Role created successfully.');
            
            return $this->redirect(route('admin.roles.index'));
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while creating the role.');
        }
    }

    public function cancel()
    {
        return $this->redirect(route('admin.roles.index'));
    }

    public function render()
    {
        $permissions = Permission::orderBy('display_name')->get();
        
        return view('livewire.admin.create-role', compact('permissions'));
    }
}
