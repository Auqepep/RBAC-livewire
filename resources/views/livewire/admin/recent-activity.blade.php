<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header with Refresh -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        <button 
            wire:click="refreshActivity"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            wire:loading.attr="disabled"
        >
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.class="animate-spin">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span wire:loading.remove>Refresh</span>
            <span wire:loading>Refreshing...</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Users -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-medium text-gray-900">Recent Users</h4>
            </div>
            <div class="p-6">
                @if($recentUsers->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentUsers as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-blue-600">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            View all users →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No users found.</p>
                @endif
            </div>
        </div>

        <!-- Recent Roles -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-medium text-gray-900">Recent Roles</h4>
            </div>
            <div class="p-6">
                @if($recentRoles->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentRoles as $role)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="h-3 w-3 rounded-full" style="background-color: {{ $role->color ?? '#3B82F6' }}"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $role->display_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400">{{ $role->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.groups.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Manage roles in groups →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No roles found.</p>
                @endif
            </div>
        </div>

        <!-- Recent Groups -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h4 class="text-sm font-medium text-gray-900">Recent Groups</h4>
            </div>
            <div class="p-6">
                @if($recentGroups->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentGroups as $group)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $group->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $group->users()->count() }} members</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400">{{ $group->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.groups.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            View all groups →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No groups found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
