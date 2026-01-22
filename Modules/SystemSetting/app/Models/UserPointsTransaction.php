<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\User;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPointsTransaction extends Model
{
    use Translation, SoftDeletes, HumanDates;

    protected $table = 'user_points_transactions';
    protected $guarded = [];

    protected $casts = [
        'points' => 'decimal:2',
        'points_per_currency' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the polymorphic transactionable model
     */
    public function transactionable()
    {
        return $this->morphTo();
    }


    public function getDescriptionAttribute() {
        return $this->getTranslation('description', app()->getLocale());
    }

    /**
     * Scope to get earned points
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope to get redeemed points
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    /**
     * Scope to get expired points
     */
    public function scopeExpired($query)
    {
        return $query->where('type', 'expired');
    }

    /**
     * Scope to get adjusted points
     */
    public function scopeAdjusted($query)
    {
        return $query->where('type', 'adjusted');
    }

    /**
     * Scope to get active transactions (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Check if transaction is expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isPast();
    }
}
