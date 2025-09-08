<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailOtp extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'type',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public static function generateOtp($email, $type = 'login')
    {
        // Clean up expired OTPs first
        self::cleanupExpiredOtps();
        
        // Delete all existing OTPs for this email and type (both used and unused)
        self::where('email', $email)
            ->where('type', $type)
            ->delete();

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'email' => $email,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }

    public function isValid()
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    public function markAsUsed()
    {
        $this->update(['is_used' => true]);
        
        // Delete this OTP record after marking as used
        $this->delete();
    }

    public static function verifyOtp($email, $otp, $type = 'login')
    {
        // Clean up expired OTPs first
        self::cleanupExpiredOtps();
        
        $otpRecord = self::where('email', $email)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('is_used', false)
            ->first();

        if ($otpRecord && $otpRecord->isValid()) {
            $otpRecord->markAsUsed(); // This will also delete the record
            return true;
        }

        return false;
    }

    /**
     * Clean up expired OTP records
     */
    public static function cleanupExpiredOtps()
    {
        self::where('expires_at', '<', Carbon::now())->delete();
    }

    /**
     * Clean up all used OTP records for a specific email and type
     */
    public static function cleanupUsedOtps($email, $type)
    {
        self::where('email', $email)
            ->where('type', $type)
            ->where('is_used', true)
            ->delete();
    }
}
