<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Role') }}: {{ $role->display_name }}
            </h2>
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Roles
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <livewire:admin.edit-role :role="$role" />
        </div>
    </div>
</x-admin.layout>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name</label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role->display_name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g., Manager, Content Editor">
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">This will be shown to users.</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Describe what this role can do...">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color -->
                <div class="mt-6">
                    <label for="color" class="block text-sm font-medium text-gray-700">Role Color</label>
                    <div class="mt-1 flex items-center space-x-3">
                        <input type="color" name="color" id="color" value="{{ old('color', $role->color ?? '#3B82F6') }}" required
                               class="h-10 w-20 rounded border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="flex-1">
                            <input type="text" id="color_text" value="{{ old('color', $role->color ?? '#3B82F6') }}" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="#3B82F6" pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <div id="color-preview" class="w-10 h-10 rounded border-2 border-gray-300" style="background-color: {{ old('color', $role->color ?? '#3B82F6') }};"></div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This color will be used for role badges throughout the system.</p>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Only active roles can be assigned to users.</p>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Permissions Display -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Current Permissions</label>
                    @if($role->permissions->count() > 0)
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">This role currently has {{ $role->permissions->count() }} permissions assigned:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions as $permission)
                                    <span class="inline-flex px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                        {{ $permission->display_name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-4">No permissions currently assigned</p>
                    @endif
                </div>

                <!-- Assign New Permissions -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Assign Permissions</label>
                    
                    @if($permissions->count() > 0)
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="mb-6 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3 capitalize">
                                    {{ $module }} Module
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($modulePermissions as $permission)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="permission_{{ $permission->id }}" class="ml-2 block text-sm text-gray-900">
                                                <span class="font-medium">{{ $permission->display_name }}</span>
                                                @if($permission->description)
                                                    <span class="block text-xs text-gray-500">{{ $permission->description }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500">No permissions available.</p>
                    @endif
                    
                    @error('permissions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Information Display -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Users with this Role</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->users->count() }} users</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.roles.show', $role) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        View Role
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sync color picker and text input
        const colorPicker = document.getElementById('color');
        const colorText = document.getElementById('color_text');
        const colorPreview = document.getElementById('color-preview');

        colorPicker.addEventListener('input', function() {
            colorText.value = this.value;
            colorPreview.style.backgroundColor = this.value;
        });

        colorText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorPicker.value = this.value;
                colorPreview.style.backgroundColor = this.value;
            }
        });

        colorText.addEventListener('change', function() {
            colorPicker.value = this.value;
        });
    </script>
</x-admin.layout>
