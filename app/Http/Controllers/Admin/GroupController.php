<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GroupController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Group::class);

        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'name'); // Default sort by name
        $sortOrder = $request->get('sort_order', 'asc'); // Default ascending
        
        // Validate sort parameters
        $allowedSortFields = ['name', 'created_at', 'group_members_count'];
        $allowedSortOrders = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }
        
        $groups = Group::with(['creator'])
                      ->withCount('groupMembers')
                      ->when($search, function ($query, $search) {
                          return $query->where('name', 'like', "%{$search}%")
                                      ->orWhere('description', 'like', "%{$search}%");
                      })
                      ->orderBy($sortBy, $sortOrder)
                      ->paginate(15)
                      ->appends($request->query());
        
        return view('admin.groups.index', compact('groups', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Group::class);

        $users = User::whereNotNull('email_verified_at')->get();
        return view('admin.groups.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'users' => 'array',
            'users.*' => 'exists:users,id',
            'user_roles' => 'array',
            'user_roles.*' => 'in:staff,manager,admin'
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        // Create default roles for this group
        $defaultRoles = [
            'staff' => [
                'display_name' => 'Staff',
                'description' => 'Regular staff member',
                'badge_color' => '#3b82f6',
                'hierarchy_level' => 10,
            ],
            'manager' => [
                'display_name' => 'Manager',
                'description' => 'Team manager with elevated permissions',
                'badge_color' => '#8b5cf6',
                'hierarchy_level' => 50,
            ],
            'admin' => [
                'display_name' => 'Group Admin',
                'description' => 'Group administrator with full group management access',
                'badge_color' => '#ef4444',
                'hierarchy_level' => 100,
            ],
        ];

        $createdRoles = [];
        foreach ($defaultRoles as $roleName => $roleData) {
            $createdRoles[$roleName] = \App\Models\Role::create([
                'name' => $roleName,
                'group_id' => $group->id,
                'display_name' => $roleData['display_name'],
                'description' => $roleData['description'],
                'badge_color' => $roleData['badge_color'],
                'hierarchy_level' => $roleData['hierarchy_level'],
                'is_active' => true,
            ]);
        }

        // Add users with their assigned roles
        if (isset($validated['users'])) {
            foreach ($validated['users'] as $userId) {
                $roleName = $validated['user_roles'][$userId] ?? 'staff'; // Default to staff
                $role = $createdRoles[$roleName];
                
                $group->addMember($userId, $role->id, auth()->id());
            }
        }

        return redirect()->route('admin.groups.index')
                        ->with('success', 'Group created successfully with roles assigned.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $group->load(['creator', 'groupMembers.user', 'groupMembers.role']);
        
        return view('admin.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
<<<<<<< HEAD
        $this->authorize('update', $group);

=======
        // Check if user can manage this group
        if (!Gate::allows('manage-group', $group)) {
            abort(403, 'You do not have permission to edit this group.');
        }
        
>>>>>>> 1a70e8e24b5a2554f7838c00d19ce0037f28628a
        $users = User::whereNotNull('email_verified_at')->get();
        $groupUsers = $group->users->pluck('id')->toArray();
        
        // Load group roles
        $group->load('roles');
        
        // Check if user is group admin (not system admin)
        $isGroupAdminOnly = !auth()->user()->canManageSystem() && Gate::allows('manage-group', $group);
        
        return view('admin.groups.edit', compact('group', 'users', 'groupUsers', 'isGroupAdminOnly'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
<<<<<<< HEAD
        $this->authorize('update', $group);

=======
        // Check if user can manage this group
        if (!Gate::allows('manage-group', $group)) {
            abort(403, 'You do not have permission to update this group.');
        }
        
>>>>>>> 1a70e8e24b5a2554f7838c00d19ce0037f28628a
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('groups')->ignore($group->id)],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'users' => 'array',
            'users.*' => 'exists:users,id',
            'user_roles' => 'array',
            'user_roles.*' => 'exists:roles,id'
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Get current members
        $currentMembers = $group->groupMembers()->pluck('user_id')->toArray();
        $newMembers = $validated['users'] ?? [];
        
        // Check if user is trying to remove themselves as group admin (prevent lockout)
        $isGroupAdmin = $group->groupMembers()
                             ->where('user_id', auth()->id())
                             ->whereHas('role', function($query) {
                                 $query->where('name', 'admin');
                             })
                             ->exists();
        
        if ($isGroupAdmin && !in_array(auth()->id(), $newMembers)) {
            return back()->withErrors(['users' => 'You cannot remove yourself as a group admin.']);
        }
        
        // Remove members that are not in the new list
        $membersToRemove = array_diff($currentMembers, $newMembers);
        foreach ($membersToRemove as $userId) {
            $group->removeMember($userId);
        }

        // Add new members or update roles for existing ones
        foreach ($newMembers as $userId) {
            $roleId = $validated['user_roles'][$userId] ?? null;
            
            if ($roleId) {
                $existingMember = $group->groupMembers()->where('user_id', $userId)->first();
                
                if ($existingMember) {
                    // Update role if user is already a member
                    $existingMember->update([
                        'role_id' => $roleId,
                        'assigned_by' => auth()->id(),
                    ]);
                } else {
                    // Add new member with role
                    $group->addMember($userId, $roleId, auth()->id());
                }
            }
        }

        return redirect()->route('admin.groups.index')
                        ->with('success', 'Group updated successfully with role assignments.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        // Check if group has users
        if ($group->groupMembers()->count() > 0) {
            return back()->with('error', 'Cannot delete group that has members. Remove all members first.');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
                        ->with('success', 'Group deleted successfully.');
    }

    /**
     * Remove a member from the group.
     */
    public function removeMember(Request $request, Group $group, User $user)
    {
        // Check if user can manage this group's members
        if (!Gate::allows('manage-group-members', $group)) {
            abort(403, 'You do not have permission to remove members from this group.');
        }
        
        // Prevent group admin from removing themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot remove yourself from the group.']);
        }
        
        $membership = $group->groupMembers()->where('user_id', $user->id)->first();
        
        if ($membership) {
            $membership->delete();
            return back()->with('success', 'Member removed from group successfully.');
        }
        
        return back()->with('error', 'Member not found in group.');
    }
}
