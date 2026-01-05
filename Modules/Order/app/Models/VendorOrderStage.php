<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorOrderStage extends BaseModel
{
    protected $fillable = [
        'order_id',
        'vendor_id',
        'stage_id',
        'promo_code_share',
        'points_share',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'promo_code_share' => 'decimal:2',
        'points_share' => 'decimal:2',
    ];

    /**
     * Get total discount share (promo + points)
     */
    public function getTotalDiscountShareAttribute(): float
    {
        return ($this->promo_code_share ?? 0) + ($this->points_share ?? 0);
    }

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }

    /**
     * Get the stage
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(OrderStage::class, 'stage_id')->withoutGlobalScope('country_filter');
    }
}
