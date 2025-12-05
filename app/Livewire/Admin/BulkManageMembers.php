<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Group;
use App\Models\User;
use App\Models\Role;
use App\Models\GroupMember;

class BulkManageMembers extends Component
{
    use WithPagination;

    public Group $group;
    public $selectedMembers = [];
    public $bulkAction = '';
    public $newRoleId = null;
    public $search = '';
    public $showConfirmModal = false;
    public $confirmationMessage = '';

    protected $rules = [
        'newRoleId' => 'required_if:bulkAction,change_role|exists:roles,id',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function selectAll()
    {
        $members = $this->group->groupMembers()
                             ->when($this->search, function($query) {
                                 $query->whereHas('user', function($q) {
                                     $q->where('name', 'like', '%' . $this->search . '%')
                                       ->orWhere('email', 'like', '%' . $this->search . '%');
                                 });
                             })
                             ->pluck('id')
                             ->toArray();

        $this->selectedMembers = $members;
    }

    public function selectNone()
    {
        $this->selectedMembers = [];
    }

    public function confirmBulkAction()
    {
        if (empty($this->selectedMembers)) {
            session()->flash('error', 'Please select at least one member.');
            return;
        }

        if (empty($this->bulkAction)) {
            session()->flash('error', 'Please select an action.');
            return;
        }

        switch ($this->bulkAction) {
            case 'remove':
                $this->confirmationMessage = 'Are you sure you want to remove ' . count($this->selectedMembers) . ' member(s) from this group?';
                break;
            case 'change_role':
                if (!$this->newRoleId) {
                    session()->flash('error', 'Please select a role.');
                    return;
                }
                $role = Role::find($this->newRoleId);
                $this->confirmationMessage = 'Are you sure you want to change the role of ' . count($this->selectedMembers) . ' member(s) to "' . $role->display_name . '"?';
                break;
            default:
                session()->flash('error', 'Invalid action selected.');
                return;
        }

        $this->showConfirmModal = true;
    }

    public function executeBulkAction()
    {
        $this->validate();

        try {
            switch ($this->bulkAction) {
                case 'remove':
                    $this->bulkRemoveMembers();
                    break;
                case 'change_role':
                    $this->bulkChangeRole();
                    break;
            }

            $this->showConfirmModal = false;
            $this->selectedMembers = [];
            $this->bulkAction = '';
            $this->newRoleId = null;

        } catch (\Exception $e) {
            session()->flash('error', 'Error executing bulk action: ' . $e->getMessage());
        }
    }

    private function bulkRemoveMembers()
    {
        $count = 0;
        foreach ($this->selectedMembers as $memberId) {
            $member = GroupMember::where('id', $memberId)
                                ->where('group_id', $this->group->id)
                                ->first();

            if ($member && $member->user_id !== auth()->id()) {
                $member->delete();
                $count++;
            }
        }

        session()->flash('success', "Successfully removed {$count} member(s) from the group.");
    }

    private function bulkChangeRole()
    {
        $count = 0;
        foreach ($this->selectedMembers as $memberId) {
            $member = GroupMember::where('id', $memberId)
                                ->where('group_id', $this->group->id)
                                ->first();

            if ($member) {
                $member->update(['role_id' => $this->newRoleId]);
                $count++;
            }
        }

        $role = Role::find($this->newRoleId);
        session()->flash('success', "Successfully changed role of {$count} member(s) to {$role->display_name}.");
    }

    public function cancelBulkAction()
    {
        $this->showConfirmModal = false;
        $this->bulkAction = '';
        $this->newRoleId = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedMembers = [];
    }

    public function render()
    {
        $members = $this->group->groupMembers()
                             ->with(['user', 'role'])
                             ->when($this->search, function($query) {
                                 $query->whereHas('user', function($q) {
                                     $q->where('name', 'like', '%' . $this->search . '%')
                                       ->orWhere('email', 'like', '%' . $this->search . '%');
                                 });
                             })
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        // Get all active roles (roles are global, not group-specific)
        $groupRoles = Role::where('is_active', true)
                         ->orderBy('hierarchy_level', 'desc')
                         ->get();

        return view('livewire.admin.bulk-manage-members', [
            'members' => $members,
            'groupRoles' => $groupRoles,
        ]);
    }
}
