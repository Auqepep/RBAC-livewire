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

        <!-- Members -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Initial Members</label>
            <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto">
                @if($users->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($users as $user)
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="user_{{ $user->id }}"
                                    wire:model="selectedUsers"
                                    value="{{ $user->id }}"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <label for="user_{{ $user->id }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $user->name }}
                                    <span class="text-xs text-gray-500">({{ $user->email }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No users available.</p>
                @endif
            </div>
            <p class="mt-1 text-xs text-gray-500">You can add or remove members after creating the group.</p>
            @error('selectedUsers')
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
                <span wire:loading.remove>Create Group</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </form>
</div>
