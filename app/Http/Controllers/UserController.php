<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users for regular users
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'name'); // Default sort by name
        $sortOrder = $request->get('sort_order', 'asc'); // Default ascending
        
        $user = Auth::user();
        
        // Validate sort parameters
        $allowedSortFields = ['name', 'email', 'created_at'];
        $allowedSortOrders = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }
        
        // For admin users, show all users
        if ($user->canManageSystem()) {
            $users = User::with(['groups', 'roles'])
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy($sortBy, $sortOrder)
                ->paginate(15)
                ->appends($request->query());
                
            return view('users.index', compact('users', 'search', 'sortBy', 'sortOrder'));
        }
        
        // For regular users, show only users in their groups
        $userGroupIds = $user->groups()->pluck('groups.id')->toArray();
        
        $users = User::with(['groups', 'roles'])
            ->whereHas('groups', function($query) use ($userGroupIds) {
                $query->whereIn('groups.id', $userGroupIds);
            })
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(15)
            ->appends($request->query());

        return view('users.index', compact('users', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Display the authenticated user's groups
     */
    public function myGroups()
    {
        return view('users.my-groups');
    }

    /**
     * Display a specific group's homepage
     */
    public function showGroup($groupId)
    {
        return view('users.group-homepage', compact('groupId'));
    }
}
