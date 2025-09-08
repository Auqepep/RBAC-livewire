<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;

class GroupsTable extends Component
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

    public function deleteGroup($groupId)
    {
        $group = Group::find($groupId);
        
        if (!$group) {
            session()->flash('error', 'Group not found.');
            return;
        }

        // Check if group has members
        if ($group->users()->count() > 0) {
            session()->flash('error', 'Cannot delete group that has members.');
            return;
        }

        $group->delete();

        session()->flash('message', 'Group deleted successfully.');
    }

    public function toggleStatus($groupId)
    {
        $group = Group::find($groupId);
        
        if (!$group) {
            session()->flash('error', 'Group not found.');
            return;
        }

        $group->update(['is_active' => !$group->is_active]);
        
        $status = $group->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Group {$status} successfully.");
    }

    public function render()
    {
        $query = Group::with(['creator', 'users'])
            ->withCount('users');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status !== '') {
            $query->where('is_active', $this->status);
        }

        $groups = $query->orderBy($this->sortField, $this->sortDirection)
                       ->paginate($this->perPage);

        return view('livewire.admin.groups-table', compact('groups'));
    }
}
