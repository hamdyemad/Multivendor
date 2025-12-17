<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AreaSettings\app\Models\Country;

class Currency extends Model
{

    use Translation, SoftDeletes, HumanDates;

    protected $table = 'currencies';
    protected $guarded = [];

    protected $casts = [
        'use_image' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Get all attachments for the currency
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get all countries using this currency
     */
    public function countries()
    {
        return $this->hasMany(Country::class, 'currency_id');
    }


    public function pointSetting()
    {
        return $this->hasOne(PointsSetting::class, 'currency_id');
    }

    /**
     * Get the currency image
     */

    public function getNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale()) ?? '-';
    }

    public function getImageAttribute()
    {
        $attachment = $this->attachments()->where('type', 'image')->first();
        return $attachment ? $attachment->path : null;
    }

    public function scopeFilter(Builder $query, $filters) {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->whereHas('translations', function($query) use ($filters) {
                $query->where('lang_value', 'like', "%{$filters['search']}%");
            })
            ->orWhere('code', 'like', "%{$filters['search']}%")
            ->orWhere('symbol', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        if (isset($filters['created_date_from']) && !empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (isset($filters['created_date_to']) && !empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }
}
