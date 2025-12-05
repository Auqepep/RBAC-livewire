<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class RbacCacheService
{
    /**
     * Cache duration in seconds
     * 1 hour = 3600 seconds
     */
    const CACHE_TTL = 3600;

    /**
     * Cache key prefixes
     */
    const PREFIX_USER_PERMISSIONS = 'user_permissions:';
    const PREFIX_USER_GROUPS = 'user_groups:';
    const PREFIX_USER_ROLES = 'user_roles:';
    const PREFIX_GROUP_MEMBERS = 'group_members:';
    const PREFIX_ROLE_PERMISSIONS = 'role_permissions:';

    /**
     * Get user's permissions with caching
     */
    public function getUserPermissions(int $userId): array
    {
        $cacheKey = self::PREFIX_USER_PERMISSIONS . $userId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $user = User::with('groupMembers.role.permissions')->find($userId);
            
            if (!$user) {
                return [];
            }

            $permissions = [];
            
            foreach ($user->groupMembers as $membership) {
                if ($membership->role && $membership->role->permissions) {
                    foreach ($membership->role->permissions as $permission) {
                        $permissions[$permission->name] = [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'display_name' => $permission->display_name,
                            'group_id' => $membership->group_id,
                            'group_name' => $membership->group->name,
                        ];
                    }
                }
            }

            return array_values($permissions);
        });
    }

    /**
     * Get user's groups with caching
     */
    public function getUserGroups(int $userId): array
    {
        $cacheKey = self::PREFIX_USER_GROUPS . $userId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return User::find($userId)
                ->groupMembers()
                ->with(['group', 'role'])
                ->get()
                ->map(function ($membership) {
                    return [
                        'group_id' => $membership->group_id,
                        'group_name' => $membership->group->name,
                        'role_id' => $membership->role_id,
                        'role_name' => $membership->role?->name,
                        'joined_at' => $membership->joined_at,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get user's roles with caching
     */
    public function getUserRoles(int $userId): array
    {
        $cacheKey = self::PREFIX_USER_ROLES . $userId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return User::find($userId)
                ->groupMembers()
                ->with('role')
                ->get()
                ->pluck('role')
                ->filter()
                ->unique('id')
                ->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                        'hierarchy_level' => $role->hierarchy_level,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get group members with caching
     */
    public function getGroupMembers(int $groupId): array
    {
        $cacheKey = self::PREFIX_GROUP_MEMBERS . $groupId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($groupId) {
            $group = Group::with('groupMembers.user', 'groupMembers.role')->find($groupId);
            
            if (!$group) {
                return [];
            }

            return $group->groupMembers->map(function ($member) {
                return [
                    'user_id' => $member->user_id,
                    'user_name' => $member->user->name,
                    'user_email' => $member->user->email,
                    'role_id' => $member->role_id,
                    'role_name' => $member->role?->name,
                    'joined_at' => $member->joined_at,
                ];
            })->toArray();
        });
    }

    /**
     * Get role permissions with caching
     */
    public function getRolePermissions(int $roleId): array
    {
        $cacheKey = self::PREFIX_ROLE_PERMISSIONS . $roleId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($roleId) {
            $role = Role::with('permissions')->find($roleId);
            
            if (!$role) {
                return [];
            }

            return $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'description' => $permission->description,
                ];
            })->toArray();
        });
    }

    /**
     * Check if user has permission (cached)
     */
    public function userHasPermission(int $userId, string $permissionName): bool
    {
        $permissions = $this->getUserPermissions($userId);
        
        return collect($permissions)->contains('name', $permissionName);
    }

    /**
     * Check if user has permission in specific group (cached)
     */
    public function userHasPermissionInGroup(int $userId, int $groupId, string $permissionName): bool
    {
        $permissions = $this->getUserPermissions($userId);
        
        return collect($permissions)
            ->where('group_id', $groupId)
            ->contains('name', $permissionName);
    }

    /**
     * Clear all cache for a specific user
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget(self::PREFIX_USER_PERMISSIONS . $userId);
        Cache::forget(self::PREFIX_USER_GROUPS . $userId);
        Cache::forget(self::PREFIX_USER_ROLES . $userId);
    }

    /**
     * Clear cache for a specific group
     */
    public function clearGroupCache(int $groupId): void
    {
        Cache::forget(self::PREFIX_GROUP_MEMBERS . $groupId);
    }

    /**
     * Clear cache for a specific role
     */
    public function clearRoleCache(int $roleId): void
    {
        Cache::forget(self::PREFIX_ROLE_PERMISSIONS . $roleId);
    }

    /**
     * Clear cache when user joins/leaves a group
     */
    public function clearUserGroupCache(int $userId, int $groupId): void
    {
        $this->clearUserCache($userId);
        $this->clearGroupCache($groupId);
    }

    /**
     * Clear cache when role permissions change
     */
    public function clearRolePermissionCache(int $roleId): void
    {
        $this->clearRoleCache($roleId);
        
        // Also clear cache for all users with this role
        $role = Role::with('groupMembers')->find($roleId);
        if ($role) {
            foreach ($role->groupMembers as $member) {
                $this->clearUserCache($member->user_id);
            }
        }
    }

    /**
     * Clear all RBAC caches
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Warm up cache for a user (preload data)
     */
    public function warmUpUserCache(int $userId): void
    {
        $this->getUserPermissions($userId);
        $this->getUserGroups($userId);
        $this->getUserRoles($userId);
    }
}
