<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\CountryCheckIdTrait;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promocode extends BaseModel
{
    use HasFactory, HumanDates, CountryCheckIdTrait;

    protected $guarded = [];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIsValid($query)
    {
        return $query->active()
                ->where('valid_from', '<', now())
                ->where('valid_until', '>', now());
    }
}
