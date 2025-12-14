<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivacyPolicy extends Model
{
    use Translation, AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates;

    protected $table = 'privacy_policies';
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the privacy policy description
     */
    public function getDescriptionAttribute()
    {
        $description = $this->getTranslation('description', app()->getLocale());
        return $description ?? '';
    }

    /**
     * Scope to filter privacy policies
     */
    public function scopeFilter(Builder $query, $filters = [])
    {
        return $query;
    }
}
