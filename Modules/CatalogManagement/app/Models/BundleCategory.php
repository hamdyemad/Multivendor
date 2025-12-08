<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class BundleCategory extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $guarded = [];

    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }


    public function getTitleAttribute()
    {
        return $this->getTranslation('title', app()->getLocale());
    }

    /**
     * Get bundle category image
     */
    public function getImageAttribute()
    {
        $imageAttachment = $this->attachments()->where('type', 'image')->first();
        return $imageAttachment ? $imageAttachment->path : null;
    }

    /**
     * Get type attribute
     */
    public function getTypeAttribute()
    {
        return 'bundle_category';
    }

    /**
     * Scope for active bundle categories
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

    /**
     * Get SEO title with fallback to translated name
     */
    public function getSeoTitle($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->getTranslation('seo_title', $locale) ?? $this->getTranslation('name', $locale);
    }

    /**
     * Get SEO description
     */
    public function getSeoDescription($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->getTranslation('seo_description', $locale);
    }

    /**
     * Get SEO keywords
     */
    public function getSeoKeywords($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->getTranslation('seo_keywords', $locale);
    }


    public function scopeFilter(Builder $query, array $filters)
    {
         // Apply filters
        if (!empty($filters['search'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('lang_value', 'like', '%' . $filters['search'] . '%')
                  ->where('lang_key', 'name');
            });
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }
        return $query;

    }
}
