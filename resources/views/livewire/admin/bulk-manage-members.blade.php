<div class="space-y-6">
    <!-- Header with bulk actions -->
    <x-mary-card title="Bulk Manage Members - {{ $group->name }}">
        <div class="space-y-4">
            <!-- Search -->
            <x-mary-input 
                label="Search Members" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search by name or email..."
                icon="o-magnifying-glass"
            />

            <!-- Bulk Actions Controls -->
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-48">
                    <x-mary-select 
                        label="Bulk Action" 
                        wire:model="bulkAction" 
                        :options="[
                            ['id' => 'remove', 'name' => 'Remove from Group'],
                            ['id' => 'change_role', 'name' => 'Change Role']
                        ]"
                        option-value="id"
                        option-label="name"
                        placeholder="Select action..."
                    />
                </div>

                @if($bulkAction === 'change_role')
                    <div class="flex-1 min-w-48">
                        <x-mary-select 
                            label="New Role" 
                            wire:model="newRoleId" 
                            :options="$groupRoles"
                            option-value="id"
                            option-label="display_name"
                            placeholder="Select role..."
                        />
                    </div>
                @endif

                <div class="flex gap-2">
                    <x-mary-button 
                        wire:click="selectAll" 
                        class="btn-secondary"
                        icon="o-check"
                    >
                        Select All
                    </x-mary-button>
                    
                    <x-mary-button 
                        wire:click="selectNone" 
                        class="btn-ghost"
                        icon="o-x-mark"
                    >
                        Clear
                    </x-mary-button>
                    
                    <x-mary-button 
                        wire:click="confirmBulkAction" 
                        class="btn-primary"
                        :disabled="empty($selectedMembers) || empty($bulkAction)"
                        icon="o-cog"
                    >
                        Execute
                    </x-mary-button>
                </div>
            </div>

            <!-- Selected count -->
            @if(count($selectedMembers) > 0)
                <x-mary-alert title="Selected" description="{{ count($selectedMembers) }} member(s) selected" icon="o-information-circle" class="alert-info" />
            @endif
        </div>
    </x-mary-card>

    <!-- Members List -->
    <x-mary-card title="Members ({{ $members->total() }})">
        @if($members->count() > 0)
            <div class="space-y-3">
                @foreach($members as $member)
                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                        <div class="flex items-center space-x-4">
                            <x-mary-checkbox 
                                wire:model.live="selectedMembers" 
                                value="{{ $member->id }}"
                            />
                            
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h4 class="font-medium text-gray-900">{{ $member->user->name }}</h4>
                                    <x-mary-badge 
                                        value="{{ $member->role->display_name }}" 
                                        class="badge-{{ $member->role->getBadgeColor() }}"
                                    />
                                </div>
                                <p class="text-sm text-gray-600">{{ $member->user->email }}</p>
                                <p class="text-xs text-gray-500">
                                    Joined: {{ $member->joined_at?->format('M d, Y') ?? $member->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            @if($member->user_id !== auth()->id())
                                <x-mary-button 
                                    icon="o-trash" 
                                    class="btn-error btn-sm"
                                    wire:click="$dispatch('remove-member', { id: {{ $member->id }} })"
                                    title="Remove from group"
                                />
                            @else
                                <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">You</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $members->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-500">
                    @if($search)
                        <p>No members found matching "{{ $search }}"</p>
                        <x-mary-button wire:click="$set('search', '')" class="btn-ghost btn-sm mt-2">
                            Clear search
                        </x-mary-button>
                    @else
                        <p>No members in this group yet.</p>
                    @endif
                </div>
            </div>
        @endif
    </x-mary-card>

    <!-- Confirmation Modal -->
    <x-mary-modal wire:model="showConfirmModal" title="Confirm Action">
        <div class="space-y-4">
            <p>{{ $confirmationMessage }}</p>
            
            <div class="flex justify-end space-x-3">
                <x-mary-button wire:click="cancelBulkAction" class="btn-ghost">
                    Cancel
                </x-mary-button>
                <x-mary-button wire:click="executeBulkAction" class="btn-primary" spinner="executeBulkAction">
                    Confirm
                </x-mary-button>
            </div>
        </div>
    </x-mary-modal>
</div>
