<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


class Region extends Model
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
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeFilter(Builder $query, array $filters) {
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        if (!empty($filters['city_id'])) {
            $query->whereHas('city', function($q) use ($filters) {
                $q->where('id', $filters['city_id']);
            });
        }
    }
    // End Scopes
}
