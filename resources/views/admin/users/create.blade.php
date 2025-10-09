<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New User
            </h2>
            <x-mary-button 
                label="Back to Users" 
                icon="o-arrow-left" 
                class="btn-outline"
                link="{{ route('admin.users.index') }}" />
        </div>
    </x-slot>

    <x-mary-card title="Create New User" subtitle="Add a new user to the system">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name Field --}}
                <x-mary-input 
                    label="Full Name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter user's full name"
                    icon="o-user"
                    required />

                {{-- Email Field --}}
                <x-mary-input 
                    label="Email Address"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    placeholder="Enter email address"
                    icon="o-envelope"
                    required />
            </div>

            {{-- Group Assignment Section --}}
            <div class="mt-8">
                <x-mary-card title="Group Assignments" subtitle="Assign user to groups with specific roles" class="bg-gray-50">
                    <div id="group-assignments" class="space-y-4">
                        {{-- Initial Assignment Row --}}
                        <div class="group-assignment-row grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-lg border">
                            <x-mary-select 
                                label="Group"
                                name="group_assignments[0][group_id]"
                                :options="$groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->toArray()"
                                option-value="id"
                                option-label="name"
                                placeholder="Select a group"
                                class="group-select" />

                            <x-mary-select 
                                label="Role in Group"
                                name="group_assignments[0][role_id]"
                                placeholder="Select a role"
                                class="role-select"
                                disabled />

                            <div class="flex items-end">
                                <x-mary-button 
                                    label="Remove"
                                    icon="o-trash"
                                    class="btn-error btn-outline remove-assignment"
                                    type="button" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-mary-button 
                            label="Add Another Group"
                            icon="o-plus"
                            class="btn-success btn-outline"
                            type="button"
                            id="add-assignment" />
                    </div>
                </x-mary-card>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end space-x-3 mt-8">
                <x-mary-button 
                    label="Cancel"
                    icon="o-x-mark"
                    class="btn-outline"
                    link="{{ route('admin.users.index') }}" />
                
                <x-mary-button 
                    label="Create User"
                    icon="o-check"
                    class="btn-primary"
                    type="submit" />
            </div>
        </form>
    </x-mary-card>

    {{-- JavaScript for Dynamic Group/Role Selection --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let assignmentIndex = 0;
            const roles = @json($roles->groupBy('id'));
            const groupRoles = @json($groupRoles); // roles available per group

            // Add new assignment row
            document.getElementById('add-assignment').addEventListener('click', function() {
                assignmentIndex++;
                const container = document.getElementById('group-assignments');
                const newRow = createAssignmentRow(assignmentIndex);
                container.appendChild(newRow);
                setupRowEventListeners(newRow, assignmentIndex);
            });

            // Setup initial row
            setupRowEventListeners(document.querySelector('.group-assignment-row'), 0);

            function createAssignmentRow(index) {
                const row = document.createElement('div');
                row.className = 'group-assignment-row grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-lg border';
                row.innerHTML = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                        <select name="group_assignments[${index}][group_id]" class="group-select w-full rounded-md border-gray-300">
                            <option value="">Select a group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role in Group</label>
                        <select name="group_assignments[${index}][role_id]" class="role-select w-full rounded-md border-gray-300" disabled>
                            <option value="">Select a role</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-assignment btn btn-error btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove
                        </button>
                    </div>
                `;
                return row;
            }

            function setupRowEventListeners(row, index) {
                const groupSelect = row.querySelector('.group-select');
                const roleSelect = row.querySelector('.role-select');
                const removeBtn = row.querySelector('.remove-assignment');

                // Group selection changes role options
                groupSelect.addEventListener('change', function() {
                    const groupId = this.value;
                    roleSelect.innerHTML = '<option value="">Select a role</option>';
                    
                    if (groupId && groupRoles[groupId]) {
                        roleSelect.disabled = false;
                        groupRoles[groupId].forEach(role => {
                            const option = document.createElement('option');
                            option.value = role.id;
                            option.textContent = role.display_name;
                            roleSelect.appendChild(option);
                        });
                    } else {
                        roleSelect.disabled = true;
                    }
                });

                // Remove row
                removeBtn.addEventListener('click', function() {
                    if (document.querySelectorAll('.group-assignment-row').length > 1) {
                        row.remove();
                    }
                });
            }
        });
    </script>
    @endpush
</x-admin.layout>
