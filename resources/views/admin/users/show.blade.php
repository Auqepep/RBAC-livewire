<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}: {{ $user->name }}
            </h2>
            <div class="flex space-x-2">
                <x-mary-button icon="o-pencil" class="btn-primary" link="{{ route('admin.users.edit', $user) }}">
                    Edit User
                </x-mary-button>
                <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.users.index') }}">
                    Back to Users
                </x-mary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- User Information Card -->
            <x-mary-card title="User Information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-mary-input 
                        label="Name" 
                        value="{{ $user->name }}" 
                        readonly 
                    />
                    
                    <x-mary-input 
                        label="Email" 
                        value="{{ $user->email }}" 
                        readonly 
                    />
                    
                    <x-mary-input 
                        label="Created At" 
                        value="{{ $user->created_at->format('M d, Y H:i:s') }}" 
                        readonly 
                    />
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Status</label>
                        @if($user->email_verified_at)
                            <x-mary-badge value="Verified" class="badge-success" />
                            <div class="text-sm text-gray-500 mt-1">
                                Verified {{ $user->email_verified_at->format('M d, Y H:i:s') }}
                            </div>
                        @else
                            <x-mary-badge value="Unverified" class="badge-error" />
                        @endif
                    </div>
                </div>
            </x-mary-card>

            <!-- Group Memberships Card -->
            <x-mary-card title="Group Memberships ({{ $user->groupMembers->count() }})">
                @if($user->groupMembers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-1/3">Group</th>
                                    <th class="w-1/4">Role</th>
                                    <th class="w-1/4">Joined At</th>
                                    <th class="w-1/6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->groupMembers as $membership)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $membership->group->name }}</div>
                                                @if($membership->group->description)
                                                    <div class="text-sm text-gray-500">{{ Str::limit($membership->group->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($membership->role)
                                                <span class="badge text-xs text-white" style="background-color: {{ $membership->role->badge_color ?? '#6366f1' }};">
                                                    {{ $membership->role->display_name ?? $membership->role->name }}
                                                </span>
                                            @else
                                                <span class="badge badge-error">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-600">
                                                {{ $membership->joined_at ? $membership->joined_at->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center space-x-2">
                                                <x-mary-button 
                                                    icon="o-eye" 
                                                    class="btn-sm btn-ghost" 
                                                    link="{{ route('admin.groups.show', $membership->group) }}"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-mary-alert icon="o-information-circle" class="alert-info">
                        This user is not a member of any groups.
                    </x-mary-alert>
                @endif
            </x-mary-card>

            <!-- Actions Card -->
            <x-mary-card title="User Actions">
                <div class="flex flex-wrap gap-4">
                    <x-mary-button 
                        icon="o-pencil" 
                        class="btn-primary" 
                        link="{{ route('admin.users.edit', $user) }}"
                    >
                        Edit User
                    </x-mary-button>

                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                              class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <x-mary-button 
                                icon="o-trash" 
                                class="btn-error" 
                                type="submit"
                            >
                                Delete User
                            </x-mary-button>
                        </form>
                    @endif
                </div>
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
