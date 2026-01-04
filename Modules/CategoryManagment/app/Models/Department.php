<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Traits\HasSlug;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\Product;

class Department extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug, AutoStoreCountryId, CountryCheckIdTrait;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'description',
        'slug',
        'active',
        'view_status',
        'sort_number',
        'commission',
        'country_id'
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Vendors relationship
     */
    public function vendors()
    {
        return $this->belongsToMany(\Modules\Vendor\app\Models\Vendor::class, 'department_vendor');
    }



    /**
     * Categories relationship
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // ============================================
    // GETTERS & ACCESSORS
    // ============================================

    /**
     * Get department image
     */
    public function getImageAttribute()
    {
        $imageAttachment = $this->attachments()->where('type', 'image')->first();
        return $imageAttachment ? $imageAttachment->path : null;
    }

    public function getIconAttribute()
    {
        $iconAttachment = $this->attachments()->where('type', 'icon')->first();
        return $iconAttachment ? $iconAttachment->path : null;
    }

    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale());
    }

    /**
     * Get department description
     */
    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description', app()->getLocale()) ?? '-';
    }

    public function getTypeAttribute()
    {
        return 'department';
    }



    /**
     * Get active categories
     */
    public function activeCategories()
    {
        return $this->categories()->active();
    }



    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->products()->whereHas('vendorProducts', function($q){
            $q->where('is_active', true)
              ->where('status', 'approved');
        });
    }

    /**
     * Override scopeByBrand for Department
     * Department filters by brand through products relationship
     */
    public function scopeByBrand(Builder $query, $brandIdentifier)
    {
        return $query->whereHas('activeProducts', function ($q) use ($brandIdentifier) {
            $q->whereHas('brand', function ($subQ) use ($brandIdentifier) {
                $subQ->where('id', $brandIdentifier)
                    ->orWhere('slug', $brandIdentifier);
            });
        });
    }


    /**
     * Scope: Filter by vendor ID or slug
     * Overrides HasFilterScopes because relationship name is 'vendors'
     */
    public function scopeByVendor(Builder $query, $vendorIdentifier)
    {
        return $query->whereHas('vendors', function ($subQ) use ($vendorIdentifier) {
            $subQ->where('vendors.id', $vendorIdentifier) // Explicitly qualify id to avoid ambiguity if needed
                ->orWhere('vendors.slug', $vendorIdentifier);
        });
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        parent::scopeFilter($query, $filters);
        
        // Ensure view_status filter is applied (in case parent doesn't handle it)
        if (isset($filters['view_status']) && $filters['view_status'] !== '') {
            $query->where('view_status', $filters['view_status']);
        }
        
        return $query;
    }
}
