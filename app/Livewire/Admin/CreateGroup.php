<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateGroup extends Component
{
    public $name = '';
    public $description = '';
    public $is_active = true;
    public $selectedUsers = [];

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'selectedUsers' => ['array'],
            'selectedUsers.*' => ['exists:users,id'],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            $group = Group::create([
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'created_by' => Auth::id(),
            ]);

            // Add selected users as members
            if (!empty($this->selectedUsers)) {
                $group->users()->attach($this->selectedUsers, [
                    'added_by' => Auth::id(),
                    'joined_at' => now(),
                ]);
            }

            session()->flash('message', 'Group created successfully.');
            
            return $this->redirect(route('admin.groups.index'));
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while creating the group.');
        }
    }

    public function cancel()
    {
        return $this->redirect(route('admin.groups.index'));
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        
        return view('livewire.admin.create-group', compact('users'));
    }
}
