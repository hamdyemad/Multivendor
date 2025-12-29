<?php

namespace Modules\Vendor\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorFcmToken extends Model
{
    use HasFactory;

    protected $table = 'vendor_fcm_tokens';

    protected $guarded = [];

    /**
     * Get the vendor that owns this token
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
