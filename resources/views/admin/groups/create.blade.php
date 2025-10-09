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
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto border rounded-lg p-4">
                                @foreach($users as $user)
                                    <x-mary-checkbox 
                                        label="{{ $user->name }} ({{ $user->email }})" 
                                        name="users[]" 
                                        value="{{ $user->id }}" 
                                        checked="{{ in_array($user->id, old('users', [])) }}"
                                    />
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Selected users will be added as regular members with default permissions.</p>
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
</x-admin.layout>
