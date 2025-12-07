<?php

namespace Modules\CategoryManagment\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\Vendor\app\Models\Vendor;

class Activity extends Model
{
    use HasFactory, Translation, SoftDeletes, HumanDates, HasSlug, CountryCheckIdTrait;

    protected $table = 'activities';
    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\ActivityFactory::new();
    }



    public function departments()
    {
        return $this->belongsToMany(Department::class, 'activities_departments', 'activity_id', 'department_id');
    }

    public function activeDepartments()
    {
        return $this->departments()->active();
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendors_activities', 'activity_id', 'vendor_id');
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description', app()->getLocale()) ?? '-';
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
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

        // Order by latest
        $query->orderBy('created_at', 'desc');

        return $query;
    }
}
