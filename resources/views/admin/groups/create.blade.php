<x-admin.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Group') }}
            </h2>
            <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Groups
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <livewire:admin.create-group />
        </div>
    </div>
</x-admin.layout>
