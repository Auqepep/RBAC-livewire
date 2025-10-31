<div>
    @if($hasGatewayAccess)
        <!-- Gateway Access Granted -->
        <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center">
            <div class="text-center">
                <div class="mb-8">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-green-500 to-blue-500 rounded-full flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-6xl font-bold text-gray-800 mb-4">GATEWAY</h1>
                    <div class="space-y-2">
                        <p class="text-xl text-gray-600">Access Granted to {{ $group->name }}</p>
                        <x-mary-badge value="Role: {{ $userRole?->display_name ?? 'N/A' }}" class="badge-success badge-lg" />
                    </div>
                </div>
                
                <!-- Gateway Info -->
                <div class="bg-white rounded-lg p-6 shadow-lg max-w-md mx-auto">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Gateway Information</h3>
                    <div class="space-y-3 text-left">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Group:</span>
                            <span class="font-medium">{{ $group->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Your Role:</span>
                            <span class="font-medium">{{ $userRole?->display_name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Role Group:</span>
                            <span class="font-medium">{{ $group->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Access Level:</span>
                            <span class="font-medium text-green-600">Authorized</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">{{ now()->format('H:i:s') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 space-x-4">
                    <x-mary-button 
                        label="Back to Group" 
                        link="{{ route('groups.show', $group->id) }}"
                        class="btn-primary"
                        icon="o-arrow-left"
                    />
                    <x-mary-button 
                        label="My Groups" 
                        link="{{ route('my-groups') }}"
                        class="btn-secondary"
                        icon="o-home"
                    />
                </div>
            </div>
        </div>
    @else
        <!-- Access Denied -->
        <div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center">
            <div class="text-center">
                <div class="mb-8">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h1 class="text-6xl font-bold text-gray-800 mb-4">ACCESS DENIED</h1>
                    <div class="space-y-2">
                        <p class="text-xl text-gray-600">Gateway to {{ $group->name }}</p>
                        <x-mary-badge value="Unauthorized" class="badge-error badge-lg" />
                    </div>
                </div>
                
                <!-- Denial Reason -->
                <div class="bg-white rounded-lg p-6 shadow-lg max-w-md mx-auto">
                    <h3 class="text-lg font-semibold text-red-600 mb-4">Access Restriction</h3>
                    <p class="text-gray-700 mb-4">{{ $accessDeniedReason }}</p>
                    
                    <div class="space-y-3 text-left">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Group:</span>
                            <span class="font-medium">{{ $group->name }}</span>
                        </div>
                        @if($userRole)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Your Role:</span>
                            <span class="font-medium">{{ $userRole->display_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Role Group:</span>
                            <span class="font-medium">{{ $group->name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Required Roles:</span>
                            <span class="font-medium text-gray-500">Admin, Staff, Manager</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 space-x-4">
                    <x-mary-button 
                        label="Back to Group" 
                        link="{{ route('groups.show', $group->id) }}"
                        class="btn-secondary"
                        icon="o-arrow-left"
                    />
                    <x-mary-button 
                        label="My Groups" 
                        link="{{ route('my-groups') }}"
                        class="btn-primary"
                        icon="o-home"
                    />
                </div>
            </div>
        </div>
    @endif
</div>
