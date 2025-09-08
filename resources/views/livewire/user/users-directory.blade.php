<div>
    <!-- Search Section -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search users by name or email..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                @if($search)
                    <button 
                        wire:click="clearSearch"
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
                    >
                        Clear
                    </button>
                @endif

                <div class="flex items-center space-x-2">
                    <label for="perPage" class="text-sm text-gray-600">Show:</label>
                    <select 
                        id="perPage"
                        wire:model.live="perPage" 
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="6">6</option>
                        <option value="12">12</option>
                        <option value="24">24</option>
                        <option value="48">48</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Sort Options -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-4">
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Sort by:</span>
                
                <button 
                    wire:click="sortBy('name')"
                    class="inline-flex items-center space-x-1 text-sm {{ $sortField === 'name' ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    <span>Name</span>
                    @if($sortField === 'name')
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            @if($sortDirection === 'asc')
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                    @endif
                </button>

                <button 
                    wire:click="sortBy('email')"
                    class="inline-flex items-center space-x-1 text-sm {{ $sortField === 'email' ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    <span>Email</span>
                    @if($sortField === 'email')
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            @if($sortDirection === 'asc')
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                    @endif
                </button>

                <button 
                    wire:click="sortBy('created_at')"
                    class="inline-flex items-center space-x-1 text-sm {{ $sortField === 'created_at' ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}"
                >
                    <span>Joined</span>
                    @if($sortField === 'created_at')
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            @if($sortDirection === 'asc')
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        @if($users->count() > 0)
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($users as $user)
                        <div wire:key="user-{{ $user->id }}" class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                            <div class="flex flex-col items-center text-center">
                                <!-- Avatar -->
                                <div class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                                    <span class="text-xl font-medium text-blue-600">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                </div>
                                
                                <!-- Name -->
                                <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $user->name }}</h3>
                                
                                <!-- Email -->
                                <p class="text-sm text-gray-500 mb-3 break-all">{{ $user->email }}</p>
                                
                                <!-- Roles -->
                                @if($user->roles->count() > 0)
                                    <div class="flex flex-wrap justify-center gap-1 mb-3">
                                        @foreach($user->roles->take(2) as $role)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" style="background-color: {{ $role->color ?? '#3B82F6' }}">
                                                {{ $role->display_name }}
                                            </span>
                                        @endforeach
                                        @if($user->roles->count() > 2)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                                +{{ $user->roles->count() - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Groups Count -->
                                <div class="text-xs text-gray-500">
                                    {{ $user->groups->count() }} {{ Str::plural('group', $user->groups->count()) }}
                                </div>
                                
                                <!-- Join Date -->
                                <div class="text-xs text-gray-400 mt-1">
                                    Joined {{ $user->created_at->format('M Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search)
                        No users match your search criteria.
                    @else
                        No verified users available.
                    @endif
                </p>
                @if($search)
                    <div class="mt-6">
                        <button 
                            wire:click="clearSearch"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Clear search
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Results Info -->
    @if($users->count() > 0)
        <div class="mt-4 text-sm text-gray-700">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
        </div>
    @endif
</div>
