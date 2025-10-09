<div>
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <x-mary-alert icon="o-check-circle" class="alert-success mb-4">
            {{ session('message') }}
        </x-mary-alert>
    @endif

    {{-- Header with Refresh Button --}}
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium">System Statistics</h3>
        <div class="flex gap-3">
            {{-- Pending Requests Notification --}}
            @if($stats['pending_join_requests'] > 0)
                <x-mary-button 
                    label="{{ $stats['pending_join_requests'] }} Pending {{ Str::plural('Request', $stats['pending_join_requests']) }}"
                    icon="o-envelope"
                    class="btn-warning btn-sm"
                    link="{{ route('admin.group-join-requests') }}" />
            @endif
            
            <x-mary-button 
                label="Refresh"
                icon="o-arrow-path"
                wire:click="refreshStats"
                spinner="refreshStats"
                class="btn-outline btn-sm" />
        </div>
    </div>

    {{-- Statistics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-mary-stat
            title="Total Users"
            description="Registered users"
            value="{{ number_format($stats['users']) }}"
            icon="o-users"
            color="text-primary" />

        <x-mary-stat
            title="Active Groups"
            description="Groups currently active"
            value="{{ number_format($stats['active_groups']) }}"
            icon="o-building-office"
            color="text-success" />

        <x-mary-stat
            title="Total Roles"
            description="Available system roles"
            value="{{ number_format($stats['roles']) }}"
            icon="o-identification"
            color="text-warning" />

        <x-mary-stat
            title="Active Assignments"
            description="User-group assignments"
            value="{{ number_format($stats['active_assignments']) }}"
            icon="o-link"
            color="text-info" />
    </div>

    {{-- Additional Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <x-mary-stat
            title="Total Assignments"
            description="All user-group assignments"
            value="{{ number_format($stats['total_assignments']) }}"
            icon="o-rectangle-group"
            color="text-neutral" />

        <x-mary-stat
            title="Permissions"
            description="System permissions"
            value="{{ number_format($stats['permissions'] ?? 0) }}"
            icon="o-key"
            color="text-accent" />

        <x-mary-stat
            title="Join Requests"
            description="Pending group join requests"
            value="{{ number_format($stats['pending_join_requests']) }}"
            icon="o-envelope"
            color="{{ $stats['pending_join_requests'] > 0 ? 'text-warning' : 'text-success' }}" />
    </div>

    {{-- Last Updated Info --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500">
            Last updated: <span wire:loading.remove>{{ now()->format('M d, Y H:i:s') }}</span>
            <span wire:loading>Updating...</span>
        </p>
    </div>
</div>
