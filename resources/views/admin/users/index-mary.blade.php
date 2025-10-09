<x-admin.mary-layout>
    <x-slot name="header">Users Management</x-slot>

    <div class="space-y-6">
        {{-- Header Actions --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Users</h1>
                <p class="text-gray-600">Manage system users and their group assignments</p>
            </div>
            <x-mary-button 
                label="Create User" 
                icon="o-user-plus" 
                class="btn-primary"
                link="{{ route('admin.users.create') }}" />
        </div>

        {{-- Users Table --}}
        <x-mary-card>
            <livewire:admin.users-table-mary />
        </x-mary-card>
    </div>
</x-admin.mary-layout>
