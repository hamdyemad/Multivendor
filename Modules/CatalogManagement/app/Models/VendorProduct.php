<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;

class VendorProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'points' => 'integer',
        'max_per_order' => 'integer',
        'offer_date_view' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the vendor that owns this vendor product
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the tax
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    /**
     * Get vendor product variants
     */
    public function variants()
    {
        return $this->hasMany(VendorProductVariant::class);
    }
}
