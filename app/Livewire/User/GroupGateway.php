<?php

namespace App\Livewire\User;

use App\Models\Group;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GroupGateway extends Component
{
    public $group;
    public $userRole;
    public $hasGatewayAccess = false;
    public $accessDeniedReason = '';

    public function mount($groupId)
    {
        // Find the group
        $this->group = Group::with(['groupMembers.user', 'groupMembers.role'])->findOrFail($groupId);
        
        // Check if user is a member of this group
        $membership = $this->group->groupMembers()->where('user_id', Auth::id())->first();
        
        if (!$membership) {
            $this->accessDeniedReason = 'You are not a member of this group.';
            $this->hasGatewayAccess = false;
            return;
        }

        $this->userRole = $membership->role;
        
        // Check if user's role in this group has gateway access
        $this->checkGatewayAccess($membership);
    }

    private function checkGatewayAccess($membership)
    {
        // Define roles that can access the gateway
        $gatewayRoles = ['admin', 'administrator', 'staff', 'manager'];
        
        if (!$membership->role) {
            $this->accessDeniedReason = 'You do not have a role assigned in this group.';
            $this->hasGatewayAccess = false;
            return;
        }

        // Check if user's role is in the allowed gateway roles
        if (in_array(strtolower($membership->role->name), $gatewayRoles)) {
            $this->hasGatewayAccess = true;
        } else {
            $this->accessDeniedReason = "Your role '{$membership->role->display_name}' does not have gateway access for this group.";
            $this->hasGatewayAccess = false;
        }

        // System admins always have access
        if (Auth::user()->canManageSystem()) {
            $this->hasGatewayAccess = true;
            $this->accessDeniedReason = '';
        }
    }

    public function render()
    {
        return view('livewire.user.group-gateway')
            ->layout('components.user.layout');
    }
}
