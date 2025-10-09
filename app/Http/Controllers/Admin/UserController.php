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
    public function index()
    {
        $users = User::with(['groupMembers.group', 'groupMembers.role'])
                    ->orderBy('name')
                    ->paginate(15);
                    
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = \App\Models\Group::where('is_active', true)->get();
        $roles = \App\Models\Role::where('is_active', true)->get();
        
        // Get roles available per group (for JavaScript)
        $groupRoles = [];
        foreach ($groups as $group) {
            $groupRoles[$group->id] = \App\Models\Role::where('is_active', true)->get();
        }
        
        return view('admin.users.create', compact('groups', 'roles', 'groupRoles'));
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
}
