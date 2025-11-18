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

    protected $casts = [
        'price' => 'decimal:2',
        'has_offer' => 'boolean',
        'price_before_discount' => 'decimal:2',
        'offer_end_date' => 'date',
    ];

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
        if ($this->has_offer && $this->offer_end_date && $this->offer_end_date->isFuture()) {
            return $this->price_before_discount;
        }

        return $this->price;
    }

    /**
     * Check if the offer is still valid
     */
    public function isOfferValid()
    {
        return $this->has_offer && $this->offer_end_date && $this->offer_end_date->isFuture();
    }

    /**
     * Get total stock across all regions for this vendor
     */
    public function getTotalStock()
    {
        return $this->stocks()->sum('quantity');
    }
}
