<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <!-- Refresh Button -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">System Statistics</h3>
        <button 
            wire:click="refreshStats"
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['users'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_users'] }} verified</p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Roles -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Roles</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['roles'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_assignments'] }} assignments</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Groups -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Groups</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['groups'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_groups'] }} active</p>
                    </div>
                    <div class="h-12 w-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Permissions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Assignments</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_assignments'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">System wide</p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Distribution Section -->
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Role Distribution</h3>
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                @if($stats['role_distribution']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['role_distribution'] as $role)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="h-4 w-4 rounded-full" style="background-color: {{ $role->color ?? '#3B82F6' }}"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $role->display_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">{{ $role->users_count }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $stats['users'] > 0 ? round(($role->users_count / $stats['users']) * 100, 1) : 0 }}% of users
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No roles found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
