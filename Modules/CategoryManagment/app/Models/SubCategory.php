<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\CatalogManagement\app\Models\Product;


class SubCategory extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug;

    protected $guarded = [];


    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get subcategory image
     */
    public function getImageAttribute()
    {
        $imageAttachment = $this->attachments()->where('type', 'image')->first();
        return $imageAttachment ? $imageAttachment->path : null;
    }

    /**
     * Get subcategory icon
     */
    public function getIconAttribute()
    {
        $iconAttachment = $this->attachments()->where('type', 'icon')->first();
        return $iconAttachment ? $iconAttachment->path : null;
    }

    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale()) ?? '--';
    }

    public function getTypeAttribute()
    {
        return 'sub_category';
    }

    /**
     * Category relationship
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
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

    public function scopeFilter(Builder $query, array $filters)
    {
        parent::scopeFilter($query, $filters);

        if (!empty($filters['main_category_id'])) {
            $query->where('category_id', $filters['main_category_id']);
        }
    }

}
