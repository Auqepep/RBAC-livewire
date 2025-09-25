<div class="space-y-6">
    <!-- Group Header -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $group->name; }}</h1>
                    @if($group->description)
                        <p class="mt-2 text-gray-600">{{ $group->description; }}</p>
                    @endif
                    
                    <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ $group->members->count(); }} members
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 6v6m1-10V4a5 5 0 00-10 0v3M3 7h18l-1 13H4L3 7z"/>
                            </svg>
                            Created {{ $group->created_at->format('M d, Y'); }}
                        </div>
                        @if($group->creator)
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Created by {{ $group->creator->name; }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    @if($group->is_active)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            Inactive
                        </span>
                    @endif
                    
                    @if($isMember)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            Member
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Group Members -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Group Members ({{ $group->members->count(); }})
            </h2>
            
            @if($group->members->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($group->members as $member)
                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                            <div class="h-10 w-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ substr($member->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $member->user->name }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $member->user->email }}
                                </p>
                                @if($member->joined_at)
                                    <p class="text-xs text-gray-400">
                                        Joined {{ $member->joined_at->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                            @if($member->user->roles->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($member->user->roles->take(2) as $role)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" style="{{ $role->badge_color }}">
                                            {{ $role->display_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="mx-auto h-16 w-16 text-gray-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Members Yet</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        This group doesn't have any members yet.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Group Activity/Information -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Group Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Basic Details</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Group ID</dt>
                            <dd class="text-sm font-medium text-gray-900">#{{ $group->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Status</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ $group->is_active ? 'Active' : 'Inactive' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Created</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ $group->created_at->format('M d, Y \a\t g:i A') }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Last Updated</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                {{ $group->updated_at->format('M d, Y \a\t g:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Membership</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Total Members</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $group->members->count() }}</dd>
                        </div>
                        @if($isMember)
                            @php
                                $userMembership = $group->members->where('user_id', auth()->id())->first();
                            @endphp
                            @if($userMembership?->joined_at)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">You Joined</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ $userMembership->joined_at->format('M d, Y') }}
                                    </dd>
                                </div>
                            @endif
                        @endif
                        @if($group->creator)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Created By</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $group->creator->name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex justify-between">
        <a href="{{ route('my-groups') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to My Groups
        </a>
        
        <a href="{{ route('available-groups') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Browse Other Groups
        </a>
    </div>
</div>
