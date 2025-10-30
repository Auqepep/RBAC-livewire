<div>
    <x-mary-header 
        title="{{ $group->name }}" 
        subtitle="Group Information and Members"
        size="text-2xl"
        separator
    >
        <x-slot:actions>
            @if($isMember)
                <x-mary-badge value="Member" class="badge-success" />
            @endif
            @if(auth()->user()->canManageSystem())
                <x-mary-badge value="Admin Access" class="badge-info" />
            @endif
        </x-slot:actions>
    </x-mary-header>

    <div class="grid gap-6">
        <!-- Group Details Card -->
        <x-mary-card title="Group Details" shadow separator>
            <div class="space-y-4">
                <div>
                    <x-mary-badge value="Name" class="badge-neutral mb-2" />
                    <p class="text-lg font-semibold">{{ $group->name }}</p>
                </div>
                
                @if($group->description)
                    <div>
                        <x-mary-badge value="Description" class="badge-neutral mb-2" />
                        <p class="text-gray-700">{{ $group->description }}</p>
                    </div>
                @endif

                <div>
                    <x-mary-badge value="Created" class="badge-neutral mb-2" />
                    <p class="text-sm text-gray-600">
                        {{ $group->created_at->format('M j, Y \a\t g:i A') }}
                        @if($group->creator)
                            by {{ $group->creator->name }}
                        @endif
                    </p>
                </div>

                <div>
                    <x-mary-badge value="Members" class="badge-primary mb-2" />
                    <p class="text-lg">{{ $group->groupMembers->count() }} {{ Str::plural('member', $group->groupMembers->count()) }}</p>
                </div>
            </div>
        </x-mary-card>

        <!-- Group Members Card -->
        <x-mary-card title="Group Members" shadow separator>
            @if($group->groupMembers->count() > 0)
                <div class="space-y-3">
                    @foreach($group->groupMembers as $membership)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($membership->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $membership->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $membership->user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($membership->role)
                                    @php
                                        $badgeClasses = [
                                            'admin' => 'badge-error',
                                            'manager' => 'badge-warning', 
                                            'member' => 'badge-success',
                                            'viewer' => 'badge-info'
                                        ];
                                        $roleClass = $badgeClasses[strtolower($membership->role->name)] ?? 'badge-neutral';
                                    @endphp
                                    <x-mary-badge 
                                        value="{{ $membership->role->name }}" 
                                        class="{{ $roleClass }}"
                                    />
                                @endif
                                <p class="text-xs text-gray-500">
                                    Joined {{ $membership->created_at->format('M j, Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <x-mary-icon name="o-users" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-500">No members in this group yet.</p>
                </div>
            @endif
        </x-mary-card>

        <!-- Admin Actions (if user is admin) -->
        @if(auth()->user()->canManageSystem())
            <x-mary-card title="Admin Actions" shadow separator>
                <div class="flex flex-wrap gap-3">
                    <x-mary-button 
                        label="Edit Group Details" 
                        icon="o-pencil-square"
                        link="/admin/groups/{{ $group->id }}/edit"
                        class="btn-primary"
                    />
                    <x-mary-button 
                        label="Manage Members" 
                        icon="o-users"
                        link="/admin/groups/{{ $group->id }}/members"
                        class="btn-secondary"
                    />
                    <x-mary-button 
                        label="Manage Roles" 
                        icon="o-shield-check"
                        link="/admin/groups/{{ $group->id }}/roles"
                        class="btn-accent"
                    />
                </div>
            </x-mary-card>
        @endif

        <!-- Navigation Actions -->
        <div class="flex justify-between items-center">
            <x-mary-button 
                label="Back to My Groups" 
                icon="o-arrow-left"
                link="/dashboard/my-groups"
                class="btn-outline"
            />
            
            @if(auth()->user()->canManageSystem())
                <x-mary-button 
                    label="All Groups" 
                    icon="o-rectangle-stack"
                    link="/admin/groups"
                    class="btn-outline btn-primary"
                />
            @endif
        </div>
    </div>
</div>
