<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use Translation, AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates;

    protected $table = 'ads';
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'type' => 'array',
    ];

    /**
     * Get all attachments for the ad
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the ad title
     */
    public function getTitleAttribute()
    {
        return $this->getTranslation('title', app()->getLocale());
    }

    /**
     * Get the ad subtitle
     */
    public function getSubtitleAttribute()
    {
        return $this->getTranslation('subtitle', app()->getLocale());
    }

    /**
     * Get the ad image
     */
    public function getImageAttribute()
    {
        $attachment = $this->attachments()->where('type', 'image')->first();
        return $attachment ? $attachment->path : null;
    }

    /**
     * Get position label
     */
    public function getPositionLabelAttribute()
    {
        return __('systemsetting::ads.positions.' . $this->position);
    }


    public function scopeActive(Builder $query) {
        return $query->where('active', 1);
    }
    /**
     * Scope for filtering
     */
    public function scopeFilter(Builder $query, $filters)
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->whereHas('translations', function($query) use ($filters) {
                $query->where('lang_value', 'like', "%{$filters['search']}%");
            })
            ->orWhere('link', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['position']) && !empty($filters['position'])) {
            $query->where('position', $filters['position']);
        }
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->whereJsonContains('type', $filters['type']);
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

    /**
     * Available positions
     */
    public static function getPositions()
    {
        return [
            'header' => __('systemsetting::ads.positions.header'),
            'footer' => __('systemsetting::ads.positions.footer'),
            'sidebar' => __('systemsetting::ads.positions.sidebar'),
            'home_banner' => __('systemsetting::ads.positions.home_banner'),
            'product_page' => __('systemsetting::ads.positions.product_page'),
            'category_page' => __('systemsetting::ads.positions.category_page'),
        ];
    }
}
