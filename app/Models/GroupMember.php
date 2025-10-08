<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'role_id',
        'assigned_by',
        'joined_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime'
    ];

    /**
     * Get the group this membership belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user this membership belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role assigned in this membership
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user who assigned this member
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if this membership is for a specific role
     */
    public function hasRole($roleId): bool
    {
        return $this->role_id == $roleId;
    }

    /**
     * Check if this member can manage another member based on hierarchy
     */
    public function canManage(GroupMember $otherMember): bool
    {
        // Must be in the same group
        if ($this->group_id !== $otherMember->group_id) {
            return false;
        }

        // Check hierarchy levels
        return $this->role->hierarchy_level > $otherMember->role->hierarchy_level;
    }

    /**
     * Assign a user to a role within a specific group
     */
    public static function assignUserToGroupRole($userId, $groupId, $roleId, $assignedBy = null): GroupMember
    {
        // Remove any existing membership in this group
        static::where('user_id', $userId)
              ->where('group_id', $groupId)
              ->delete();

        return static::create([
            'user_id' => $userId,
            'group_id' => $groupId,
            'role_id' => $roleId,
            'assigned_by' => $assignedBy ?? auth()->id(),
            'joined_at' => now(),
        ]);
    }
}
