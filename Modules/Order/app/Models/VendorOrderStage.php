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
    ];

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
