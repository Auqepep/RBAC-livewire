<div>
    <!-- Search and Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
            <input type="text" wire:model.live="search" id="search" placeholder="Search by name or email..."
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <!-- Role Filter -->
        <div>
            <label for="roleFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
            <select wire:model.live="roleFilter" id="roleFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Per Page -->
        <div>
            <label for="perPage" class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
            <select wire:model.live="perPage" id="perPage"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Results Info -->
    <div class="mb-4 text-sm text-gray-600">
        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700">
                            <span>Name</span>
                            @if($sortBy === 'name')
                                @if($sortDirection === 'asc')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                    </svg>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortBy('email')" class="flex items-center space-x-1 hover:text-gray-700">
                            <span>Email</span>
                            @if($sortBy === 'email')
                                @if($sortDirection === 'asc')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                    </svg>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Roles
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700">
                            <span>Joined</span>
                            @if($sortBy === 'created_at')
                                @if($sortDirection === 'asc')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                    </svg>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-600">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            @if($user->email_verified_at)
                                <div class="text-xs text-green-600">Verified</div>
                            @else
                                <div class="text-xs text-yellow-600">Unverified</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                                          style="background-color: {{ $role->color ?? '#3B82F6' }}">
                                        {{ $role->display_name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-500">No roles assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="text-blue-600 hover:text-blue-900">Edit</a>
                                @if($user->id !== auth()->id())
                                    <button wire:click="deleteUser({{ $user->id }})" 
                                            wire:confirm="Are you sure you want to delete this user?"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            @if($search || $roleFilter)
                                No users found matching your criteria.
                            @else
                                No users found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-4 flex items-center space-x-2">
            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-600">Loading...</span>
        </div>
    </div>
</div>
