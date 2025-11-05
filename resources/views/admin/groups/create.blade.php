<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Group') }}
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

                <x-mary-form method="POST" action="{{ route('admin.groups.store') }}">
                    <div class="grid grid-cols-1 gap-6">
                        <x-mary-input 
                            label="Group Name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            placeholder="Enter group name"
                            hint="This name must be unique"
                        />

                        <x-mary-textarea 
                            label="Description" 
                            name="description" 
                            value="{{ old('description') }}" 
                            placeholder="Describe the purpose of this group"
                            rows="3"
                        />

                        <x-mary-checkbox 
                            label="Active" 
                            name="is_active" 
                            value="1" 
                            checked="{{ old('is_active', true) }}"
                            hint="Inactive groups cannot be assigned to users"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Initial Members (Optional)</label>
                            <div id="group-members-list" class="space-y-2 max-h-96 overflow-y-auto border rounded-lg p-4">
                                @foreach($users as $user)
                                    @php
                                        $isChecked = in_array($user->id, old('users', []));
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
                                            <input type="hidden" name="user_roles[{{ $user->id }}]" value="staff" class="role-value">
                                            <select class="select select-sm select-bordered role-select" {{ !$isChecked ? 'disabled' : '' }}>
                                                <option value="staff" selected>Staff (Default)</option>
                                                <option value="manager">Manager</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                            <small class="text-gray-500 block mt-1">Role will be created for this group</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Select users and assign them roles. Default role is "Staff".</p>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <x-mary-button 
                                label="Cancel" 
                                class="btn-secondary" 
                                link="{{ route('admin.groups.index') }}"
                            />
                            <x-mary-button 
                                label="Create Group" 
                                class="btn-primary" 
                                type="submit"
                                icon="o-plus"
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
                    const selectElement = roleSelector.querySelector('.role-select');
                    
                    if (this.checked) {
                        roleSelector.style.display = 'block';
                        selectElement.disabled = false;
                    } else {
                        roleSelector.style.display = 'none';
                        selectElement.disabled = true;
                    }
                });
            });

            // Update hidden input when role changes
            document.querySelectorAll('.role-select').forEach(select => {
                select.addEventListener('change', function() {
                    const memberRow = this.closest('.member-row');
                    const hiddenInput = memberRow.querySelector('.role-value');
                    hiddenInput.value = this.value;
                });
            });
        });
    </script>
</x-admin.layout>
