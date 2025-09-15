<?php

namespace App\Livewire\Components;

use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\Attributes\Validate;

class OtpManager extends Component
{
    public $mode = 'send'; // 'send' or 'verify'
    public $type = 'login'; // 'login', 'register', 'password_reset', etc.
    public $title = '';
    public $subtitle = '';
    
    #[Validate('required|string|email')]
    public $email = '';
    
    #[Validate('required|string|size:6')]
    public $otp = '';
    
    public $loading = false;
    public $resending = false;
    public $countdown = 0;
    public $autoVerify = true; // Auto-verify when 6 digits entered

    protected $listeners = [
        'switchToVerifyMode' => 'switchToVerifyMode',
        'switchToSendMode' => 'switchToSendMode',
    ];

    public function mount($type = 'login', $email = '', $autoVerify = true)
    {
        $this->type = $type;
        $this->email = $email;
        $this->autoVerify = $autoVerify;
        $this->setTexts();
        
        if ($email) {
            $this->mode = 'verify';
            $this->dispatch('focus-otp');
        } else {
            $this->dispatch('focus-email');
        }
    }

    public function updatedEmail()
    {
        $this->validateOnly('email');
    }

    public function updatedOtp()
    {
        $this->validateOnly('otp');
        
        if ($this->autoVerify && strlen($this->otp) === 6) {
            $this->verifyOtp();
        }
    }

    public function sendOtp()
    {
        $this->loading = true;
        $this->validate(['email' => 'required|string|email']);

        try {
            // Check if user exists for login type
            if ($this->type === 'login') {
                $user = User::where('email', $this->email)->first();
                
                if (!$user) {
                    session()->flash('error', 'No account found with this email address.');
                    $this->loading = false;
                    return;
                }

                if (!$user->hasVerifiedEmail()) {
                    session()->flash('error', 'Please verify your email address first.');
                    $this->loading = false;
                    return;
                }
            }

            // Generate and send OTP
            $otpRecord = EmailOtp::generateOtp($this->email, $this->type);
            
            Mail::to($this->email)->send(new SendOtpMail($otpRecord->otp, $this->type));
            
            $this->switchToVerifyMode();
            $this->startCountdown();
            
            session()->flash('message', 'OTP sent to your email. Please check your inbox.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send OTP. Please try again.');
            \Log::error('OTP send error: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function verifyOtp()
    {
        $this->loading = true;
        $this->validate(['otp' => 'required|string|size:6']);

        try {
            if (EmailOtp::verifyOtp($this->email, $this->otp, $this->type)) {
                EmailOtp::cleanupExpiredOtps();
                
                $this->dispatch('otp-verified', [
                    'email' => $this->email,
                    'type' => $this->type,
                    'otp' => $this->otp
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

    public function switchToVerifyMode()
    {
        $this->mode = 'verify';
        $this->setTexts();
        $this->reset('otp');
        $this->dispatch('focus-otp');
    }

    public function switchToSendMode()
    {
        $this->mode = 'send';
        $this->setTexts();
        $this->reset(['otp', 'countdown']);
        $this->dispatch('focus-email');
    }

    private function setTexts()
    {
        $texts = [
            'login' => [
                'send' => [
                    'title' => 'Sign in to your account',
                    'subtitle' => 'Enter your email to receive a login code'
                ],
                'verify' => [
                    'title' => 'Enter Login Code',
                    'subtitle' => "We've sent a 6-digit code to {$this->email}"
                ]
            ],
            'register' => [
                'send' => [
                    'title' => 'Verify your email',
                    'subtitle' => 'Enter your email to receive a verification code'
                ],
                'verify' => [
                    'title' => 'Enter Verification Code',
                    'subtitle' => "We've sent a 6-digit code to {$this->email}"
                ]
            ],
            'password_reset' => [
                'send' => [
                    'title' => 'Reset your password',
                    'subtitle' => 'Enter your email to receive a reset code'
                ],
                'verify' => [
                    'title' => 'Enter Reset Code',
                    'subtitle' => "We've sent a 6-digit code to {$this->email}"
                ]
            ]
        ];

        $typeTexts = $texts[$this->type] ?? $texts['login'];
        $modeTexts = $typeTexts[$this->mode] ?? $typeTexts['send'];

        $this->title = $modeTexts['title'];
        $this->subtitle = $modeTexts['subtitle'];
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
        return view('livewire.components.otp-manager');
    }
}
