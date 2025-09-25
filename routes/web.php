<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\UserController;

Route::view('/', 'welcome')->name('home');

// Custom OTP Auth Routes
Route::view('login', 'auth.login')->name('login')->middleware('guest');
Route::view('register', 'auth.register')->name('register')->middleware('guest');

// Email verification route (for clickable links in emails)
Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->name('verification.verify')
    ->middleware(['signed', 'throttle:6,1']);

// Logout route
Route::post('logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

Route::get('dashboard', function () {
    // Redirect system administrators to admin dashboard
    if (Auth::user()->canManageSystem()) {
        return redirect()->route('admin.dashboard');
    }
    
    // Show regular user dashboard for non-admin users
    return view('dashboard');
})
    ->middleware(['auth'])
    ->name('dashboard');

// User routes for regular users
Route::middleware(['auth'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('my-groups', [UserController::class, 'myGroups'])->name('my-groups');
    Route::get('available-groups', function () {
        return view('users.available-groups');
    })->name('available-groups');
    Route::get('groups/{group}', [UserController::class, 'showGroup'])->name('groups.show');
});

// Debug route for testing verification
Route::get('debug-verify/{id}/{hash}', function ($id, $hash) {
    $user = \App\Models\User::find($id);
    if (!$user) {
        return "User not found with ID: $id";
    }
    
    $expectedHash = sha1($user->email);
    
    return [
        'user_id' => $id,
        'provided_hash' => $hash,
        'expected_hash' => $expectedHash,
        'hash_matches' => hash_equals($hash, $expectedHash),
        'user_email' => $user->email,
        'is_verified' => $user->hasVerifiedEmail(),
    ];
})->name('debug.verify');

// Admin Routes (Only for system administrators)
Route::middleware(['auth', 'system.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('groups', GroupController::class);
    
    // Group member management
    Route::get('groups/{group}/members', function (\App\Models\Group $group) {
        return view('admin.group-members', compact('group'));
    })->name('groups.members');
    
    // User to Group Management
    Route::get('manage-memberships', function () {
        return view('admin.manage-memberships');
    })->name('manage-memberships');
    
    // Group Roles Management
    Route::get('manage-group-roles', function () {
        return view('admin.manage-group-roles');
    })->name('manage-group-roles');
    
    // Group join requests
    Route::get('group-join-requests', function () {
        return view('admin.group-join-requests');
    })->name('group-join-requests');
    
    // Admin Dashboard
    Route::get('/', function () {
        $stats = [
            'users' => \App\Models\User::count(),
            'roles' => \App\Models\Role::count(),
            'total_assignments' => \App\Models\GroupMember::count(),
            'groups' => \App\Models\Group::count(),
            'active_assignments' => \App\Models\GroupMember::count(),
            'active_groups' => \App\Models\Group::where('is_active', true)->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    })->name('dashboard');
});

// Comment out the default auth routes since we're using custom OTP auth
// require __DIR__.'/auth.php';
