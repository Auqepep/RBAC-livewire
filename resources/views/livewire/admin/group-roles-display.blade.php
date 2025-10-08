<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-8">
        <!-- Groups with their Current Roles -->
        @foreach($groups as $group)
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <!-- Group Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-white">{{ $group->name }}</h1>
                            @if($group->description)
                                <p class="text-indigo-100 mt-1">{{ $group->description }}</p>
                            @endif
                            <p class="text-indigo-200 text-sm mt-2">
                                {{ $group->groupMembers->count() }} total members • {{ $group->unique_roles->count() }} different roles
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.groups.edit', $group->id) }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 border border-white border-opacity-50 rounded-md font-semibold text-sm text-white tracking-wide transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Manage Group
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Roles Currently Used in this Group -->
                <div class="p-6">
                    @if($group->unique_roles->count() > 0)
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Roles in {{ $group->name }}</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($group->unique_roles as $role)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <!-- Role Header -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <span 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                                style="background-color: {{ $role->badge_color }}"
                                            >
                                                Level {{ $role->hierarchy_level }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.roles.show', $role->id) }}" class="text-gray-600 hover:text-gray-900 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @if(!in_array($role->name, ['Super Admin', 'Admin']))
                                                <button 
                                                    wire:click="deleteRole({{ $role->id }})"
                                                    wire:confirm="Are you sure you want to delete this role? This will affect {{ $role->users_count_in_group }} users in this group."
                                                    class="text-red-600 hover:text-red-900 text-sm"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Role Info -->
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $role->display_name }}</h3>
                                        @if($role->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $role->description }}</p>
                                        @endif
                                        
                                        <!-- Usage Stats -->
                                        <div class="mt-3 flex items-center space-x-4 text-xs text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                </svg>
                                                <strong>{{ $role->users_count_in_group }}</strong> users in {{ $group->name }}
                                            </span>
                                            @if($role->permissions->count() > 0)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                    </svg>
                                                    {{ $role->permissions->count() }} permissions
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Permissions Preview -->
                                        @if($role->permissions->count() > 0)
                                            <div class="mt-3">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($role->permissions->take(3) as $permission)
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                                            {{ str_replace('_', ' ', ucfirst($permission->name)) }}
                                                        </span>
                                                    @endforeach
                                                    @if($role->permissions->count() > 3)
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-200 text-gray-600">
                                                            +{{ $role->permissions->count() - 3 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No roles assigned in this group yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Add members to {{ $group->name }} to see their roles here.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.groups.edit', $group->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Members
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Unassigned Roles Section -->
        @if($unassignedRoles->count() > 0)
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Unassigned Roles</h1>
                            <p class="text-gray-200 text-sm mt-2">
                                {{ $unassignedRoles->count() }} roles not currently assigned to any users
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 border border-white border-opacity-50 rounded-md font-semibold text-sm text-white tracking-wide transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create New Role
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Available Roles</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($unassignedRoles as $role)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                                <!-- Role Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                            style="background-color: {{ $role->badge_color }}"
                                        >
                                            Level {{ $role->hierarchy_level }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                            Unassigned
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="text-gray-600 hover:text-gray-900 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <button 
                                            wire:click="deleteRole({{ $role->id }})"
                                            wire:confirm="Are you sure you want to delete this unassigned role?"
                                            class="text-red-600 hover:text-red-900 text-sm"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Role Info -->
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $role->display_name }}</h3>
                                    @if($role->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $role->description }}</p>
                                    @endif
                                    
                                    <!-- Stats -->
                                    <div class="mt-3 flex items-center space-x-4 text-xs text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                            No users assigned
                                        </span>
                                        @if($role->permissions->count() > 0)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                </svg>
                                                {{ $role->permissions->count() }} permissions
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Permissions Preview -->
                                    @if($role->permissions->count() > 0)
                                        <div class="mt-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($role->permissions->take(3) as $permission)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                                        {{ str_replace('_', ' ', ucfirst($permission->name)) }}
                                                    </span>
                                                @endforeach
                                                @if($role->permissions->count() > 3)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-200 text-gray-600">
                                                        +{{ $role->permissions->count() - 3 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
