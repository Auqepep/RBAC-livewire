<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Login extends Component
{
    public $step = 1; // 1: Enter email, 2: Verify OTP
    public $email = '';
    public $otp = '';
    public $otp_sent = false;

    protected function rules()
    {
        if ($this->step === 1) {
            return [
                'email' => 'required|string|email|exists:users,email',
            ];
        }

        return [
            'otp' => 'required|string|size:6',
        ];
    }

    protected $messages = [
        'email.exists' => 'No account found with this email address.',
        'otp.required' => 'Please enter the OTP code.',
        'otp.size' => 'OTP must be 6 digits.',
    ];

    public function sendOtp()
    {
        $this->validate();

        // Check if user exists and is verified
        $user = User::where('email', $this->email)->first();
        
        if (!$user->hasVerifiedEmail()) {
            session()->flash('error', 'Please verify your email address first. Check your email for verification instructions.');
            return;
        }

        // Generate and send OTP
        $otpRecord = EmailOtp::generateOtp($this->email, 'login');
        
        try {
            Mail::to($this->email)->send(new SendOtpMail($otpRecord->otp, 'login'));
            $this->otp_sent = true;
            $this->step = 2;
            session()->flash('message', 'OTP sent to your email. Please check your inbox.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send OTP. Please try again.');
        }
    }

    public function verifyOtpAndLogin()
    {
        $this->validate();

        if (EmailOtp::verifyOtp($this->email, $this->otp, 'login')) {
            $user = User::where('email', $this->email)->first();
            auth()->login($user);
            
            // Clean up any remaining OTP records for this email
            EmailOtp::cleanupExpiredOtps();

            session()->flash('message', 'Login successful!');
            return redirect()->intended('/dashboard');
        } else {
            session()->flash('error', 'Invalid or expired OTP. Please try again.');
            $this->otp = '';
        }
    }

    public function resendOtp()
    {
        if ($this->email) {
            $otpRecord = EmailOtp::generateOtp($this->email, 'login');
            
            try {
                Mail::to($this->email)->send(new SendOtpMail($otpRecord->otp, 'login'));
                session()->flash('message', 'New OTP sent to your email.');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to send OTP. Please try again.');
            }
        }
    }

    public function goBack()
    {
        $this->step = 1;
        $this->otp = '';
        $this->otp_sent = false;
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
