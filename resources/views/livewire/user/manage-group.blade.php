<div>
    <x-mary-header 
        title="Manage {{ $group->name }}" 
        subtitle="Edit group details and manage members"
        size="text-2xl"
        separator
    >
        <x-slot:actions>
            @php
                $userRole = $group->groupMembers()->where('user_id', auth()->id())->first()?->role;
                $isSystemAdmin = auth()->user()->canManageSystem();
            @endphp
            @if($isSystemAdmin)
                <x-mary-badge value="System Admin" class="badge-error" />
            @elseif($userRole?->name === 'admin')
                <x-mary-badge value="Group Admin" class="badge-warning" />
            @elseif($userRole?->name === 'manager')
                <x-mary-badge value="Manager" class="badge-info" />
            @endif
        </x-slot:actions>
    </x-mary-header>

    <div class="grid gap-6">
        <!-- Group Details Form -->
        @can('edit-group-description', $group)
            <x-mary-card title="📝 Group Details" shadow separator>
                <form wire:submit.prevent="updateGroupDetails">
                    <div class="space-y-4">
                        <x-mary-input 
                            label="Group Name" 
                            wire:model="name" 
                            placeholder="Enter group name"
                            required
                        />
                        
                        <x-mary-textarea 
                            label="Description" 
                            wire:model="description" 
                            placeholder="Enter group description (optional)"
                            rows="4"
                        />
                        
                        <x-mary-toggle 
                            label="Active" 
                            wire:model="is_active"
                            hint="Inactive groups are hidden from members"
                        />

                        <div class="flex justify-end gap-2">
                            <x-mary-button 
                                label="Cancel" 
                                link="{{ route('groups.show', $group->id) }}"
                                class="btn-ghost"
                            />
                            <x-mary-button 
                                label="Save Changes" 
                                type="submit"
                                class="btn-primary"
                                spinner="updateGroupDetails"
                            />
                        </div>
                    </div>
                </form>
            </x-mary-card>
        @endcan

        <!-- Members Management -->
        @can('manage-group-members', $group)
            <x-mary-card shadow separator>
                <x-slot:title>
                    <div class="flex items-center justify-between">
                        <span>👥 Group Members ({{ $members->count() }})</span>
                        <x-mary-button 
                            label="Add Member" 
                            icon="o-plus"
                            wire:click="openAddMemberModal"
                            class="btn-sm btn-primary"
                        />
                    </div>
                </x-slot:title>

                @if($members->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $membership)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-primary text-primary-content rounded-full w-10">
                                                        <span class="text-sm">{{ substr($membership->user->name, 0, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $membership->user->name }}</div>
                                                    @if($membership->user_id === auth()->id())
                                                        <span class="badge badge-xs badge-info">You</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $membership->user->email }}</td>
                                        <td>
                                            @can('edit-member-roles-in-group', $group)
                                                @if($membership->user_id !== auth()->id())
                                                    <select 
                                                        wire:change="updateMemberRole({{ $membership->user_id }}, $event.target.value)"
                                                        class="select select-bordered select-sm"
                                                        @if($membership->role && $membership->role->hierarchy_level > ($userRole?->hierarchy_level ?? 0))
                                                            disabled
                                                        @endif
                                                    >
                                                        @foreach($availableRoles as $role)
                                                            <option 
                                                                value="{{ $role->id }}" 
                                                                @selected($membership->role_id === $role->id)
                                                                @if($role->hierarchy_level > ($userRole?->hierarchy_level ?? 0))
                                                                    disabled
                                                                @endif
                                                            >
                                                                {{ $role->display_name }}
                                                                @if($role->hierarchy_level > ($userRole?->hierarchy_level ?? 0))
                                                                    (Higher than yours)
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <x-mary-badge 
                                                        value="{{ $membership->role->display_name }}" 
                                                        class="badge-primary"
                                                    />
                                                @endif
                                            @else
                                                @if($membership->role)
                                                    @php
                                                        $badgeClass = match($membership->role->name) {
                                                            'admin' => 'badge-error',
                                                            'manager' => 'badge-warning',
                                                            'staff' => 'badge-info',
                                                            default => 'badge-neutral'
                                                        };
                                                    @endphp
                                                    <x-mary-badge 
                                                        value="{{ $membership->role->display_name }}" 
                                                        class="{{ $badgeClass }}"
                                                    />
                                                @endif
                                            @endcan
                                        </td>
                                        <td class="text-sm text-gray-600">
                                            {{ $membership->joined_at ? $membership->joined_at->format('M j, Y') : 'N/A' }}
                                        </td>
                                        <td class="text-right">
                                            @if($membership->user_id !== auth()->id())
                                                <x-mary-button 
                                                    icon="o-trash" 
                                                    wire:click="removeMember({{ $membership->user_id }})"
                                                    wire:confirm="Are you sure you want to remove this member?"
                                                    class="btn-sm btn-error btn-ghost"
                                                    spinner
                                                />
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-mary-icon name="o-users" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                        <p class="text-gray-500">No members in this group yet.</p>
                    </div>
                @endif
            </x-mary-card>
        @endcan

        <!-- Navigation -->
        <div class="flex justify-between items-center">
            <x-mary-button 
                label="Back to Group" 
                icon="o-arrow-left"
                link="/my-groups/{{ $group->id }}"
                class="btn-ghost"
            />
            
            @if(auth()->user()->canManageSystem())
                <x-mary-button 
                    label="Admin View" 
                    icon="o-cog"
                    link="/admin/groups/{{ $group->id }}/edit"
                    class="btn-outline btn-primary"
                />
            @endif
        </div>
    </div>

    <!-- Add Member Modal -->
    <x-mary-modal wire:model="showAddMemberModal" title="Add New Member" separator>
        <div class="space-y-4">
            <x-mary-input 
                label="Search Users" 
                wire:model.live="searchTerm" 
                placeholder="Search by name or email..."
                icon="o-magnifying-glass"
            />

            @if($availableUsers->count() > 0)
                <div>
                    <label class="label">
                        <span class="label-text">Select User</span>
                    </label>
                    <select wire:model="selectedUserId" class="select select-bordered w-full">
                        <option value="">Choose a user...</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="alert alert-info">
                    <x-mary-icon name="o-information-circle" />
                    <span>
                        @if($searchTerm)
                            No users found matching "{{ $searchTerm }}"
                        @else
                            All verified users are already members of this group.
                        @endif
                    </span>
                </div>
            @endif

            @if($selectedUserId)
                <div>
                    <label class="label">
                        <span class="label-text">Assign Role</span>
                    </label>
                    <select wire:model="selectedRoleId" class="select select-bordered w-full">
                        <option value="">Choose a role...</option>
                        @foreach($availableRoles as $role)
                            <option value="{{ $role->id }}">
                                {{ $role->display_name }} 
                                ({{ $role->description }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <x-slot:actions>
            <x-mary-button label="Cancel" wire:click="closeAddMemberModal" />
            <x-mary-button 
                label="Add Member" 
                class="btn-primary" 
                wire:click="addMember"
                :disabled="!$selectedUserId || !$selectedRoleId"
                spinner="addMember"
            />
        </x-slot:actions>
    </x-mary-modal>
</div>
