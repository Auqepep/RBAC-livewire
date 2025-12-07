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
    
    /**
     * Redirect to third-party app using standard OAuth 2.0 flow
     */
    private function redirectToThirdPartyApp()
    {
        if (!$this->group->oauth_client_id) {
            // OAuth client ID is required for secure authentication
            abort(500, 'OAuth client not configured for this group. Please contact administrator.');
        }
        
        // Build OAuth authorization URL
        $params = [
            'client_id' => $this->group->oauth_client_id,
            'redirect_uri' => $this->group->third_party_app_url,
            'response_type' => 'code',
            'scope' => '*', // Grant all scopes (since we control both authorization server and client)
            'state' => base64_encode(json_encode([
                'group_id' => $this->group->id,
                'timestamp' => now()->timestamp,
                'nonce' => bin2hex(random_bytes(16))
            ]))
        ];
        
        // Redirect to our own OAuth authorization endpoint
        $this->redirectUrl = route('passport.authorizations.authorize', $params);
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
