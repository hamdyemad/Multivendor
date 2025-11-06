<?php

namespace Modules\Vendor\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Attachment;
use Modules\AreaSettings\app\Models\Country;
use Modules\CategoryManagment\app\Models\Activity;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, Translation;

    protected $guarded = [];


    /**
     * Get the user that owns the vendor
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the activity (single - for backward compatibility)
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the vendor's activities (many-to-many)
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'vendors_activities', 'vendor_id', 'activity_id');
    }

    /**
     * Alias for attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function logo()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'logo');
    }

    public function banner()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'banner');
    }

    public function documents()
    {
        return $this->morphMany(Attachment::class, 'attachable')->where('type', 'docs');
    }

    /**
     * Get the vendor's commission
     */
    public function commission()
    {
        return $this->hasOne(VendorCommission::class);
    }
}
