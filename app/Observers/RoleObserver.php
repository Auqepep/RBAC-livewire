<?php

namespace App\Observers;

use App\Models\Role;
use App\Services\RbacCacheService;

class RoleObserver
{
    protected RbacCacheService $cacheService;

    public function __construct(RbacCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        // Clear cache for this role and all users with this role
        $this->cacheService->clearRolePermissionCache($role->id);
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        // Clear cache for this role and all users with this role
        $this->cacheService->clearRolePermissionCache($role->id);
    }
}
