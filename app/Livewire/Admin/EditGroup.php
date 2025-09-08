<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditGroup extends Component
{
    public Group $group;
    public $name = '';
    public $description = '';
    public $is_active = true;
    public $selectedUsers = [];

    public function mount(Group $group)
    {
        $this->group = $group;
        $this->name = $group->name;
        $this->description = $group->description ?? '';
        $this->is_active = $group->is_active;
        $this->selectedUsers = $group->users->pluck('id')->toArray();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('groups', 'name')->ignore($this->group->id)],
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
            $this->group->update([
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            // Sync group members
            $syncData = [];
            foreach ($this->selectedUsers as $userId) {
                $syncData[$userId] = [
                    'added_by' => Auth::id(),
                    'joined_at' => now(),
                ];
            }
            
            $this->group->users()->sync($syncData);

            session()->flash('message', 'Group updated successfully.');
            
            return $this->redirect(route('admin.groups.index'));
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the group.');
        }
    }

    public function cancel()
    {
        return $this->redirect(route('admin.groups.index'));
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        
        return view('livewire.admin.edit-group', compact('users'));
    }
}
