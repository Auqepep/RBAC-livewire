<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Group') }}: {{ $group->name }}
            </h2>
            <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.groups.index') }}">
                Back to Groups
            </x-mary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-mary-card>
                @if($errors->any())
                    <x-mary-alert icon="o-x-circle" class="alert-error mb-6">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-mary-alert>
                @endif

                <x-mary-form method="POST" action="{{ route('admin.groups.update', $group) }}">
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <x-mary-input 
                            label="Group Name" 
                            name="name" 
                            value="{{ old('name', $group->name) }}" 
                            required 
                            placeholder="Enter group name"
                            hint="This name must be unique"
                        />

                        <x-mary-textarea 
                            label="Description" 
                            name="description" 
                            value="{{ old('description', $group->description) }}" 
                            placeholder="Describe the purpose of this group"
                            rows="3"
                        />

                        <x-mary-checkbox 
                            label="Active" 
                            name="is_active" 
                            value="1" 
                            checked="{{ old('is_active', $group->is_active) }}"
                            hint="Inactive groups cannot be assigned to users"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Group Members</label>
                            <div id="group-members-list" class="space-y-2 max-h-96 overflow-y-auto border rounded-lg p-4">
                                @foreach($users as $user)
                                    @php
                                        $isChecked = in_array($user->id, old('users', $groupUsers));
                                        $userMembership = $group->groupMembers()->where('user_id', $user->id)->first();
                                        $currentRole = $userMembership ? $userMembership->role : null;
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg member-row">
                                        <x-mary-checkbox 
                                            name="users[]" 
                                            value="{{ $user->id }}" 
                                            checked="{{ $isChecked }}"
                                            class="member-checkbox"
                                            data-user-id="{{ $user->id }}"
                                        />
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                        <div class="role-selector" style="{{ !$isChecked ? 'display: none;' : '' }}">
                                            <select 
                                                name="user_roles[{{ $user->id }}]" 
                                                class="select select-sm select-bordered"
                                                {{ !$isChecked ? 'disabled' : '' }}
                                            >
                                                @foreach($group->roles as $role)
                                                    <option value="{{ $role->id }}" 
                                                        {{ (old("user_roles.{$user->id}", $currentRole?->id) == $role->id) ? 'selected' : '' }}>
                                                        {{ $role->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-2">
                                Select users and assign them roles. Unchecking a user will remove them from the group.
                            </p>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <x-mary-button 
                                label="Cancel" 
                                class="btn-secondary" 
                                link="{{ route('admin.groups.index') }}"
                            />
                            <x-mary-button 
                                label="Update Group" 
                                class="btn-primary" 
                                type="submit"
                                icon="o-pencil"
                            />
                        </div>
                    </div>
                </x-mary-form>
            </x-mary-card>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle checkbox changes to show/hide role selectors
            document.querySelectorAll('.member-checkbox input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const memberRow = this.closest('.member-row');
                    const roleSelector = memberRow.querySelector('.role-selector');
                    const selectElement = roleSelector.querySelector('select');
                    
                    if (this.checked) {
                        roleSelector.style.display = 'block';
                        selectElement.disabled = false;
                    } else {
                        roleSelector.style.display = 'none';
                        selectElement.disabled = true;
                    }
                });
            });
        });
    </script>
</x-admin.layout>
