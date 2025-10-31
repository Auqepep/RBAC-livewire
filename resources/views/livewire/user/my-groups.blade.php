<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- User Info Card -->
        <x-mary-card title="Your Profile">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-xl font-bold text-white">
                        {{ substr($user->name, 0, 1) }}
                    </span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    
                    @if($user->roles->count() > 0)
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <x-mary-badge 
                                    value="{{ $role->display_name }}" 
                                    class="badge-{{ $role->getBadgeColor() }}"
                                />
                            @endforeach
                        </div>
                    @endif
                </div>
                
                @if($user->email_verified_at)
                    <x-mary-badge value="Verified" class="badge-success" />
                @else
                    <x-mary-badge value="Unverified" class="badge-warning" />
                @endif
            </div>
        </x-mary-card>

        <!-- Groups Section -->
        <x-mary-card title="Your Groups ({{ $groups->count() }})">
            @if($groups->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($groups as $group)
                        <div class="border rounded-lg p-6 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ route('groups.show', $group->id) }}" class="hover:text-blue-600 transition-colors">
                                            {{ $group->name }}
                                        </a>
                                    </h4>
                                    @if($group->description)
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ Str::limit($group->description, 80) }}
                                        </p>
                                    @endif
                                </div>
                                
                                @if($group->is_active)
                                    <x-mary-badge value="Active" class="badge-success" />
                                @else
                                    <x-mary-badge value="Inactive" class="badge-error" />
                                @endif
                            </div>

                            <!-- Your Role in this Group -->
                            @php
                                $userGroupMembership = $group->groupMembers->where('user_id', $user->id)->first();
                            @endphp
                            @if($userGroupMembership && $userGroupMembership->role)
                                <div class="mb-4">
                                    <p class="text-xs text-gray-500">Your Role:</p>
                                    <x-mary-badge 
                                        value="{{ $userGroupMembership->role->display_name }}" 
                                        class="badge-{{ $userGroupMembership->role->getBadgeColor() }}"
                                    />
                                </div>
                            @endif

                            <!-- Group Members -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Members</span>
                                    <span class="text-sm text-gray-500">{{ $group->groupMembers->count() }}</span>
                                </div>
                                
                                @if($group->groupMembers->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($group->groupMembers->take(5) as $member)
                                            <div class="flex items-center space-x-2 bg-white rounded-full px-3 py-1 border border-gray-200">
                                                <div class="h-6 w-6 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-medium text-white">
                                                        {{ substr($member->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-700">
                                                    {{ $member->user->name }}
                                                </span>
                                            </div>
                                        @endforeach
                                        
                                        @if($group->groupMembers->count() > 5)
                                            <div class="flex items-center justify-center h-8 w-8 bg-gray-200 rounded-full">
                                                <span class="text-xs text-gray-600">
                                                    +{{ $group->groupMembers->count() - 5 }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">No other members</p>
                                @endif
                            </div>

                            <!-- Group Stats -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Created</span>
                                        <p class="font-medium">{{ $group->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Your Join Date</span>
                                        <p class="font-medium">
                                            {{ $userGroupMembership?->joined_at?->format('M d, Y') ?? $userGroupMembership?->created_at?->format('M d, Y') ?? 'Unknown' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mt-3 space-y-2">
                                    <x-mary-button 
                                        link="{{ route('groups.show', $group->id) }}" 
                                        class="btn-primary btn-sm w-full"
                                        icon="o-arrow-right"
                                    >
                                        View Group
                                    </x-mary-button>
                                    
                                    <x-mary-button 
                                        link="{{ route('groups.gateway', $group->id) }}" 
                                        class="btn-accent btn-sm w-full"
                                        icon="o-key"
                                    >
                                        🚪 Gateway Access
                                    </x-mary-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <x-mary-icon name="o-user-group" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Groups Yet</h3>
                    <p class="text-gray-500 mb-4">You haven't joined any groups yet.</p>
                    <x-mary-button 
                        link="{{ route('available-groups') }}" 
                        class="btn-primary"
                        icon="o-plus"
                    >
                        Browse Available Groups
                    </x-mary-button>
                </div>
            @endif
        </x-mary-card>
    </div>
</div>
