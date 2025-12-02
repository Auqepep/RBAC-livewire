<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OAuthController extends Controller
{
    /**
     * Get authenticated user information
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->email_verified_at !== null,
            'is_super_admin' => $user->is_super_admin,
            'groups' => $user->groupMembers()->with(['group', 'role'])->get()->map(function ($membership) {
                return [
                    'group_id' => $membership->group_id,
                    'group_name' => $membership->group->name,
                    'role_id' => $membership->role_id,
                    'role_name' => $membership->role?->display_name,
                ];
            }),
            'permissions' => $this->getUserPermissions($user),
        ]);
    }

    /**
     * Get user's permissions (cached)
     */
    protected function getUserPermissions(User $user): array
    {
        return app(\App\Services\RbacCacheService::class)
            ->getUserPermissions($user->id);
    }

    /**
     * Logout and revoke tokens
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        
        return response()->json([
            'message' => 'Successfully logged out and revoked all tokens'
        ]);
    }
}
