<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">
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
        <div class="navbar-end">
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5H9M21 9H9M21 13H9M21 17H9" />
                    </svg>
                    Theme
                </div>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
                    <li><a onclick="changeTheme('light')">Light</a></li>
                    <li><a onclick="changeTheme('dark')">Dark</a></li>
                    <li><a onclick="changeTheme('corporate')">Corporate</a></li>
                    <li><a onclick="changeTheme('synthwave')">Synthwave</a></li>
                    <li><a onclick="changeTheme('cyberpunk')">Cyberpunk</a></li>
                    <li><a onclick="changeTheme('valentine')">Valentine</a></li>
                    <li><a onclick="changeTheme('aqua')">Aqua</a></li>
                </ul>
            </div>
        </div>
    </div>

    @livewire('auth.login')
    
    @livewireScripts
    
    <script>
        function changeTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        }

        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'corporate';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</body>
</html>
