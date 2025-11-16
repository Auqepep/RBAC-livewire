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

    {{-- Data for JavaScript (hidden) --}}
    <script type="application/json" id="groups-data">
        @json($groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name]))
    </script>
    
    <script type="application/json" id="group-roles-data">
        @json($groupRoles)
    </script>

    {{-- Include external JavaScript file --}}
    @vite(['resources/js/admin-user-create.js'])
</x-admin.layout>
