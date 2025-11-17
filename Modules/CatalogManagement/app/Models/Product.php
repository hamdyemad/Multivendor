<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\Vendor\app\Models\Vendor;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Translation;

    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'points' => 'integer',
        'max_per_order' => 'integer',
    ];

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

    public function getCurrencyAttribute()
    {
        return $this->vendor->country->currency ?? null;
    }

    /**
     * Get the brand
     */
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
     * Get the tax
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    /**
     * Get the vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }



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



    // Scopes
    public function scopeFilter($query, $filters)
    {
        // Search in translations
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('translations', function($query) use ($searchTerm) {
                    $query->where('lang_key', 'name')
                          ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by active status
        if (isset($filters['is_active']) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by featured status
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', $filters['is_featured']);
        }

        // Filter by brand
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by department
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // Filter by date range
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }
        return $query;
    }

}
