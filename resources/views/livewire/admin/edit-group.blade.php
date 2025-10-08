<div>
    <form wire:submit="save" class="space-y-6">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Group Name</label>
            <input 
                type="text" 
                id="name"
                wire:model.blur="name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g., Marketing Team"
            >
            <p class="mt-1 text-xs text-gray-500">Must be unique and descriptive.</p>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea 
                id="description"
                wire:model.blur="description"
                rows="3"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Describe the purpose of this group..."
            ></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div>
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="is_active"
                    wire:model="is_active"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                >
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
            </div>
            <p class="mt-1 text-xs text-gray-500">Only active groups can have members added.</p>
        </div>

        <!-- Available Roles Reference -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Available Roles</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($roles as $role)
                    <div class="p-3 bg-white rounded border">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium text-sm">{{ $role->display_name }}</h5>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-white" 
                                  style="background-color: {{ $role->badge_color }}">
                                Level {{ $role->hierarchy_level }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mb-2">{{ $role->description }}</p>
                        @if($role->permissions->isNotEmpty())
                            <div class="text-xs">
                                <span class="text-gray-500">Permissions:</span>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($role->permissions as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-800">
                                            {{ str_replace('_', ' ', ucfirst($permission->name)) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Members with Role Assignment -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Members and Roles</label>
            <div class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto">
                @if($users->count() > 0)
                    <div class="space-y-4">
                        @foreach($users as $user)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg {{ in_array($user->id, $selectedUsers) ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                                <div class="flex items-center flex-1">
                                    <input 
                                        type="checkbox" 
                                        id="user_{{ $user->id }}"
                                        wire:model.live="selectedUsers"
                                        value="{{ $user->id }}"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    >
                                    <div class="ml-3">
                                        <label for="user_{{ $user->id }}" class="block text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </label>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                
                                @if(in_array($user->id, $selectedUsers))
                                    <div class="ml-4 min-w-0 flex-1 max-w-xs">
                                        <select 
                                            wire:model="userRoles.{{ $user->id }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                        >
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">
                                                    {{ $role->display_name }}
                                                    (Level {{ $role->hierarchy_level }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @if(isset($userRoles[$user->id]))
                                            @php
                                                $selectedRole = $roles->find($userRoles[$user->id]);
                                            @endphp
                                            @if($selectedRole)
                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $selectedRole->description }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No users available.</p>
                @endif
            </div>
            @error('selectedUsers')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('userRoles')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-4">
            <button 
                type="button" 
                wire:click="cancel"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Cancel
            </button>
            <button 
                type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Update Group</span>
                <span wire:loading>Updating...</span>
            </button>
        </div>
    </form>
</div>
