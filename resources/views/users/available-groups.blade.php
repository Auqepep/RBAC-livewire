<x-user.layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Available Groups') }}
            </h2>
        </div>
    </x-slot>

    <livewire:user.available-groups />
</x-user.layout>
