<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Group Header -->
        <x-mary-card>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h1>
                    @if($group->description)
                        <p class="mt-2 text-gray-600">{{ $group->description }}</p>
                    @endif
                    
                    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <div class="flex items-center">
                            <x-mary-icon name="o-users" class="w-4 h-4 mr-1" />
                            {{ $group->groupMembers->count() }} members
                        </div>
                        <div class="flex items-center">
                            <x-mary-icon name="o-calendar" class="w-4 h-4 mr-1" />
                            Created {{ $group->created_at->format('M d, Y') }}
                        </div>
                        @if($group->creator)
                            <div class="flex items-center">
                                <x-mary-icon name="o-user" class="w-4 h-4 mr-1" />
                                Created by {{ $group->creator->name }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    @if($group->is_active)
                        <x-mary-badge value="Active" class="badge-success" />
                    @else
                        <x-mary-badge value="Inactive" class="badge-error" />
                    @endif
                    
                    @if($isMember)
                        <x-mary-badge value="Member" class="badge-info" />
                    @endif
                </div>
            </div>
        </x-mary-card>

        <!-- Group Members -->
        <x-mary-card title="Group Members ({{ $group->groupMembers->count() }})">
            @if($group->groupMembers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($group->groupMembers as $member)
                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border">
                            <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-bold text-white">
                                    {{ substr($member->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $member->user->name }}
                                    @if($member->user->id === auth()->id())
                                        <span class="text-xs text-gray-500">(You)</span>
                                    @endif
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
                            <div class="text-right">
                                @if($member->role)
                                    <x-mary-badge 
                                        value="{{ $member->role->display_name }}" 
                                        class="badge-{{ $member->role->getBadgeColor() }}"
                                    />
                                @endif
                                
                                @if($member->user->email_verified_at)
                                    <x-mary-badge value="Verified" class="badge-success mt-1" />
                                @else
                                    <x-mary-badge value="Unverified" class="badge-warning mt-1" />
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <x-mary-icon name="o-users" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Members</h3>
                    <p class="text-gray-500">This group doesn't have any members yet.</p>
                </div>
            @endif
        </x-mary-card>

        <!-- Group Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-mary-card title="Member Statistics">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Members</span>
                        <span class="font-semibold">{{ $group->groupMembers->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Verified Members</span>
                        <span class="font-semibold">{{ $group->groupMembers->whereNotNull('user.email_verified_at')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Active Roles</span>
                        <span class="font-semibold">{{ $group->groupMembers->whereNotNull('role_id')->groupBy('role_id')->count() }}</span>
                    </div>
                </div>
            </x-mary-card>

            <x-mary-card title="Group Activity">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created</span>
                        <span class="font-semibold">{{ $group->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status</span>
                        @if($group->is_active)
                            <x-mary-badge value="Active" class="badge-success" />
                        @else
                            <x-mary-badge value="Inactive" class="badge-error" />
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Updated</span>
                        <span class="font-semibold">{{ $group->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </x-mary-card>

            <x-mary-card title="Your Status">
                @php
                    $userMembership = $group->groupMembers->where('user_id', auth()->id())->first();
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Membership</span>
                        @if($isMember)
                            <x-mary-badge value="Member" class="badge-success" />
                        @else
                            <x-mary-badge value="Not a member" class="badge-ghost" />
                        @endif
                    </div>
                    @if($userMembership)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Your Role</span>
                            @if($userMembership->role)
                                <x-mary-badge 
                                    value="{{ $userMembership->role->display_name }}" 
                                    class="badge-{{ $userMembership->role->getBadgeColor() }}"
                                />
                            @else
                                <span class="text-gray-500">No role assigned</span>
                            @endif
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Joined</span>
                            <span class="font-semibold">
                                {{ $userMembership->joined_at?->format('M d, Y') ?? $userMembership->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    @endif
                </div>
            </x-mary-card>
        </div>
    </div>
</div>
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
