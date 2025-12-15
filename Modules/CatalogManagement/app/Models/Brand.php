<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Traits\HasSlug;
use App\Models\Traits\CountryCheckIdTrait;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;

class Brand extends BaseModel
{
    use HasFactory, Translation, SoftDeletes, HumanDates, HasSlug, AutoStoreCountryId, CountryCheckIdTrait;

    protected $table = 'brands';
    protected $guarded = [];

    /**
     * Get all attachments for the brand
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the logo attachment
     */
    public function logo()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'logo');
    }

    /**
     * Get the cover attachment
     */
    public function cover()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'cover');
    }

    public function getImageAttribute()
    {
        $imageAttachment = $this->logo;
        return $imageAttachment ? $imageAttachment->path : null;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getTypeAttribute()
    {
        return 'brand';
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByDepartment($query, $departmentIdentifier)
    {
        return $query->whereHas('products', function ($query) use ($departmentIdentifier) {
            $query->whereHas('department', function ($query) use ($departmentIdentifier) {
                $query->where('departments.id', $departmentIdentifier)->orWhere('departments.slug', $departmentIdentifier);
            });
        });
    }

    public function scopeByCategory($query, $categoryIdentifier)
    {
        return $query->whereHas('products', function ($query) use ($categoryIdentifier) {
            $query->whereHas('category', function ($query) use ($categoryIdentifier) {
                $query->where('categories.id', $categoryIdentifier)->orWhere('categories.slug', $categoryIdentifier);
            });
        });
    }

    public function scopeBySubCategory($query, $subCategoryIdentifier)
    {
        return $query->whereHas('products', function ($query) use ($subCategoryIdentifier) {
            $query->whereHas('subCategory', function ($query) use ($subCategoryIdentifier) {
                $query->where('sub_categories.id', $subCategoryIdentifier)->orWhere('sub_categories.slug', $subCategoryIdentifier);
            });
        });
    }

    /**
     * Override filter scope to handle Brand-specific filtering
     */
    public function scopeFilter($query, array $filters)
    {

        // Char filter (filter by first letter)
        if (isset($filters['brand_id'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('id', $filters['brand_id'])
                ->orWhere('slug', $filters['brand_id']);
            });
        }

        if (isset($filters['vendor_id'])) {
            $vendor = Vendor::where('slug', $filters['vendor_id'])->orWhere('id', $filters['vendor_id'])->first();
            $query->whereHas('products', function ($query) use($vendor) {
                $query->whereHas('vendorProducts', function ($query) use($vendor) {
                    $query->where('vendor_id', $vendor->id);
                });
            });
        }


        return $query;
    }

}
