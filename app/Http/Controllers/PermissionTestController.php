<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionTestController extends Controller
{
    /**
     * Show permission test page for current user
     */
    public function index()
    {
        $user = auth()->user();
        $allPermissions = Permission::where('is_active', true)->orderBy('category')->orderBy('display_name')->get();
        
        // Group permissions by category
        $permissionsByCategory = $allPermissions->groupBy('category');
        
        return view('test.permissions', compact('user', 'permissionsByCategory'));
    }
    
    /**
     * Test a specific permission
     */
    public function testPermission(Request $request)
    {
        $permission = $request->input('permission');
        $result = auth()->user()->can($permission);
        
        return response()->json([
            'permission' => $permission,
            'allowed' => $result,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'timestamp' => now()->toISOString()
        ]);
    }
}
