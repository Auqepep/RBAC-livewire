<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

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
            'user_roles.*' => 'in:staff,manager' // Only staff and manager
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        // Create default roles for this group (Manager and Staff only)
        // These are the ONLY two roles available for any group
        $groupName = $validated['name']; // e.g., "HR", "Gateway"
        
        $defaultRoles = [
            'manager' => [
                'name' => strtolower(str_replace(' ', '_', $groupName)) . '_manager',
                'display_name' => "{$groupName} Manager",
                'description' => "Manager role for {$groupName} group - can manage members and content",
                'badge_color' => '#2563eb', // Blue
                'hierarchy_level' => 70, // Manager level
            ],
            'staff' => [
                'name' => strtolower(str_replace(' ', '_', $groupName)) . '_staff',
                'display_name' => "{$groupName} Staff",
                'description' => "Staff member role for {$groupName} group - basic access",
                'badge_color' => '#059669', // Green
                'hierarchy_level' => 30, // Staff level
            ],
        ];

        // ALWAYS create both Manager and Staff roles for every group
        $createdRoles = [];
        
        // Create Manager role
        $createdRoles['manager'] = \App\Models\Role::create([
            'name' => $defaultRoles['manager']['name'],
            'display_name' => $defaultRoles['manager']['display_name'],
            'description' => $defaultRoles['manager']['description'],
            'group_id' => $group->id,
            'badge_color' => $defaultRoles['manager']['badge_color'],
            'hierarchy_level' => $defaultRoles['manager']['hierarchy_level'],
            'is_active' => true,
        ]);
        
        // Create Staff role
        $createdRoles['staff'] = \App\Models\Role::create([
            'name' => $defaultRoles['staff']['name'],
            'display_name' => $defaultRoles['staff']['display_name'],
            'description' => $defaultRoles['staff']['description'],
            'group_id' => $group->id,
            'badge_color' => $defaultRoles['staff']['badge_color'],
            'hierarchy_level' => $defaultRoles['staff']['hierarchy_level'],
            'is_active' => true,
        ]);

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
        $this->authorize('update', $group);
        
        $users = User::whereNotNull('email_verified_at')->get();
        
        // Get only roles that are currently used in this specific group
        $groupRoleIds = $group->groupMembers()->distinct()->pluck('role_id')->toArray();
        $roles = \App\Models\Role::whereIn('id', $groupRoleIds)->get();
        
        $groupUsers = $group->groupMembers()->pluck('user_id')->toArray();
        
        return view('admin.groups.edit', compact('group', 'users', 'roles', 'groupUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('groups')->ignore($group->id)],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'third_party_app_url' => 'nullable|url|max:500',
            'oauth_client_id' => 'nullable|string|max:255',
            'enable_gateway_redirect' => 'boolean',
            'users' => 'array',
            'users.*' => 'exists:users,id',
            'user_roles' => 'array',
            'user_roles.*' => 'exists:roles,id'
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
            'third_party_app_url' => $validated['third_party_app_url'] ?? null,
            'oauth_client_id' => $validated['oauth_client_id'] ?? null,
            'enable_gateway_redirect' => $validated['enable_gateway_redirect'] ?? false,
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
        $this->authorize('update', $group);
        
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
