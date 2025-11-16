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
                            <div class="mt-2 flex flex-wrap gap-1">
                                @php
                                    // Get user's groups with pivot data
                                    $userGroups = Auth::user()->groups;
                                    
                                    // Get all unique role IDs from the groups
                                    $roleIds = $userGroups->pluck('pivot.role_id')->unique();
                                    
                                    // Load all roles at once
                                    $roles = \App\Models\Role::whereIn('id', $roleIds)->get()->keyBy('id');
                                    
                                    // Group by role_id and collect the groups for each role
                                    $roleGroups = $userGroups->groupBy(function($group) {
                                        return $group->pivot->role_id;
                                    })->map(function($groups) use ($roles) {
                                        $roleId = $groups->first()->pivot->role_id;
                                        return [
                                            'role' => $roles[$roleId] ?? null,
                                            'groups' => $groups
                                        ];
                                    })->filter(function($roleGroup) {
                                        return $roleGroup['role'] !== null;
                                    });
                                @endphp
                                
                                @foreach($roleGroups as $roleGroup)
                                    <button 
                                        onclick="role_modal_{{ $roleGroup['role']->id }}.showModal()" 
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white cursor-pointer hover:opacity-80 transition-opacity" 
                                        style="background-color: {{ $roleGroup['role']->badge_color }}">
                                        {{ $roleGroup['role']->display_name }}
                                    </button>

                                    <!-- Modal for this role -->
                                    <dialog id="role_modal_{{ $roleGroup['role']->id }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <h3 class="font-bold text-lg mb-4">
                                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full text-white mr-2" style="background-color: {{ $roleGroup['role']->badge_color }}">
                                                    {{ $roleGroup['role']->display_name }}
                                                </span>
                                            </h3>
                                            <p class="text-sm text-gray-600 mb-4">You have this role in the following groups:</p>
                                            <div class="space-y-2">
                                                @foreach($roleGroup['groups'] as $group)
                                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span class="font-medium text-gray-900">{{ $group->name }}</span>
                                                        </div>
                                                        <a href="{{ route('groups.show', $group->id) }}" class="btn btn-xs btn-primary">
                                                            View
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>
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
                        <a href="{{ route('groups.index') }}" class="text-sm text-blue-600 hover:underline">
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

        
    </div>
</x-user.layout>
