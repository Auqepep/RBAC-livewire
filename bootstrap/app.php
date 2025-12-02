<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'system.admin' => \App\Http\Middleware\SystemAdminMiddleware::class,
            'has.permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
            'user.level' => \App\Http\Middleware\EnsureUserLevel::class,
            'can' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // If CSRF token mismatch on logout, just redirect to home
            if ($request->is('logout')) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('home');
            }
            
            // For other routes, show the default error
            return null;
        });
    })->create();
