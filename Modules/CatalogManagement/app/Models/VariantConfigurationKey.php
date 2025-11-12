<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class VariantConfigurationKey extends Model
{
    use HasFactory, Translation, SoftDeletes, HumanDates;

    protected $table = 'variants_configurations_keys';
    protected $guarded = [];

    public function variants()
    {
        return $this->hasMany(VariantsConfiguration::class, 'key_id');
    }

    public function childrenKeys()
    {
        return $this->hasMany(VariantConfigurationKey::class, 'parent_key_id');
    }

    public function parent()
    {
        return $this->belongsTo(VariantConfigurationKey::class, 'parent_key_id');
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        // Search filter - searches in both variant key name and parent name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->where(function($q) use ($search) {
                // Search in variant key's own translations
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_key', 'name')
                          ->where('lang_value', 'like', "%{$search}%");
                })
                // Also search in parent's translations
                ->orWhereHas('parent.translations', function($query) use ($search) {
                    $query->where('lang_key', 'name')
                          ->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        // Parent filter
        if (isset($filters['parent_key_id']) && $filters['parent_key_id'] !== '') {
            if($filters['parent_key_id'] == 'without') {
                $builder->whereNull('parent_key_id');
            } else {
                $builder->where('parent_key_id', $filters['parent_key_id']);
            }
        }

        // Date from filter
        if (isset($filters['created_date_from'])) {
            $builder->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (isset($filters['created_date_to'])) {
            $builder->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $builder;
    }
}
