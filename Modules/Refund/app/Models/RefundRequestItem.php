<?php

namespace Modules\Refund\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\app\Models\OrderProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;

class RefundRequestItem extends Model
{
    protected $fillable = [
        'refund_request_id',
        'order_product_id',
        'vendor_id',
        'quantity',
        'unit_price',
        'total_price',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'refund_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Get the refund request
     */
    public function refundRequest(): BelongsTo
    {
        return $this->belongsTo(RefundRequest::class);
    }

    /**
     * Get the order product
     */
    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * Get the product variant through order product relationship
     */
    public function getProductVariantAttribute()
    {
        return $this->orderProduct?->vendorProductVariant;
    }

    /**
     * Get the vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }
}
