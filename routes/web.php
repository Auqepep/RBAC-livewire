<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    // If user is authenticated, redirect to dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    
    // If not authenticated, show welcome page
    return view('welcome');
})->name('home');

// Custom OTP Auth Routes
Route::view('login', 'auth.login')->name('login')->middleware('guest');
// Note: Registration is only available to admins through admin.users.create

// Email verification route (for clickable links in emails)
Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->name('verification.verify')
    ->middleware(['signed', 'throttle:6,1']);

// Logout route
Route::post('logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// Also handle GET logout for expired sessions
Route::get('logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout.get');

Route::get('dashboard', function () {
    // Return the dashboard view for all authenticated users
    return view('dashboard');
})
    ->middleware(['auth'])
    ->name('dashboard');

// User routes for regular users
Route::middleware(['auth'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('groups', [UserController::class, 'myGroups'])->name('groups.index');
    Route::get('available-groups', function () {
        return view('users.available-groups');
    })->name('available-groups');
    Route::get('groups/{group}', [UserController::class, 'showGroup'])->name('groups.show');
    
    // Group Gateway routes
    Route::get('groups/{group}/gateway', function ($groupId) {
        return view('users.group-gateway', compact('groupId'));
    })->name('groups.gateway');
    
    // Group Management routes (for managers)
    Route::prefix('groups/{group}')->name('groups.')->group(function () {
        Route::get('edit', [\App\Http\Controllers\User\GroupManagementController::class, 'edit'])->name('edit');
        Route::put('update', [\App\Http\Controllers\User\GroupManagementController::class, 'update'])->name('update');
        Route::post('members', [\App\Http\Controllers\User\GroupManagementController::class, 'addMember'])->name('members.add');
        Route::put('members/{user}/role', [\App\Http\Controllers\User\GroupManagementController::class, 'updateMemberRole'])->name('members.update-role');
        Route::delete('members/{user}', [\App\Http\Controllers\User\GroupManagementController::class, 'removeMemberByUser'])->name('members.remove');
        
        // Join request management (using request ID instead of group prefix)
    });
    
    Route::post('requests/{request}/approve', [\App\Http\Controllers\User\GroupManagementController::class, 'approveRequest'])->name('requests.approve');
    Route::post('requests/{request}/reject', [\App\Http\Controllers\User\GroupManagementController::class, 'rejectRequest'])->name('requests.reject');
    
    // Permission testing routes
    Route::get('test/permissions', [App\Http\Controllers\PermissionTestController::class, 'index'])->name('test.permissions');
    Route::post('test/permission', [App\Http\Controllers\PermissionTestController::class, 'testPermission'])->name('test.permission');
    
    // Group management route (legacy) - redirect to unified edit page
    Route::get('groups/{group}/manage', function ($groupId) {
        return redirect()->route('groups.edit', $groupId);
    })->name('groups.manage')->middleware('can:manage-group,group');
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
    
    // Quick user permission testing routes
    Route::post('users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::get('users/{user}/permissions', [AdminUserController::class, 'permissions'])->name('users.permissions');
    
    Route::resource('permissions', PermissionController::class);
    Route::resource('groups', GroupController::class);
    
    // Role routes - only accessible through group context
    Route::get('groups/{group}/roles', [RoleController::class, 'index'])->name('groups.roles.index');
    Route::get('groups/{group}/roles/create', [RoleController::class, 'create'])->name('groups.roles.create');
    Route::post('groups/{group}/roles', [RoleController::class, 'store'])->name('groups.roles.store');
    Route::get('groups/{group}/roles/{role}', [RoleController::class, 'show'])->name('groups.roles.show');
    Route::get('groups/{group}/roles/{role}/edit', [RoleController::class, 'edit'])->name('groups.roles.edit');
    Route::put('groups/{group}/roles/{role}', [RoleController::class, 'update'])->name('groups.roles.update');
    Route::delete('groups/{group}/roles/{role}', [RoleController::class, 'destroy'])->name('groups.roles.destroy');
    
    // Group member management
    Route::get('groups/{group}/members', function (\App\Models\Group $group) {
        return view('admin.group-members', compact('group'));
    })->name('groups.members');
    
    // Remove member from group
    Route::delete('groups/{group}/members/{user}', function (\App\Models\Group $group, \App\Models\User $user) {
        $membership = $group->groupMembers()->where('user_id', $user->id)->first();
        if ($membership) {
            $membership->delete();
            return back()->with('success', 'Member removed from group successfully.');
        }
        return back()->with('error', 'Member not found in group.');
    })->name('groups.members.remove');
    
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

// Test route for Tailwind v4
Route::get('/tailwind-test', function () {
    return view('tailwind-test');
})->name('tailwind.test');

// Comment out the default auth routes since we're using custom OTP auth
// require __DIR__.'/auth.php';
