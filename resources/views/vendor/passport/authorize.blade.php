<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authorization Request - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="background-color: var(--bg-secondary);">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- App Logo -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">{{ config('app.name') }}</h1>
                <p class="text-sm text-gray-600 mt-2">Authorization Request</p>
            </div>

            <!-- Client Information -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">
                    <strong>{{ $client->name }}</strong> is requesting permission to access your account
                </h2>
                
                @if(count($scopes) > 0)
                    <div class="mt-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2">This application will be able to:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($scopes as $scope)
                                <li class="text-sm text-gray-600">{{ $scope->description }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- User Information -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-sm text-gray-700">
                    You are logged in as <strong>{{ $user->name }}</strong> ({{ $user->email }})
                </p>
            </div>

            <!-- Authorization Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Authorize Button -->
                <form method="post" action="{{ route('passport.authorizations.approve') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    
                    <button type="submit" class="w-full btn btn-success">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Authorize
                    </button>
                </form>

                <!-- Cancel Button -->
                <form method="post" action="{{ route('passport.authorizations.deny') }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    
                    <button type="submit" class="w-full btn btn-error btn-outline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                </form>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded flex gap-2">
                <x-mary-icon name="o-exclamation-triangle" class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5" />
                <p class="text-xs text-yellow-800">
                    <strong>Security Notice:</strong> Only authorize applications you trust. 
                    This will give <strong>{{ $client->name }}</strong> access to your account data.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Powered by {{ config('app.name') }} OAuth 2.0
            </p>
        </div>
    </div>
</body>
</html>
