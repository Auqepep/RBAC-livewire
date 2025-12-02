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
        
        // If access granted and redirect is enabled, redirect to third-party app
        if ($this->hasGatewayAccess && $this->group->enable_gateway_redirect && $this->group->third_party_app_url) {
            $this->redirectToThirdPartyApp();
        }
    }
    
    public $redirectUrl = null;
    
    private function redirectToThirdPartyApp()
    {
        $baseUrl = $this->group->third_party_app_url;
        
        // Add user information as query parameters for the third-party app
        $params = [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'user_name' => Auth::user()->name,
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'role' => $this->userRole?->name,
            'role_display' => $this->userRole?->display_name,
            'timestamp' => now()->timestamp,
        ];
        
        // If OAuth client is configured, add it
        if ($this->group->oauth_client_id) {
            $params['client_id'] = $this->group->oauth_client_id;
        }
        
        // Build the full redirect URL
        $this->redirectUrl = $baseUrl . '?' . http_build_query($params);
    }

    private function checkGatewayAccess($membership)
    {
        if (!$membership->role) {
            $this->accessDeniedReason = 'You do not have a role assigned in this group.';
            $this->hasGatewayAccess = false;
            return;
        }

        // Check if the role belongs to this specific group
        if ($membership->role->group_id !== $this->group->id) {
            $this->accessDeniedReason = 'Your role does not belong to this group.';
            $this->hasGatewayAccess = false;
            return;
        }

        // Define role names that can access the gateway (group-specific roles)
        $gatewayRoleNames = ['admin', 'administrator', 'staff', 'manager', 'supervisor'];
        
        // Check if user's role in this specific group has gateway access
        if (in_array(strtolower($membership->role->name), $gatewayRoleNames)) {
            $this->hasGatewayAccess = true;
        } else {
            $this->accessDeniedReason = "Your role '{$membership->role->display_name}' in the '{$this->group->name}' group does not have gateway access.";
            $this->hasGatewayAccess = false;
        }

        // System admins always have access (bypass group-specific restrictions)
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
