<x-user.layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-xl font-medium text-blue-600">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                Welcome back, {{ Auth::user()->name }}!
                            </h3>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="text-xs text-gray-500">Access Level:</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ Auth::user()->getUserLevel() }}
                                </span>
                            </div>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach(Auth::user()->roles as $role)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" style="background-color: {{ $role->badge_color }}">
                                        {{ $role->display_name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Admin Quick Actions -->
                    @if(Auth::user()->canManageSystem())
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Admin Panel
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- My Groups Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 truncate">My Groups</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ Auth::user()->groups->count() }}</dd>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('my-groups') }}" class="text-sm text-blue-600 hover:underline">
                            View all groups →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m6 0V9a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 9v10.5h15V9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ \App\Models\User::count() }}</dd>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('users.index') }}" class="text-sm text-blue-600 hover:underline">
                            Browse users →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Status Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 bg-{{ Auth::user()->email_verified_at ? 'green' : 'yellow' }}-100 rounded-full flex items-center justify-center">
                                <svg class="h-6 w-6 text-{{ Auth::user()->email_verified_at ? 'green' : 'yellow' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if(Auth::user()->email_verified_at)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    @endif
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 truncate">Account Status</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ Auth::user()->email_verified_at ? 'Verified' : 'Unverified' }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Your Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Account Details</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm text-gray-600">Member since</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ Auth::user()->created_at->format('F j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Last updated</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ Auth::user()->updated_at->format('F j, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Quick Actions</h4>
                        <div class="space-y-2">
                            <a href="{{ route('users.index') }}" class="block text-sm text-blue-600 hover:underline">
                                View all users
                            </a>
                            <a href="{{ route('my-groups') }}" class="block text-sm text-blue-600 hover:underline">
                                Manage my groups
                            </a>
                            @if(Auth::user()->hasRole('administrator'))
                                <a href="{{ route('admin.dashboard') }}" class="block text-sm text-blue-600 hover:underline">
                                    Access admin panel
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user.layout>
