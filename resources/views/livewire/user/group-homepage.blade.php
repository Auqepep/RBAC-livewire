@php
    use Illuminate\Support\Facades\Gate;
@endphp

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

        <!-- Manager Actions (if user is manager or admin) -->
        @if($canEdit || $canManageMembers)
            <x-mary-card title="Manager Actions" shadow separator class="border-l-4 border-l-warning">
                <div class="mb-4">
                    <x-mary-alert 
                        icon="o-shield-check" 
                        class="alert-info"
                        title="Manager Privileges"
                    >
                        You have manager-level access to this group. You can edit group details and manage members.
                    </x-mary-alert>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @if($canEdit)
                        <x-mary-button 
                            label="Edit Group Details" 
                            icon="o-pencil-square"
                            link="{{ route('users.groups.edit', $group->id) }}"
                            class="btn-warning"
                        />
                    @endif
                    
                    @if($canManageMembers)
                        <x-mary-button 
                            label="Manage Members" 
                            icon="o-users"
                            link="{{ route('users.groups.edit', $group->id) }}"
                            class="btn-secondary"
                        />
                    @endif
                </div>
            </x-mary-card>
        @endif

        <!-- Admin Actions (if user is system admin) -->
        @if(auth()->user()->canManageSystem())
            <x-mary-card title="System Admin Actions" shadow separator class="border-l-4 border-l-error">
                <div class="mb-4">
                    <x-mary-alert 
                        icon="o-shield-exclamation" 
                        class="alert-error"
                        title="System Administrator"
                    >
                        You have full system access. These actions are available to system administrators only.
                    </x-mary-alert>
                </div>

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
                    <x-mary-button 
                        label="Delete Group" 
                        icon="o-trash"
                        class="btn-error"
                        onclick="confirm('Are you sure you want to delete this group?') || event.stopImmediatePropagation()"
                    />
                </div>
            </x-mary-card>
        @endif

        <!-- TEST: This should always show -->
        <div class="bg-red-100 border-2 border-red-500 p-4 text-red-800 font-bold">
            🚨 TEST MARKER - If you see this, the view is rendering correctly. 
            Can manage group? {{ Gate::allows('manage-group', $group) ? 'YES' : 'NO' }}
        </div>

        <!-- Admin/Manager Actions (if user is system admin, group admin, or manager) -->
        @can('manage-group', $group)
            {{-- TEST: Inside @can block --}}
            <div class="bg-green-100 border-2 border-green-500 p-4 text-green-800 font-bold mb-4">
                ✅ INSIDE @can BLOCK - This proves the gate passed!
            </div>
            
            @php
                $userRole = $group->groupMembers()->where('user_id', auth()->id())->first()?->role;
                $isSystemAdmin = auth()->user()->canManageSystem();
                $isGroupAdmin = $userRole?->name === 'admin' || $userRole?->name === 'administrator';
                $isManager = $userRole?->name === 'manager';
                
                if ($isSystemAdmin) {
                    $cardTitle = 'System Admin Actions';
                } elseif ($isGroupAdmin) {
                    $cardTitle = 'Group Admin Actions';
                } else {
                    $cardTitle = 'Manager Actions';
                }
            @endphp
            
            <x-mary-card title="{{ $cardTitle }}" shadow separator>
                <div class="flex flex-wrap gap-3">
                    @can('edit-group-description', $group)
                        <x-mary-button 
                            label="Manage Group" 
                            icon="o-cog-6-tooth"
                            link="/my-groups/{{ $group->id }}/manage"
                            class="btn-primary"
                        />
                    @endcan
                    
                    @if($isSystemAdmin)
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
                    @endif
                </div>
                
                @if(!$isSystemAdmin)
                    <div class="mt-3 p-3 {{ $isGroupAdmin ? 'bg-blue-50' : 'bg-purple-50' }} rounded-lg">
                        <p class="text-sm {{ $isGroupAdmin ? 'text-blue-800' : 'text-purple-800' }}">
                            @if($isGroupAdmin)
                                <strong>Note:</strong> As a group admin, you can edit the group description and manage members within this group only.
                            @else
                                <strong>Note:</strong> As a manager, you can edit the group description and manage members within this group. You cannot manage roles or delete the group.
                            @endif
                        </p>
                    </div>
                @endif
            </x-mary-card>
        @endcan

        <!-- Navigation Actions -->
        <div class="flex flex-wrap justify-between items-center gap-4">
            <x-mary-button 
                label="Back to My Groups" 
                icon="o-arrow-left"
                link="/dashboard/my-groups"
                class="btn-outline"
            />
            
            <div class="flex flex-wrap gap-2">
                <!-- Gateway Access Button -->
                <x-mary-button 
                    label="🚪 Gateway Access" 
                    icon="o-key"
                    link="{{ route('groups.gateway', $group->id) }}"
                    class="btn-accent"
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
</div>
