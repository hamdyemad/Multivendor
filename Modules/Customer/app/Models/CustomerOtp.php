<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOtp extends Model
{
    protected $table = 'customer_otps';

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Scope to get valid otps
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                     ->whereNull('verified_at');
    }

    /**
     * Scope for email verification
     */
    public function scopeEmailVerification($query)
    {
        return $query->where('type', 'email_verification');
    }

    /**
     * Scope for password reset
     */
    public function scopePasswordReset($query)
    {
        return $query->where('type', 'password_reset');
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified()
    {
        return $this->update(['verified_at' => now()]);
    }

    /**
     * Generate a unique verification token
     */
    public static function generateVerificationToken(): string
    {
        return \Illuminate\Support\Str::random(64);
    }
}
