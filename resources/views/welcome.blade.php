<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AEROFOOD Gateway</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="relative min-h-screen bg-white overflow-hidden" style="background-image: url('{{ asset('background.png') }}'); background-size: 100% 100%; background-position: center; background-repeat: no-repeat;">
            <!-- Main Content -->
            <div class="relative min-h-screen flex flex-col items-center justify-center px-6">
                <div class="text-center">
                    <!-- AEROFOOD Text -->
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-green-700 mb-4 sm:mb-6 md:mb-8 tracking-wider">AEROFOOD</h1>
                    
                    <!-- Logo -->
                    <div class="mb-4 sm:mb-6 md:mb-8 flex justify-center">
                        <img src="{{ asset('logo.svg') }}" alt="AEROFOOD Logo" class="w-32 h-32 sm:w-40 sm:h-40 md:w-48 md:h-48 lg:w-56 lg:h-56">
                    </div>
                    
                    <!-- GATEWAY Text -->
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-green-700 mb-6 sm:mb-8 md:mb-12 tracking-wide">GATEWAY</h2>
                    
                    <!-- Login Button -->
                    <div class="mb-4 sm:mb-6">
                        <a href="{{ route('login') }}" 
                           class="inline-block bg-gray-900 text-white text-base sm:text-lg md:text-xl font-medium px-12 sm:px-16 md:px-20 py-3 sm:py-3.5 md:py-4 rounded-full hover:bg-gray-800 transition-colors duration-200">
                            Login
                        </a>
                    </div>
                    
                    <!-- Indonesian Text -->
                    <p class="text-gray-700 text-sm sm:text-base md:text-lg">
                        Silahkan login dengan akun<br>yang telah disediakan
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
