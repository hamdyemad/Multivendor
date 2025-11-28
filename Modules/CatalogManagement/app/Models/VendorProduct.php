<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;


class VendorProduct extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, HasSlug;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $guarded = [];
    protected $casts = [
        'points' => 'integer',
        'max_per_order' => 'integer',
        'offer_date_view' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get all available status values
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => __('common.pending'),
            self::STATUS_APPROVED => __('common.approved'),
            self::STATUS_REJECTED => __('common.rejected'),
        ];
    }

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

    /**
     * Apply custom search logic for Product
     * Searches by title, brand, and category in addition to translations
     */
    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query->orWhereHas('brand', function($subQ) use ($search) {
                $subQ->whereHas('translations', function($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            })
            ->orWhereHas('category', function($subQ) use ($search) {
                $subQ->whereHas('translations', function($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            });
    }

    /**
     * Scope: Filter by price range (through variants)
     */
    public function scopePriceRange(Builder $query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice || $maxPrice) {
            $query->whereHas('variants', function($subQ) use ($minPrice, $maxPrice) {
                if ($minPrice) {
                    $subQ->where('price', '>=', $minPrice);
                }
                if ($maxPrice) {
                    $subQ->where('price', '<=', $maxPrice);
                }
            });
        }
        return $query;
    }

    /**
     * Scope: Filter featured products
     */
    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Override filter to add price range and featured filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from trait
        parent::scopeFilter($query, $filters);

        // Price range filter
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->priceRange(
                $filters['min_price'] ?? null,
                $filters['max_price'] ?? null
            );
        }

        // Featured filter
        if (!empty($filters['featured'])) {
            $query->featured();
        }

        return $query;
    }
}
