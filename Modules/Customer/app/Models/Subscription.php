<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, CountryCheckIdTrait, AutoStoreCountryId;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'country_id',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}
