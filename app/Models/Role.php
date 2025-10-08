<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'badge_color',
        'hierarchy_level',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hierarchy_level' => 'integer',
    ];

    /**
     * Get permissions assigned to this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * Get group members that have this role
     */
    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get users that have this role (through group membership)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot(['group_id', 'assigned_by', 'joined_at'])
                    ->withTimestamps();
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Assign permission to role
     */
    public function givePermission(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission && !$this->hasPermission($permission->name)) {
            $this->permissions()->attach($permission->id);
        }
    }

    /**
     * Remove permission from role
     */
    public function revokePermission(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }

    /**
     * Get the CSS style for role badge based on color
     */
    public function getBadgeStyleAttribute(): string
    {
        return 'background-color: ' . $this->attributes['badge_color'] . '; color: white;';
    }

    /**
     * Check if this role is higher in hierarchy than another role
     */
    public function isHigherThan(Role $role): bool
    {
        return $this->hierarchy_level > $role->hierarchy_level;
    }

    /**
     * Check if this role is lower in hierarchy than another role  
     */
    public function isLowerThan(Role $role): bool
    {
        return $this->hierarchy_level < $role->hierarchy_level;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByHierarchy($query)
    {
        return $query->orderByDesc('hierarchy_level');
    }
}
