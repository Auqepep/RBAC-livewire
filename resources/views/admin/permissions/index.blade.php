<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permissions Management') }}
            </h2>
            <div class="flex space-x-2">
                @can('manage-permissions')
                <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('admin.permissions.create') }}">
                    Add Permission
                </x-mary-button>
                @endcan
                <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.dashboard') }}">
                    Back to Dashboard
                </x-mary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Permissions Table -->
            <x-mary-card>
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <span>All Permissions ({{ $permissions->total() }})</span>
                        @can('manage-permissions')
                        <x-mary-button icon="o-plus" class="btn-primary btn-sm" link="{{ route('admin.permissions.create') }}">
                            Add Permission
                        </x-mary-button>
                        @endcan
                    </div>
                </x-slot>
                
                @if($permissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles Using</th>
                                    @can('manage-permissions')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($permissions as $permission)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</div>
                                                <div class="text-sm text-gray-500 font-mono">{{ $permission->name }}</div>
                                                @if($permission->description)
                                                    <div class="text-xs text-gray-400 mt-1">{{ $permission->description }}</div>
                                                @endif
                                            </div>
                                        </td>                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($permission->category)
                                <x-mary-badge value="{{ ucfirst($permission->category) }}" class="badge-ghost" />
                            @else
                                <span class="text-gray-400">No category</span>
                            @endif
                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($permission->is_active)
                                                <x-mary-badge value="Active" class="badge-success" />
                                            @else
                                                <x-mary-badge value="Inactive" class="badge-error" />
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php $rolesCount = $permission->roles()->count(); @endphp
                                            @if($rolesCount > 0)
                                                <x-mary-badge value="{{ $rolesCount }} roles" class="badge-info" />
                                            @else
                                                <span class="text-gray-400">Not assigned</span>
                                            @endif
                                        </td>
                                        @can('manage-permissions')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <x-mary-button 
                                                    icon="o-eye" 
                                                    class="btn-sm btn-ghost"
                                                    link="{{ route('admin.permissions.show', $permission) }}"
                                                    tooltip="View Details"
                                                />
                                                <x-mary-button 
                                                    icon="o-pencil" 
                                                    class="btn-sm btn-primary"
                                                    link="{{ route('admin.permissions.edit', $permission) }}"
                                                    tooltip="Edit Permission"
                                                />
                                                @if($permission->roles()->count() == 0)
                                                <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-mary-button 
                                                        icon="o-trash" 
                                                        class="btn-sm btn-error"
                                                        type="submit"
                                                        tooltip="Delete Permission"
                                                        onclick="return confirm('Are you sure you want to delete this permission?')"
                                                    />
                                                </form>
                                                @else
                                                <x-mary-button 
                                                    icon="o-trash" 
                                                    class="btn-sm btn-disabled"
                                                    disabled
                                                    tooltip="Cannot delete - assigned to roles"
                                                />
                                                @endif
                                            </div>
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($permissions->hasPages())
                        <div class="mt-6">
                            {{ $permissions->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <x-mary-icon name="o-shield-exclamation" class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Permissions Found</h3>
                        <p class="text-gray-500 mb-4">Start by creating your first permission to control access to your application.</p>
                        @can('manage-permissions')
                        <x-mary-button 
                            link="{{ route('admin.permissions.create') }}" 
                            class="btn-primary"
                            icon="o-plus"
                        >
                            Create Permission
                        </x-mary-button>
                        @endcan
                    </div>
                @endif
            </x-mary-card>

            <!-- Permission Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-mary-card>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $permissions->where('is_active', true)->count() }}</div>
                        <div class="text-sm text-gray-500">Active Permissions</div>
                    </div>
                </x-mary-card>
                
                <x-mary-card>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $permissions->whereNotNull('category')->unique('category')->count() }}</div>
                        <div class="text-sm text-gray-500">Categories</div>
                    </div>
                </x-mary-card>
                
                <x-mary-card>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $permissions->sum(fn($p) => $p->roles()->count()) }}</div>
                        <div class="text-sm text-gray-500">Role Assignments</div>
                    </div>
                </x-mary-card>
            </div>
        </div>
    </div>
</x-admin.layout>
