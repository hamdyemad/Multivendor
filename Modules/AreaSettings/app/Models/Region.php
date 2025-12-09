<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\VariantStock;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Modules\Order\app\Models\OrderFulfillment;

class Region extends BaseModel
{
    use Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'regions';
    protected $guarded = [];

    // Start Relations
    public function subRegions() {
        return $this->hasMany(SubRegion::class, 'region_id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function selected_vendors() {
        return $this->belongsToMany(\Modules\Vendor\app\Models\Vendor::class, 'vendor_regions', 'region_id', 'vendor_id');
    }

    public function stocks() {
        return $this->hasMany(VendorProductVariantStock::class, 'region_id');
    }

    public function fulfillments() {
        return $this->hasMany(OrderFulfillment::class, 'region_id');
    }
    // End Relations


    // Start Geters
    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale());
    }
    // End Geters

    /**
     * Get available stock for a variant in this region
     * Available = Total Stock - Allocated Stock (from fulfillments)
     */
    public function getAvailableStockForVariant($variantId)
    {
        // Get total stock for this variant in this region
        $variantStock = $this->stocks()
            ->where('vendor_product_variant_id', $variantId)
            ->first();

        $totalStock = $variantStock ? $variantStock->quantity : 0;

        // Get allocated stock from fulfillments using the relation
        $allocatedStock = $this->fulfillments()->whereIn('status', ['delivered', 'shipped'])
            ->whereHas('orderProduct', function($query) use ($variantId) {
                $query->where('vendor_product_variant_id', $variantId);
            })
            ->sum('allocated_quantity');

        return $totalStock - $allocatedStock;
    }

    public function scopeByVendor(Builder $query, $vendorIdentifier)
    {
        return $query->whereHas('city.country.vendors', function($q) use ($vendorIdentifier) {
            $q
            ->where('vendors.id', $vendorIdentifier)
                ->orWhere('vendors.slug', $vendorIdentifier)
                ;
        });

    }

    /**
     * Override filter scope to add region-specific filters
     * Calls parent filter from HasFilterScopes trait and adds custom filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        // Filter by vendor (through city.country.vendors)
        if (!empty($filters['vendor_id'])) {
           $query->whereHas('city.country.vendors', function($q) use ($filters) {
                $q->where('id', $filters['vendor_id']);
            });
            // Filter by vendor selected regions (through vendor_regions table)
            if (!empty($filters['vendor_selected_regions'])) {
                $vendorId = $filters['vendor_id'];
                $query->whereHas('selected_vendors', function($q) use ($vendorId) {
                    $q->where('vendor_regions.vendor_id', $vendorId);
                });
            }
        }


        return $query;
    }
    // End Scopes
}
