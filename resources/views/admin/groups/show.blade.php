<x-admin.layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                {{ __('Group') }}: {{ Str::limit($group->name, 30) }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <x-mary-button icon="o-pencil" class="btn-primary btn-sm sm:btn-md" link="{{ route('admin.groups.edit', $group) }}">
                    <span class="hidden sm:inline">Edit Group</span>
                    <span class="sm:hidden">Edit</span>
                </x-mary-button>
                <x-mary-button icon="o-arrow-left" class="btn-secondary btn-sm sm:btn-md" link="{{ route('admin.groups.index') }}">
                    <span class="hidden sm:inline">Back to Groups</span>
                    <span class="sm:hidden">Back</span>
                </x-mary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            <!-- Group Information Card -->
            <x-mary-card title="Group Information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-mary-input 
                        label="Name" 
                        value="{{ $group->name }}" 
                        readonly 
                    />
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        @if($group->is_active)
                            <x-mary-badge value="Active" class="badge-success badge-xs sm:badge-sm" />
                        @else
                            <x-mary-badge value="Inactive" class="badge-error badge-xs sm:badge-sm" />
                        @endif
                    </div>
                    
                    <x-mary-input 
                        label="Created By" 
                        value="{{ $group->creator?->name ?? 'System' }}" 
                        readonly 
                    />
                    
                    <x-mary-input 
                        label="Created At" 
                        value="{{ $group->created_at->format('M d, Y H:i:s') }}" 
                        readonly 
                    />
                    
                    @if($group->description)
                        <div class="col-span-2">
                            <x-mary-textarea 
                                label="Description" 
                                value="{{ $group->description }}" 
                                readonly 
                                rows="3"
                            />
                        </div>
                    @endif
                </div>
            </x-mary-card>

            <!-- Group Members Card -->
            <x-mary-card title="Group Members ({{ $group->groupMembers->count() }})">
                @if($group->groupMembers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/4">Name</th>
                                    <th class="w-1/4">Email</th>
                                    <th class="w-1/6">Role</th>
                                    <th class="w-1/6">Joined At</th>
                                    <th class="w-1/6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->groupMembers as $member)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-gray-900">{{ $member->user->name }}</div>
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-600">{{ $member->user->email }}</div>
                                        </td>
                                        <td>
                                            @if($member->role)
                                                <x-mary-badge 
                                                    value="{{ $member->role->name }}" 
                                                    class="badge-primary badge-xs sm:badge-sm" 
                                                />
                                            @else
                                                <x-mary-badge value="No Role" class="badge-error badge-xs sm:badge-sm" />
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-600">
                                                {{ $member->joined_at ? $member->joined_at->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center space-x-2">
                                                <form method="POST" action="{{ route('admin.groups.members.remove', [$group, $member->user]) }}" 
                                                      class="inline" onsubmit="return confirm('Remove this member from the group?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-mary-button icon="o-trash" class="btn-sm btn-error" type="submit" />
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-mary-alert icon="o-information-circle" class="alert-info">
                        No members found in this group.
                    </x-mary-alert>
                @endif
            </x-mary-card>

            <!-- Actions Card -->
            <x-mary-card title="Group Actions">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">Member Management</h5>
                        <x-mary-button 
                            icon="o-user-plus" 
                            class="btn-primary w-full" 
                            link="{{ route('admin.groups.members', $group) }}"
                        >
                            Manage Members
                        </x-mary-button>
                    </div>
                    
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">Role Management</h5>
                        <x-mary-button 
                            icon="o-cog-6-tooth" 
                            class="btn-secondary w-full" 
                            link="{{ route('admin.groups.roles.index', $group) }}"
                        >
                            Manage Roles
                        </x-mary-button>
                    </div>

                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">Danger Zone</h5>
                        @if($group->groupMembers->count() == 0)
                            <form method="POST" action="{{ route('admin.groups.destroy', $group) }}" 
                                  class="w-full" onsubmit="return confirm('Are you sure you want to delete this group? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <x-mary-button 
                                    icon="o-trash" 
                                    class="btn-error w-full" 
                                    type="submit"
                                >
                                    Delete Group
                                </x-mary-button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500 italic">Remove all members before deleting</p>
                        @endif
                    </div>
                </div>
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
