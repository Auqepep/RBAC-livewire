<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <flux:heading size="lg">{{ __('Create New Role') }}</flux:heading>
            <flux:button href="{{ route('admin.roles.index') }}" variant="outline">
                Back to Roles
            </flux:button>
        </div>
    </x-slot>

    <flux:card>
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <flux:field>
                    <flux:label>Role Name (System Name)</flux:label>
                    <flux:input name="name" value="{{ old('name') }}" placeholder="e.g., manager, editor" required />
                    <flux:error name="name" />
                    <flux:description>This will be used in code. Use lowercase, no spaces.</flux:description>
                </flux:field>

                <!-- Display Name -->
                <flux:field>
                    <flux:label>Display Name</flux:label>
                    <flux:input name="display_name" value="{{ old('display_name') }}" placeholder="e.g., Manager, Content Editor" required />
                    <flux:error name="display_name" />
                    <flux:description>This will be shown to users.</flux:description>
                </flux:field>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea name="description" rows="3" placeholder="Describe what this role can do...">{{ old('description') }}</flux:textarea>
                    <flux:error name="description" />
                </flux:field>
            </div>

            <!-- Color -->
            <div class="mt-6">
                <flux:field>
                    <flux:label>Role Color</flux:label>
                    <flux:input type="color" name="color" value="{{ old('color', '#3B82F6') }}" />
                    <flux:error name="color" />
                    <flux:description>Choose a color for this role's badge display.</flux:description>
                </flux:field>
            </div>

            <!-- Status -->
            <div class="mt-6">
                <flux:checkbox name="is_active" value="1" :checked="old('is_active', true)" label="Active" description="Users can be assigned to this role" />
            </div>

            <!-- Permissions -->
            <div class="mt-8">
                <flux:heading size="sm">Permissions</flux:heading>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select the permissions that users with this role should have.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($permissions as $permission)
                        <flux:checkbox 
                            name="permissions[]" 
                            value="{{ $permission->id }}"
                            :checked="in_array($permission->id, old('permissions', []))"
                            label="{{ $permission->display_name }}"
                            description="{{ $permission->description }}"
                        />
                    @endforeach
                </div>
                <flux:error name="permissions" />
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex justify-end space-x-3">
                <flux:button href="{{ route('admin.roles.index') }}" variant="outline">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Create Role
                </flux:button>
            </div>
        </form>
    </flux:card>
</x-admin.layout>
