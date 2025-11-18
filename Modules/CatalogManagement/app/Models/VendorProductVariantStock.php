<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AreaSettings\app\Models\Region;

class VendorProductVariantStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_product_variant_stocks';
    protected $fillable = [
        'vendor_product_variant_id',
        'region_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the vendor product variant that owns the stock
     */
    public function vendorProductVariant()
    {
        return $this->belongsTo(VendorProductVariant::class);
    }

    /**
     * Get the region
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
