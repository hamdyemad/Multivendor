<?php

namespace Modules\Accounting\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\app\Models\Scopes\VendorScope;

class ExpenseItem extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait, Translation;

    protected $fillable = [
        'active',
        'country_id',
        'vendor_id'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new VendorScope);
        
        static::creating(function ($model) {
            if (isVendor()) {
                $model->vendor_id = auth()->user()->vendor->id ?? null;
            }
        });
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function vendor()
    {
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeFilter($query, array $filters)
    {
        parent::scopeFilter($query, $filters);

        if (!empty($filters['status'])) {
            $query->where('active', $filters['status']);
        }

        return $query;
    }
}
