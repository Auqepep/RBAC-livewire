<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
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
        ];
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
     * Check if user has permission (through their roles)
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function($query) use ($permissionName) {
                        $query->where('name', $permissionName);
                    })
                    ->exists();
    }

    /**
     * Check if user is system admin (has Super Admin or Admin role)
     */
    public function isSystemAdmin(): bool
    {
        return $this->hasAnyRole(['Super Admin', 'Admin']);
    }

    /**
     * Check if user can manage system
     */
    public function canManageSystem(): bool
    {
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
}
