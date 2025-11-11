<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use Translation, SoftDeletes, HumanDates;

    protected $table = 'cities';
    protected $guarded = [];

    public function regions() {
        return $this->hasMany(Region::class, 'city_id');
    }

    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }

}
