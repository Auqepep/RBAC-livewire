<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- User Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                        <p class="mt-1 text-sm">
                            @if($user->email_verified_at)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Not Verified
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Member Since</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Roles -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Assigned Roles</h3>
                <button 
                    wire:click="toggleRoleModal"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Manage Roles
                </button>
            </div>
            <div class="p-6">
                @if($user->roles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($user->roles as $role)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $role->display_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                </div>
                                <button 
                                    wire:click="removeRole({{ $role->id }})"
                                    wire:confirm="Are you sure you want to remove this role from the user?"
                                    class="text-red-600 hover:text-red-900 text-sm"
                                >
                                    Remove
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No roles assigned to this user.</p>
                @endif
            </div>
        </div>

        <!-- User Groups -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Group Memberships</h3>
                <button 
                    wire:click="toggleGroupModal"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    Manage Groups
                </button>
            </div>
            <div class="p-6">
                @if($user->groups->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($user->groups as $group)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $group->name }}</p>
                                    @if($group->description)
                                        <p class="text-xs text-gray-500">{{ Str::limit($group->description, 30) }}</p>
                                    @endif
                                </div>
                                <button 
                                    wire:click="removeGroup({{ $group->id }})"
                                    wire:confirm="Are you sure you want to remove this user from the group?"
                                    class="text-red-600 hover:text-red-900 text-sm"
                                >
                                    Remove
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">This user is not a member of any groups.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Role Management Modal -->
    @if($showRoleModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Manage User Roles</h3>
                    
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($availableRoles as $role)
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="role_{{ $role->id }}"
                                    wire:model="selectedRoles"
                                    value="{{ $role->id }}"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <label for="role_{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $role->display_name }}
                                    <span class="text-xs text-gray-500">({{ $role->name }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button 
                            wire:click="toggleRoleModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="updateRoles"
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Update Roles
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Group Management Modal -->
    @if($showGroupModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Manage User Groups</h3>
                    
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($availableGroups as $group)
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="group_{{ $group->id }}"
                                    wire:model="selectedGroups"
                                    value="{{ $group->id }}"
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                >
                                <label for="group_{{ $group->id }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $group->name }}
                                    @if($group->description)
                                        <span class="text-xs text-gray-500">({{ Str::limit($group->description, 30) }})</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button 
                            wire:click="toggleGroupModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="updateGroups"
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                        >
                            Update Groups
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
