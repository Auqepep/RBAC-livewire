<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permission Details') }}: {{ $permission->display_name }}
            </h2>
            <div class="flex space-x-2">
                <x-mary-button icon="o-pencil" class="btn-primary" link="{{ route('admin.permissions.edit', $permission) }}">
                    Edit Permission
                </x-mary-button>
                <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.permissions.index') }}">
                    Back to Permissions
                </x-mary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Permission Information -->
            <x-mary-card title="Permission Information">
                <!-- Gates Status Demo -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h5 class="font-medium text-blue-900 mb-2">🚪 Laravel Gates Status for Current User:</h5>
                    <div class="text-sm">
                        @can($permission->name)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✅ You have {{ $permission->display_name }} permission
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ❌ You don't have {{ $permission->display_name }} permission
                            </span>
                        @endcan
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-mary-input 
                        label="Permission Name" 
                        value="{{ $permission->name }}" 
                        readonly 
                    />
                    
                    <x-mary-input 
                        label="Display Name" 
                        value="{{ $permission->display_name }}" 
                        readonly 
                    />
                    
                    @if($permission->module)
                        <x-mary-input 
                            label="Module" 
                            value="{{ $permission->module }}" 
                            readonly 
                        />
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        @if($permission->is_active)
                            <x-mary-badge value="Active" class="badge-success" />
                        @else
                            <x-mary-badge value="Inactive" class="badge-error" />
                        @endif
                    </div>
                    
                    @if($permission->description)
                        <div class="md:col-span-2">
                            <x-mary-textarea 
                                label="Description" 
                                readonly 
                                rows="3"
                            >{{ $permission->description }}</x-mary-textarea>
                        </div>
                    @endif
                </div>
            </x-mary-card>

            <!-- Roles that have this permission -->
            <x-mary-card title="Roles with this Permission">
                @if($permission->roles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($permission->roles as $role)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $role->display_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $role->name }}</p>
                                        @if($role->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($role->description, 50) }}</p>
                                        @endif
                                    </div>
                                    <x-mary-badge 
                                        value="{{ $role->is_active ? 'Active' : 'Inactive' }}" 
                                        class="badge-{{ $role->is_active ? 'success' : 'error' }}"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-mary-icon name="o-shield-exclamation" class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                        <p class="text-gray-500">This permission is not assigned to any roles yet.</p>
                    </div>
                @endif
            </x-mary-card>

            <!-- Permission Metadata -->
            <x-mary-card title="Metadata">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-mary-input 
                        label="Created At" 
                        value="{{ $permission->created_at->format('M d, Y H:i:s') }}" 
                        readonly 
                    />
                    
                    <x-mary-input 
                        label="Updated At" 
                        value="{{ $permission->updated_at->format('M d, Y H:i:s') }}" 
                        readonly 
                    />
                    
                    <x-mary-input 
                        label="Permission ID" 
                        value="{{ $permission->id }}" 
                        readonly 
                    />
                </div>
            </x-mary-card>

            <!-- Actions -->
            <x-mary-card title="Actions">
                <div class="flex space-x-3">
                    <x-mary-button 
                        icon="o-pencil" 
                        class="btn-primary" 
                        link="{{ route('admin.permissions.edit', $permission) }}"
                    >
                        Edit Permission
                    </x-mary-button>
                    
                    <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-mary-button 
                            icon="o-trash" 
                            class="btn-error"
                            type="submit"
                            onclick="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')"
                        >
                            Delete Permission
                        </x-mary-button>
                    </form>
                </div>
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
