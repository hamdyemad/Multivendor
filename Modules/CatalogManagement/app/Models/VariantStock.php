<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Region;
use App\Traits\Translation;
use Modules\AreaSettings\app\Models\Region as ModelsRegion;

class VariantStock extends Model
{
    use HasFactory, SoftDeletes, Translation;

    protected $fillable = [
        'product_variant_id',
        'region_id',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    /**
     * Get the product variant that owns the stock
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the region
     */
    public function region()
    {
        return $this->belongsTo(ModelsRegion::class);
    }
}
