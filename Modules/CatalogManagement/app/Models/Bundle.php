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
        'admin_approval' => 'integer',
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
        return $this->hasMany(BundleProduct::class, 'bundle_id');
    }

    public function bundleTotalPrice()
    {
        return $this->bundleProducts()->sum(\DB::raw('min_quantity * price'));
    }

    public function bundleProductsCount()
    {
        return $this->bundleProducts()->sum('min_quantity');
    }

    public function bundleProductsPrice()
    {
        return $this->bundleProducts()->sum('price');
    }

    /**
     * Get type attribute
     */
    public function getTypeAttribute()
    {
        return 'bundle';
    }


    /**
     * Scope for active bundles (is_active = true AND admin_approval = 1)
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', 1)->where('admin_approval', 1);
    }

    /**
     * Scope for only is_active check (without approval)
     */
    public function scopeIsActive(Builder $query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope for approved bundles
     * admin_approval: 0 = pending, 1 = approved, 2 = rejected
     */
    public function scopeApproved(Builder $query)
    {
        return $query->where('admin_approval', 1);
    }

    /**
     * Scope for pending approval bundles
     */
    public function scopePending(Builder $query)
    {
        return $query->where('admin_approval', 0);
    }

    /**
     * Scope for rejected bundles
     */
    public function scopeRejected(Builder $query)
    {
        return $query->where('admin_approval', 2);
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

        if (isset($filters['bundle_id'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('id', $filters['bundle_id'])
                ->orWhere('slug', $filters['bundle_id']);
            });
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('is_active', $filters['active']);
        }

        if (isset($filters['approval_status']) && $filters['approval_status'] !== '') {
            $query->where('admin_approval', $filters['approval_status']);
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
