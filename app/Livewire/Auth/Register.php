<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Mail\VerifyEmailMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Livewire\Component;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $registrationComplete = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ];
    }

    public function register()
    {
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
        }
    }

    public function resendVerificationEmail()
    {
        if ($this->email) {
            $user = User::where('email', $this->email)->first();
            if ($user && !$user->hasVerifiedEmail()) {
                try {
                    Mail::to($user->email)->send(new VerifyEmailMail($user));
                    session()->flash('message', 'Verification email sent! Please check your inbox.');
                } catch (\Exception $e) {
                    session()->flash('error', 'Failed to send verification email. Please try again.');
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
