<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        // Validate sort parameters
        $allowedSortFields = ['name', 'email', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'name';
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';

        $query = User::with(['groupMembers.group', 'groupMembers.role']);

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = \App\Models\Group::where('is_active', true)->get();
        
        // Get roles available per group (for JavaScript)
        $groupRoles = [];
        foreach ($groups as $group) {
            $groupRoles[$group->id] = \App\Models\Role::where('group_id', $group->id)
                ->where('is_active', true)
                ->get()
                ->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name
                    ];
                });
        }
        
        return view('admin.users.create', compact('groups', 'groupRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'group_assignments' => 'array',
            'group_assignments.*.group_id' => 'required|exists:groups,id',
            'group_assignments.*.role_id' => 'required|exists:roles,id'
        ]);

        // Create user
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_verified_at' => now(), // Auto-verify for admin-created users
        ]);

        // Assign to groups with roles
        if (!empty($validated['group_assignments'])) {
            foreach ($validated['group_assignments'] as $assignment) {
                if (!empty($assignment['group_id']) && !empty($assignment['role_id'])) {
                    \App\Models\GroupMember::create([
                        'group_id' => $assignment['group_id'],
                        'user_id' => $user->id,
                        'role_id' => $assignment['role_id'],
                        'assigned_by' => auth()->id(),
                        'joined_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['groupMembers.group', 'groupMembers.role']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load(['groupMembers.group', 'groupMembers.role']);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Note: In our group-based RBAC system, roles are managed through groups
        // Direct role assignment is no longer supported

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully. Manage roles through group memberships.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Remove user from all groups
        $user->groupMembers()->delete();
        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User deleted successfully.');
    }
    
    /**
     * Quick toggle admin privileges for testing
     */
    public function toggleAdmin(User $user)
    {
        // Prevent removing super admin privileges
        if ($user->isSuperAdmin()) {
            return back()->with('error', "Cannot remove admin privileges from {$user->name}. This is a permanent administrator.");
        }
        
        // Find or create the administrator group
        $adminGroup = \App\Models\Group::firstOrCreate([
            'name' => 'Administrators'
        ], [
            'description' => 'System administrators with full access',
            'is_active' => true,
            'created_by' => auth()->id()
        ]);
        
        // Find or create the admin role
        $adminRole = \App\Models\Role::firstOrCreate([
            'name' => 'administrator',
            'group_id' => $adminGroup->id
        ], [
            'display_name' => 'Administrator',
            'description' => 'Full system administrator access',
            'is_active' => true,
            'group_id' => $adminGroup->id
        ]);
        
        // Check if user is already an admin
        $isAdmin = $user->groupMembers()
            ->where('group_id', $adminGroup->id)
            ->where('role_id', $adminRole->id)
            ->exists();
            
        if ($isAdmin) {
            // Remove admin privileges
            $user->groupMembers()
                ->where('group_id', $adminGroup->id)
                ->where('role_id', $adminRole->id)
                ->delete();
            $message = "Admin privileges removed from {$user->name}";
        } else {
            // Add admin privileges
            \App\Models\GroupMember::updateOrCreate([
                'user_id' => $user->id,
                'group_id' => $adminGroup->id,
            ], [
                'role_id' => $adminRole->id,
                'joined_at' => now()
            ]);
            $message = "Admin privileges granted to {$user->name}";
        }
        
        // Clear user cache to reflect changes immediately
        $cacheService = app(\App\Services\RbacCacheService::class);
        $cacheService->clearUserCache($user->id);
        $cacheService->clearGroupCache($adminGroup->id);
        
        return back()->with('success', $message);
    }
    
    /**
     * Show user permission testing page
     */
    public function permissions(User $user)
    {
        $allPermissions = \App\Models\Permission::where('is_active', true)
            ->orderBy('category')
            ->orderBy('display_name')
            ->get();
            
        $permissionsByCategory = $allPermissions->groupBy('category');
        
        return view('admin.users.permissions', compact('user', 'permissionsByCategory'));
    }
}
