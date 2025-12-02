<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\RbacCacheService;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Get RBAC Cache Service instance
     */
    protected function cacheService(): RbacCacheService
    {
        return app(RbacCacheService::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_super_admin' => 'boolean',
        ];
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * Get the groups this user belongs to
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot(['role_id', 'assigned_by', 'joined_at'])
                    ->withTimestamps();
    }

    /**
     * Get the roles this user has (through group membership)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'group_members')
                    ->withPivot(['group_id', 'assigned_by', 'joined_at'])
                    ->withTimestamps();
    }

    /**
     * Get group memberships for this user
     */
    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Alias for groupMemberships (for consistency)
     */
    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has permission (through their roles) - CACHED
     */
    public function hasPermission(string $permissionName): bool
    {
        // Use cache service for better performance
        return $this->cacheService()->userHasPermission($this->id, $permissionName);
    }

    /**
     * Check if user is system admin (has admin role or system_admin permission)
     */
    public function isSystemAdmin(): bool
    {
        // Check global permissions first
        if ($this->hasPermission('manage_system') || $this->hasPermission('system_admin')) {
            return true;
        }

        // Check if user is member of the global "Administrators" group
        $adminGroup = \App\Models\Group::where('name', 'Administrators')->first();
        
        if (!$adminGroup) {
            return false;
        }
        
        return $this->groupMembers()
            ->where('group_id', $adminGroup->id)
            ->exists();
    }

    /**
     * Check if user can manage system
     */
    public function canManageSystem(): bool
    {
        // Super admins (permanent admins) always have system access
        if ($this->is_super_admin) {
            return true;
        }
        
        return $this->isSystemAdmin();
    }

    /**
     * Get user's roles in a specific group
     */
    public function getRolesInGroup(int $groupId)
    {
        return $this->groupMemberships()
                    ->where('group_id', $groupId)
                    ->with('role')
                    ->get()
                    ->pluck('role');
    }

    /**
     * Assign user to a group with a role
     */
    public function assignToGroup(int $groupId, int $roleId, int $assignedBy = null): void
    {
        GroupMember::create([
            'group_id' => $groupId,
            'user_id' => $this->id,
            'role_id' => $roleId,
            'assigned_by' => $assignedBy ?? auth()->id(),
            'joined_at' => now()
        ]);
    }

    /**
     * Remove user from a group
     */
    public function removeFromGroup(int $groupId): void
    {
        $this->groupMemberships()
             ->where('group_id', $groupId)
             ->delete();
    }

    /**
     * Get user's highest role hierarchy level
     */
    public function getHighestRoleLevel(): int
    {
        return $this->roles()->max('hierarchy_level') ?? 0;
    }

    /**
     * Check if user has minimum role level
     */
    public function hasMinimumLevel(int $minimumLevel): bool
    {
        return $this->getHighestRoleLevel() >= $minimumLevel;
    }

    /**
     * Check if user is Super Admin (permanent admin from database field)
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Check if user is Admin (Super Admin or Admin)
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['Super Admin', 'Admin', 'administrator', 'admin', 'super_admin']);
    }

    /**
     * Check if user is Manager level (Manager, Admin, or Super Admin)
     */
    public function isManager(): bool
    {
        return $this->hasMinimumLevel(4);
    }

    /**
     * Check if user is Supervisor level (Supervisor or above)
     */
    public function isSupervisor(): bool
    {
        return $this->hasMinimumLevel(3);
    }

    /**
     * Check if user is Staff level (Staff or above)
     */
    public function isStaff(): bool
    {
        return $this->hasMinimumLevel(2);
    }

    /**
     * Get user level as string
     */
    public function getUserLevel(): string
    {
        $level = $this->getHighestRoleLevel();
        
        return match($level) {
            6 => 'Super Admin',
            5 => 'Admin',
            4 => 'Manager', 
            3 => 'Supervisor',
            2 => 'Staff',
            1 => 'Member',
            default => 'Guest'
        };
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermission('manage_users');
    }

    /**
     * Check if user can manage groups
     */
    public function canManageGroups(): bool
    {
        return $this->hasPermission('manage_groups');
    }

    /**
     * Check if user can manage roles
     */
    public function canManageRoles(): bool
    {
        return $this->hasPermission('manage_roles');
    }

    /**
     * Check if user can view reports
     */
    public function canViewReports(): bool
    {
        return $this->hasPermission('view_reports');
    }

    /**
     * Check if user can approve requests
     */
    public function canApproveRequests(): bool
    {
        return $this->hasPermission('approve_requests');
    }

    /**
     * Check if user can assign roles in a specific group
     */
    public function canAssignRolesInGroup(int $groupId): bool
    {
        // System admins can assign roles in any group
        if ($this->isSystemAdmin()) {
            return true;
        }
        
        // Check if user has manage_group_members permission in this specific group
        return $this->hasPermissionInGroup($groupId, 'manage_group_members');
    }

    /**
     * Check if user has a specific permission in a specific group - CACHED
     */
    public function hasPermissionInGroup(int $groupId, string $permissionName): bool
    {
        // Use cache service for better performance
        return $this->cacheService()->userHasPermissionInGroup($this->id, $groupId, $permissionName);
    }

    /**
     * Check if user has all permissions (custom helper)
     */
    public function hasAllPermissions(array $abilities): bool
    {
        foreach ($abilities as $ability) {
            if (!\Gate::forUser($this)->check($ability)) {
                return false;
            }
        }
        return true;
    }
}
