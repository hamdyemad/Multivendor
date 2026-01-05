<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseModel
{
    use HasFactory, HumanDates;

    protected $fillable = [
        'order_id',
        'paymob_payment_id',
        'paymob_order_id',
        'payment_method',
        'amount_cents',
        'status',
        'transaction_id',
        'payment_data',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'payment_data' => 'array',
    ];

    /**
     * Payment statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Get the order associated with the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if payment is successful
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get amount in currency (not cents)
     */
    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }
}
