<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SystemSetting\app\Models\Currency;

class Country extends Model
{
    use Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'countries';
    protected $guarded = [];

    public function cities() {
        return $this->hasMany(City::class, 'country_id');
    }

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

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
                })
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('phone_code', 'like', "%{$search}%")
                ;
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
        $query->orderBy('created_at', 'desc');
    }
}
