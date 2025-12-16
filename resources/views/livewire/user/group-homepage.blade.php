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
        @php
            $userMembership = $group->groupMembers()->where('user_id', auth()->id())->first();
            $userRole = $userMembership?->role;
            $isSystemAdmin = auth()->user()->canManageSystem();
            $isGroupAdmin = $userRole?->name === 'admin';
            $isManager = $userRole?->name === 'manager';
            $canManage = $isSystemAdmin || $isGroupAdmin || $isManager;
        @endphp

        <!-- Manager/Admin Actions -->
        @if($canManage)
            <x-mary-card 
                title="{{ $isSystemAdmin ? 'System Admin Actions' : ($isGroupAdmin ? 'Group Admin Actions' : 'Manager Actions') }}" 
                shadow 
                separator 
                class="border-l-4 {{ $isSystemAdmin ? 'border-l-error' : ($isGroupAdmin ? 'border-l-warning' : 'border-l-info') }}"
            >
                <div class="mb-4">
                    <x-mary-alert 
                        icon="o-shield-check" 
                        class="{{ $isSystemAdmin ? 'alert-error' : 'alert-info' }}"
                        title="{{ $isSystemAdmin ? 'System Administrator' : ($isGroupAdmin ? 'Group Administrator' : 'Manager') }}"
                    >
                        @if($isSystemAdmin)
                            You have full system access. These actions are available to system administrators only.
                        @elseif($isGroupAdmin)
                            You have group administrator access. You can manage all aspects of this group.
                        @else
                            You have manager access. You can edit group details and manage members within this group.
                        @endif
                    </x-mary-alert>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @if($isSystemAdmin)
                        <x-mary-button 
                            label="Edit Group Details" 
                            icon="o-pencil-square"
                            link="/admin/groups/{{ $group->id }}/edit"
                            class="btn-primary"
                            tooltip="Edit group details and manage members"
                        />
                        <x-mary-button 
                            label="Manage Roles" 
                            icon="o-shield-check"
                            link="/admin/groups/{{ $group->id }}/roles"
                            class="btn-accent"
                        />
                        
                        @if($group->groupMembers()->count() > 0)
                            <x-mary-button 
                                label="{{ __('Hapus Grup') }}" 
                                icon="o-trash"
                                class="btn-error"
                                disabled
                                tooltip="{{ __('Cannot delete group that has members. Remove all members first.') }}"
                            />
                        @else
                            <form method="POST" action="{{ route('admin.groups.destroy', $group->id) }}" class="inline" 
                                onsubmit="return confirm('{{ __('Are you sure you want to delete this group? This action cannot be undone.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-error">
                                    <x-mary-icon name="o-trash" class="w-5 h-5" />
                                    {{ __('Hapus Grup') }}
                                </button>
                            </form>
                        @endif
                    @else
                        <x-mary-button 
                            label="Edit Group" 
                            icon="o-pencil-square"
                            link="{{ route('groups.edit', $group->id) }}"
                            class="btn-warning"
                            tooltip="Edit group details and manage members"
                        />
                    @endif
                </div>
            </x-mary-card>
        @endif

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
        <x-mary-card title="{{ __('Group Members') }} ({{ $group->groupMembers->count() }})" shadow separator>
            @if($group->groupMembers->count() > 0)
                <!-- Members List -->
                <div class="space-y-3">
                    @foreach($group->groupMembers as $membership)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($membership->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium member-name">{{ $membership->user->name }}</p>
                                    <p class="text-sm text-gray-600 member-email">{{ $membership->user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($membership->role)
                                    @php
                                        $badgeClasses = [
                                            'admin' => 'badge-error',
                                            'manager' => 'badge-warning', 
                                            'staff' => 'badge-success',
                                            'member' => 'badge-info'
                                        ];
                                        $roleClass = $badgeClasses[strtolower($membership->role->name)] ?? 'badge-neutral';
                                    @endphp
                                    <x-mary-badge 
                                        value="{{ $membership->role->name }}" 
                                        class="{{ $roleClass }}"
                                    />
                                @endif
                                <p class="text-xs text-gray-500 member-date">
                                    {{ __('Joined') }} {{ $membership->created_at->format('M j, Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <x-mary-icon name="o-users" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-500">{{ __('No members in this group yet.') }}</p>
                </div>
            @endif
        </x-mary-card>

        <!-- Navigation Actions -->
        <div class="flex flex-wrap justify-between items-center gap-4">
            <x-mary-button 
                label="Back to My Groups" 
                icon="o-arrow-left"
                link="{{ route('groups.index') }}"
                class="btn-outline"
            />
            
            <div class="flex flex-wrap gap-2">
                <x-mary-button 
                    label="Gateway Access" 
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
