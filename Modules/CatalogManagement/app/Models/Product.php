<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Models\User;
use App\Traits\Translation;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\Vendor\app\Models\Vendor;

class Product extends Model
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
     * Scope to filter products based on various criteria
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhereHas('brand', function($query) use ($search) {
                    $query->whereHas('translations', function($subQuery) use ($search) {
                        $subQuery->where('lang_value', 'like', "%{$search}%");
                    });
                })
                ->orWhereHas('category', function($query) use ($search) {
                    $query->whereHas('translations', function($subQuery) use ($search) {
                        $subQuery->where('lang_value', 'like', "%{$search}%");
                    });
                });
            });
        }

        // Apply brand filter
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Active filter
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        $query->orderBy('created_at', 'desc');
    }

}


