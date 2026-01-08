<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Models\BaseModel;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use App\Models\Traits\CountryCheckIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Occasion extends BaseModel
{
    use HasFactory, Translation, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $table = 'occasions';
    protected $guarded = [];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($occasion) {
            if (empty($occasion->slug)) {
                // Get English title from translations if available
                $englishTitle = null;
                
                // Try to get from request if creating via form
                if (request()->has('translations')) {
                    $translations = request()->input('translations', []);
                    foreach ($translations as $translation) {
                        if (isset($translation['lang_code']) && $translation['lang_code'] === 'en' && isset($translation['name'])) {
                            $englishTitle = $translation['name'];
                            break;
                        }
                    }
                }
                
                // Generate slug from English title or use UUID
                $occasion->slug = $englishTitle ? \Illuminate\Support\Str::slug($englishTitle) : \Illuminate\Support\Str::uuid();
            }
        });
    }


    /**
     * Get all attachments for the occasion
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the occasion products (variants with special prices)
     */
    public function occasionProducts()
    {
        return $this->hasMany(OccasionProduct::class)->orderBy('position');
    }

    /**
     * Scope for active occasions (is_active = true and not expired)
     */
    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where('end_date', '>=', now()->toDateString());
    }

    /**
     * Scope for valid occasions (not expired - end_date >= today)
     */
    public function scopeNotExpired($query)
    {
        return $query->where('end_date', '>=', now()->toDateString());
    }

    /**
     * Scope for filtering occasions
     */
    public function scopeFilter($query, array $filters)
    {
        // Search by name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('lang_key', 'name')
                  ->where('lang_value', 'like', "%{$search}%");
            });
        }

        if (isset($filters['occasion_id'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('id', $filters['occasion_id'])
                ->orWhere('slug', $filters['occasion_id']);
            });
        }

        // Filter by active status
        if (isset($filters['active']) && $filters['active'] !== '' && $filters['active'] !== null) {
            $query->where('is_active', $filters['active']);
        }

        // Filter out expired occasions (end_date < today)
        if (!empty($filters['not_expired'])) {
            $query->where('end_date', '>=', now()->toDateString());
        }

        // Filter by created_from
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        // Filter by created_until
        if (!empty($filters['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_until']);
        }

        // Filter by start_date
        if (!empty($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        // Filter by end_date
        if (!empty($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        return $query;
    }

    /**
     * Cast dates
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}
