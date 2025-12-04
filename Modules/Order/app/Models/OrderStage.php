<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\AreaSettings\app\Models\Country;

class OrderStage extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'is_system' => 'boolean',
    ];


    public function country() {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get type attribute
     */
    public function getTypeAttribute()
    {
        return 'order_stage';
    }

    /**
     * Scope for active order stages
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

    /**
     * Scope for system stages
     */
    public function scopeSystem(Builder $query)
    {
        return $query->where('is_system', 1);
    }

    /**
     * Scope for custom stages (non-system)
     */
    public function scopeCustom(Builder $query)
    {
        return $query->where('is_system', 0);
    }

    /**
     * Filter scope
     */
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

    /**
     * Check if stage can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }
}
