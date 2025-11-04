<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Group;
use App\Policies\GroupPolicy;

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
        // Register model policies
        Gate::policy(Group::class, GroupPolicy::class);

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
