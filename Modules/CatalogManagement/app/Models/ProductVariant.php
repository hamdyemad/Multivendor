<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HasSlug;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes, Translation, HasSlug;

    protected $fillable = [
        'product_id',
        'slug',
        'sku',
        'price',
        'has_discount',
        'discount_price',
        'discount_end_date',
    ];

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
     * Get all translations for the variant
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get the variant stocks
     */
    public function stocks()
    {
        return $this->hasMany(VariantStock::class);
    }

    /**
     * Get translation for specific language and key
     */
    public function getTranslation($key, $langCode = null)
    {
        $langCode = $langCode ?? app()->getLocale();

        $translation = $this->translations()
            ->whereHas('language', function ($query) use ($langCode) {
                $query->where('code', $langCode);
            })
            ->where('lang_key', $key)
            ->first();

        return $translation ? $translation->lang_value : '';
    }

    /**
     * Set translation for specific language and key
     */
    public function setTranslation($key, $value, $langId)
    {
        $this->translations()->updateOrCreate(
            [
                'lang_id' => $langId,
                'lang_key' => $key,
            ],
            [
                'lang_value' => $value,
            ]
        );
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
