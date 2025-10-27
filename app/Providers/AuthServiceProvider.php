<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Dynamically register all permissions from database as Gates
        // Wrap in try-catch to avoid errors during migrations
        try {
            Permission::where('is_active', true)->each(function ($permission) {
                Gate::define($permission->name, function (User $user) use ($permission) {
                    
                    // 1. Check Super Admin permission (bypass all checks)
                    if ($user->isSuperAdmin()) {
                        return true;
                    }

                    // 2. Check permission through role memberships in groups
                    return $user->roles()->whereHas('permissions', function ($query) use ($permission) {
                        $query->where('permissions.id', $permission->id);
                    })->exists();
                });
            });

            // Register some common administrative gates
            Gate::define('manage-system', function (User $user) {
                return $user->isSuperAdmin();
            });

            Gate::define('manage-users', function (User $user) {
                return Gate::forUser($user)->check('manage_users') || $user->isAdmin();
            });

            Gate::define('manage-groups', function (User $user) {
                return Gate::forUser($user)->check('manage_groups') || $user->isAdmin();
            });

            Gate::define('manage-permissions', function (User $user) {
                return Gate::forUser($user)->check('manage_permissions') || $user->isSuperAdmin();
            });

        } catch (\Exception $e) {
            // Ignore errors if tables don't exist yet (during migrations)
            \Log::warning('Could not register permission gates: ' . $e->getMessage());
        }
    }
}
