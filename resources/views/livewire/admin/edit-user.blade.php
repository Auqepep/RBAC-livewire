<div>
    <form wire:submit="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model="name" id="name" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="email" id="email" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Current Roles Display -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Current Roles</label>
            @if($user->roles->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($user->roles as $role)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full text-white" style="background-color: {{ $role->color ?? '#3B82F6' }}">
                            {{ $role->display_name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm mb-4">No roles currently assigned</p>
            @endif
        </div>

        <!-- Assign New Roles -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Assign Roles</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($roles as $role)
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" 
                               id="role_{{ $role->id }}"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="role_{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                            <span class="font-medium">{{ $role->display_name }}</span>
                            @if($role->description)
                                <span class="block text-xs text-gray-500">{{ $role->description }}</span>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
            @error('selectedRoles')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- User Information Display -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-200">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                <p class="mt-1">
                    @if($user->email_verified_at)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Verified at {{ $user->email_verified_at->format('M d, Y H:i') }}
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Not Verified
                        </span>
                    @endif
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Joined</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.users.show', $user) }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                View User
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <span wire:loading.remove>Update User</span>
                <span wire:loading>
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                </span>
            </button>
        </div>
    </form>
</div>
