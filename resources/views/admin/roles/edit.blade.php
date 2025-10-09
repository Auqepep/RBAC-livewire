<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Role in ' . $group->name) }}: {{ $role->display_name }}
            </h2>
            <a href="{{ route('admin.groups.roles.index', $group) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Group Roles
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.groups.roles.update', [$group, $role]) }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Role Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Role Name (Slug)</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., manager, staff, supervisor" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name</label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role->display_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Project Manager, Team Lead" required>
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe what this role does...">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hierarchy Level -->
                    <div>
                        <label for="hierarchy_level" class="block text-sm font-medium text-gray-700">Hierarchy Level</label>
                        <input type="number" name="hierarchy_level" id="hierarchy_level" value="{{ old('hierarchy_level', $role->hierarchy_level) }}" min="1" max="100" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <p class="mt-1 text-sm text-gray-500">Higher numbers indicate more senior roles (1 = entry level, 100 = highest level)</p>
                        @error('hierarchy_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Badge Color -->
                    <div>
                        <label for="badge_color" class="block text-sm font-medium text-gray-700">Badge Color</label>
                        <input type="color" name="badge_color" id="badge_color" value="{{ old('badge_color', $role->badge_color) }}" class="mt-1 block border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('badge_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-4">
                            @foreach($permissions->groupBy('category') as $category => $categoryPermissions)
                                <div class="border-b border-gray-100 pb-2 mb-2">
                                    <h4 class="font-medium text-gray-900 text-sm uppercase">{{ $category }}</h4>
                                    <div class="mt-2 space-y-1">
                                        @foreach($categoryPermissions as $permission)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                       {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">{{ $permission->display_name }}</span>
                                                @if($permission->description)
                                                    <span class="ml-2 text-xs text-gray-500">({{ $permission->description }})</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('permissions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.groups.roles.index', $group) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout>
