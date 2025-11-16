<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\Attachment;
use App\Traits\HasSlug;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Department extends Model
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug;

    protected $guarded = [];


    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get department image
     */
    public function getImageAttribute()
    {
        $imageAttachment = $this->attachments()->where('type', 'image')->first();
        return $imageAttachment ? $imageAttachment->path : null;
    }

    /**
     * Activities relationship
     */
    public function activities() {
        return $this->belongsToMany(Activity::class, 'activities_departments', 'department_id', 'activity_id');
    }

    public function activeActivities() {
        return $this->activities()->active();
    }

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function activeCategories() {
        return $this->categories()->active();
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        if (!empty($filters['activity_id'])) {
            $query->whereHas('activeActivities', function($q) use ($filters) {
                $q->where(fn($query) => $query->where('id', $filters['activity_id'])->orWhere('slug', $filters['activity_id']));
            });
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        return $query;
    }
}
