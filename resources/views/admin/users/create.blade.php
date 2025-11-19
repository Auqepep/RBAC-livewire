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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                                <select name="group_assignments[0][group_id]" 
                                        class="group-select w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 select select-bordered">
                                    <option value="">Select a group</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role in Group</label>
                                <select name="group_assignments[0][role_id]" 
                                        class="role-select w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 select select-bordered"
                                        disabled>
                                    <option value="">Select a role</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="button" 
                                        class="remove-assignment btn btn-error btn-outline w-full">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" 
                                id="add-assignment"
                                class="btn btn-success btn-outline">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Another Group
                        </button>
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
