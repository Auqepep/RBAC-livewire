<?php

namespace App\Observers;

use App\Models\GroupMember;
use App\Services\RbacCacheService;

class GroupMemberObserver
{
    protected RbacCacheService $cacheService;

    public function __construct(RbacCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the GroupMember "created" event.
     */
    public function created(GroupMember $groupMember): void
    {
        $this->cacheService->clearUserGroupCache(
            $groupMember->user_id,
            $groupMember->group_id
        );
    }

    /**
     * Handle the GroupMember "updated" event.
     */
    public function updated(GroupMember $groupMember): void
    {
        $this->cacheService->clearUserGroupCache(
            $groupMember->user_id,
            $groupMember->group_id
        );
    }

    /**
     * Handle the GroupMember "deleted" event.
     */
    public function deleted(GroupMember $groupMember): void
    {
        $this->cacheService->clearUserGroupCache(
            $groupMember->user_id,
            $groupMember->group_id
        );
    }
}
