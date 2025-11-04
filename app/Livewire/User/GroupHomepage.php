<?php

namespace App\Livewire\User;

use App\Models\Group;
use App\Models\GroupMember;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GroupHomepage extends Component
{
    public $group;
    public $isMember = false;
    public $isManager = false;
    public $canEdit = false;
    public $canManageMembers = false;

    public function mount($groupId)
    {
        $this->group = Group::with(['groupMembers.user', 'groupMembers.role', 'creator'])->findOrFail($groupId);
        $this->isMember = $this->group->hasMember(Auth::id());
        
        // Only allow access if user is a member or admin
        if (!$this->isMember && !Auth::user()->canManageSystem()) {
            abort(403, 'You do not have permission to view this group.');
        }

        // Check if user is a manager or has elevated permissions
        $this->checkManagerStatus();
        
        // Check specific permissions using the policy
        $this->canEdit = Gate::allows('update', $this->group);
        $this->canManageMembers = Gate::allows('manageMembers', $this->group);
    }

    protected function checkManagerStatus()
    {
        $membership = GroupMember::where('user_id', Auth::id())
            ->where('group_id', $this->group->id)
            ->with('role')
            ->first();

        if ($membership && $membership->role) {
            // Manager role has hierarchy_level >= 70
            $this->isManager = $membership->role->hierarchy_level >= 70;
        }
    }

    public function render()
    {
        return view('livewire.user.group-homepage');
    }
}
