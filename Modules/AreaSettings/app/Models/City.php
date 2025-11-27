<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


class City extends BaseModel
{
    use Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'cities';
    protected $guarded = [];

    public function regions() {
        return $this->hasMany(Region::class, 'city_id');
    }

    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Override filter scope to add city-specific filters
     * Calls parent filter from HasFilterScopes trait and adds custom filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        parent::scopeFilter($query, $filters);

        // Filter by country
        if (!empty($filters['country_id'])) {
            $query->byCountry($filters['country_id']);
        }

        return $query;
    }
}
