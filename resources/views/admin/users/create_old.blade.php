<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New User') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <livewire:admin.create-user />
        </div>
    </div>
</x-admin.layout>
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Roles -->
                <div class="mt-6">
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-4">
                        <svg class="h-4 w-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Assign Roles
                        @if($roles->count() > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $roles->count() }} available
                            </span>
                        @endif
                    </label>
                    
                    @if($roles->count() > 0)
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <!-- Search Bar -->
                            <div class="mb-4">
                                <div class="relative">
                                    <input type="text" id="roleSearch" placeholder="Search roles by name or description..." 
                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900 text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Selection Controls -->
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex space-x-2">
                                        <button type="button" id="selectAllRoles" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Select All Visible
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button type="button" id="deselectAllRoles" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Deselect All
                                        </button>
                                    </div>
                                    <span id="selectedRoleCount" class="text-xs text-gray-500 font-medium">0 selected</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="rolesList">
                                @foreach($roles as $role)
                                    <div class="role-item flex items-start p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition duration-200" 
                                         data-role-name="{{ strtolower($role->display_name) }}" 
                                         data-role-description="{{ strtolower($role->description) }}">
                                        <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}"
                                               {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                               class="role-checkbox mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="role_{{ $role->id }}" class="ml-3 cursor-pointer flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $role->display_name }}</p>
                                                    @if($role->description)
                                                        <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                                    @endif
                                                </div>
                                                @if($role->is_active)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- No Results Message -->
                            <div id="noRoleResults" class="hidden text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-gray-500">No roles found matching your search.</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <p class="text-gray-500">No roles available to assign.</p>
                        </div>
                    @endif
                    @error('roles')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSearchInput = document.getElementById('roleSearch');
            const roleItems = document.querySelectorAll('.role-item');
            const noRoleResults = document.getElementById('noRoleResults');
            const selectAllRolesBtn = document.getElementById('selectAllRoles');
            const deselectAllRolesBtn = document.getElementById('deselectAllRoles');
            const selectedRoleCount = document.getElementById('selectedRoleCount');
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');

            // Update selected role count
            function updateSelectedRoleCount() {
                const checked = document.querySelectorAll('.role-checkbox:checked').length;
                selectedRoleCount.textContent = `${checked} selected`;
            }

            // Role search functionality
            if (roleSearchInput) {
                roleSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    roleItems.forEach(function(item) {
                        const roleName = item.dataset.roleName;
                        const roleDescription = item.dataset.roleDescription;
                        
                        if (roleName.includes(searchTerm) || roleDescription.includes(searchTerm)) {
                            item.style.display = 'flex';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Show/hide no results message
                    if (visibleCount === 0 && searchTerm !== '') {
                        noRoleResults.classList.remove('hidden');
                    } else {
                        noRoleResults.classList.add('hidden');
                    }
                });
            }

            // Select all visible roles
            if (selectAllRolesBtn) {
                selectAllRolesBtn.addEventListener('click', function() {
                    roleItems.forEach(function(item) {
                        if (item.style.display !== 'none') {
                            const checkbox = item.querySelector('.role-checkbox');
                            checkbox.checked = true;
                        }
                    });
                    updateSelectedRoleCount();
                });
            }

            // Deselect all roles
            if (deselectAllRolesBtn) {
                deselectAllRolesBtn.addEventListener('click', function() {
                    roleCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = false;
                    });
                    updateSelectedRoleCount();
                });
            }

            // Update count when checkboxes change
            roleCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateSelectedRoleCount);
            });

            // Initial count update
            updateSelectedRoleCount();
        });
    </script>
</x-admin.layout>
