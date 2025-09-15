<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">{{ $title }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ $subtitle }}</p>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($mode === 'send')
        <!-- Send OTP Mode -->
        <form wire:submit.prevent="sendOtp" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <div class="mt-1 relative">
                    <input wire:model.live.debounce.300ms="email" 
                           id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email"
                           required
                           class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}"
                           placeholder="Enter your email address">
                    @if($loading)
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                @error('email') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" 
                        :disabled="loading"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    @if($loading)
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending Code...
                    @else
                        Send Verification Code
                    @endif
                </button>
            </div>
        </form>
    @else
        <!-- Verify OTP Mode -->
        <form wire:submit.prevent="verifyOtp" class="space-y-6">
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">Verification Code</label>
                <div class="mt-1 relative">
                    <input wire:model.live="otp" 
                           id="otp" 
                           name="otp" 
                           type="text" 
                           maxlength="6"
                           autocomplete="one-time-code"
                           class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-center text-2xl tracking-widest {{ $errors->has('otp') ? 'border-red-300' : 'border-gray-300' }}"
                           placeholder="000000">
                    @if($loading)
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                @error('otp') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" 
                        :disabled="loading"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    @if($loading)
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Verifying...
                    @else
                        Verify Code
                    @endif
                </button>
            </div>
        </form>

        <div class="text-center space-y-2">
            @if($countdown > 0)
                <p class="text-sm text-gray-500">
                    Resend code in {{ $countdown }} seconds
                </p>
            @else
                <button wire:click="resendOtp" 
                        :disabled="resending"
                        class="text-sm text-indigo-600 hover:text-indigo-500 disabled:opacity-50">
                    @if($resending)
                        Sending...
                    @else
                        Resend Code
                    @endif
                </button>
            @endif
            <br>
            <button wire:click="switchToSendMode" class="text-sm text-gray-600 hover:text-gray-500">
                ← Change Email Address
            </button>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Focus management
        Livewire.on('focus-email', () => {
            setTimeout(() => document.getElementById('email')?.focus(), 100);
        });
        
        Livewire.on('focus-otp', () => {
            setTimeout(() => document.getElementById('otp')?.focus(), 100);
        });

        // Countdown timer
        let countdownInterval;
        
        Livewire.on('start-countdown', () => {
            if (countdownInterval) clearInterval(countdownInterval);
            
            countdownInterval = setInterval(() => {
                @this.call('decrementCountdown');
            }, 1000);
        });

        // Auto-format OTP input
        document.addEventListener('input', (e) => {
            if (e.target.id === 'otp') {
                e.target.value = e.target.value.replace(/\D/g, '');
            }
        });
    });
</script>
