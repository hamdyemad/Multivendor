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

    /**
     * Get department description
     */
    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description', app()->getLocale()) ?? '-';
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

    /**
     * Override scopeByVendor for Department
     * Department filters by vendor through activities → vendors relationship
     */
    public function scopeByVendor(Builder $query, $vendorIdentifier)
    {
        return $query->whereHas('activities', function($q) use ($vendorIdentifier) {
            $q->whereHas('vendors', function($subQ) use ($vendorIdentifier) {
                $subQ->where('id', $vendorIdentifier)
                    ->orWhere('slug', $vendorIdentifier);
            });
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->products()->active();
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

}
