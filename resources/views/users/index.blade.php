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
                <!-- Search Form -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('users.index') }}" class="flex gap-4">
                        <div class="flex-1">
                            <x-mary-input 
                                name="search" 
                                value="{{ $search }}" 
                                placeholder="Search users by name or email..."
                                icon="o-magnifying-glass"
                            />
                        </div>
                        <x-mary-button type="submit" class="btn-primary" icon="o-magnifying-glass">
                            Search
                        </x-mary-button>
                        @if($search)
                            <x-mary-button link="{{ route('users.index') }}" class="btn-secondary" icon="o-x-mark">
                                Clear
                            </x-mary-button>
                        @endif
                    </form>
                </div>

                <!-- Users List -->
                @if($users->count() > 0)
                    <div class="space-y-4">
                        @foreach($users as $user)
                            <div class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-lg font-bold text-white">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $user->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                            
                                            <!-- Email Verification Status -->
                                            @if($user->email_verified_at)
                                                <x-mary-badge value="Verified" class="badge-success mt-1" />
                                            @else
                                                <x-mary-badge value="Unverified" class="badge-warning mt-1" />
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right space-y-2">
                                        <!-- Roles -->
                                        @if($user->roles->count() > 0)
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Roles:</p>
                                                <div class="flex flex-wrap gap-1 justify-end">
                                                    @foreach($user->roles as $role)
                                                        <x-mary-badge 
                                                            value="{{ $role->display_name }}" 
                                                            class="badge-{{ $role->getBadgeColor() }}"
                                                        />
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Groups -->
                                        @if($user->groups->count() > 0)
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Groups:</p>
                                                <div class="flex flex-wrap gap-1 justify-end">
                                                    @foreach($user->groups->take(3) as $group)
                                                        <x-mary-badge 
                                                            value="{{ $group->name }}" 
                                                            class="badge-ghost"
                                                        />
                                                    @endforeach
                                                    @if($user->groups->count() > 3)
                                                        <x-mary-badge 
                                                            value="+{{ $user->groups->count() - 3 }} more" 
                                                            class="badge-ghost"
                                                        />
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400">No groups</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-500">
                            @if($search)
                                <x-mary-icon name="o-users" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                                <p class="text-lg font-medium">No users found for "{{ $search }}"</p>
                                <p class="mt-1">Try adjusting your search terms.</p>
                                <x-mary-button link="{{ route('users.index') }}" class="btn-ghost btn-sm mt-4">
                                    Clear search
                                </x-mary-button>
                            @else
                                <x-mary-icon name="o-users" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                                <p class="text-lg font-medium">No users found</p>
                            @endif
                        </div>
                    </div>
                @endif
            </x-mary-card>
        </div>
    </div>
</x-user.layout>
