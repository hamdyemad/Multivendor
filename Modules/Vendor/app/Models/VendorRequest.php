<?php

namespace Modules\Vendor\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CategoryManagment\app\Models\Activity;

class VendorRequest extends Model
{
    use HasFactory, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $table = 'vendor_requests';

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the vendor created from this request
     */
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'vendor_request_id');
    }


    /**
     * Scope to filter pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to filter rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope to search by email or company name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('email', 'like', "%{$search}%")
                     ->orWhere('company_name', 'like', "%{$search}%");
    }
}
