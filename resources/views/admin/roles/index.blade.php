<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if(isset($group))
                    {{ __('Roles for Group') }}: {{ $group->name }}
                @else
                    {{ __('All Roles Management') }}
                @endif
            </h2>
            <div class="flex space-x-2">
                @if(isset($group))
                    <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('admin.groups.roles.create', $group) }}">
                        Create Role
                    </x-mary-button>
                    <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.groups.show', $group) }}">
                        Back to Group
                    </x-mary-button>
                @else
                    <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('admin.roles.create') }}">
                        Create Role
                    </x-mary-button>
                    <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.dashboard') }}">
                        Back to Dashboard
                    </x-mary-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(isset($group))
                <!-- Group Info -->
                <x-mary-card>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $group->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $group->description ?? 'No description' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Total Members</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $group->groupMembers->count() }}</div>
                        </div>
                    </div>
                </x-mary-card>

                <!-- Roles Used in This Group -->
                <x-mary-card title="Roles Used in This Group ({{ $groupRoles->count() }})">
            @else
                <!-- General Role Management -->
                <x-mary-card title="All System Roles ({{ $roles->total() }})">
            @endif
                @if(session('message'))
                    <x-mary-alert icon="o-check-circle" class="alert-success mb-4">
                        {{ session('message') }}
                    </x-mary-alert>
                @endif

                @if(session('error'))
                    <x-mary-alert icon="o-x-circle" class="alert-error mb-4">
                        {{ session('error') }}
                    </x-mary-alert>
                @endif

                <!-- Search and Sort Form -->
                <div class="mb-6 space-y-4">
                    <form method="GET" action="{{ isset($group) ? route('groups.roles.index', $group) : route('admin.roles.index') }}" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <x-mary-input 
                                name="search" 
                                value="{{ $search ?? '' }}" 
                                placeholder="Search roles by name or description..."
                                icon="o-magnifying-glass"
                            />
                        </div>
                        
                        <!-- Sort Controls -->
                        <div class="flex gap-2">
                            <x-mary-select 
                                name="sort_by" 
                                :options="[
                                    ['value' => 'name', 'label' => 'Name'],
                                    ['value' => 'hierarchy_level', 'label' => 'Hierarchy Level'],
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
                                    ['value' => 'asc', 'label' => 'A-Z / Lowest / Oldest'],
                                    ['value' => 'desc', 'label' => 'Z-A / Highest / Newest']
                                ]"
                                option-value="value"
                                option-label="label"
                                value="{{ $sortOrder ?? 'asc' }}"
                                placeholder="Order..."
                            />
                        </div>
                        
                        <div class="flex gap-2">
                            <x-mary-button type="submit" class="btn-primary" icon="o-magnifying-glass">
                                Search
                            </x-mary-button>
                            @if(($search ?? '') || ($sortBy ?? 'name') !== 'name' || ($sortOrder ?? 'asc') !== 'asc')
                                <x-mary-button link="{{ route('groups.roles.index', $group) }}" class="btn-secondary" icon="o-x-mark">
                                    Reset
                                </x-mary-button>
                            @endif
                        </div>
                    </form>
                </div>

                @php
                    $rolesToDisplay = isset($group) ? $groupRoles : $roles;
                    $hasRoles = isset($group) ? $groupRoles->count() > 0 : $roles->count() > 0;
                @endphp
                
                @if($hasRoles)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/5">Role</th>
                                    <th class="w-1/3">Description</th>
                                    <th class="w-1/8 text-center">Level</th>
                                    <th class="w-1/8 text-center">Users</th>
                                    <th class="w-1/6">Created</th>
                                    <th class="w-1/6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rolesToDisplay as $role)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $role->display_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $role->name }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ Str::limit($role->description ?? 'No description', 100) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <x-mary-badge value="{{ $role->hierarchy_level }}" class="badge-primary" />
                                        </td>
                                        <td class="text-center">
                                            @if(isset($group))
                                                @php
                                                    $usageCount = $group->groupMembers->where('role_id', $role->id)->count();
                                                @endphp
                                                <x-mary-badge value="{{ $usageCount }}" class="badge-secondary" />
                                            @else
                                                @php
                                                    $totalUsage = \App\Models\GroupMember::where('role_id', $role->id)->count();
                                                @endphp
                                                <x-mary-badge value="{{ $totalUsage }}" class="badge-secondary" />
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-900">{{ $role->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $role->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center space-x-2">
                                                @if(isset($group))
                                                    <x-mary-button icon="o-eye" class="btn-sm btn-ghost" link="{{ route('admin.groups.roles.show', [$group, $role]) }}" />
                                                    <x-mary-button icon="o-pencil" class="btn-sm btn-primary" link="{{ route('admin.groups.roles.edit', [$group, $role]) }}" />
                                                    
                                                    @php
                                                        $usageCount = $group->groupMembers->where('role_id', $role->id)->count();
                                                    @endphp
                                                    @if($usageCount == 0)
                                                        <form method="POST" action="{{ route('admin.groups.roles.destroy', [$group, $role]) }}" class="inline" 
                                                              onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <x-mary-button icon="o-trash" class="btn-sm btn-error" type="submit" />
                                                        </form>
                                                    @endif
                                                @else
                                                    <x-mary-button icon="o-eye" class="btn-sm btn-ghost" link="{{ route('admin.roles.show', $role) }}" />
                                                    <x-mary-button icon="o-pencil" class="btn-sm btn-primary" link="{{ route('admin.roles.edit', $role) }}" />
                                                    
                                                    @php
                                                        $totalUsage = \App\Models\GroupMember::where('role_id', $role->id)->count();
                                                    @endphp
                                                    @if($totalUsage == 0)
                                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" 
                                                              onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <x-mary-button icon="o-trash" class="btn-sm btn-error" type="submit" />
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(!isset($group) && isset($roles))
                        <div class="mt-4">
                            {{ $roles->links() }}
                        </div>
                    @endif
                @else
                    <x-mary-alert icon="o-information-circle" class="alert-info">
                        @if(isset($group))
                            No roles are currently being used in this group.
                        @else
                            No roles have been created yet.
                        @endif
                    </x-mary-alert>
                @endif
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
