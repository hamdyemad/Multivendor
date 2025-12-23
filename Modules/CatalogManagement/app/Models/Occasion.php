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
use Modules\Vendor\app\Models\Vendor;

class Occasion extends BaseModel
{
    use HasFactory, Translation, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $table = 'occasions';
    protected $guarded = [];


    /**
     * Get all attachments for the occasion
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the vendor that owns this occasion
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the occasion products (variants with special prices)
     */
    public function occasionProducts()
    {
        return $this->hasMany(OccasionProduct::class)->orderBy('position');
    }

    /**
     * Scope for active occasions
     */
    public function scopeActive($query)
    {
        return $query
        ->where('start_date', '>=', now())
        ->whereColumn('end_date', '<=', 'start_date')
        ->where('is_active', true);
    }

    /**
     * Scope for filtering occasions
     */
    public function scopeFilter($query, array $filters)
    {
        // Filter by vendor if user is a vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }
        }

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

        // Filter by vendor_id (for admin filtering)
        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        // Filter by active status
        if (isset($filters['active']) && $filters['active'] !== '' && $filters['active'] !== null) {
            $query->where('is_active', $filters['active']);
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
