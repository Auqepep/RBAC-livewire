<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Group') }}: {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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

            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('groups.edit', $group->id) }}?tab=details" 
                       class="border-transparent {{ request('tab', 'details') === 'details' ? 'border-yellow-500 text-yellow-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Group Details
                    </a>
                    @can('manageMembers', $group)
                        <a href="{{ route('groups.edit', $group->id) }}?tab=members" 
                           class="border-transparent {{ request('tab') === 'members' ? 'border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Manage Members
                        </a>
                    @endcan
                </nav>
            </div>
