<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * Get users that have this permission through roles
     */
    public function users()
    {
        return User::whereHas('roles.permissions', function ($query) {
            $query->where('permissions.id', $this->id);
        });
    }

    /**
     * Scope by module
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }
}
