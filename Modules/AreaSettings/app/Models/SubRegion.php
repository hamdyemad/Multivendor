<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


class SubRegion extends BaseModel
{
    use Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'subregions';
    protected $guarded = [];


    public function region() {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Override filter scope to add subregion-specific filters
     * Calls parent filter from HasFilterScopes trait and adds custom filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        parent::scopeFilter($query, $filters);

        // Filter by region
        if (!empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        return $query;
    }
}
