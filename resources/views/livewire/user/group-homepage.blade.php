<div>
    <!-- Group Header -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="relative">
            <!-- Cover area -->
            <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
            
            <!-- Group info -->
            <div class="px-6 pb-6">
                <div class="flex items-end -mt-8">
                    <!-- Group Avatar -->
                    <div class="h-20 w-20 bg-white rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                        <span class="text-2xl font-bold text-indigo-600">
                            {{ substr($group->name, 0, 1) }}
                        </span>
                    </div>
                    
                    <!-- Group Name and Description -->
                    <div class="ml-6 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h1>
                                @if($group->description)
                                    <p class="text-gray-600 mt-1">{{ $group->description }}</p>
                                @endif
                            </div>
                            
                            <!-- Status Badge -->
                            @if($group->is_active)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Active Group
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive Group
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="mt-6 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $group->members->count() }}</div>
                        <div class="text-sm text-gray-500">{{ Str::plural('Member', $group->members->count()) }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $group->created_at->format('M Y') }}</div>
                        <div class="text-sm text-gray-500">Created</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $group->creator->name ?? 'Unknown' }}</div>
                        <div class="text-sm text-gray-500">Created by</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Group Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">About This Group</h3>
                </div>
                <div class="p-6">
                    @if($group->description)
                        <p class="text-gray-700 text-base leading-relaxed">{{ $group->description }}</p>
                    @else
                        <p class="text-gray-500 italic">No description provided for this group.</p>
                    @endif
                    
                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Created</span>
                            <p class="font-medium text-gray-900">{{ $group->created_at->format('F d, Y \a\t H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Status</span>
                            <p class="font-medium text-gray-900">
                                {{ $group->is_active ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Placeholder -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-6">
                    <div class="text-center py-8">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="mt-2 text-sm font-medium text-gray-900">No Recent Activity</h4>
                        <p class="mt-1 text-sm text-gray-500">Activity and discussions will appear here.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Members List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Members ({{ $group->members->count() }})
                    </h3>
                </div>
                <div class="p-6">
                    @if($group->members->count() > 0)
                        <div class="space-y-4">
                            @foreach($group->members->take(10) as $member)
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-600">
                                            {{ substr($member->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $member->user->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $member->user->email }}
                                        </p>
                                        @if($member->user->roles->count() > 0)
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach($member->user->roles->take(2) as $role)
                                                    <span class="inline-flex px-1.5 py-0.5 text-xs font-semibold rounded text-white" style="background-color: {{ $role->color ?? '#3B82F6' }}">
                                                        {{ $role->display_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($group->members->count() > 10)
                                <div class="text-center">
                                    <p class="text-sm text-gray-500">
                                        And {{ $group->members->count() - 10 }} more members
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No members in this group yet.</p>
                    @endif
                </div>
            </div>

            <!-- Group Actions -->
            @if($isMember)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md text-sm font-medium transition-colors">
                            Create Discussion
                        </button>
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md text-sm font-medium transition-colors">
                            Schedule Event
                        </button>
                        <button class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md text-sm font-medium transition-colors">
                            View Group Settings
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
