<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::with(['users', 'creator'])
                      ->withCount('users')
                      ->paginate(15);
        
        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
                $group->addMember($userId, auth()->id());
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
        $group->load(['creator']);
        // Load users with pivot data
        $group->load(['users' => function($query) {
            $query->withPivot(['added_by', 'joined_at']);
        }]);
        
        return view('admin.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $users = User::whereNotNull('email_verified_at')->get();
        $groupUsers = $group->users->pluck('id')->toArray();
        return view('admin.groups.edit', compact('group', 'users', 'groupUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
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
            // Remove all current users
            $group->users()->detach();
            // Add selected users
            foreach ($validated['users'] as $userId) {
                $group->addMember($userId, auth()->id());
            }
        } else {
            // No users selected, remove all
            $group->users()->detach();
        }

        return redirect()->route('admin.groups.index')
                        ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        // Check if group has users
        if ($group->users()->count() > 0) {
            return back()->with('error', 'Cannot delete group that has members. Remove all members first.');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
                        ->with('success', 'Group deleted successfully.');
    }
}
