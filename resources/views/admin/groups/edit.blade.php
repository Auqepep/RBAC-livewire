<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Group: {{ $group->name }}
            </h2>
            <a href="{{ route('admin.groups.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Groups
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('admin.groups.update', $group) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Group Name
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900" 
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900">{{ old('description', $group->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Status
                        </label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-4">
                            <svg class="h-4 w-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                            Group Members
                            @if($users->count() > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $users->count() }} available
                                </span>
                            @endif
                        </label>
                        
                        @if($users->count() > 0)
                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                <!-- Search Bar -->
                                <div class="mb-4">
                                    <div class="relative">
                                        <input type="text" id="userSearch" placeholder="Search users by name or email..." 
                                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900 text-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <!-- Selection Controls -->
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex space-x-2">
                                            <button type="button" id="selectAll" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                                Select All Visible
                                            </button>
                                            <span class="text-gray-300">|</span>
                                            <button type="button" id="deselectAll" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                                Deselect All
                                            </button>
                                        </div>
                                        <span id="selectedCount" class="text-xs text-gray-500 font-medium">0 selected</span>
                                    </div>
                                </div>
                                
                                <div class="max-h-60 overflow-y-auto space-y-3" id="usersList">
                                    @php
                                        $currentMemberIds = old('members', $group->members->pluck('id')->toArray());
                                    @endphp
                                    @foreach($users as $user)
                                        <div class="user-item flex items-start p-3 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 transition duration-200" 
                                             data-user-name="{{ strtolower($user->name) }}" 
                                             data-user-email="{{ strtolower($user->email) }}">
                                            <input type="checkbox" name="members[]" value="{{ $user->id }}" 
                                                   id="member_{{ $user->id }}"
                                                   {{ in_array($user->id, $currentMemberIds) ? 'checked' : '' }}
                                                   class="user-checkbox mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <label for="member_{{ $user->id }}" class="ml-3 cursor-pointer flex-1">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                    </div>
                                                    @if($user->roles->count() > 0)
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($user->roles->take(2) as $role)
                                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                                                                    {{ $role->display_name }}
                                                                </span>
                                                            @endforeach
                                                            @if($user->roles->count() > 2)
                                                                <span class="text-xs text-gray-500">+{{ $user->roles->count() - 2 }} more</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- No Results Message -->
                                <div id="noResults" class="hidden text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="text-gray-500">No users found matching your search.</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                </svg>
                                <p class="text-gray-500">No verified users available to add to the group.</p>
                            </div>
                        @endif
                        @error('members')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('members.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 space-x-3">
                    <a href="{{ route('admin.groups.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Group
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('userSearch');
            const userItems = document.querySelectorAll('.user-item');
            const noResults = document.getElementById('noResults');
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');
            const selectedCount = document.getElementById('selectedCount');
            const checkboxes = document.querySelectorAll('.user-checkbox');

            // Update selected count
            function updateSelectedCount() {
                const checked = document.querySelectorAll('.user-checkbox:checked').length;
                selectedCount.textContent = `${checked} selected`;
            }

            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    userItems.forEach(function(item) {
                        const userName = item.dataset.userName;
                        const userEmail = item.dataset.userEmail;
                        
                        if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                            item.style.display = 'flex';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Show/hide no results message
                    if (visibleCount === 0 && searchTerm !== '') {
                        noResults.classList.remove('hidden');
                    } else {
                        noResults.classList.add('hidden');
                    }
                });
            }

            // Select all visible users
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    userItems.forEach(function(item) {
                        if (item.style.display !== 'none') {
                            const checkbox = item.querySelector('.user-checkbox');
                            checkbox.checked = true;
                        }
                    });
                    updateSelectedCount();
                });
            }

            // Deselect all users
            if (deselectAllBtn) {
                deselectAllBtn.addEventListener('click', function() {
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false;
                    });
                    updateSelectedCount();
                });
            }

            // Update count when checkboxes change
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            // Initial count update
            updateSelectedCount();
        });
    </script>
</x-admin.layout>
