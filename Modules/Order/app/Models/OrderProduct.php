<?php

namespace Modules\Order\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Order\database\factories\OrderProductFactory;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;

class OrderProduct extends Model
{
    use HasFactory, Translation;

    public static function newFactory()
    {
        return OrderProductFactory::new();
    }

    protected $fillable = [
        'order_id',
        'vendor_product_id',
        'vendor_product_variant_id',
        'quantity',
        'price',
        'vendor_id',
        'commission',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'commission' => 'decimal:2',
    ];

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the vendor product.
     */
    public function vendorProduct(): BelongsTo
    {
        return $this->belongsTo(VendorProduct::class);
    }

    /**
     * Get the vendor product variant.
     */
    public function vendorProductVariant(): BelongsTo
    {
        return $this->belongsTo(VendorProductVariant::class);
    }

    /**
     * Get the order product taxes.
     */
    public function taxes(): HasMany
    {
        return $this->hasMany(OrderProductTax::class);
    }

    /**
     * Get the order fulfillments.
     */
    public function fulfillments(): HasMany
    {
        return $this->hasMany(OrderFulfillment::class);
    }
}
