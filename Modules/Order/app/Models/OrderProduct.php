<?php

namespace Modules\Order\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\Occasion;
use Modules\Order\database\factories\OrderProductFactory;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;

class OrderProduct extends Model
{
    use HasFactory, Translation;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        static::observe(\Modules\Order\app\Observers\OrderProductObserver::class);
    }

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
        'shipping_cost',
        'stage_id',
        'occasion_id',
        'bundle_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'commission' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
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
     * Get the first/primary order product tax (for backward compatibility).
     */
    public function tax(): HasOne
    {
        return $this->hasOne(OrderProductTax::class);
    }

    /**
     * Get the order fulfillments.
     */
    public function fulfillments(): HasMany
    {
        return $this->hasMany(OrderFulfillment::class);
    }

    public function getProductTitleAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id');
    }

    public function occasion()
    {
        return $this->belongsTo(Occasion::class, 'occasion_id');
    }

    /**
     * Get the stage for this order product.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(OrderStage::class, 'stage_id');
    }
}
