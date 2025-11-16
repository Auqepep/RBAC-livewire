<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-semibold text-gray-800">
                                {{ config('app.name') }} - Admin
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-blue-600 focus:outline-none focus:text-blue-600 focus:border-blue-300 transition duration-150 ease-in-out">
                                Dashboard
                            </a>
                            
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-blue-600 focus:outline-none focus:text-blue-600 focus:border-blue-300 transition duration-150 ease-in-out">
                                Users
                            </a>
                            
                            <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.groups.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-blue-600 focus:outline-none focus:text-blue-600 focus:border-blue-300 transition duration-150 ease-in-out">
                                Groups
                            </a>
                            
                            @can('manage-permissions')
                            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.permissions.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-blue-600 focus:outline-none focus:text-blue-600 focus:border-blue-300 transition duration-150 ease-in-out">
                                Permissions
                            </a>
                            @endcan
                            
                            <a href="{{ route('test.permissions') }}" target="_blank" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-orange-500 text-sm font-medium leading-5 hover:text-orange-600 focus:outline-none focus:text-orange-600 focus:border-orange-300 transition duration-150 ease-in-out">
                                🧪 Test Permissions
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <div class="relative">
                                <button type="button" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="hidden md:block mr-2 text-gray-700">{{ Auth::user()->name }}</span>
                                    <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}" class="ml-4" onsubmit="handleLogout(event)" id="logout-form" data-home-url="{{ route('home') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('message'))
                    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
    
    @vite(['resources/js/layout/logout-handler.js'])
</body>
</html>
