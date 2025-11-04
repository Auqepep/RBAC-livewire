<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Group') }}: {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Success/Error Messages -->
            @if (session('success'))
                <x-mary-alert icon="o-check-circle" class="alert-success" dismissible>
                    {{ session('success') }}
                </x-mary-alert>
            @endif

            @if (session('error'))
                <x-mary-alert icon="o-x-circle" class="alert-error" dismissible>
                    {{ session('error') }}
                </x-mary-alert>
            @endif

            <!-- Edit Group Details -->
            <x-mary-card title="Group Details" shadow separator>
                <form method="POST" action="{{ route('users.groups.update', $group->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <!-- Group Name -->
                        <x-mary-input 
                            label="Group Name" 
                            name="name" 
                            value="{{ old('name', $group->name) }}"
                            placeholder="Enter group name"
                            required
                            :error="$errors->first('name')"
                        />

                        <!-- Group Description -->
                        <x-mary-textarea 
                            label="Description" 
                            name="description" 
                            rows="4"
                            placeholder="Enter group description (optional)"
                            :error="$errors->first('description')"
                        >{{ old('description', $group->description) }}</x-mary-textarea>

                        <div class="flex gap-3">
                            <x-mary-button 
                                label="Update Group" 
                                type="submit" 
                                icon="o-check"
                                class="btn-warning"
                            />
                            <x-mary-button 
                                label="Cancel" 
                                link="{{ route('groups.gateway', $group->id) }}"
                                icon="o-x-mark"
                                class="btn-ghost"
                            />
                        </div>
                    </div>
                </form>
            </x-mary-card>

            <!-- Manage Members -->
            @can('manageMembers', $group)
                <x-mary-card title="Group Members" shadow separator>
                    
                    <!-- Add Member Form -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold mb-3">Add New Member</h3>
                        <form method="POST" action="{{ route('users.groups.members.add', $group->id) }}" class="flex gap-3">
                            @csrf
                            
                            <div class="flex-1">
                                <x-mary-select 
                                    label="Select User" 
                                    name="user_id"
                                    :options="$users->map(fn($u) => ['id' => $u->id, 'name' => $u->name . ' (' . $u->email . ')'])"
                                    option-value="id"
                                    option-label="name"
                                    placeholder="Choose a user..."
                                    required
                                />
                            </div>

                            <div class="flex-1">
                                <x-mary-select 
                                    label="Select Role" 
                                    name="role_id"
                                    :options="$roles->map(fn($r) => ['id' => $r->id, 'name' => $r->display_name])"
                                    option-value="id"
                                    option-label="name"
                                    placeholder="Choose a role..."
                                    required
                                />
                            </div>

                            <div class="flex items-end">
                                <x-mary-button 
                                    label="Add Member" 
                                    type="submit" 
                                    icon="o-plus"
                                    class="btn-secondary"
                                />
                            </div>
                        </form>
                    </div>

                    <!-- Current Members List -->
                    <div class="space-y-3">
                        @forelse($groupMembers as $membership)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($membership->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $membership->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $membership->user->email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <!-- Role Badge -->
                                    @if($membership->role)
                                        <x-mary-badge 
                                            value="{{ $membership->role->display_name }}" 
                                            class="badge-primary"
                                        />
                                        <span class="text-sm text-gray-500">
                                            (Level {{ $membership->role->hierarchy_level }})
                                        </span>
                                    @endif

                                    <!-- Remove Button (if not current user) -->
                                    @if($membership->user_id !== auth()->id())
                                        <form method="POST" action="{{ route('users.groups.members.remove', [$group->id, $membership->id]) }}" onsubmit="return confirm('Are you sure you want to remove this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-mary-button 
                                                type="submit"
                                                icon="o-trash"
                                                class="btn-sm btn-error btn-outline"
                                                tooltip="Remove member"
                                            />
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500 italic">(You)</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>No members in this group yet.</p>
                            </div>
                        @endforelse
                    </div>
                </x-mary-card>
            @endcan

        </div>
    </div>
</x-app-layout>
