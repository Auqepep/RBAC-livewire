<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind v4 Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-8">
            <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">
                Tailwind CSS v4.1.13 Test
            </div>
            <h1 class="block mt-1 text-lg leading-tight font-medium text-black">
                Laravel 12 + Tailwind v4 ✨
            </h1>
            <p class="mt-2 text-gray-500">
                This page tests that Tailwind CSS v4.1.13 is working correctly with Laravel 12.
            </p>
            
            <!-- Test modern Tailwind v4 features -->
            <div class="mt-4 space-y-2">
                <div class="bg-gradient-to-r from-purple-400 to-pink-400 text-white p-4 rounded-lg">
                    Gradient background (built-in v4 feature)
                </div>
                
                <div class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg transition-colors duration-200 cursor-pointer">
                    Hover effect with transitions
                </div>
                
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="bg-green-100 p-4 rounded text-green-800">
                        Grid item 1
                    </div>
                    <div class="bg-yellow-100 p-4 rounded text-yellow-800">
                        Grid item 2
                    </div>
                </div>
            </div>
            
            <!-- Test form elements -->
            <form class="mt-6 space-y-4">
                <input type="text" placeholder="Test input" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="button" 
                        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
                    Test Button
                </button>
            </form>
        </div>
    </div>
    
    <div class="mt-8 text-center text-gray-600">
        <p>Laravel {{ app()->version() }}</p>
        <p>PHP {{ PHP_VERSION }}</p>
        <p>Node.js: Check console for version</p>
    </div>
    
    <script>
        console.log('Node.js version:', process?.versions?.node || 'N/A');
        console.log('Tailwind CSS v4 loaded successfully!');
    </script>
</body>
</html>
