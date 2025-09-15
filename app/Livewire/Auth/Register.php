<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Mail\VerifyEmailMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Register extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';
    
    #[Validate('required|string|email|max:255|unique:users')]
    public $email = '';
    
    public $registrationComplete = false;
    public $loading = false;
    public $resending = false;

    protected $messages = [
        'name.required' => 'Please enter your full name.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email address is already registered.',
    ];

    public function mount()
    {
        // Auto-focus on name input when component loads
        $this->dispatch('focus-name');
    }

    public function updatedName()
    {
        $this->validateOnly('name');
    }

    public function updatedEmail()
    {
        $this->validateOnly('email');
    }

    public function register()
    {
        $this->loading = true;
        $this->validate();

        try {
            // Create user (email will be unverified initially)
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'email_verified_at' => null, // Will be set when they click the verification link
            ]);

            event(new Registered($user));

            // Send verification email
            Mail::to($user->email)->send(new VerifyEmailMail($user));

            $this->registrationComplete = true;
            session()->flash('message', 'Registration successful! Please check your email and click the verification link to activate your account.');

        } catch (\Exception $e) {
            session()->flash('error', 'Registration failed. Please try again.');
            \Log::error('Registration error: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function resendVerificationEmail()
    {
        $this->resending = true;

        try {
            if ($this->email) {
                $user = User::where('email', $this->email)->first();
                if ($user && !$user->hasVerifiedEmail()) {
                    Mail::to($user->email)->send(new VerifyEmailMail($user));
                    session()->flash('message', 'Verification email sent! Please check your inbox.');
                } else {
                    session()->flash('error', 'User not found or already verified.');
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send verification email. Please try again.');
            \Log::error('Resend verification error: ' . $e->getMessage());
        } finally {
            $this->resending = false;
        }
    }

    public function goToLogin()
    {
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
