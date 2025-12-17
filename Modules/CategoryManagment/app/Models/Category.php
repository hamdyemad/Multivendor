<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;

use App\Traits\HasSlug;
use App\Traits\Translation;
use App\Models\Traits\CountryCheckIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CategoryManagment\app\Models\DepartmentTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Modules\CatalogManagement\app\Models\Product;

class Category extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug, AutoStoreCountryId, CountryCheckIdTrait;

    protected $guarded = [];


    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get category image
     */
    public function getImageAttribute()
    {
        $imageAttachment = $this->attachments()->where('type', 'image')->first();
        return $imageAttachment ? $imageAttachment->path : null;
    }

    /**
     * Get category icon
     */
    public function getIconAttribute()
    {
        $iconAttachment = $this->attachments()->where('type', 'icon')->first();
        return $iconAttachment ? $iconAttachment->path : null;
    }

    public function getTypeAttribute()
    {
        return 'category';
    }

    /**
     * Department relationship
     */
    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function subs()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function activeSubs()
    {
        return $this->subs()->active();
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

    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description', app()->getLocale()) ?? '-';
    }


}
