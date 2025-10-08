<?php

namespace App\Livewire\Components;

use App\Models\EmailOtp;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\Attributes\Validate;

class OtpVerification extends Component
{
    #[Validate('required|string|size:6')]
    public $otp = '';
    
    public $email;
    public $type;
    public $title = 'Enter Verification Code';
    public $subtitle = '';
    public $loading = false;
    public $resending = false;
    public $countdown = 0;
    public $autoVerifying = false;

    public function mount($email, $type = 'login', $title = null, $subtitle = null)
    {
        $this->email = $email;
        $this->type = $type;
        $this->title = $title ?? 'Enter Verification Code';
        $this->subtitle = $subtitle ?? "We've sent a 6-digit code to {$email}";
        
        $this->dispatch('focus-otp');
    }

    public function updatedOtp()
    {
        $this->validateOnly('otp');
        
        // Auto-verify when 6 digits are entered, but only if not already loading
        if (strlen($this->otp) === 6 && !$this->loading) {
            $this->autoVerifying = true;
            $this->verifyOtp();
        }
    }

    public function verifyOtp()
    {
        // Prevent double submission
        if ($this->loading) {
            return;
        }
        
        $this->loading = true;
        $this->validate();

        try {
            if (EmailOtp::verifyOtp($this->email, $this->otp, $this->type)) {
                $this->dispatch('otp-verified', [
                    'email' => $this->email,
                    'type' => $this->type
                ]);
            } else {
                session()->flash('error', 'Invalid or expired OTP. Please try again.');
                $this->reset('otp');
                $this->dispatch('focus-otp');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred. Please try again.');
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
            $otpRecord = EmailOtp::generateOtp($this->email, $this->type);
            
            Mail::to($this->email)->send(new SendOtpMail($otpRecord->otp, $this->type));
            
            $this->startCountdown();
            session()->flash('message', 'New OTP sent to your email.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send OTP. Please try again.');
            \Log::error('OTP resend error: ' . $e->getMessage());
        } finally {
            $this->resending = false;
        }
    }

    public function goBack()
    {
        $this->dispatch('go-back');
    }

    private function startCountdown()
    {
        $this->countdown = 60; // 60 seconds
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
        return view('livewire.components.otp-verification');
    }
}
