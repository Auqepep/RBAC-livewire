<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Role;
use App\Policies\GroupPolicy;
use App\Observers\GroupMemberObserver;
use App\Observers\RoleObserver;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Passport's authorization view response
        $this->app->bind(
            \Laravel\Passport\Contracts\AuthorizationViewResponse::class,
            function ($app) {
                return new class implements \Laravel\Passport\Contracts\AuthorizationViewResponse {
                    protected $parameters = [];
                    
                    public function withParameters(array $parameters = []): static
                    {
                        $this->parameters = $parameters;
                        return $this;
                    }
                    
                    public function toResponse($request)
                    {
                        return response()->view('vendor.passport.authorize', array_merge([
                            'client' => $request->client,
                            'user' => $request->user(),
                            'scopes' => $request->scopes ?? [],
                            'request' => $request,
                            'authToken' => $request->authToken ?? '',
                        ], $this->parameters));
                    }
                };
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Passport
        Passport::enablePasswordGrant();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        
        // Define OAuth scopes
        Passport::tokensCan([
            'read-user' => 'Read user information including name and email',
            'read-groups' => 'Read user group memberships and roles',
            'read-permissions' => 'Read user permissions',
        ]);

        // Register model observers for cache invalidation
        GroupMember::observe(GroupMemberObserver::class);
        Role::observe(RoleObserver::class);

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
