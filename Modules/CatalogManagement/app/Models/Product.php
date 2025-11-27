<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Models\User;
use App\Traits\Translation;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\CatalogManagement\app\Http\Requests\Api\ProductReviewRequest;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\Vendor\app\Models\Vendor;

class Product extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates;

    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
        'configuration_type' => 'string',
    ];

    // Product type constants
    const TYPE_PRODUCT = 'product';
    const TYPE_BANK = 'bank';

    /**
     * Scope to filter only bank products
     */
    public function scopeBank(Builder $query)
    {
        return $query->where('type', self::TYPE_BANK);
    }

    /**
     * Scope to filter only regular products
     */
    public function scopeRegular(Builder $query)
    {
        return $query->where('type', self::TYPE_PRODUCT);
    }

    /**
     * Check if product is a bank product
     */
    public function isBankProduct(): bool
    {
        return $this->type === self::TYPE_BANK;
    }

    /**
     * Get all attachments for the product
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the main product image
     */
    public function mainImage()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'main_image');
    }

    /**
     * Get additional product images
     */
    public function additionalImages()
    {
        return $this->morphMany(Attachment::class, 'attachable')->where('type', 'additional_image');
    }

    /**
     * Get the product variants
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
     public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the sub category
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get the vendor (if created by vendor)
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who created this product
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }


    /**
     * Get vendors that have added this product
     */
    public function vendorProducts()
    {
        return $this->hasMany(VendorProduct::class);
    }

    /**
     * Get the first vendor product (for single vendor products)
     */
    public function vendorProduct()
    {
        return $this->hasOne(VendorProduct::class);
    }

    /**
     * Get vendors through vendor_products
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_products')
                    ->withPivot('status', 'rejection_reason')
                    ->withTimestamps();
    }

    // public function reviews()
    // {
    //     return $this->hasMany(ProductReview::class);
    // }

    // public function approvedReviews()
    // {
    //     return $this->reviews()->where('is_approved', true);
    // }

    // Start Getters
    public function getTitleAttribute()
    {
        return $this->getTranslation('title', app()->getLocale());
    }

    public function getDetailsAttribute()
    {
        return $this->getTranslation('details', app()->getLocale());
    }

    public function getSummaryAttribute()
    {
        return $this->getTranslation('summary', app()->getLocale());
    }

    public function getFeaturesAttribute()
    {
        return $this->getTranslation('features', app()->getLocale());
    }

    public function getInstructionsAttribute()
    {
        return $this->getTranslation('instructions', app()->getLocale());
    }

    public function getExtraDescriptionAttribute()
    {
        return $this->getTranslation('extra_description', app()->getLocale());
    }

    public function getMaterialAttribute()
    {
        return $this->getTranslation('material', app()->getLocale());
    }

    public function getTagsAttribute()
    {
        return $this->getTranslation('tags', app()->getLocale());
    }

    public function getMetaTitleAttribute()
    {
        return $this->getTranslation('meta_title', app()->getLocale());
    }

    public function getMetaKeywordsAttribute()
    {
        return $this->getTranslation('meta_keywords', app()->getLocale());
    }

    public function getMetaDescriptionAttribute()
    {
        return $this->getTranslation('meta_description', app()->getLocale());
    }

    public function getCurrencyAttribute()
    {
        return $this->vendor->country->currency ?? null;
    }
    // End Getters



    /**
     * Get the route key for the model
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get tags as a comma-separated string for a specific language
     */
    public function getTagsString($languageCode = 'en')
    {
        $language = \App\Models\Language::where('code', $languageCode)->first();
        if (!$language) {
            return '';
        }

        $translation = $this->translations()
            ->where('lang_id', $language->id)
            ->where('lang_key', 'tags')
            ->first();

        return $translation ? $translation->lang_value : '';
    }

    /**
     * Get meta keywords as a comma-separated string for a specific language
     */
    public function getMetaKeywordsString($languageCode = 'en')
    {
        $language = \App\Models\Language::where('code', $languageCode)->first();
        if (!$language) {
            return '';
        }

        $translation = $this->translations()
            ->where('lang_id', $language->id)
            ->where('lang_key', 'meta_keywords')
            ->first();

        return $translation ? $translation->lang_value : '';
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
        // if (!empty($filters['featured'])) {
        //     $query->featured();
        // }

        return $query;
    }

}
