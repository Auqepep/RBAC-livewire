<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users Management') }}
            </h2>
            <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('admin.users.create') }}">
                Create User
            </x-mary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-mary-card>
                <!-- Search and Sort Form -->
                <div class="mb-6 space-y-4">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <x-mary-input 
                                name="search" 
                                value="{{ $search ?? '' }}" 
                                placeholder="Search users by name or email..."
                                icon="o-magnifying-glass"
                            />
                        </div>
                        
                        <!-- Sort Controls -->
                        <div class="flex gap-2">
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
                                    ['value' => 'asc', 'label' => 'A-Z / Oldest'],
                                    ['value' => 'desc', 'label' => 'Z-A / Newest']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortOrder ?? 'asc' }}"
                                placeholder="Order..."
                            />
                            
                            <x-mary-button type="submit" icon="o-funnel" class="btn-primary">
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
                    <div class="overflow-x-auto">
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
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
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
                                                            class="badge-primary badge-sm" 
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
                                                    <x-mary-badge value="Verified" class="badge-success badge-sm" />
                                                @else
                                                    <x-mary-badge value="Unverified" class="badge-error badge-sm" />
                                                @endif
                                                
                                                @if($user->isSuperAdmin())
                                                    <x-mary-badge value="Super Admin" class="badge-error badge-sm" />
                                                @elseif($user->canManageSystem())
                                                    <x-mary-badge value="Admin" class="badge-warning badge-sm" />
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
                    
                    <!-- Modals for all users - outside the table -->
                    @foreach($users as $user)
                        <dialog id="showGroupsModal{{ $user->id }}" class="modal">
                            <div class="modal-box max-w-2xl">
                                <form method="dialog">
                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                </form>
                                <h3 class="font-bold text-lg mb-4">{{ $user->name }}'s Groups & Roles</h3>
                                
                                @if($user->groupMembers->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="table table-zebra w-full">
                                            <thead>
                                                <tr>
                                                    <th>Group</th>
                                                    <th>Role</th>
                                                    <th>Joined</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->groupMembers as $membership)
                                                    <tr>
                                                        <td>
                                                            <div class="font-medium">{{ $membership->group->name }}</div>
                                                            @if($membership->group->description)
                                                                <div class="text-xs text-gray-500">{{ Str::limit($membership->group->description, 50) }}</div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <x-mary-badge 
                                                                value="{{ $membership->role->display_name ?? $membership->role->name }}" 
                                                                class="badge-sm"
                                                                style="background-color: {{ $membership->role->badge_color ?? '#6b7280' }}; color: white;"
                                                            />
                                                        </td>
                                                        <td>
                                                            <div class="text-sm">{{ $membership->joined_at ? $membership->joined_at->format('M j, Y') : 'N/A' }}</div>
                                                            <div class="text-xs text-gray-400">{{ $membership->joined_at ? $membership->joined_at->diffForHumans() : '' }}</div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <x-mary-icon name="o-user-group" class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                        <p class="text-gray-500">This user is not a member of any groups.</p>
                                    </div>
                                @endif
                                
                                <div class="modal-action">
                                    <form method="dialog">
                                        <button class="btn">Close</button>
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
