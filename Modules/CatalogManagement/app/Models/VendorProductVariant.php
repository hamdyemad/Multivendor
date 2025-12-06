<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;
use Modules\Order\app\Models\OrderFulfillment;
use Modules\Order\app\Models\OrderProduct;

class VendorProductVariant extends Model
{
    use HasFactory, SoftDeletes, HumanDates;

    protected $table = 'vendor_product_variants';
    protected $guarded = [];

    protected $with = ['stocks'];

    protected $casts = [
        'price' => 'decimal:2',
        'has_discount' => 'boolean',
        'price_before_discount' => 'decimal:2',
        'discount_end_date' => 'date',
    ];

    protected $appends = ['total_stock', 'variant_name', 'discount', 'quantity_in_cart', 'cart_id', 'countDeliveredProduct', 'countOfAvailable', 'variant_path_en', 'variant_path_ar'];

    /**
     * Accessor for has_discount (backward compatibility)
     */
    public function getHasDiscountAttribute()
    {
        return $this->attributes['has_discount'] ?? false;
    }

    /**
     * Mutator for has_discount (backward compatibility)
     */
    public function setHasDiscountAttribute($value)
    {
        $this->attributes['has_discount'] = $value;
    }

    /**
     * Get the vendor product
     */
    public function vendorProduct()
    {
        return $this->belongsTo(VendorProduct::class);
    }

    /**
     * Get the variant configuration
     */
    public function variantConfiguration()
    {
        return $this->belongsTo(VariantsConfiguration::class);
    }

    /**
     * Get the vendor product variant stocks
     */
    public function stocks()
    {
        return $this->hasMany(VendorProductVariantStock::class);
    }

    /**
     * Get the order fulfillments for this variant
     */
    public function fulfillments()
    {
        return $this->hasManyThrough(
            OrderFulfillment::class,
            OrderProduct::class,
            'vendor_product_variant_id',
            'order_product_id'
        );
    }

    /**
     * Get the effective price (with discount if applicable)
     */
    public function getEffectivePrice()
    {
        if ($this->has_discount && $this->discount_end_date && $this->discount_end_date->isFuture()) {
            return $this->price_before_discount;
        }

        return $this->price;
    }

    /**
     * Check if the discount is still valid
     */
    public function isDiscountValid()
    {
        return $this->has_discount && $this->discount_end_date && $this->discount_end_date->isFuture();
    }

    /**
     * Get total stock across all regions for this vendor
     */
    public function getTotalStock()
    {
        return $this->stocks()->sum('quantity');
    }

    /**
     * Accessor: total_stock - Sum of all stocks across regions
     */
    public function getTotalStockAttribute()
    {
        return $this->stocks->sum('quantity') ?? 0;
    }

    /**
     * Accessor: variant_name - Get variant configuration translation
     */
    public function getVariantNameAttribute()
    {
        return $this->variantConfiguration->name;
    }

    /**
     * Get the full variant path as a string (English)
     */
    public function getVariantPathEnAttribute()
    {
        if (!$this->variantConfiguration) {
            return '';
        }

        $path = [];
        $current = $this->variantConfiguration;

        // Build path from current variant
        $path[] = $current->getTranslation('color', 'en') ?? '';

        // Add key name if exists
        if ($current->key) {
            $keyName = $current->key->getTranslation('name', 'en') ?? '';
            if ($keyName) {
                $path[] = $keyName;
            }
        }

        return implode(' - ', array_filter($path));
    }

    /**
     * Get the full variant path as a string (Arabic)
     */
    public function getVariantPathArAttribute()
    {
        if (!$this->variantConfiguration) {
            return '';
        }

        $path = [];
        $current = $this->variantConfiguration;

        // Build path from current variant
        $path[] = $current->getTranslation('color', 'ar') ?? '';

        // Add key name if exists
        if ($current->key) {
            $keyName = $current->key->getTranslation('name', 'ar') ?? '';
            if ($keyName) {
                $path[] = $keyName;
            }
        }

        return implode(' - ', array_filter($path));
    }

    /**
     * Accessor: discount - Calculate discount percentage
     */
    public function getDiscountAttribute()
    {
        if (($this->has_discount && $this->price_before_discount && $this->price) && $this->price_before_discount != 0) {
            $discount = (($this->price_before_discount - $this->price) / $this->price_before_discount) * 100;
            return round($discount, 2);
        }
        return null;
    }

    /**
     * Accessor: quantity_in_cart - Placeholder for cart quantity
     */
    public function getQuantityInCartAttribute()
    {
        return null;
    }

    /**
     * Accessor: cart_id - Placeholder for cart ID
     */
    public function getCartIdAttribute()
    {
        // return Auth::check()
        //     ? Cart::where('product_id', $this->product_id)
        //     ->where('user_id', auth()->id())
        //     ->where('product_size_color_id', $this->id)
        //     ->where('type', 'product')
        //     ->first()
        //     : null;
        return null;
    }

    /**
     * Accessor: countDeliveredProduct - Count of delivered products (placeholder)
     */
    public function getCountDeliveredProductAttribute()
    {
        return (int) $this->fulfillments()
            ->where('status', 'delivered')
            ->sum('allocated_quantity');
    }

    /**
     * Accessor: countOfAvailable - Total available stock (checks fulfillments)
     * Available = Total Stock - Allocated Stock
     */
    public function getCountOfAvailableAttribute()
    {
        $totalStock = $this->total_stock ?? 0;

        return $totalStock - $this->CountDeliveredProduct;
    }
}
