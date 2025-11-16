<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <!-- Theme Selector -->
    <div class="navbar bg-base-100 shadow-lg">
        <div class="navbar-start">
            <a class="btn btn-ghost text-xl">{{ config('app.name', 'Laravel') }}</a>
        </div>
    </div>
    @livewire('auth.login')
    
    @livewireScripts

</body>
</html>
