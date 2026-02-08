<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Models\Attachment;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SystemSetting\app\Models\Currency;
use Modules\Vendor\app\Models\Vendor;

class Country extends BaseModel
{
    use Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'countries';
    protected $guarded = [];


    public function getCodeAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function image()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'image');
    }

    public function vendors() {
        return $this->hasMany(Vendor::class, 'country_id');
    }
    public function cities() {
        return $this->hasMany(City::class, 'country_id');
    }

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id');
    }


    public function scopeDefault($query) {
        return $query->where('default', 1);
    }

    /**
     * Apply custom search logic for Country
     * Searches by code and phone_code in addition to translations
     */
    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query->orWhere('code', 'like', "%{$search}%")
                     ->orWhere('phone_code', 'like', "%{$search}%");
    }
}
