<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        @if (session()->has('message'))
            <div class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="text-center mb-6">
            <h2 class="card-title justify-center text-2xl mb-2">{{ $title }}</h2>
            <p class="text-base-content/70">{{ $subtitle }}</p>
        </div>

        <form wire:submit.prevent="verifyOtp" class="space-y-6">
            <div class="form-control">
                <label class="label" for="otp">
                    <span class="label-text font-medium">Enter OTP Code</span>
                </label>
                <div class="relative">
                    <input wire:model.live="otp" 
                           id="otp" 
                           name="otp" 
                           type="text" 
                           maxlength="6"
                           autocomplete="one-time-code"
                           class="input input-bordered w-full text-center text-2xl tracking-widest {{ $errors->has('otp') ? 'input-error' : '' }}"
                           placeholder="000000">
                    @if($loading)
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="loading loading-spinner loading-sm"></span>
                        </div>
                    @endif
                </div>
                @error('otp') 
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <button type="submit" 
                    class="btn btn-primary w-full"
                    :class="{ 'btn-disabled': $wire.loading || $wire.otp.length !== 6 }"
                    :disabled="$wire.loading || $wire.otp.length !== 6"
                    wire:loading.attr="disabled">
                <span wire:loading wire:target="verifyOtp" class="loading loading-spinner loading-sm"></span>
                <span wire:loading.remove wire:target="verifyOtp">Verify Code</span>
                <span wire:loading wire:target="verifyOtp">Verifying...</span>
            </button>
        </form>

        <div class="divider"></div>

        <div class="text-center space-y-3">
            @if($countdown > 0)
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Resend code in {{ $countdown }} seconds</span>
                </div>
            @else
                <button wire:click="resendOtp" 
                        @if($resending) disabled @endif
                        class="btn btn-outline btn-sm {{ $resending ? 'btn-disabled' : '' }}"
                        wire:loading.attr="disabled" 
                        wire:target="resendOtp">
                    <span wire:loading wire:target="resendOtp" class="loading loading-spinner loading-xs"></span>
                    <span wire:loading.remove wire:target="resendOtp">Resend Code</span>
                    <span wire:loading wire:target="resendOtp">Sending...</span>
                </button>
            @endif
            
            <button wire:click="goBack" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Focus management
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
