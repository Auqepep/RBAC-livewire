<div class="space-y-6">
    <!-- Debug Info (Remove after testing) -->
    <div class="bg-yellow-50 border border-yellow-200 p-4 text-sm space-y-2">
        <div><strong>Debug:</strong> showEditUserModal = {{ $showEditUserModal ? 'true' : 'false' }}</div>
        <div>editUserId = {{ $editUserId ?? 'null' }}</div>
        <div>editUserName = {{ $editUserName ?? 'empty' }}</div>
        <button 
            wire:click="openEditUserModal(1)" 
            class="px-3 py-1 bg-blue-500 text-white rounded text-xs"
        >
            Test Modal (User ID 1)
        </button>
    </div>

    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $group->name }} - Members</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $group->description }}</p>
            </div>
            @if(auth()->user()->canAssignRolesInGroup($group->id))
                <button 
                    wire:click="openAddMemberModal"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full sm:w-auto"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Members
                </button>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Search -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <div class="w-full sm:max-w-md">
            <label for="search" class="block text-sm font-medium text-gray-700">Search Members</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    id="search"
                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 text-sm sm:text-sm border-gray-300 rounded-md" 
                    placeholder="Search by name or email..."
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Members List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-base sm:text-lg font-medium text-gray-900">Group Members ({{ $members->total() }})</h3>
        </div>
        
        @if($members->count() > 0)
            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned By</th>
                            @if(auth()->user()->canAssignRolesInGroup($group->id))
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($members as $member)
                            <tr class="hover:bg-gray-50" wire:key="member-desktop-{{ $member->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($member->user->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $member->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $member->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($member->role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                              style="background-color: {{ $member->role->badge_color ?? '#6b7280' }}; color: white;">
                                            {{ $member->role->display_name }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Level {{ $member->role->hierarchy_level }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">No role assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $member->joined_at ? $member->joined_at->format('M j, Y') : 'N/A' }}
                                    @if($member->joined_at)
                                        <div class="text-xs text-gray-500">
                                            {{ $member->joined_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $member->assignedBy->name ?? 'System' }}
                                </td>
                                @if(auth()->user()->canAssignRolesInGroup($group->id))
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button 
                                                wire:click="openEditUserModal({{ $member->user_id }})"
                                                type="button"
                                                class="text-green-600 hover:text-green-900"
                                                title="Edit User"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @if($member->user_id !== auth()->id())
                                                <button 
                                                    wire:click="removeMember({{ $member->id }})"
                                                    wire:confirm="Are you sure you want to remove {{ $member->user->name }} from this group?"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Remove Member"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (visible on mobile and tablet) -->
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach($members as $member)
                    <div class="p-4 hover:bg-gray-50" wire:key="member-mobile-{{ $member->id }}">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ substr($member->user->name, 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $member->user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $member->user->email }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    @if($member->role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" 
                                              style="background-color: {{ $member->role->badge_color ?? '#6b7280' }}; color: white;">
                                            {{ $member->role->display_name }} (Lvl {{ $member->role->hierarchy_level }})
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">No role</span>
                                    @endif
                                </div>
                                
                                <div class="mt-2 text-xs text-gray-500 space-y-1">
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Joined: {{ $member->joined_at ? $member->joined_at->format('M j, Y') : 'N/A' }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Added by: {{ $member->assignedBy->name ?? 'System' }}
                                    </div>
                                </div>

                                @if(auth()->user()->canAssignRolesInGroup($group->id))
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button 
                                            wire:click="openEditUserModal({{ $member->user_id }})"
                                            type="button"
                                            class="flex-1 min-w-[120px] inline-flex items-center justify-center px-3 py-1.5 border border-green-300 text-xs font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit User
                                        </button>
                                        @if($member->user_id !== auth()->id())
                                            <button 
                                                wire:click="removeMember({{ $member->id }})"
                                                wire:confirm="Are you sure you want to remove {{ $member->user->name }} from this group?"
                                                class="flex-1 min-w-[120px] inline-flex items-center justify-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            >
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                {{ $members->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No members found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search)
                        No members match your search criteria.
                    @else
                        This group doesn't have any members yet.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Add Members Modal -->
    @if($showAddMemberModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit="addMember">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Add Members to {{ $group->name }}
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label for="role" class="block text-sm font-medium text-gray-700">
                                                Role <span class="text-red-500">*</span>
                                            </label>
                                            <select 
                                                wire:model="selectedRoleId" 
                                                id="role" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            >
                                                <option value="">Select a role...</option>
                                                @foreach($availableRoles as $role)
                                                    <option value="{{ $role->id }}">
                                                        {{ $role->display_name }} (Level {{ $role->hierarchy_level }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('selectedRoleId') 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Select Users <span class="text-red-500">*</span>
                                                @if(count($selectedUserIds) > 0)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ count($selectedUserIds) }} selected
                                                    </span>
                                                @endif
                                            </label>
                                            
                                            @if($availableUsers->count() > 0)
                                                <div class="mt-1 border border-gray-300 rounded-md max-h-64 overflow-y-auto">
                                                    @foreach($availableUsers as $user)
                                                        <label class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0" wire:key="user-{{ $user->id }}">
                                                            <input 
                                                                type="checkbox" 
                                                                wire:model.live="selectedUserIds" 
                                                                value="{{ $user->id }}"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                            >
                                                            <div class="ml-3 flex-1">
                                                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="mt-1 text-center py-8 border border-gray-200 rounded-md bg-gray-50">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                    <p class="mt-2 text-sm text-gray-500">All users are already members of this group.</p>
                                                </div>
                                            @endif
                                            
                                            @error('selectedUserIds') 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(count($selectedUserIds) === 0) disabled @endif
                            >
                                @if(count($selectedUserIds) === 0)
                                    Select Users to Add
                                @elseif(count($selectedUserIds) === 1)
                                    Add 1 Member
                                @else
                                    Add {{ count($selectedUserIds) }} Members
                                @endif
                            </button>
                            <button 
                                type="button" 
                                wire:click="closeModals" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditUserModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="updateUser">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Edit User Information
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label for="editName" class="block text-sm font-medium text-gray-700">
                                                Name <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                wire:model="editUserName" 
                                                type="text" 
                                                id="editName"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                                placeholder="Enter user name"
                                            >
                                            @error('editUserName') 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="editEmail" class="block text-sm font-medium text-gray-700">
                                                Email <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                wire:model="editUserEmail" 
                                                type="email" 
                                                id="editEmail"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                                placeholder="Enter email address"
                                            >
                                            @error('editUserEmail') 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Update User
                            </button>
                            <button 
                                type="button" 
                                wire:click="closeModals" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
