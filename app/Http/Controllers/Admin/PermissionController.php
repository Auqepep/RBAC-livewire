<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PermissionController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user can manage permissions
        $this->authorize('manage-permissions');

        $permissions = Permission::paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('manage-permissions');
        
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-permissions');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $this->authorize('manage-permissions');
        
        $permission->load('roles.users');
        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $this->authorize('manage-permissions');
        
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $this->authorize('manage-permissions');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $this->authorize('manage-permissions');
        
        // Check if permission has roles
        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'Cannot delete permission that is assigned to roles.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission deleted successfully.');
    }
}
