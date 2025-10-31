<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Gates for group management authorization
        \Gate::define('manage-group', function ($user, $group = null) {
            // If no group provided, deny access
            if (!$group) {
                return false;
            }
            
            // System admins can manage any group
            if ($user->canManageSystem()) {
                return true;
            }
            
            // Group admins and managers can manage their own groups
            return $group->groupMembers()
                        ->where('user_id', $user->id)
                        ->whereHas('role', function($query) {
                            $query->whereIn('name', ['admin', 'manager'])
                                  ->where('hierarchy_level', '>=', 50); // Manager level or higher
                        })
                        ->exists();
        });

        \Gate::define('edit-group-description', function ($user, $group = null) {
            // If no group provided, deny access
            if (!$group) {
                return false;
            }
            
            // System admins can edit any group
            if ($user->canManageSystem()) {
                return true;
            }
            
            // Group admins and managers can edit their group's description
            return $group->groupMembers()
                        ->where('user_id', $user->id)
                        ->whereHas('role', function($query) {
                            $query->whereIn('name', ['admin', 'manager'])
                                  ->where('hierarchy_level', '>=', 50); // Manager level or higher
                        })
                        ->exists();
        });

        \Gate::define('manage-group-members', function ($user, $group = null) {
            // If no group provided, deny access
            if (!$group) {
                return false;
            }
            
            // System admins can manage members of any group
            if ($user->canManageSystem()) {
                return true;
            }
            
            // Group admins and managers can manage members of their group
            return $group->groupMembers()
                        ->where('user_id', $user->id)
                        ->whereHas('role', function($query) {
                            $query->whereIn('name', ['admin', 'manager'])
                                  ->where('hierarchy_level', '>=', 50); // Manager level or higher
                        })
                        ->exists();
        });
        
        // Register custom Blade directives for permission checking
        \Blade::if('can', function ($permission) {
            return \Gate::check($permission);
        });

        \Blade::if('cannot', function ($permission) {
            return \Gate::denies($permission);
        });

        \Blade::if('canany', function (...$permissions) {
            return \Gate::any($permissions);
        });

        \Blade::if('canall', function (...$permissions) {
            foreach ($permissions as $permission) {
                if (!\Gate::check($permission)) {
                    return false;
                }
            }
            return true;
        });
    }
}
