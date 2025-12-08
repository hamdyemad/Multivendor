<?php

namespace Modules\CatalogManagement\app\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OccasionProduct extends Model
{
    use HasFactory;

    protected $table = 'occasion_products';
    protected $guarded = [];

    protected $casts = [
        'special_price' => 'decimal:2',
        'position' => 'integer',
    ];

    /**
     * Get the occasion that owns this product
     */
    public function occasion()
    {
        return $this->belongsTo(Occasion::class);
    }

    /**
     * Get the vendor product variant
     */
    public function vendorProductVariant()
    {
        return $this->belongsTo(VendorProductVariant::class);
    }
}
