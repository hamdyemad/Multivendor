<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Modules\AreaSettings\app\Models\Country;
use Modules\Vendor\app\Models\Vendor;

class Bundle extends Model
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug, AutoStoreCountryId, CountryCheckIdTrait;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }




    public function main_image()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'main_image');
    }

    /**
     * Relationships
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bundleCategory()
    {
        return $this->belongsTo(BundleCategory::class);
    }

    public function bundleProducts()
    {
        return $this->hasMany(BundleProduct::class);
    }

    /**
     * Get type attribute
     */
    public function getTypeAttribute()
    {
        return 'bundle';
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description');
    }

    /**
     * Scope for active bundles
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Filter scope
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Apply filters
        if (!empty($filters['search'])) {
            $query
            ->whereHas('translations', function ($q) use ($filters) {
                $q->where('lang_value', 'like', '%' . $filters['search'] . '%')
                  ->where('lang_key', 'name');
            })
            ->orWhere('sku', 'like' ,"%" . $filters['search'] . "%");
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('is_active', $filters['active']);
        }

        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('bundle_category_id', $filters['category_id']);
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_until']);
        }

        return $query;
    }
}
