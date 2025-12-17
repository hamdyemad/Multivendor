<?php

namespace App\Models\Traits;

use Modules\AreaSettings\app\Models\Country;

trait AutoStoreCountryId
{
    public static function bootAutoStoreCountryId()
    {
        static::creating(function ($model) {
            if (array_key_exists('country_id', $model->getAttributes())) {
                return;
            }
            $countryCode = session('country_code');
            $country_id = Country::where('code', $countryCode)->value('id');
            if ($country_id) {
                $model->country_id = $country_id;
            }
        });
    }
}
