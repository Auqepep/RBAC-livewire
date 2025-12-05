<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OAuthController;

/*
|--------------------------------------------------------------------------
| API Routes - OAuth 2.0 Protected
|--------------------------------------------------------------------------
*/

// Public OAuth endpoints (managed by Passport)
// POST /oauth/token - Get access token
// POST /oauth/token/refresh - Refresh token
// GET /oauth/authorize - Authorization page
// POST /oauth/authorize - Approve/deny authorization

// Protected API endpoints - require Bearer token
Route::middleware('auth:api')->group(function () {
    
    // User information endpoint
    Route::get('/user', [OAuthController::class, 'user']);
    
    // Logout and revoke tokens
    Route::post('/logout', [OAuthController::class, 'logout']);
    
    // Additional protected API endpoints can be added here
    Route::prefix('v1')->group(function () {
        // Example: Route::get('/resource', [ResourceController::class, 'index']);
    });
});
