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
                    
                    // 1. Check System Admin permission (bypass all checks)
                    if ($user->canManageSystem()) {
                        return true;
                    }

                    // 2. Check permission through role memberships in groups
                    return $user->roles()->whereHas('permissions', function ($query) use ($permission) {
                        $query->where('permissions.id', $permission->id);
                    })->exists();
                });
            });

            // Register alias gates with hyphens for backwards compatibility
            Permission::where('is_active', true)->each(function ($permission) {
                $hyphenName = str_replace('_', '-', $permission->name);
                if ($hyphenName !== $permission->name) {
                    Gate::define($hyphenName, function (User $user) use ($permission) {
                        return $user->can($permission->name);
                    });
                }
            });

        } catch (\Exception $e) {
            // Ignore errors if tables don't exist yet (during migrations)
            \Log::warning('Could not register permission gates: ' . $e->getMessage());
        }
    }
}
