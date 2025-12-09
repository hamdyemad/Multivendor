<?php

namespace Modules\CatalogManagement\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes, Translation;

    protected $table = 'product_variants';
    protected $guarded = [];


    /**
     * Get the product that owns the variant
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    /**
     * Get the variant configuration value
     */
    public function variantConfiguration()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'variant_configuration_id');
    }

}
