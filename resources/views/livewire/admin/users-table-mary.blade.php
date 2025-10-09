<div>
    {{-- Search and Filters --}}
    <div class="flex justify-between items-center mb-6">
        <x-mary-input 
            placeholder="Search users..." 
            wire:model.live.debounce="search" 
            clearable 
            icon="o-magnifying-glass" 
            class="w-64" />
            
        <div class="flex gap-2">
            <x-mary-select 
                placeholder="Filter by Group" 
                wire:model.live="selectedGroup" 
                :options="$groups" 
                option-label="name" 
                option-value="id" 
                clearable 
                class="w-48" />
                
            <x-mary-button 
                icon="o-funnel" 
                wire:click="resetFilters" 
                class="btn-ghost btn-sm" 
                tooltip="Reset filters" />
        </div>
    </div>

    {{-- Users Table --}}
    <x-mary-table :headers="$headers" :rows="$users" striped @row-click="$wire.showUser($event.detail.id)">
        
        {{-- Avatar Column --}}
        @scope('cell_avatar', $user)
            <x-mary-avatar :label="substr($user['name'], 0, 1)" class="!w-8 !h-8" />
        @endscope

        {{-- Name Column --}}
        @scope('cell_name', $user)
            <div>
                <div class="font-medium">{{ $user['name'] }}</div>
                <div class="text-sm text-gray-500">{{ $user['email'] }}</div>
            </div>
        @endscope

        {{-- Groups Column --}}
        @scope('cell_groups', $user)
            <div class="flex flex-wrap gap-1">
                @foreach($user['groups'] as $group)
                    <x-mary-badge :value="$group['name']" class="badge-outline badge-sm" />
                @endforeach
                @if(empty($user['groups']))
                    <span class="text-sm text-gray-400">No groups</span>
                @endif
            </div>
        @endscope

        {{-- Roles Column --}}
        @scope('cell_roles', $user)
            <div class="flex flex-wrap gap-1">
                @foreach($user['roles'] as $role)
                    <x-mary-badge 
                        :value="$role['display_name']" 
                        style="background-color: {{ $role['badge_color'] ?? '#6366f1' }}; color: white;" 
                        class="badge-sm" />
                @endforeach
                @if(empty($user['roles']))
                    <span class="text-sm text-gray-400">No roles</span>
                @endif
            </div>
        @endscope

        {{-- Status Column --}}
        @scope('cell_status', $user)
            <x-mary-badge 
                :value="$user['email_verified_at'] ? 'Verified' : 'Unverified'" 
                :class="$user['email_verified_at'] ? 'badge-success' : 'badge-warning'" />
        @endscope

        {{-- Actions Column --}}
        @scope('cell_actions', $user)
            <div class="flex gap-1">
                <x-mary-button 
                    icon="o-eye" 
                    wire:click="showUser({{ $user['id'] }})"
                    spinner 
                    class="btn-ghost btn-sm" 
                    tooltip="View User" />
                    
                <x-mary-button 
                    icon="o-pencil" 
                    link="{{ route('admin.users.edit', $user['id']) }}"
                    class="btn-ghost btn-sm" 
                    tooltip="Edit User" />
                    
                <x-mary-button 
                    icon="o-trash" 
                    wire:click="confirmDelete({{ $user['id'] }})"
                    wire:confirm="Are you sure you want to delete this user?"
                    spinner 
                    class="btn-ghost btn-sm text-error" 
                    tooltip="Delete User" />
            </div>
        @endscope
    </x-mary-table>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $users->links() }}
    </div>

    {{-- User Details Modal --}}
    <x-mary-modal wire:model="showUserModal" title="User Details" class="backdrop-blur">
        @if($selectedUser)
            <div class="space-y-4">
                {{-- User Info --}}
                <div class="flex items-center gap-4">
                    <x-mary-avatar :label="substr($selectedUser['name'], 0, 1)" class="!w-16 !h-16" />
                    <div>
                        <h3 class="text-lg font-semibold">{{ $selectedUser['name'] }}</h3>
                        <p class="text-gray-600">{{ $selectedUser['email'] }}</p>
                        <x-mary-badge 
                            :value="$selectedUser['email_verified_at'] ? 'Email Verified' : 'Email Not Verified'" 
                            :class="$selectedUser['email_verified_at'] ? 'badge-success' : 'badge-warning'" />
                    </div>
                </div>

                {{-- Group Memberships --}}
                <div>
                    <h4 class="font-medium mb-2">Group Memberships</h4>
                    @if(!empty($selectedUser['group_memberships']))
                        <div class="space-y-2">
                            @foreach($selectedUser['group_memberships'] as $membership)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium">{{ $membership['group_name'] }}</div>
                                        <div class="text-sm text-gray-600">
                                            Role: <x-mary-badge :value="$membership['role_name']" class="badge-outline badge-sm" />
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Joined: {{ $membership['joined_at'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No group memberships</p>
                    @endif
                </div>
            </div>

            <x-slot:actions>
                <x-mary-button label="Edit" icon="o-pencil" link="{{ route('admin.users.edit', $selectedUser['id']) }}" class="btn-primary" />
                <x-mary-button label="Close" @click="$wire.showUserModal = false" />
            </x-slot:actions>
        @endif
    </x-mary-modal>
</div>
