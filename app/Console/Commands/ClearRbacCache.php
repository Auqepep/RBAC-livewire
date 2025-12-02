<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RbacCacheService;
use App\Models\User;

class ClearRbacCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:cache-clear 
                            {--user= : Clear cache for specific user ID}
                            {--all : Clear all RBAC caches}
                            {--stats : Show cache statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage RBAC cache (clear user, group, role caches)';

    protected RbacCacheService $cacheService;

    public function __construct(RbacCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('stats')) {
            $this->showStats();
            return 0;
        }

        if ($this->option('all')) {
            $this->cacheService->clearAllCache();
            $this->info('✓ All RBAC caches cleared successfully!');
            return 0;
        }

        if ($userId = $this->option('user')) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return 1;
            }

            $this->cacheService->clearUserCache($userId);
            $this->info("✓ Cache cleared for user: {$user->name} (ID: {$userId})");
            return 0;
        }

        $this->info('RBAC Cache Management');
        $this->info('--------------------');
        $this->line('');
        
        $choice = $this->choice(
            'What would you like to do?',
            [
                'Clear specific user cache',
                'Clear all RBAC caches',
                'Show cache statistics',
                'Warm up cache for a user',
                'Exit'
            ],
            4
        );

        switch ($choice) {
            case 'Clear specific user cache':
                $this->clearUserCache();
                break;
            case 'Clear all RBAC caches':
                $this->clearAllCaches();
                break;
            case 'Show cache statistics':
                $this->showStats();
                break;
            case 'Warm up cache for a user':
                $this->warmUpUserCache();
                break;
            default:
                $this->info('Goodbye!');
                break;
        }

        return 0;
    }

    protected function clearUserCache()
    {
        $userId = $this->ask('Enter user ID');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return;
        }

        $this->cacheService->clearUserCache($userId);
        $this->info("✓ Cache cleared for user: {$user->name}");
    }

    protected function clearAllCaches()
    {
        if ($this->confirm('Are you sure you want to clear ALL RBAC caches?', false)) {
            $this->cacheService->clearAllCache();
            $this->info('✓ All RBAC caches cleared successfully!');
        } else {
            $this->info('Operation cancelled.');
        }
    }

    protected function warmUpUserCache()
    {
        $userId = $this->ask('Enter user ID to warm up cache');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return;
        }

        $this->info("Warming up cache for {$user->name}...");
        $this->cacheService->warmUpUserCache($userId);
        $this->info('✓ Cache warmed up successfully!');
    }

    protected function showStats()
    {
        $this->info('RBAC Cache Statistics');
        $this->info('--------------------');
        
        $totalUsers = User::count();
        $this->line("Total Users: {$totalUsers}");
        
        $this->info("\nCache Configuration:");
        $this->line("Cache Driver: " . config('cache.default'));
        $this->line("Cache TTL: " . RbacCacheService::CACHE_TTL . " seconds (" . (RbacCacheService::CACHE_TTL / 60) . " minutes)");
        
        $this->info("\nCache Key Prefixes:");
        $this->line("- User Permissions: " . RbacCacheService::PREFIX_USER_PERMISSIONS);
        $this->line("- User Groups: " . RbacCacheService::PREFIX_USER_GROUPS);
        $this->line("- User Roles: " . RbacCacheService::PREFIX_USER_ROLES);
        $this->line("- Group Members: " . RbacCacheService::PREFIX_GROUP_MEMBERS);
        $this->line("- Role Permissions: " . RbacCacheService::PREFIX_ROLE_PERMISSIONS);
    }
}
