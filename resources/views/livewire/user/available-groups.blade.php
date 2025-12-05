<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Search -->
        <x-mary-card>
            <x-mary-input 
                wire:model.live="search" 
                placeholder="Search available groups..." 
                icon="o-magnifying-glass"
                clearable
            />
        </x-mary-card>

        <!-- Groups Grid -->
        <x-mary-card title="Available Groups ({{ $groups->total() }})">
            @if($groups->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($groups as $group)
                        <div wire:key="group-{{ $group->id }}" class="border rounded-lg p-6 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ $group->name }}
                                    </h4>
                                    @if($group->description)
                                        <p class="text-sm text-gray-600 mb-3">
                                            {{ Str::limit($group->description, 100) }}
                                        </p>
                                    @endif
                                </div>
                                <x-mary-badge value="Active" class="badge-success" />
                            </div>

                            <!-- Group Stats -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Members</span>
                                    <span class="font-medium text-gray-900">{{ $group->members_count }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Created</span>
                                    <span class="font-medium text-gray-900">{{ $group->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="mt-4">
                                @if(in_array($group->id, $userPendingRequests))
                                    <x-mary-button disabled class="btn-warning w-full" icon="o-clock">
                                        Request Pending
                                    </x-mary-button>
                                @else
                                    <x-mary-button 
                                        wire:click="openRequestModal({{ $group->id }})" 
                                        class="btn-primary w-full"
                                        icon="o-plus"
                                    >
                                        Request to Join
                                    </x-mary-button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($groups->hasPages())
                    <div class="mt-6">
                        {{ $groups->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <x-mary-icon name="o-user-group" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                    @if($search)
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No groups found</h3>
                        <p class="text-gray-500 mb-4">No groups match your search for "{{ $search }}"</p>
                        <x-mary-button wire:click="$set('search', '')" class="btn-ghost">
                            Clear search
                        </x-mary-button>
                    @else
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No available groups</h3>
                        <p class="text-gray-500">There are no groups you can join at this time.</p>
                    @endif
                </div>
            @endif
        </x-mary-card>
    </div>

    <!-- Request Modal -->
    <x-mary-modal wire:model="showRequestModal" title="Request to Join Group">
        @if($selectedGroupId)
            @php
                $selectedGroup = \App\Models\Group::find($selectedGroupId);
            @endphp
            
            @if($selectedGroup)
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $selectedGroup->name }}</h4>
                        @if($selectedGroup->description)
                            <p class="text-sm text-gray-600 mt-1">{{ $selectedGroup->description }}</p>
                        @endif
                    </div>

                    <x-mary-textarea 
                        wire:model="requestMessage" 
                        label="Message (optional)" 
                        placeholder="Add a message to your join request..."
                        rows="3"
                        hint="This message will be sent to group administrators"
                    />

                    <div class="flex justify-end space-x-3">
                        <x-mary-button wire:click="closeRequestModal" class="btn-ghost">
                            Cancel
                        </x-mary-button>
                        <x-mary-button wire:click="requestToJoin" class="btn-primary" spinner="requestToJoin">
                            Send Request
                        </x-mary-button>
                    </div>
                </div>
            @endif
        @endif
    </x-mary-modal>
</div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $groups->links(); }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Available Groups</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if($search)
                        No groups found matching your search.
                    @else
                        You are already a member of all available groups.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Request Modal -->
    @if($showRequestModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Request to Join Group</h3>
                    
                    <div class="mb-4">
                        <label for="requestMessage" class="block text-sm font-medium text-gray-700 mb-2">
                            Message (Optional)
                        </label>
                        <textarea wire:model="requestMessage" id="requestMessage" rows="3"
                                  placeholder="Tell the administrators why you'd like to join this group..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('requestMessage') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button wire:click="closeRequestModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="requestToJoin" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                            Send Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
