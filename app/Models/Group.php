<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
        'third_party_app_url',
        'oauth_client_id',
        'enable_gateway_redirect',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enable_gateway_redirect' => 'boolean',
    ];

    /**
     * Get the group members (new user-centric structure)
     */
    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get the users that belong to this group through the group_members table
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot(['role_id', 'joined_at'])
                    ->withTimestamps();
    }

    /**
     * Alias for users() method for backward compatibility
     */
    public function members(): BelongsToMany
    {
        return $this->users();
    }

    /**
     * Get the user who created this group
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if a user is a member of this group
     */
    public function hasMember($userId): bool
    {
        return $this->groupMembers()->where('user_id', $userId)->exists();
    }

    /**
     * Add a user to this group with a specific role
     */
    public function addMember($userId, $roleId = null, $assignedBy = null): GroupMember
    {
        $user = User::findOrFail($userId);
        
        return GroupMember::create([
            'group_id' => $this->id,
            'user_id' => $userId,
            'role_id' => $roleId,
            'assigned_by' => $assignedBy ?? auth()->id(),
            'joined_at' => now(),
        ]);
    }

    /**
     * Change a user's role in this group
     */
    public function changeUserRole($userId, $roleId, $assignedBy = null): bool
    {
        $member = $this->groupMembers()->where('user_id', $userId)->first();
        
        if (!$member) {
            return false;
        }
        
        return $member->update([
            'role_id' => $roleId,
            'assigned_by' => $assignedBy ?? auth()->id(),
        ]);
    }

    /**
     * Remove a user from this group
     */
    public function removeMember($userId): void
    {
        $this->groupMembers()->where('user_id', $userId)->delete();
    }

    /**
     * Remove a specific role from a user in this group
     */
    public function removeUserRole($userId, $roleId = null): bool
    {
        $query = $this->groupMembers()->where('user_id', $userId);
        
        if ($roleId) {
            $query->where('role_id', $roleId);
        }
        
        return $query->delete() > 0;
    }

    /**
     * Get user's roles in this group
     */
    public function getUserRoles($userId)
    {
        return $this->groupMembers()
                   ->where('user_id', $userId)
                   ->with('role')
                   ->get()
                   ->pluck('role')
                   ->filter();
    }

    /**
     * Check if user has a specific role in this group
     */
    public function userHasRole($userId, $roleId): bool
    {
        return $this->groupMembers()
                   ->where('user_id', $userId)
                   ->where('role_id', $roleId)
                   ->exists();
    }

    /**
     * Get all unique roles used in this group
     */
    public function getAllRoles()
    {
        return Role::whereIn('id', 
            $this->groupMembers()
                ->whereNotNull('role_id')
                ->pluck('role_id')
                ->unique()
        )->get();
    }

    /**
     * Get all permissions available through this group's roles
     */
    public function getAllPermissions(): array
    {
        $permissions = [];
        $roles = $this->getAllRoles();
        
        foreach ($roles as $role) {
            $rolePermissions = $role->permissions()->pluck('name')->toArray();
            $permissions = array_merge($permissions, $rolePermissions);
        }
        
        return array_unique($permissions);
    }

    /**
     * Get users with a specific role in this group
     */
    public function getUsersWithRole($roleId)
    {
        return User::whereIn('id', 
            $this->groupMembers()
                ->where('role_id', $roleId)
                ->pluck('user_id')
        )->get();
    }

    /**
     * Get the highest hierarchy level in this group (based on roles)
     */
    public function getHighestHierarchyLevel(): int
    {
        $roles = $this->getAllRoles();
        $maxLevel = 0;
        
        foreach ($roles as $role) {
            if ($role->hierarchy_level && $role->hierarchy_level > $maxLevel) {
                $maxLevel = $role->hierarchy_level;
            }
        }
        
        return $maxLevel;
    }

    /**
     * Get the roles that are currently used in this group (through group members)
     * This returns roles that have been assigned to at least one member.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'group_members', 'group_id', 'role_id')
                    ->distinct();
    }
}
