<div>
    <form wire:submit="save" class="space-y-6">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
            <input 
                type="text" 
                id="name"
                wire:model.blur="name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g., moderator"
            >
            <p class="mt-1 text-xs text-gray-500">Must be unique, lowercase, and can contain letters, numbers, dashes, and underscores.</p>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Display Name -->
        <div>
            <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name</label>
            <input 
                type="text" 
                id="display_name"
                wire:model.blur="display_name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g., Moderator"
            >
            <p class="mt-1 text-xs text-gray-500">Human-friendly name for the role.</p>
            @error('display_name')
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
                placeholder="Describe what this role is for..."
            ></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Color -->
        <div>
            <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
            <div class="mt-1 flex items-center space-x-3">
                <input 
                    type="color" 
                    id="color"
                    wire:model.blur="color"
                    class="h-10 w-20 border border-gray-300 rounded-md cursor-pointer"
                >
                <input 
                    type="text" 
                    wire:model.blur="color"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="#3B82F6"
                >
            </div>
            <p class="mt-1 text-xs text-gray-500">Color for visual identification of the role.</p>
            @error('color')
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
            <p class="mt-1 text-xs text-gray-500">Only active roles can be assigned to users.</p>
        </div>

        <!-- Permissions -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
            <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto">
                @if($permissions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($permissions as $permission)
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="permission_{{ $permission->id }}"
                                    wire:model="selectedPermissions"
                                    value="{{ $permission->id }}"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <label for="permission_{{ $permission->id }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $permission->display_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No permissions available.</p>
                @endif
            </div>
            @error('selectedPermissions')
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
                <span wire:loading.remove>Create Role</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </form>
</div>
