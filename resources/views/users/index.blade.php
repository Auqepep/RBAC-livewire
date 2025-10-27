<x-user.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users Directory') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-mary-card title="Users Directory">
                <!-- Search and Sort Form -->
                <div class="mb-6 space-y-4">
                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <x-mary-input 
                                name="search" 
                                value="{{ $search }}" 
                                placeholder="Search users by name or email..."
                                icon="o-magnifying-glass"
                            />
                        </div>
                        
                        <!-- Sort Controls -->
                        <div class="flex gap-2">
                            <x-mary-select 
                                name="sort_by" 
                                :options="[
                                    ['value' => 'name', 'label' => 'Name'],
                                    ['value' => 'email', 'label' => 'Email'],
                                    ['value' => 'created_at', 'label' => 'Date Created']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortBy }}"
                                placeholder="Sort by..."
                            />
                            
                            <x-mary-select 
                                name="sort_order" 
                                :options="[
                                    ['value' => 'asc', 'label' => 'A-Z / Oldest'],
                                    ['value' => 'desc', 'label' => 'Z-A / Newest']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortOrder }}"
                                placeholder="Order..."
                            />
                        </div>
                        
                        <div class="flex gap-2">
                            <x-mary-button type="submit" class="btn-primary" icon="o-magnifying-glass">
                                Search
                            </x-mary-button>
                            @if($search || $sortBy !== 'name' || $sortOrder !== 'asc')
                                <x-mary-button link="{{ route('users.index') }}" class="btn-secondary" icon="o-x-mark">
                                    Reset
                                </x-mary-button>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Users List -->
                @if($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groups</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-bold text-white">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->email_verified_at)
                                                <x-mary-badge value="Verified" class="badge-success" />
                                            @else
                                                <x-mary-badge value="Unverified" class="badge-warning" />
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->roles->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user->roles->take(2) as $role)
                                                        <x-mary-badge 
                                                            value="{{ $role->display_name }}" 
                                                            class="badge-{{ $role->getBadgeColor() }} text-xs"
                                                        />
                                                    @endforeach
                                                    @if($user->roles->count() > 2)
                                                        <x-mary-badge value="+{{ $user->roles->count() - 2 }}" class="badge-ghost text-xs" />
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">No roles</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->groups->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user->groups->take(2) as $group)
                                                        <x-mary-badge value="{{ $group->name }}" class="badge-outline text-xs" />
                                                    @endforeach
                                                    @if($user->groups->count() > 2)
                                                        <x-mary-badge value="+{{ $user->groups->count() - 2 }}" class="badge-ghost text-xs" />
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">No groups</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $user->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <x-mary-icon name="o-user-group" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Users Found</h3>
                        @if($search)
                            <p class="text-gray-500 mb-4">No users match your search criteria.</p>
                            <x-mary-button link="{{ route('users.index') }}" class="btn-primary">
                                View All Users
                            </x-mary-button>
                        @else
                            <p class="text-gray-500">There are no users to display.</p>
                        @endif
                    </div>
                @endif
            </x-mary-card>
        </div>
    </div>
</x-user.layout>
