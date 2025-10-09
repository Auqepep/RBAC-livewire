<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit User') }}: {{ $user->name }}
            </h2>
            <x-mary-button icon="o-arrow-left" class="btn-secondary" link="{{ route('admin.users.index') }}">
                Back to Users
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

                <x-mary-form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <x-mary-input 
                            label="Name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required 
                            placeholder="Enter user name"
                        />

                        <x-mary-input 
                            label="Email" 
                            name="email" 
                            type="email"
                            value="{{ old('email', $user->email) }}" 
                            required 
                            placeholder="Enter email address"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Group Memberships</label>
                            @if($user->groupMembers->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                                    @foreach($user->groupMembers as $membership)
                                        <div class="flex items-center justify-between p-3 bg-white rounded border">
                                            <div>
                                                <div class="font-medium">{{ $membership->group->name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    Role: {{ $membership->role?->name ?? 'No Role' }}
                                                </div>
                                            </div>
                                            <x-mary-button 
                                                icon="o-eye" 
                                                class="btn-sm btn-ghost" 
                                                link="{{ route('admin.groups.show', $membership->group) }}"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-gray-500 text-sm p-4 bg-gray-50 rounded-lg">
                                    This user is not a member of any groups.
                                </div>
                            @endif
                            <p class="text-sm text-gray-500 mt-2">
                                Note: Group memberships and roles are managed separately from user details. 
                                Use the group management interface to modify user roles and memberships.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Verification Status</label>
                            @if($user->email_verified_at)
                                <div class="flex items-center space-x-2">
                                    <x-mary-badge value="Verified" class="badge-success" />
                                    <span class="text-sm text-gray-500">
                                        Verified {{ $user->email_verified_at->format('M d, Y H:i:s') }}
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2">
                                    <x-mary-badge value="Unverified" class="badge-error" />
                                    <span class="text-sm text-gray-500">User needs to verify their email address</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <x-mary-button 
                                label="Cancel" 
                                class="btn-secondary" 
                                link="{{ route('admin.users.index') }}"
                            />
                            <x-mary-button 
                                label="Update User" 
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
</x-admin.layout>
