<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Region extends BaseModel
{
    use Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'regions';
    protected $guarded = [];

    // Start Relations
    public function subRegions() {
        return $this->hasMany(SubRegion::class, 'region_id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }
    // End Relations


    // Start Geters
    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale());
    }
    // End Geters

    // Start Scopes

    /**
     * Override filter scope to add region-specific filters
     * Calls parent filter from HasFilterScopes trait and adds custom filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        parent::scopeFilter($query, $filters);

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        // Filter by vendor (through city.country.vendors)
        if (!empty($filters['vendor_id'])) {
            $query->whereHas('city.country.vendors', function($q) use ($filters) {
                $q->where('id', $filters['vendor_id']);
            });
        }

        return $query;
    }
    // End Scopes
}
