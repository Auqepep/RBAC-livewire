<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Permission') }}
            </h2>
            <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.permissions.index') }}">
                Back to Permissions
            </x-mary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-mary-card title="Create New Permission">
                <form method="POST" action="{{ route('admin.permissions.store') }}">
                    @csrf
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-mary-input 
                                label="Permission Name" 
                                name="name" 
                                value="{{ old('name') }}"
                                placeholder="e.g., manage_users" 
                                required 
                                hint="Use lowercase with underscores"
                            />

                            <x-mary-input 
                                label="Display Name" 
                                name="display_name" 
                                value="{{ old('display_name') }}"
                                placeholder="e.g., Manage Users" 
                                required 
                            />
                        </div>

                        <x-mary-textarea 
                            label="Description" 
                            name="description" 
                            rows="3"
                            placeholder="Describe what this permission allows..."
                        >{{ old('description') }}</x-mary-textarea>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-mary-select 
                                label="Category" 
                                name="category" 
                                :options="[
                                    ['id' => 'system', 'name' => 'System Management'],
                                    ['id' => 'users', 'name' => 'User Management'],
                                    ['id' => 'groups', 'name' => 'Group Management'],
                                    ['id' => 'content', 'name' => 'Content Management'],
                                    ['id' => 'reports', 'name' => 'Reports & Analytics'],
                                    ['id' => 'profile', 'name' => 'Profile Management'],
                                    ['id' => 'department', 'name' => 'Department Management'],
                                    ['id' => 'team', 'name' => 'Team Management'],
                                    ['id' => 'approvals', 'name' => 'Approval System']
                                ]"
                                option-value="id"
                                option-label="name"
                                placeholder="Select a module"
                                value="{{ old('category') }}"
                                hint="Optional grouping for permissions"
                            />

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <x-mary-checkbox 
                                    name="is_active" 
                                    label="Active Permission" 
                                    value="1"
                                    checked="{{ old('is_active', true) }}"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <x-mary-button type="button" class="btn-ghost" link="{{ route('admin.permissions.index') }}">
                                Cancel
                            </x-mary-button>
                            <x-mary-button type="submit" class="btn-primary">
                                Create Permission
                            </x-mary-button>
                        </div>
                    </div>
                </form>
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
