<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;

class VendorProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_product_variants';
    protected $guarded = [];

    protected $with = ['stocks'];

    protected $casts = [
        'price' => 'decimal:2',
        'has_discount' => 'boolean',
        'price_before_discount' => 'decimal:2',
        'discount_end_date' => 'date',
    ];

    /**
     * Accessor for has_discount (backward compatibility)
     */
    public function getHasDiscountAttribute()
    {
        return $this->attributes['has_discount'] ?? $this->attributes['has_discount'] ?? false;
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
}
