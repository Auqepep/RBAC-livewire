<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Permission') }}: {{ $permission->display_name }}
            </h2>
            <div class="flex space-x-2">
                <x-mary-button icon="o-eye" class="btn-ghost" link="{{ route('admin.permissions.show', $permission) }}">
                    View Details
                </x-mary-button>
                <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.permissions.index') }}">
                    Back to Permissions
                </x-mary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-mary-card title="Edit Permission">
                <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-mary-input 
                                label="Permission Name" 
                                name="name" 
                                value="{{ old('name', $permission->name) }}"
                                placeholder="e.g., manage_users" 
                                required 
                                hint="Use lowercase with underscores"
                            />

                            <x-mary-input 
                                label="Display Name" 
                                name="display_name" 
                                value="{{ old('display_name', $permission->display_name) }}"
                                placeholder="e.g., Manage Users" 
                                required 
                            />
                        </div>

                        <x-mary-textarea 
                            label="Description" 
                            name="description" 
                            rows="3"
                            placeholder="Describe what this permission allows..."
                        >{{ old('description', $permission->description) }}</x-mary-textarea>

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
                                value="{{ old('category', $permission->category) }}"
                                hint="Optional grouping for permissions"
                            />

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <x-mary-checkbox 
                                    name="is_active" 
                                    label="Active Permission" 
                                    value="1"
                                    checked="{{ old('is_active', $permission->is_active) }}"
                                />
                            </div>
                        </div>

                        <!-- Warning about roles using this permission -->
                        @if($permission->roles->count() > 0)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <x-mary-icon name="o-exclamation-triangle" class="w-5 h-5 text-yellow-400 mr-2" />
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800">Permission in Use</h4>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            This permission is currently assigned to {{ $permission->roles->count() }} role(s). 
                                            Changes may affect user access.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3">
                            <x-mary-button type="button" class="btn-ghost" link="{{ route('admin.permissions.show', $permission) }}">
                                Cancel
                            </x-mary-button>
                            <x-mary-button type="submit" class="btn-primary">
                                Update Permission
                            </x-mary-button>
                        </div>
                    </div>
                </form>
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
