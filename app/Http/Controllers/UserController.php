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
        
        $users = User::with(['roles', 'groups'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(15)
            ->appends($request->query());

        return view('users.index', compact('users', 'search'));
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
