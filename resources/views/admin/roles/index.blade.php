<x-admin.layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                @if(isset($group))
                    Roles for: {{ Str::limit($group->name, 20) }}
                @else
                    All Roles
                @endif
            </h2>
            <div class="flex space-x-2">
                <a href="{{ isset($group) ? route('admin.groups.show', $group) : route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <span class="hidden sm:inline">{{ isset($group) ? 'Back to Group' : 'Back to Dashboard' }}</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            
            @if(isset($group))
                <!-- Group Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base sm:text-lg font-medium text-gray-900">{{ $group->name }}</h3>
                            <p class="text-sm text-gray-600">{{ Str::limit($group->description ?? 'No description', 80) }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <div class="text-sm text-gray-500">Total Members</div>
                            <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ $group->groupMembers->count() }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Roles Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold mb-4">
                        @if(isset($group))
                            Roles in This Group ({{ $groupRoles->count() }})
                        @else
                            All System Roles ({{ isset($roles) ? $roles->total() : 0 }})
                        @endif
                    </h3>

                    @if(isset($group))
                        <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-3 sm:px-4 py-3 rounded text-sm">
                            <p><strong>Note:</strong> Each group has two roles: <strong>Manager</strong> and <strong>Staff</strong>.</p>
                        </div>
                    @endif

                    @if(session('message'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search Form -->
                    <div class="mb-4 sm:mb-6">
                        <form method="GET" action="{{ isset($group) ? route('admin.groups.roles.index', $group) : route('admin.roles.index') }}" class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                            <div class="flex-1">
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search roles..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2 sm:flex sm:gap-2">
                                <select name="sort_by" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="name" {{ ($sortBy ?? 'name') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="hierarchy_level" {{ ($sortBy ?? '') == 'hierarchy_level' ? 'selected' : '' }}>Level</option>
                                    <option value="created_at" {{ ($sortBy ?? '') == 'created_at' ? 'selected' : '' }}>Date</option>
                                </select>
                                <select name="sort_order" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="asc" {{ ($sortOrder ?? 'asc') == 'asc' ? 'selected' : '' }}>Asc</option>
                                    <option value="desc" {{ ($sortOrder ?? '') == 'desc' ? 'selected' : '' }}>Desc</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                    Search
                                </button>
                                @if(($search ?? '') || ($sortBy ?? 'name') !== 'name' || ($sortOrder ?? 'asc') !== 'asc')
                                    <a href="{{ isset($group) ? route('admin.groups.roles.index', $group) : route('admin.roles.index') }}" class="flex-1 sm:flex-none px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 text-center">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    @php
                        $rolesToDisplay = isset($group) ? $groupRoles : $roles;
                        $hasRoles = isset($group) ? $groupRoles->count() > 0 : $roles->count() > 0;
                    @endphp
                    
                    @if($hasRoles)
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rolesToDisplay as $role)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ $role->display_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $role->name }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ Str::limit($role->description ?? 'No description', 100) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $role->hierarchy_level }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if(isset($group))
                                                    @php
                                                        $usageCount = $group->groupMembers->where('role_id', $role->id)->count();
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ $usageCount }}
                                                    </span>
                                                @else
                                                    @php
                                                        $totalUsage = \App\Models\GroupMember::where('role_id', $role->id)->count();
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ $totalUsage }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $role->created_at->format('M j, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $role->created_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ isset($group) ? route('admin.groups.roles.show', [$group, $role]) : route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-3">
                            @foreach($rolesToDisplay as $role)
                                <div class="bg-gray-50 rounded-lg p-4 border">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $role->display_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $role->name }}</div>
                                        </div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Level {{ $role->hierarchy_level }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($role->description ?? 'No description', 60) }}</p>
                                    <div class="flex justify-between items-center pt-2 border-t">
                                        <div class="flex items-center gap-3 text-xs text-gray-500">
                                            <span>
                                                @if(isset($group))
                                                    {{ $group->groupMembers->where('role_id', $role->id)->count() }} users
                                                @else
                                                    {{ \App\Models\GroupMember::where('role_id', $role->id)->count() }} users
                                                @endif
                                            </span>
                                            <span>{{ $role->created_at->diffForHumans() }}</span>
                                        </div>
                                        <a href="{{ isset($group) ? route('admin.groups.roles.show', [$group, $role]) : route('admin.roles.show', $role) }}" class="text-sm text-blue-600 hover:text-blue-900">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if(!isset($group) && isset($roles))
                            <div class="mt-4">
                                {{ $roles->links() }}
                            </div>
                        @endif
                    @else
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded text-sm">
                            @if(isset($group))
                                No roles are currently being used in this group.
                            @else
                                No roles have been created yet.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>
