<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupMember;
use App\Models\Role;
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
    public $userRoles = []; // Store role assignments for each user

    public function mount(Group $group)
    {
        $this->group = $group;
        $this->name = $group->name;
        $this->description = $group->description ?? '';
        $this->is_active = $group->is_active;
        
        // Load existing members and their roles
        $this->selectedUsers = $group->users->pluck('id')->toArray();
        
        // Initialize userRoles array with existing role assignments
        $this->userRoles = [];
        foreach ($group->groupMembers as $member) {
            $this->userRoles[$member->user_id] = $member->role_id;
        }
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('groups', 'name')->ignore($this->group->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'selectedUsers' => ['array'],
            'selectedUsers.*' => ['exists:users,id'],
            'userRoles' => ['array'],
            'userRoles.*' => ['exists:roles,id'],
        ];
    }

    public function updated($propertyName)
    {
        // When a user is added to selectedUsers, set default role if not already set
        if ($propertyName === 'selectedUsers') {
            $defaultRole = Role::where('name', 'Member')->orWhere('name', 'Staff')->first();
            foreach ($this->selectedUsers as $userId) {
                if (!isset($this->userRoles[$userId])) {
                    $this->userRoles[$userId] = $defaultRole ? $defaultRole->id : null;
                }
            }
            
            // Remove roles for users that are no longer selected
            $this->userRoles = array_intersect_key($this->userRoles, array_flip($this->selectedUsers));
        }
        
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

            // First, remove all existing group members
            $this->group->groupMembers()->delete();

            // Add new role assignments for selected users
            foreach ($this->selectedUsers as $userId) {
                $roleId = $this->userRoles[$userId] ?? null;
                
                GroupMember::create([
                    'group_id' => $this->group->id,
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'is_active' => true,
                    'joined_at' => now(),
                ]);
            }

            session()->flash('message', 'Group updated successfully.');
            
            return $this->redirect(route('admin.groups.index'));
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the group: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('admin.groups.index'));
    }

    public function addUser($userId)
    {
        if (!in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
            $defaultRole = Role::where('name', 'Member')->orWhere('name', 'Staff')->first();
            $this->userRoles[$userId] = $defaultRole ? $defaultRole->id : null;
        }
    }

    public function removeUser($userId)
    {
        $this->selectedUsers = array_values(array_filter($this->selectedUsers, function($id) use ($userId) {
            return $id != $userId;
        }));
        unset($this->userRoles[$userId]);
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        $roles = Role::with('permissions')->orderBy('hierarchy_level', 'desc')->orderBy('name')->get();
        
        return view('livewire.admin.edit-group', compact('users', 'roles'));
    }
}
