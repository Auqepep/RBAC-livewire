<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit User') }}: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <x-mary-alert icon="o-check-circle" class="alert-success" dismissible>
                    {{ session('success') }}
                </x-mary-alert>
            @endif

            @if (session('error'))
                <x-mary-alert icon="o-x-circle" class="alert-error" dismissible>
                    {{ session('error') }}
                </x-mary-alert>
            @endif

            @if($errors->any())
                <x-mary-alert icon="o-x-circle" class="alert-error">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-mary-alert>
            @endif

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.users.edit', $user->id) }}?tab=details" 
                       class="border-transparent {{ request('tab', 'details') === 'details' ? 'border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        User Details
                    </a>
                    <a href="{{ route('admin.users.edit', $user->id) }}?tab=groups" 
                       class="border-transparent {{ request('tab') === 'groups' ? 'border-green-500 text-green-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Group Memberships
                        @if($user->groupMembers->count() > 0)
                            <span class="ml-2 bg-green-100 text-green-800 py-0.5 px-2 rounded-full text-xs font-medium">
                                {{ $user->groupMembers->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.users.edit', $user->id) }}?tab=security" 
                       class="border-transparent {{ request('tab') === 'security' ? 'border-purple-500 text-purple-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Security & Status
                    </a>
                </nav>
            </div>
            <!-- Tab Content -->
            @if(request('tab', 'details') === 'details')
                <!-- User Details Tab -->
                <x-mary-card>
                    <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input 
                                    type="text"
                                    name="name" 
                                    value="{{ old('name', $user->name) }}"
                                    placeholder="Enter user name"
                                    required
                                    class="input input-bordered w-full @error('name') input-error @enderror"
                                />
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input 
                                    type="email"
                                    name="email" 
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="Enter email address"
                                    required
                                    class="input input-bordered w-full @error('email') input-error @enderror"
                                />
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-sm text-blue-800">
                                        <strong>Note:</strong> To manage this user's group memberships and roles, use the "Group Memberships" tab or edit groups directly.
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Update User
                                </button>
                            </div>
                        </div>
                    </form>
                </x-mary-card>

            @elseif(request('tab') === 'groups')
                <!-- Group Memberships Tab -->
                <x-mary-card>
                    <h3 class="text-lg font-semibold mb-4">Group Memberships & Roles</h3>
                    
                    @if($user->groupMembers->count() > 0)
                        <div class="space-y-3">
                            @foreach($user->groupMembers as $membership)
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-white to-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-semibold" 
                                             style="background: linear-gradient(135deg, {{ $membership->role->badge_color ?? '#6366f1' }} 0%, {{ $membership->role->badge_color ?? '#4f46e5' }}dd 100%);">
                                            {{ strtoupper(substr($membership->group->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $membership->group->name }}</h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="badge text-xs" style="background-color: {{ $membership->role->badge_color ?? '#6366f1' }}; color: white;">
                                                    {{ $membership->role?->name ?? 'No Role' }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    • Joined {{ $membership->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.groups.edit', $membership->group) }}" class="btn btn-sm btn-ghost">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Manage Group
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-lg font-medium">Not a member of any groups</p>
                            <p class="text-sm mt-1">Add this user to groups to assign roles and permissions</p>
                            <a href="{{ route('admin.groups.index') }}" class="btn btn-primary btn-sm mt-4">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Browse Groups
                            </a>
                        </div>
                    @endif
                </x-mary-card>

            @elseif(request('tab') === 'security')
                <!-- Security & Status Tab -->
                <x-mary-card>
                    <h3 class="text-lg font-semibold mb-4">Security & Account Status</h3>
                    
                    <div class="space-y-6">
                        <!-- Email Verification Status -->
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email Verification
                            </h4>
                            @if($user->email_verified_at)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="badge badge-success">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Verified
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            Verified on {{ $user->email_verified_at->format('M d, Y \a\t H:i:s') }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="badge badge-error">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Unverified
                                        </span>
                                        <span class="text-sm text-gray-600">User needs to verify their email</span>
                                    </div>
                                    <button class="btn btn-sm btn-primary btn-outline">
                                        Resend Verification
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Account Created -->
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Account Information
                            </h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Created:</span>
                                    <span class="font-medium ml-2">{{ $user->created_at->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Last Updated:</span>
                                    <span class="font-medium ml-2">{{ $user->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Actions -->
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                            <h4 class="font-medium text-red-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Danger Zone
                            </h4>
                            <p class="text-sm text-red-800 mb-3">Permanent actions that cannot be undone</p>
                            <div class="flex gap-3">
                                <button class="btn btn-sm btn-error btn-outline" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone!')">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete User
                                </button>
                            </div>
                        </div>
                    </div>
                </x-mary-card>
            @endif
        </div>
    </div>
</x-admin.layout>
