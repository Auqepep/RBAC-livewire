<x-admin.layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                {{ __('Users Management') }}
            </h2>
            <x-mary-button icon="o-plus" class="btn-primary btn-sm sm:btn-md" link="{{ route('admin.users.create') }}">
                <span class="hidden sm:inline">Create User</span>
                <span class="sm:hidden">New</span>
            </x-mary-button>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            
            <x-mary-card>
                <!-- Search and Sort Form -->
                <div class="mb-4 sm:mb-6 space-y-3 sm:space-y-4">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 sm:gap-4">
                        <div class="flex-1">
                            <x-mary-input 
                                name="search" 
                                value="{{ $search ?? '' }}" 
                                placeholder="Search users..."
                                icon="o-magnifying-glass"
                            />
                        </div>
                        
                        <!-- Sort Controls -->
                        <div class="grid grid-cols-2 sm:flex gap-2">
                            <x-mary-select 
                                name="sort_by" 
                                :options="[
                                    ['value' => 'name', 'label' => 'Name'],
                                    ['value' => 'email', 'label' => 'Email'],
                                    ['value' => 'created_at', 'label' => 'Date Created']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortBy ?? 'name' }}"
                                placeholder="Sort by..."
                            />
                            
                            <x-mary-select 
                                name="sort_order" 
                                :options="[
                                    ['value' => 'asc', 'label' => 'Ascending'],
                                    ['value' => 'desc', 'label' => 'Descending']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortOrder ?? 'asc' }}"
                                placeholder="Order..."
                            />
                            
                            <x-mary-button type="submit" icon="o-funnel" class="btn-primary col-span-2 sm:col-span-1">
                                Filter
                            </x-mary-button>
                        </div>
                    </form>
                    
                    <!-- Clear filters if any are applied -->
                    @if(($search ?? '') || ($sortBy ?? 'name') !== 'name' || ($sortOrder ?? 'asc') !== 'asc')
                        <div class="flex justify-between items-center">
                            <x-mary-button link="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">
                                Clear all filters
                            </x-mary-button>
                        </div>
                    @endif
                </div>

                @if(session('success'))
                    <x-mary-alert icon="o-check-circle" class="alert-success mb-4">
                        {{ session('success') }}
                    </x-mary-alert>
                @endif

                @if(session('error'))
                    <x-mary-alert icon="o-x-circle" class="alert-error mb-4">
                        {{ session('error') }}
                    </x-mary-alert>
                @endif

                @if($users->count() > 0)
                    <!-- Desktop Table View (hidden on mobile) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/5">User</th>
                                    <th class="w-1/5">Email</th>
                                    <th class="w-1/5">Groups</th>
                                    <th class="w-1/6">Created Date</th>
                                    <th class="w-1/8 text-center">Status</th>
                                    <th class="w-1/8 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-gray-900">
                                                {{ $user->name }}
                                                @if($user->id === Auth::id())
                                                    <span class="text-blue-600 text-sm font-normal">({{ __('You') }})</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ $user->email }}</div>
                                        </td>
                                        <td>
                                            @if($user->groupMembers->count() > 0)
                                                <button 
                                                    onclick="showGroupsModal{{ $user->id }}.showModal()" 
                                                    class="flex flex-wrap gap-1 items-center hover:opacity-75 transition-opacity"
                                                >
                                                    @foreach($user->groupMembers->take(2) as $membership)
                                                        <x-mary-badge 
                                                            value="{{ $membership->group->name }}" 
                                                            class="badge-primary badge-xs sm:badge-sm" 
                                                        />
                                                    @endforeach
                                                    @if($user->groupMembers->count() > 2)
                                                        <x-mary-badge 
                                                            value="+{{ $user->groupMembers->count() - 2 }} more" 
                                                            class="badge-neutral badge-sm cursor-pointer" 
                                                        />
                                                    @endif
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-sm">No groups</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-600">{{ $user->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="space-y-1">
                                                @if($user->email_verified_at)
                                                    <x-mary-badge value="Verified" class="badge-success badge-xs sm:badge-sm" />
                                                @else
                                                    <x-mary-badge value="Unverified" class="badge-error badge-xs sm:badge-sm" />
                                                @endif
                                                
                                                @if($user->isSuperAdmin())
                                                    <x-mary-badge value="Super Admin" class="badge-error badge-xs sm:badge-sm" />
                                                @elseif($user->canManageSystem())
                                                    <x-mary-badge value="Admin" class="badge-warning badge-xs sm:badge-sm" />
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center space-x-1 flex-wrap gap-1">
                                                <x-mary-button icon="o-eye" class="btn-sm btn-ghost" link="{{ route('admin.users.show', $user) }}" />
                                                <x-mary-button icon="o-pencil" class="btn-sm btn-primary" link="{{ route('admin.users.edit', $user) }}" />
                                                
                                                <!-- Quick Admin Toggle - Hidden for Super Admins -->
                                                @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                                    @php
                                                        $isAdmin = $user->canManageSystem();
                                                    @endphp
                                                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline"
                                                          onsubmit="return confirm('Are you sure you want to {{ $isAdmin ? 'remove admin privileges from' : 'grant admin privileges to' }} {{ $user->name }}?')">
                                                        @csrf
                                                        <x-mary-button 
                                                            icon="{{ $isAdmin ? 'o-shield-exclamation' : 'o-shield-check' }}" 
                                                            class="btn-sm {{ $isAdmin ? 'btn-warning' : 'btn-success' }}" 
                                                            type="submit"
                                                            title="{{ $isAdmin ? 'Remove Admin' : 'Make Admin' }}"
                                                        />
                                                    </form>
                                                @endif
                                                
                                                @if($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-mary-button icon="o-trash" class="btn-sm btn-error" type="submit" />
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View (shown on mobile and tablet) -->
                    <div class="lg:hidden space-y-4">
                        @foreach($users as $user)
                            <div class="card bg-base-100 shadow-sm border">
                                <div class="card-body p-4">
                                    <div class="flex justify-between items-start gap-2">
                                        <div class="min-w-0 flex-1">
                                            <h3 class="font-semibold text-gray-900 truncate">
                                                {{ $user->name }}
                                                @if($user->id === Auth::id())
                                                    <span class="text-blue-600 text-sm font-normal">({{ __('You') }})</span>
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                        </div>
                                        <div class="flex flex-col items-end gap-1 shrink-0">
                                            @if($user->email_verified_at)
                                                <x-mary-badge value="Verified" class="badge-success badge-xs" />
                                            @else
                                                <x-mary-badge value="Unverified" class="badge-error badge-xs" />
                                            @endif
                                            @if($user->isSuperAdmin())
                                                <x-mary-badge value="Super Admin" class="badge-error badge-xs" />
                                            @elseif($user->canManageSystem())
                                                <x-mary-badge value="Admin" class="badge-warning badge-xs" />
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Groups -->
                                    <div class="mt-2">
                                        @if($user->groupMembers->count() > 0)
                                            <button 
                                                onclick="showGroupsModal{{ $user->id }}.showModal()" 
                                                class="flex flex-wrap gap-1 items-center hover:opacity-75 transition-opacity"
                                            >
                                                @foreach($user->groupMembers->take(2) as $membership)
                                                    <x-mary-badge 
                                                        value="{{ $membership->group->name }}" 
                                                        class="badge-primary badge-xs" 
                                                    />
                                                @endforeach
                                                @if($user->groupMembers->count() > 2)
                                                    <x-mary-badge 
                                                        value="+{{ $user->groupMembers->count() - 2 }}" 
                                                        class="badge-neutral badge-xs cursor-pointer" 
                                                    />
                                                @endif
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs">No groups</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t">
                                        <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                                        <div class="flex gap-1">
                                            <x-mary-button icon="o-eye" class="btn-xs btn-ghost" link="{{ route('admin.users.show', $user) }}" />
                                            <x-mary-button icon="o-pencil" class="btn-xs btn-primary" link="{{ route('admin.users.edit', $user) }}" />
                                            @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                                @php $isAdmin = $user->canManageSystem(); @endphp
                                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline"
                                                      onsubmit="return confirm('{{ $isAdmin ? 'Remove admin?' : 'Make admin?' }}')">
                                                    @csrf
                                                    <x-mary-button 
                                                        icon="{{ $isAdmin ? 'o-shield-exclamation' : 'o-shield-check' }}" 
                                                        class="btn-xs {{ $isAdmin ? 'btn-warning' : 'btn-success' }}" 
                                                        type="submit"
                                                    />
                                                </form>
                                            @endif
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" 
                                                      onsubmit="return confirm('Delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-mary-button icon="o-trash" class="btn-xs btn-error" type="submit" />
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Modals for all users - outside the table -->
                    @foreach($users as $user)
                        <dialog id="showGroupsModal{{ $user->id }}" class="modal">
                            <div class="modal-box w-11/12 max-w-2xl max-h-[85vh] flex flex-col p-4">
                                <form method="dialog">
                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                </form>
                                <h3 class="font-bold text-sm sm:text-base mb-3">
                                    {{ $user->name }}@if($user->id === Auth::id()) <span class="text-blue-600 text-sm font-normal">({{ __('You') }})</span>@endif's Groups & Roles
                                </h3>
                                
                                @if($user->groupMembers->count() > 0)
                                    <div class="overflow-x-auto overflow-y-auto flex-1">
                                        <table class="table table-zebra w-full text-xs sm:text-sm">
                                            <thead>
                                                <tr class="text-xs">
                                                    <th class="py-2">Group</th>
                                                    <th class="py-2">Role</th>
                                                    <th class="py-2 hidden sm:table-cell">Joined</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->groupMembers as $membership)
                                                    <tr>
                                                        <td class="py-2">
                                                            <div class="font-medium text-xs sm:text-sm">{{ $membership->group->name }}</div>
                                                            @if($membership->group->description)
                                                                <div class="text-xs text-gray-500 hidden sm:block line-clamp-1">{{ Str::limit($membership->group->description, 40) }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="py-2">
                                                            <x-mary-badge 
                                                                value="{{ $membership->role->display_name ?? $membership->role->name }}" 
                                                                class="badge-xs"
                                                                style="background-color: {{ $membership->role->badge_color ?? '#6b7280' }}; color: white;"
                                                            />
                                                        </td>
                                                        <td class="py-2 hidden sm:table-cell">
                                                            <div class="text-xs">{{ $membership->joined_at ? $membership->joined_at->format('M j, Y') : 'N/A' }}</div>
                                                            <div class="text-xs text-gray-400">{{ $membership->joined_at ? $membership->joined_at->diffForHumans() : '' }}</div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-6">
                                        <x-mary-icon name="o-user-group" class="w-10 h-10 mx-auto mb-2 text-gray-400" />
                                        <p class="text-sm text-gray-500">This user is not a member of any groups.</p>
                                    </div>
                                @endif
                                
                                <div class="modal-action mt-3 pt-3 border-t">
                                    <form method="dialog">
                                        <button class="btn btn-sm">Close</button>
                                    </form>
                                </div>
                            </div>
                            <form method="dialog" class="modal-backdrop">
                                <button>close</button>
                            </form>
                        </dialog>
                    @endforeach

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-mary-icon name="o-user-group" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Users Found</h3>
                        @if(($search ?? ''))
                            <p class="text-gray-500 mb-4">No users match your search criteria for "{{ $search }}".</p>
                            <div class="space-x-3">
                                <x-mary-button link="{{ route('admin.users.index') }}" class="btn-primary">
                                    View All Users
                                </x-mary-button>
                                <x-mary-button link="{{ route('admin.users.create') }}" class="btn-secondary">
                                    Create New User
                                </x-mary-button>
                            </div>
                        @else
                            <p class="text-gray-500 mb-4">There are no users to display.</p>
                            <x-mary-button link="{{ route('admin.users.create') }}" class="btn-primary">
                                Create First User
                            </x-mary-button>
                        @endif
                    </div>
                @endif
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
