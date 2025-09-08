<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('debug'))
        <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-md">
            DEBUG: {{ session('debug') }}
        </div>
    @endif

    <!-- Search -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-4">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="search" class="sr-only">Search groups</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input wire:model.live="search" id="search" type="text" 
                               placeholder="Search available groups..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                @if($search)
                    <button wire:click="$set('search', '')" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Groups Grid -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        @if($groups->count() > 0)
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($groups as $group)
                        <div wire:key="group-{{ $group->id }}" class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-shadow">
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
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
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
                                    <button disabled class="w-full bg-yellow-100 text-yellow-800 py-2 px-4 rounded-md text-sm font-medium">
                                        Request Pending
                                    </button>
                                @else
                                    <button wire:click="openRequestModal({{ $group->id }})" 
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md text-sm font-medium transition-colors">
                                        Request to Join
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $groups->links() }}
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
