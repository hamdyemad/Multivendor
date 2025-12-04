<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Modules\AreaSettings\app\Models\Country;
use Illuminate\Support\Facades\Log;

trait CountryCheckIdTrait
{
    /**
     * Boot the trait - add dynamic query scope
     */
    protected static function bootCountryCheckIdTrait()
    {
        // Use querying event instead of global scope for dynamic filtering
        static::addGlobalScope('country_filter', function (Builder $builder) {
            $countryId = static::resolveCountryId();
            if ($countryId !== null) {
                $builder->where('country_id', $countryId);
            }
        });
    }

    /**
     * Resolve country_id from multiple sources
     */
    protected static function resolveCountryId()
    {
        // Try multiple sources for country code (in order of priority)
        $code = session('country_code')
            ?? request('country_code')
            ?? config('app.default_country_code')
            ?? null;

        if (!$code) {
            if(request('country_id')) {
                $countryId = Country::where('id', request('country_id'))->value('id');
            } else {
                $countryId = Country::default()->value('id');
            }
        } else {
            $countryId = Country::where('code', $code)->value('id');
        }

        return $countryId;
    }

    /**
     * Scope to bypass country filter (useful for admin queries)
     */
    public function scopeWithoutCountryFilter(Builder $query)
    {
        return $query->withoutGlobalScope('country_filter');
    }

    /**
     * Scope to filter by specific country
     */
    public function scopeForCountry(Builder $query, $countryCode)
    {
        $countryId = Country::where('code', $countryCode)->value('id');

        if ($countryId) {
            return $query->where('country_id', $countryId);
        }

        return $query;
    }
}
