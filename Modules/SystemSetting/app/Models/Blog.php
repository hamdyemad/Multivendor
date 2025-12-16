<?php

namespace Modules\SystemSetting\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Translation;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;

class Blog extends Model
{
    use HasFactory, SoftDeletes, AutoStoreCountryId, CountryCheckIdTrait, Translation, HumanDates;

    protected $fillable = [
        'blog_category_id',
        'image',
        'active',
        'slug',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * The translation fields for this model.
     */
    protected $translatable = [
        'title',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Get the blog category that owns the blog.
     */
    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    /**
     * Get the image attachments.
     */
    public function attachments()
    {
        return $this->morphMany(\App\Models\Attachment::class, 'attachable');
    }

    /**
     * Get the main image.
     */
    public function mainImage()
    {
        return $this->morphOne(\App\Models\Attachment::class, 'attachable')
            ->where('type', 'image')
            ->orderBy('id', 'asc');
    }

    /**
     * Get the title attribute (translation fallback).
     */
    public function getTitleAttribute($value)
    {
        return $this->getTranslation('title', app()->getLocale()) ?? $value;
    }

    /**
     * Get the content attribute (translation fallback).
     */
    public function getContentAttribute($value)
    {
        return $this->getTranslation('content', app()->getLocale()) ?? $value;
    }

    /**
     * Get the meta title attribute (translation fallback).
     */
    public function getMetaTitleAttribute($value)
    {
        return $this->getTranslation('meta_title', app()->getLocale()) ?? $value;
    }

    /**
     * Get the meta description attribute (translation fallback).
     */
    public function getMetaDescriptionAttribute($value)
    {
        return $this->getTranslation('meta_description', app()->getLocale()) ?? $value;
    }

    /**
     * Get the meta keywords attribute (translation fallback).
     */
    public function getMetaKeywordsAttribute($value)
    {
        return $this->getTranslation('meta_keywords', app()->getLocale()) ?? $value;
    }

    /**
     * Get meta keywords as array.
     */
    public function getMetaKeywordsArray()
    {
        $keywords = $this->getTranslation('meta_keywords', app()->getLocale());
        
        if (empty($keywords)) {
            return [];
        }

        // Try to decode as JSON first (legacy support)
        $decoded = json_decode($keywords, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Otherwise assume comma-separated string
        return array_map('trim', explode(',', $keywords));
    }

    /**
     * Get meta keywords as string.
     */
    public function getMetaKeywordsString($locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $keywords = $this->getTranslation('meta_keywords', $locale);
        if (is_array($keywords)) {
            return implode(', ', $keywords);
        }

        return $keywords ?? '';
    }

    /**
     * Scope a query to only include active blogs.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive blogs.
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    /**
     * Scope a query to filter by blog category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('blog_category_id', $categoryId);
    }

    public function scopeFilter(Builder $query, $filters)
    {
        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
        }   
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (isset($filters['blog_category_id'])) {
            $query->where('blog_category_id', $filters['blog_category_id']);
        }

        if (isset($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (isset($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }
}
