<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermsConditions extends Model
{
    use Translation, AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates;

    protected $table = 'terms_conditions';
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the terms & conditions title
     */
    public function getTitleAttribute()
    {
        $title = $this->getTranslation('title', app()->getLocale());
        return $title ?? '';
    }

    /**
     * Get the terms & conditions description
     */
    public function getDescriptionAttribute()
    {
        $description = $this->getTranslation('description', app()->getLocale());
        return $description ?? '';
    }

    /**
     * Scope to filter terms & conditions
     */
    public function scopeFilter(Builder $query, $filters = [])
    {
        return $query;
    }
}
