<?php

namespace Modules\Accounting\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\app\Models\Scopes\VendorScope;

class VendorBalance extends Model
{
    use HasFactory, SoftDeletes, HumanDates;

    protected $fillable = [
        'vendor_id',
        'total_earnings',
        'commission_deducted',
        'available_balance',
        'withdrawn_amount',
        'country_id'
    ];

    protected $casts = [
        'total_earnings' => 'decimal:2',
        'commission_deducted' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'withdrawn_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new VendorScope);
    }

    public function vendor()
    {
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }

    public function user()
    {
        return $this->hasOneThrough(
            \App\Models\User::class,
            \Modules\Vendor\app\Models\Vendor::class,
            'id',
            'id',
            'vendor_id',
            'user_id'
        );
    }

    public function withdraws()
    {
        return $this->hasMany(\Modules\Withdraw\app\Models\Withdraw::class, 'reciever_id', 'vendor_id');
    }

    public function getTotalWithdrawnAttribute()
    {
        return $this->withdraws()->where('status', 'accepted')->sum('sent_amount');
    }

    public function getActualAvailableBalanceAttribute()
    {
        return $this->available_balance - $this->total_withdrawn;
    }

    public function updateBalance($earnings, $commission)
    {
        $this->total_earnings += $earnings;
        $this->commission_deducted += $commission;
        $this->available_balance = $this->total_earnings - $this->commission_deducted - $this->withdrawn_amount;
        $this->save();
    }

    public function scopeForVendor($query, $vendorId = null)
    {
        if ($vendorId) {
            return $query->where('vendor_id', $vendorId);
        }
        return $query;
    }
}
