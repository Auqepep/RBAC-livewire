<div class="min-h-screen hero bg-base-200 px-4">
    <div class="hero-content flex-col w-full max-w-md">
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold">{{ __('Sign in to your account') }}</h1>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm sm:text-base">{{ __(session('message')) }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm sm:text-base">{{ __(session('error')) }}</span>
            </div>
        @endif

        <div class="card w-full shadow-2xl bg-base-100">
            <div class="card-body p-4 sm:p-8">
                @if ($step === 1)
                    <!-- Step 1: Email Form -->
                    <form wire:submit.prevent="sendOtp" class="space-y-6">
                        <div class="form-control">
                            <label class="label" for="email">
                                <span class="label-text font-medium">{{ __('Email Address') }}</span>
                            </label>
                            <div class="relative">
                                <input wire:model.defer="email" 
                                       id="email" 
                                       name="email" 
                                       type="email" 
                                       autocomplete="email"
                                       required
                                       class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}"
                                       placeholder="{{ __('Enter your email address') }}">
                                @if($loading)
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <span class="loading loading-spinner loading-sm"></span>
                                    </div>
                                @endif
                            </div>
                            @error('email') 
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control mt-6 text-center">
                            <button type="submit" 
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled">
                                <span wire:loading wire:target="sendOtp" class="loading loading-spinner loading-sm"></span>
                                <span wire:loading.remove wire:target="sendOtp">{{ __('Send Login Code') }}</span>
                                <span wire:loading wire:target="sendOtp">{{ __('Sending...') }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <!-- Step 2: OTP Verification -->
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold">{{ __('Enter Login Code') }}</h3>
                        <p class="py-2 text-base-content/70">
                            {{ __("We've sent a 6-digit code to") }} <span class="font-semibold">{{ $email }}</span>
                        </p>
                    </div>

                    <form wire:submit.prevent="verifyOtpAndLogin" class="space-y-6" data-auto-submit>
                        <div class="form-control">
                            <label class="label" for="otp">
                                <span class="label-text font-medium">{{ __('Enter OTP Code') }}</span>
                            </label>
                            <div class="relative">
                                <input wire:model.defer="otp" 
                                       id="otp" 
                                       name="otp" 
                                       type="text" 
                                       maxlength="6"
                                       autocomplete="one-time-code"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
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

                        <div class="form-control mt-6 flex justify-center">
                            <button type="submit" 
                                    class="btn btn-primary w-64"
                                    wire:loading.attr="disabled">
                                <span wire:loading wire:target="verifyOtpAndLogin" class="loading loading-spinner loading-sm"></span>
                                <span wire:loading.remove wire:target="verifyOtpAndLogin">{{ __('Sign In') }}</span>
                                <span wire:loading wire:target="verifyOtpAndLogin">{{ __('Verifying...') }}</span>
                            </button>
                        </div>
                    </form>

                    <div class="divider"></div>

                    <div class="text-center space-y-3">
                        @if($countdown > 0)
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('Resend code in') }} {{ $countdown }} {{ __('seconds') }}</span>
                            </div>
                        @else
                            <button wire:click="resendOtp" 
                                    class="btn btn-outline btn-sm"
                                    wire:loading.attr="disabled" 
                                    wire:target="resendOtp">
                                <span wire:loading wire:target="resendOtp" class="loading loading-spinner loading-xs"></span>
                                <span wire:loading.remove wire:target="resendOtp">{{ __('Resend Code') }}</span>
                                <span wire:loading wire:target="resendOtp">{{ __('Sending...') }}</span>
                            </button>
                        @endif
                        
                        <button wire:click="goBack" class="btn btn-ghost btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ __('Back to Email') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@vite(['resources/js/auth/otp-common.js'])