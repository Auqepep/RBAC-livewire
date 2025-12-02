<x-admin.layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <x-mary-stat
                    title="Total Users"
                    description="Registered users"
                    value="{{ $stats['users'] ?? 0 }}"
                    icon="o-users"
                    color="text-primary" />

                <x-mary-stat
                    title="Active Groups"
                    description="Active groups"
                    value="{{ $stats['active_groups'] ?? 0 }}"
                    icon="o-building-office"
                    color="text-success" />

                <x-mary-stat
                    title="Total Roles"
                    description="Available roles"
                    value="{{ $stats['roles'] ?? 0 }}"
                    icon="o-identification"
                    color="text-warning" />

                <x-mary-stat
                    title="Group Assignments"
                    description="User-group assignments"
                    value="{{ $stats['total_assignments'] ?? 0 }}"
                    icon="o-link"
                    color="text-info" />
            </div>

            <!-- Quick Actions and Management Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Quick Actions Card --}}
                <x-mary-card title="Quick Actions" subtitle="Common administrative tasks">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-mary-button 
                            label="Create User" 
                            icon="o-user-plus" 
                            class="btn-primary"
                            link="{{ route('admin.users.create') }}" />
                        
                        <x-mary-button 
                            label="Create Group" 
                            icon="o-plus-circle" 
                            class="btn-secondary"
                            link="{{ route('admin.groups.create') }}" />
                        
                        <x-mary-button 
                            label="Manage Groups" 
                            icon="o-rectangle-group" 
                            class="btn-accent"
                            link="{{ route('admin.groups.index') }}" />
                        
                        <x-mary-button 
                            label="View Permissions" 
                            icon="o-key" 
                            class="btn-outline"
                            link="{{ route('admin.permissions.index') }}" />
                    </div>
                    
                    <x-mary-alert class="mt-4" icon="o-information-circle">
                        <strong>Group-Centric Roles:</strong> Roles are now managed within groups. 
                        To create or manage roles, go to a specific group's management page.
                    </x-mary-alert>
                </x-mary-card>

                {{-- Additional Quick Links --}}
                <div class="space-y-6">
                    <x-mary-card title="User Management" subtitle="Manage system users">
                        <div class="space-y-2">
                            <x-mary-button 
                                label="View All Users" 
                                icon="o-user-group" 
                                class="btn-outline btn-sm w-full"
                                link="{{ route('admin.users.index') }}" />
                                
                            <x-mary-button 
                                label="User Directory" 
                                icon="o-book-open" 
                                class="btn-ghost btn-sm w-full"
                                link="{{ route('users.index') }}" />
                        </div>
                    </x-mary-card>

                    <x-mary-card title="Group Management" subtitle="Organize users into groups">
                        <div class="space-y-2">
                            <x-mary-button 
                                label="All Groups" 
                                icon="o-rectangle-group" 
                                class="btn-outline btn-sm w-full"
                                link="{{ route('admin.groups.index') }}" />
                        </div>
                    </x-mary-card>
                </div>
            </div>

            <!-- System Settings -->
            <x-mary-card title="System Settings" subtitle="Configure system">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <x-mary-button 
                        label="Permissions" 
                        icon="o-key" 
                        class="btn-outline btn-sm"
                        link="{{ route('admin.permissions.index') }}" />
                        
                    <x-mary-button 
                        label="System Logs" 
                        icon="o-document-text" 
                        class="btn-ghost btn-sm disabled"
                        disabled />
                </div>
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
