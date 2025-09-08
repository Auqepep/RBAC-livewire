<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white p-8 rounded-lg shadow-md">
            @if ($step === 1)
                <!-- Step 1: Email Form -->
                <form wire:submit.prevent="sendOtp" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input wire:model="email" id="email" name="email" type="email" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Send Login Code
                        </button>
                    </div>
                </form>
            @else
                <!-- Step 2: OTP Verification -->
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Enter Login Code</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        We've sent a 6-digit code to <strong>{{ $email }}</strong>
                    </p>
                </div>

                <form wire:submit.prevent="verifyOtpAndLogin" class="space-y-6">
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP Code</label>
                        <input wire:model="otp" id="otp" name="otp" type="text" maxlength="6" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-center text-2xl tracking-widest"
                               placeholder="000000">
                        @error('otp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign In
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center space-y-2">
                    <button wire:click="resendOtp" class="text-sm text-indigo-600 hover:text-indigo-500">
                        Resend Code
                    </button>
                    <br>
                    <button wire:click="goBack" class="text-sm text-gray-600 hover:text-gray-500">
                        ← Back to Email
                    </button>
                </div>
            @endif

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign up
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
