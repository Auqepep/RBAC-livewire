<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Group;

class EditGroupDetails extends Component
{
    public Group $group;
    public $name;
    public $description;
    public $is_active;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'is_active' => 'boolean',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
        $this->name = $group->name;
        $this->description = $group->description;
        $this->is_active = $group->is_active;
    }

    public function saveDetails()
    {
        $this->validate();

        $this->group->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('group-updated');
        session()->flash('success', 'Group details updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.edit-group-details');
    }
}
