<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles for a specific group.
     */
    public function index(Group $group, Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'name'); // Default sort by name
        $sortOrder = $request->get('sort_order', 'asc'); // Default ascending
        
        // Validate sort parameters
        $allowedSortFields = ['name', 'hierarchy_level', 'created_at'];
        $allowedSortOrders = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }
        
        // Get roles used in this group
        $groupRoleIds = GroupMember::where('group_id', $group->id)
            ->distinct()
            ->pluck('role_id');
            
        $groupRolesQuery = Role::whereIn('id', $groupRoleIds)->with(['permissions']);
        
        if ($search) {
            $groupRolesQuery->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('display_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $groupRoles = $groupRolesQuery->orderBy($sortBy, $sortOrder)->get();
        $allRoles = Role::with(['permissions'])->orderBy('name', 'asc')->get();
        
        return view('admin.roles.index', compact('group', 'groupRoles', 'allRoles', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new role in group context.
     */
    public function create(Group $group)
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('group', 'permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hierarchy_level' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
            'permissions' => 'array'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'hierarchy_level' => $validated['hierarchy_level'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Attach permissions if provided
        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('admin.groups.roles.index', $group)
            ->with('message', 'Role created successfully.');
    }

    /**
     * Display the specified role in group context.
     */
    public function show(Group $group, Role $role)
    {
        $usageCount = GroupMember::where('group_id', $group->id)
            ->where('role_id', $role->id)
            ->count();
        $role->load('permissions');
        return view('admin.roles.show', compact('group', 'role', 'usageCount'));
    }

    /**
     * Show the form for editing the specified role in group context.
     */
    public function edit(Group $group, Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('admin.roles.edit', compact('group', 'role', 'permissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Group $group, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hierarchy_level' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
            'permissions' => 'array'
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'hierarchy_level' => $validated['hierarchy_level'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Update permissions
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('admin.groups.roles.index', $group)
            ->with('message', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Group $group, Role $role)
    {
        // Check if role is in use in this group
        $usageCount = GroupMember::where('group_id', $group->id)
            ->where('role_id', $role->id)
            ->count();
        
        if ($usageCount > 0) {
            return redirect()->route('admin.groups.roles.index', $group)
                ->with('error', 'Cannot delete role that is currently assigned to users in this group.');
        }

        // Check if role is used in other groups before deleting
        $totalUsage = GroupMember::where('role_id', $role->id)->count();
        
        if ($totalUsage > 0) {
            return redirect()->route('admin.groups.roles.index', $group)
                ->with('error', 'Cannot delete role that is used in other groups.');
        }

        $role->delete();

        return redirect()->route('admin.groups.roles.index', $group)
            ->with('message', 'Role deleted successfully.');
    }
}
