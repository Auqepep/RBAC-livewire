<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Role Details in ' . $group->name) }}: {{ $role->display_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.groups.roles.edit', [$group, $role]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Edit Role
                </a>
                <a href="{{ route('admin.groups.roles.index', $group) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to Group Roles
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Role Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Role Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Display Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->display_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">System Name</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $role->name }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $role->description ?: 'No description provided.' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role Color</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <div class="w-8 h-8 rounded border-2 border-gray-300" style="background-color: {{ $role->badge_color ?? '#3B82F6' }};"></div>
                            <span class="text-sm text-gray-900 font-mono">{{ $role->badge_color ?? '#3B82F6' }}</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" style="background-color: {{ $role->badge_color ?? '#3B82F6' }};">
                                Preview
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hierarchy Level</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->hierarchy_level ?? 'Not set' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="mt-1">
                            @if($role->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Usage -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Usage Statistics
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $usageCount }}</div>
                        <div class="text-sm text-gray-500">Users Assigned</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            @if(is_array($role->permissions))
                                {{ count($role->permissions) }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">Permissions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $role->hierarchy_level ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Hierarchy Level</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Permissions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Role Permissions
                    @if(is_array($role->permissions))
                        ({{ count($role->permissions) }})
                    @else
                        (0)
                    @endif
                </h3>
                
                @if($role->permissions->isNotEmpty())
                    @php
                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                            return $permission->category ?? 'General';
                        });
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($groupedPermissions as $category => $permissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3 capitalize">
                                    {{ $category }}
                                </h4>
                                <div class="space-y-2">
                                    @foreach($permissions as $permission)
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div>
                                                <span class="text-gray-900 font-medium">{{ $permission['label'] }}</span>
                                                @if($permission['description'])
                                                    <p class="text-xs text-gray-500">{{ $permission['description'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">This role doesn't have any permissions assigned.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Assign Permissions
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin.layout>
