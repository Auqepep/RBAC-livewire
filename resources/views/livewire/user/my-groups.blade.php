<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
        <!-- User Info Card -->
        <x-mary-card title="Your Profile">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="h-14 w-14 sm:h-16 sm:w-16 bg-gradient-to-r from-blue-300 to-emerald-200 rounded-full flex items-center justify-center shrink-0 mx-auto sm:mx-0">
                    <span class="text-lg sm:text-xl font-bold text-white">
                        {{ substr($user->name, 0, 1) }}
                    </span>
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 break-all sm:break-normal">{{ $user->email }}</p>
                    
                    @php
                        // Get user's groups with pivot data
                        $userGroups = $user->groups;
                        
                        if ($userGroups->count() > 0) {
                            // Get all unique role IDs from the groups
                            $roleIds = $userGroups->pluck('pivot.role_id')->unique();
                            
                            // Load all roles at once
                            $roles = \App\Models\Role::whereIn('id', $roleIds)->get()->keyBy('id');
                            
                            // Group by role hierarchy level (Manager=70, Staff=30)
                            $rolesByType = $userGroups->groupBy(function($group) use ($roles) {
                                $roleId = $group->pivot->role_id;
                                $role = $roles[$roleId] ?? null;
                                if (!$role) return null;
                                
                                // Group by role type based on hierarchy
                                if ($role->hierarchy_level >= 70) return 'manager';
                                if ($role->hierarchy_level >= 30) return 'staff';
                                return 'other';
                            })->filter(function($groups, $key) {
                                return $key !== null;
                            })->map(function($groups, $type) use ($roles) {
                                // Get first role of this type for badge color
                                $firstRoleId = $groups->first()->pivot->role_id;
                                $firstRole = $roles[$firstRoleId];
                                
                                return [
                                    'type' => $type,
                                    'label' => $type === 'manager' ? 'Manager' : ($type === 'staff' ? 'Staff' : ucfirst($type)),
                                    'color' => $firstRole->badge_color,
                                    'groups' => $groups
                                ];
                            });
                        } else {
                            $rolesByType = collect();
                        }
                    @endphp
                    
                    @if($rolesByType->count() > 0)
                        <div class="mt-2 flex flex-wrap justify-center sm:justify-start gap-1">
                            @foreach($rolesByType as $type => $roleData)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
                                      style="background-color: {{ $roleData['color'] }}">
                                    {{ __($roleData['label']) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <div class="flex justify-center sm:justify-end">
                    @if($user->email_verified_at)
                        <x-mary-badge value="Verified" class="badge-success badge-xs sm:badge-sm" />
                    @else
                        <x-mary-badge value="Unverified" class="badge-warning badge-xs sm:badge-sm" />
                    @endif
                </div>
            </div>
        </x-mary-card>

        <!-- Groups Section -->
        <x-mary-card title="Your Groups ({{ $groups->count() }})">
            @if($groups->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($groups as $group)
                        <div class="rounded-lg p-4 sm:p-6 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between mb-3 sm:mb-4 gap-2">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 truncate">
                                        <a href="{{ route('groups.show', $group->id) }}" class="hover:text-blue-600 transition-colors">
                                            {{ $group->name }}
                                        </a>
                                    </h4>
                                    @if($group->description)
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                            {{ Str::limit($group->description, 80) }}
                                        </p>
                                    @endif
                                </div>
                                
                                @if($group->is_active)
                                    <x-mary-badge value="Active" class="badge-success badge-xs sm:badge-sm" />
                                @else
                                    <x-mary-badge value="Inactive" class="badge-error badge-xs sm:badge-sm" />
                                @endif
                            </div>

                            <!-- Your Role in this Group -->
                            @php
                                $userGroupMembership = $group->groupMembers->where('user_id', $user->id)->first();
                            @endphp
                            @if($userGroupMembership && $userGroupMembership->role)
                                <div class="mb-3 sm:mb-4">
                                    <p class="text-xs text-gray-500">Your Role:</p>
                                    <x-mary-badge 
                                        value="{{ $userGroupMembership->role->display_name }}" 
                                        class="badge-{{ $userGroupMembership->role->getBadgeColor() }}"
                                    />
                                </div>
                            @endif

                            <!-- Group Members -->
                            <div class="space-y-2 sm:space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Members</span>
                                    <span class="text-sm text-gray-500">{{ $group->groupMembers->count() }}</span>
                                </div>
                                
                                @if($group->groupMembers->count() > 0)
                                    <div class="flex flex-wrap gap-1 sm:gap-2">
                                        @foreach($group->groupMembers->take(3) as $member)
                                            <div class="flex items-center space-x-1 sm:space-x-2 bg-white rounded-full px-2 sm:px-3 py-1 border border-gray-200">
                                                <div class="h-5 w-5 sm:h-6 sm:w-6 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-medium text-white">
                                                        {{ substr($member->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-700 hidden sm:inline">
                                                    {{ Str::limit($member->user->name, 10) }}
                                                </span>
                                            </div>
                                        @endforeach
                                        
                                        @if($group->groupMembers->count() > 3)
                                            <div class="flex items-center justify-center h-6 w-6 sm:h-8 sm:w-8 bg-gray-200 rounded-full">
                                                <span class="text-xs text-gray-600">
                                                    +{{ $group->groupMembers->count() - 3 }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">No other members</p>
                                @endif
                            </div>

                            <!-- Group Stats -->
                            <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-2 sm:gap-4 text-xs sm:text-sm">
                                    <div>
                                        <span class="text-gray-500">Created</span>
                                        <p class="font-medium">{{ $group->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Joined</span>
                                        <p class="font-medium">
                                            {{ $userGroupMembership?->joined_at?->format('M d, Y') ?? $userGroupMembership?->created_at?->format('M d, Y') ?? 'N/A' }}
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
                                        Gateway
                                    </x-mary-button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 sm:py-12">
                    <x-mary-icon name="o-user-group" class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4 text-gray-400" />
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No Groups Yet</h3>
                    <p class="text-sm text-gray-500 mb-4">You haven't joined any groups yet.</p>
                    <x-mary-button 
                        link="{{ route('available-groups') }}" 
                        class="btn-primary btn-sm sm:btn-md"
                        icon="o-plus"
                    >
                        Browse Available Groups
                    </x-mary-button>
                </div>
            @endif
        </x-mary-card>
    </div>
</div>
