<?php

namespace Modules\Order\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderExtraFeeDiscount extends Model
{
    use HasFactory;

    protected $table = 'order_extra_fees_discounts';

    protected $fillable = [
        'order_id',
        'vendor_id',
        'cost',
        'reason',
        'type',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the vendor.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter discounts.
     */
    public function scopeDiscounts($query)
    {
        return $query->where('type', 'discount');
    }

    /**
     * Scope to filter fees.
     */
    public function scopeFees($query)
    {
        return $query->where('type', 'fee');
    }
}
