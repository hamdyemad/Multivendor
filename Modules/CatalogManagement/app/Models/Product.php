<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HasSlug;
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

}
