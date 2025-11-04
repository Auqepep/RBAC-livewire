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
            'users.*' => 'exists:users,id'
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        if (isset($validated['users'])) {
            foreach ($validated['users'] as $userId) {
                $group->addUserRole($userId, 'Member', [
                    'role_display_name' => 'Group Member',
                    'role_description' => 'Regular group member',
                    'badge_color' => '#3b82f6',
                    'hierarchy_level' => 10,
                    'permissions' => ['view_group'],
                    'is_active' => true,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now()
                ]);
            }
        }

        return redirect()->route('admin.groups.index')
                        ->with('success', 'Group created successfully.');
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
        $groupUsers = $group->users->pluck('id')->toArray();
        return view('admin.groups.edit', compact('group', 'users', 'groupUsers'));
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
            'users' => 'array',
            'users.*' => 'exists:users,id'
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Sync users
        if (isset($validated['users'])) {
            // Remove all current members in this group
            $group->groupMembers()->delete();
            
            // Add selected users with Member role
            $memberRole = \App\Models\Role::where('name', 'Member')->first();
            if ($memberRole) {
                foreach ($validated['users'] as $userId) {
                    $group->addMember($userId, $memberRole->id);
                }
            }
        } else {
            // No users selected, remove all
            $group->groupMembers()->delete();
        }

        return redirect()->route('admin.groups.index')
                        ->with('success', 'Group updated successfully.');
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
}
