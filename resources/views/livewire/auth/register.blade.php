<div class="min-h-screen hero bg-base-200">
    <div class="hero-content flex-col">
        <div class="text-center lg:text-left mb-8">
            <h1 class="text-5xl font-bold">Create your account</h1>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success w-full max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error w-full max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card w-full max-w-md shadow-2xl bg-base-100">
            <div class="card-body">
                @if (!$registrationComplete)
                    <!-- Registration Form -->
                    <form wire:submit.prevent="register" class="space-y-6">
                        <div class="form-control">
                            <label class="label" for="name">
                                <span class="label-text font-medium">Full Name</span>
                            </label>
                            <div class="relative">
                                <input wire:model.live.debounce.300ms="name" 
                                       id="name" 
                                       name="name" 
                                       type="text" 
                                       autocomplete="name"
                                       required
                                       class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}"
                                       placeholder="Enter your full name">
                                @if($loading)
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <span class="loading loading-spinner loading-sm"></span>
                                    </div>
                                @endif
                            </div>
                            @error('name') 
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label" for="email">
                                <span class="label-text font-medium">Email Address</span>
                            </label>
                            <div class="relative">
                                <input wire:model.live.debounce.300ms="email" 
                                       id="email" 
                                       name="email" 
                                       type="email" 
                                       autocomplete="email"
                                       required
                                       class="input input-bordered w-full {{ $errors->has('email') ? 'input-error' : '' }}"
                                       placeholder="Enter your email address">
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

                        <div class="form-control mt-6">
                            <button type="submit" 
                                    class="btn btn-primary"
                                    :class="{ 'btn-disabled': $wire.loading || !$wire.name || !$wire.email }"
                                    :disabled="$wire.loading || !$wire.name || !$wire.email"
                                    wire:loading.attr="disabled">
                                <span wire:loading wire:target="register" class="loading loading-spinner loading-sm"></span>
                                <span wire:loading.remove wire:target="register">Create Account</span>
                                <span wire:loading wire:target="register">Creating Account...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <!-- Registration Complete Message -->
                    <div class="text-center">
                        <div class="avatar">
                            <div class="w-16 rounded-full bg-success text-success-content flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <h3 class="text-lg font-bold mb-2">Check Your Email!</h3>
                        <p class="text-sm mb-4">
                            We've sent a verification link to <span class="font-semibold">{{ $email }}</span>
                        </p>
                        <p class="text-sm text-base-content/70 mb-6">
                            Click the link in your email to verify your account and complete registration.
                        </p>
                        
                        <div class="space-y-3">
                            <button wire:click="resendVerificationEmail" 
                                    :disabled="resending"
                                    class="btn btn-outline w-full"
                                    wire:loading.attr="disabled" 
                                    wire:target="resendVerificationEmail">
                                <span wire:loading wire:target="resendVerificationEmail" class="loading loading-spinner loading-sm"></span>
                                <span wire:loading.remove wire:target="resendVerificationEmail">Resend Verification Email</span>
                                <span wire:loading wire:target="resendVerificationEmail">Sending...</span>
                            </button>
                            
                            <button wire:click="goToLogin" 
                                    class="btn btn-ghost w-full">
                                Already verified? Sign in
                            </button>
                        </div>
                    </div>
                @endif

                @if (!$registrationComplete)
                    <div class="divider"></div>
                    
                    <div class="text-center">
                        <p class="text-sm">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="link link-primary font-medium">
                                Sign in
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Focus management
        Livewire.on('focus-name', () => {
            setTimeout(() => document.getElementById('name')?.focus(), 100);
        });
    });
</script>
