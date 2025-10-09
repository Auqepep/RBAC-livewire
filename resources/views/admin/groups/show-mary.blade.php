<x-admin.mary-layout>
    <x-header title="Group Details: {{ $group->name }}" separator>
        <x-slot:actions>
            <x-button link="{{ route('admin.groups.edit', $group) }}" icon="o-pencil" class="btn-primary">
                Edit Group
            </x-button>
            <x-button link="{{ route('admin.groups.index') }}" icon="o-arrow-left" class="btn-ghost">
                Back to Groups
            </x-button>
        </x-slot:actions>
    </x-header>

    <div class="grid gap-6">
        <!-- Group Information Card -->
        <x-card title="Group Information">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input label="Name" value="{{ $group->name }}" readonly />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Status</label>
                    @if($group->is_active)
                        <x-badge value="Active" class="badge-success" />
                    @else
                        <x-badge value="Inactive" class="badge-error" />
                    @endif
                </div>
                
                <div>
                    <x-input label="Created By" value="{{ $group->creator?->name ?? 'System' }}" readonly />
                </div>
                
                <div>
                    <x-input label="Created At" value="{{ $group->created_at->format('M d, Y H:i:s') }}" readonly />
                </div>
                
                @if($group->description)
                    <div class="col-span-2">
                        <x-textarea label="Description" value="{{ $group->description }}" readonly />
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Group Members Card -->
        <x-card title="Group Members ({{ $group->members->count() }})">
            @if($group->members->count() > 0)
                <x-table :headers="[
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'role_name', 'label' => 'Role'],
                    ['key' => 'joined_at', 'label' => 'Joined At'],
                    ['key' => 'added_by', 'label' => 'Added By']
                ]" :rows="$group->members->map(function($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                        'role_name' => $member->pivot->role?->name ?? 'No Role',
                        'joined_at' => $member->pivot->created_at->format('M d, Y'),
                        'added_by' => $member->pivot->addedBy?->name ?? 'System'
                    ];
                })" />
            @else
                <x-alert icon="o-information-circle" class="alert-info">
                    No members found in this group.
                </x-alert>
            @endif
        </x-card>

        <!-- Group Roles Card -->
        <x-card title="Group Roles">
            @livewire('admin.group-roles-display', ['group' => $group])
        </x-card>

        @if($group->joinRequests->count() > 0)
        <!-- Pending Join Requests Card -->
        <x-card title="Pending Join Requests ({{ $group->joinRequests->count() }})">
            <x-table :headers="[
                ['key' => 'user_name', 'label' => 'User'],
                ['key' => 'user_email', 'label' => 'Email'],
                ['key' => 'requested_at', 'label' => 'Requested At'],
                ['key' => 'actions', 'label' => 'Actions']
            ]" :rows="$group->joinRequests->map(function($request) {
                return [
                    'id' => $request->id,
                    'user_name' => $request->user->name,
                    'user_email' => $request->user->email,
                    'requested_at' => $request->created_at->format('M d, Y H:i:s'),
                    'actions' => ''
                ];
            })">
                <x-slot:actions>
                    <x-button icon="o-check" class="btn-success btn-sm" />
                    <x-button icon="o-x-mark" class="btn-error btn-sm" />
                </x-slot:actions>
            </x-table>
        </x-card>
        @endif
    </div>
</x-admin.mary-layout>
