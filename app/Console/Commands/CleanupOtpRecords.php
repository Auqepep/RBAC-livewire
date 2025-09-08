<?php

namespace App\Console\Commands;

use App\Models\EmailOtp;
use Illuminate\Console\Command;

class CleanupOtpRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and used OTP records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCount = EmailOtp::where('expires_at', '<', now())->count();
        $usedCount = EmailOtp::where('is_used', true)->count();
        
        // Delete expired OTPs
        EmailOtp::where('expires_at', '<', now())->delete();
        
        // Delete used OTPs older than 1 hour
        EmailOtp::where('is_used', true)
            ->where('updated_at', '<', now()->subHour())
            ->delete();
        
        $this->info("Cleaned up {$expiredCount} expired OTP records");
        $this->info("Cleaned up {$usedCount} used OTP records");
        $this->info('OTP cleanup completed successfully!');
        
        return 0;
    }
}
