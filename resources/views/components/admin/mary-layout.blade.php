<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-mary-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-mary-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-mary-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-mary-nav>

    {{-- MAIN --}}
    <x-mary-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <x-mary-app-brand class="p-5 pt-3">
                <x-slot:brand>
                    <div class="text-xl font-bold text-primary">{{ config('app.name') }}</div>
                    <div class="text-sm text-gray-500">Admin Panel</div>
                </x-slot:brand>
            </x-mary-app-brand>

            {{-- MENU --}}
            <x-mary-menu activate-by-route>
                {{-- Dashboard --}}
                <x-mary-menu-item title="Dashboard" icon="o-squares-2x2" link="{{ route('admin.dashboard') }}" />
                
                {{-- Users Management --}}
                <x-mary-menu-sub title="Users" icon="o-users">
                    <x-mary-menu-item title="All Users" icon="o-user-group" link="{{ route('admin.users.index') }}" />
                    <x-mary-menu-item title="Create User" icon="o-user-plus" link="{{ route('admin.users.create') }}" />
                </x-mary-menu-sub>

                {{-- Groups Management --}}
                <x-mary-menu-sub title="Groups" icon="o-building-office">
                    <x-mary-menu-item title="All Groups" icon="o-rectangle-group" link="{{ route('admin.groups.index') }}" />
                    <x-mary-menu-item title="Create Group" icon="o-plus-circle" link="{{ route('admin.groups.create') }}" />
                    <x-mary-menu-item title="Group Members" icon="o-user-group" link="{{ route('admin.groups.members', 1) }}" />
                </x-mary-menu-sub>

                {{-- Permissions --}}
                <x-mary-menu-item title="Permissions" icon="o-key" link="{{ route('admin.permissions.index') }}" />

                {{-- Group Requests --}}
                <x-mary-menu-item title="Join Requests" icon="o-envelope" link="{{ route('admin.group-join-requests') }}" />

                <x-mary-menu-separator />

                {{-- Main Site --}}
                <x-mary-menu-item title="Main Site" icon="o-arrow-left" link="{{ route('dashboard') }}" external />
            </x-mary-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{-- PAGE HEADER --}}
            @if(isset($header))
                <x-mary-header title="{{ $header }}" separator progress-indicator>
                    <x-slot:middle class="!justify-end">
                        <x-mary-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
                    </x-slot:middle>
                    <x-slot:actions>
                        {{-- User menu --}}
                        <x-mary-dropdown>
                            <x-slot:trigger>
                                <x-mary-button icon="o-user" class="btn-circle">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </x-mary-button>
                            </x-slot:trigger>

                            <x-mary-menu-item title="Profile" icon="o-user" />
                            <x-mary-menu-item title="Settings" icon="o-cog-6-tooth" />
                            <x-mary-menu-separator />
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-mary-menu-item title="Logout" icon="o-power" onclick="this.closest('form').submit();" />
                            </form>
                        </x-mary-dropdown>
                    </x-slot:actions>
                </x-mary-header>
            @endif

            {{-- MAIN CONTENT --}}
            <div class="p-6">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-mary-main>

    {{--  TOAST area --}}
    <x-mary-toast />
</body>
</html>
