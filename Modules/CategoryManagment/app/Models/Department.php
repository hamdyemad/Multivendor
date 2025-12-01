<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Traits\HasSlug;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\Product;

class Department extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug;

    protected $guarded = [];

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
     * Activities relationship
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activities_departments', 'department_id', 'activity_id');
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
     * Get active activities
     */
    public function activeActivities()
    {
        return $this->activities()->active();
    }

    /**
     * Get active categories
     */
    public function activeCategories()
    {
        return $this->categories()->active();
    }

    public function scopeByVendor(Builder $query, $vendorIdentifier)
    {
        return $query->whereHas('activities', function($q) use ($vendorIdentifier) {
            $q->whereHas('vendors', function($subQ) use ($vendorIdentifier) {
                $subQ->where('vendors.id', $vendorIdentifier)
                    ->orWhere('vendors.slug', $vendorIdentifier);
            });
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->products()->whereHas('vendorProducts', function($q){
            $q->where('is_active', true);
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


    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        parent::scopeFilter($query, $filters);


        if (!empty($filters['vendor_id'])) {
            $query->byVendor($filters['vendor_id']);
        }

        return $query;
    }
}
