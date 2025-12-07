<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $minimumLevel): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this resource.');
        }

        $user = auth()->user();
        $userLevel = $user->getHighestRoleLevel();

        $requiredLevel = $this->getLevelNumber($minimumLevel);

        if ($userLevel < $requiredLevel) {
            abort(403, 'Your role level is insufficient to access this resource.');
        }

        return $next($request);
    }

    /**
     * Convert level name to number for comparison
     */
    private function getLevelNumber(string $level): int
    {
        $levels = [
            'member' => 1,
            'staff' => 2, 
            'manager' => 4,
            'admin' => 5,
            'super_admin' => 6
        ];

        return $levels[strtolower($level)] ?? 0;
    }
}
