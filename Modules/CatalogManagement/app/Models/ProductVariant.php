<?php

namespace Modules\CatalogManagement\app\Models;

use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes, Translation, HasSlug;

    protected $table = 'product_variants';
    protected $guarded = [];

    protected $casts = [
        'price' => 'integer',
        'has_discount' => 'boolean',
        'discount_price' => 'integer',
        'discount_end_date' => 'date',
    ];

    /**
     * The field to generate slug from (for HasSlug trait)
     */
    protected $slugFrom = 'title';

    /**
     * Get the product that owns the variant
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant configuration key
     */
    public function variantKey()
    {
        return $this->belongsTo(VariantConfigurationKey::class, 'variant_key_id');
    }

    /**
     * Get the variant configuration value
     */
    public function variantValue()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'variant_value_id');
    }

    /**
     * Get the variant stocks
     */
    public function stocks()
    {
        return $this->hasMany(VariantStock::class);
    }

    /**
     * Get the effective price (with discount if applicable)
     */
    public function getEffectivePrice()
    {
        if ($this->has_discount && $this->discount_end_date && $this->discount_end_date->isFuture()) {
            return $this->discount_price;
        }

        return $this->price;
    }

    /**
     * Get total stock across all regions
     */
    public function getTotalStock()
    {
        return $this->stocks()->sum('stock');
    }
}
