<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Login extends Component
{
    public $step = 1; // 1: Enter email, 2: Verify OTP
    
    #[Validate('required|string|email|exists:users,email')]
    public $email = '';
    
    #[Validate('required|string|size:6')]
    public $otp = '';
    
    public $otp_sent = false;
    public $loading = false;
    public $resending = false;
    public $countdown = 0;
    public $autoVerifying = false;
    public $otpLength = 0; // Track OTP length to prevent multiple updates

    protected function messages()
    {
        return [
            'email.exists' => __('No account found with this email address.'),
            'otp.required' => __('Please enter the OTP code.'),
            'otp.size' => __('OTP must be 6 digits.'),
        ];
    }

    public function mount()
    {
        // Auto-focus on email input when component loads
        $this->dispatch('focus-email');
    }

    // Removed updatedEmail() to prevent database queries on every keystroke
    // Validation will happen when the form is submitted

    // Removed updatedOtp() - auto-submit is now handled by JavaScript to prevent network spam

    public function sendOtp()
    {
        $this->loading = true;
        $this->validate(['email' => 'required|string|email|exists:users,email']);

        try {
            // Check if user exists and is verified
            $user = User::where('email', $this->email)->first();
            
            if (!$user->hasVerifiedEmail()) {
                session()->flash('error', __('Please verify your email address first. Check your email for verification instructions.'));
                $this->loading = false;
                return;
            }

            // Generate and send OTP
            $otpRecord = EmailOtp::generateOtp($this->email, 'login');
            
            Mail::to($this->email)->send(new SendOtpMail($otpRecord->otp, 'login'));
            
            $this->otp_sent = true;
            $this->step = 2;
            $this->startCountdown();
            
            session()->flash('message', __('OTP sent to your email. Please check your inbox.'));
            $this->dispatch('focus-otp');
            
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to send OTP. Please try again.'));
            \Log::error('OTP send error: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function verifyOtpAndLogin()
    {
        // Prevent double submission
        if ($this->loading) {
            return;
        }
        
        $this->loading = true;
        $this->validate(['otp' => 'required|string|size:6']);

        try {
            if (EmailOtp::verifyOtp($this->email, $this->otp, 'login')) {
                $user = User::where('email', $this->email)->first();
                auth()->login($user);
                
                // Clean up any remaining OTP records for this email
                EmailOtp::cleanupExpiredOtps();

                session()->flash('message', __('Login successful!'));
                return redirect()->intended('/dashboard');
            } else {
                session()->flash('error', __('Invalid or expired OTP. Please try again.'));
                $this->reset('otp');
                $this->dispatch('focus-otp');
            }
        } catch (\Exception $e) {
            session()->flash('error', __('An error occurred. Please try again.'));
            \Log::error('OTP verification error: ' . $e->getMessage());
        } finally {
            $this->loading = false;
            $this->autoVerifying = false;
        }
    }

    public function resendOtp()
    {
        if ($this->countdown > 0) {
            return; // Prevent spam
        }

        $this->resending = true;

        try {
            if ($this->email) {
                $otpRecord = EmailOtp::generateOtp($this->email, 'login');
                
                Mail::to($this->email)->send(new SendOtpMail($otpRecord->otp, 'login'));
                
                $this->startCountdown();
                session()->flash('message', __('New OTP sent to your email.'));
            }
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to send OTP. Please try again.'));
        } finally {
            $this->resending = false;
        }
    }

    public function goBack()
    {
        $this->step = 1;
        $this->reset(['otp', 'otp_sent', 'countdown']);
        $this->dispatch('focus-email');
    }

    private function startCountdown()
    {
        $this->countdown = 60; // 60 seconds
        // This will be handled by JavaScript on the frontend
        $this->dispatch('start-countdown');
    }

    public function decrementCountdown()
    {
        if ($this->countdown > 0) {
            $this->countdown--;
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
