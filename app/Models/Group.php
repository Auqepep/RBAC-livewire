<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the group members (pivot table records)
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    /**
     * Get the users that belong to this group through the group_members table
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot(['added_by', 'joined_at'])
                    ->withTimestamps();
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
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Add a user to this group
     */
    public function addMember($userId, $addedBy = null): void
    {
        if (!$this->hasMember($userId)) {
            $this->users()->attach($userId, [
                'added_by' => $addedBy ?: $userId,
                'joined_at' => now()
            ]);
        }
    }

    /**
     * Remove a user from this group
     */
    public function removeMember($userId): void
    {
        $this->users()->detach($userId);
    }

    /**
     * Get join requests for this group
     */
    public function joinRequests(): HasMany
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    /**
     * Get pending join requests for this group
     */
    public function pendingJoinRequests(): HasMany
    {
        return $this->hasMany(GroupJoinRequest::class)->where('status', 'pending');
    }
}
