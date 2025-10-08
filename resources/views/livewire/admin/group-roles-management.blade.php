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

    <!-- Groups with their Roles -->
    <div class="space-y-8">
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
                                {{ $group->groupMembers->count() }} total members • {{ $group->unique_roles->count() }} roles
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button 
                                wire:click="showCreateRoleForm({{ $group->id }})"
                                class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 border border-white border-opacity-50 rounded-md font-semibold text-sm text-white tracking-wide transition duration-150 ease-in-out"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create New Role
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Create Role Form (conditional) -->
                @if($showCreateRole && $selectedGroup == $group->id)
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <form wire:submit="createRole" class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Role for {{ $group->name }}</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                                    <input 
                                        type="text" 
                                        wire:model.blur="newRoleName"
                                        placeholder="e.g., senior_analyst"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                    @error('newRoleName')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                                    <input 
                                        type="text" 
                                        wire:model.blur="newRoleDisplayName"
                                        placeholder="e.g., Senior Analyst"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                    @error('newRoleDisplayName')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hierarchy Level</label>
                                    <select 
                                        wire:model="newRoleHierarchy"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}">Level {{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('newRoleHierarchy')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Badge Color</label>
                                    <input 
                                        type="color" 
                                        wire:model="newRoleColor"
                                        class="w-full h-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                    @error('newRoleColor')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea 
                                    wire:model.blur="newRoleDescription"
                                    rows="3"
                                    placeholder="Describe the responsibilities of this role..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                ></textarea>
                                @error('newRoleDescription')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end space-x-3">
                                <button 
                                    type="button"
                                    wire:click="cancelCreateRole"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                >
                                    Create Role
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Roles in this Group -->
                <div class="p-6">
                    @if($group->unique_roles->count() > 0)
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Roles Available in {{ $group->name }}</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($group->unique_roles->sortByDesc('hierarchy_level') as $role)
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
                                            @if(!in_array($role->name, ['Super Admin', 'Admin']))
                                                <button 
                                                    wire:click="deleteRole({{ $role->id }})"
                                                    wire:confirm="Are you sure you want to delete this role?"
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
                                        
                                        <!-- Users Count in this Group -->
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                </svg>
                                                {{ $role->users_count_in_group }} users in {{ $group->name }}
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
                                            <div class="mt-2">
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No roles in this group yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new role for {{ $group->name }}.</p>
                            <div class="mt-6">
                                <button 
                                    wire:click="showCreateRoleForm({{ $group->id }})"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create First Role
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($groups->count() === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No active groups found</h3>
            <p class="mt-1 text-sm text-gray-500">Create some groups first to manage their roles.</p>
        </div>
    @endif
</div>
