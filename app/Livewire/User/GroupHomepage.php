<?php

namespace App\Livewire\User;

use App\Models\Group;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GroupHomepage extends Component
{
    public $group;
    public $isMember = false;

    public function mount($groupId)
    {
        $this->group = Group::with(['groupMembers.user', 'groupMembers.role', 'creator', 'roles'])->findOrFail($groupId);
        $this->isMember = $this->group->hasMember(Auth::id());
        
        // Only allow access if user is a member or admin
        if (!$this->isMember && !Auth::user()->canManageSystem()) {
            abort(403, 'You do not have permission to view this group.');
        }
    }

    public function render()
    {
        return view('livewire.user.group-homepage');
    }
}
