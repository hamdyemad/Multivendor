<?php

namespace Modules\Accounting\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Order\app\Models\Order;
use Modules\Vendor\app\Models\Vendor;
use Modules\Accounting\app\Models\Scopes\VendorScope;

class AccountingEntry extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $fillable = [
        'order_id',
        'vendor_id',
        'type',
        'amount',
        'commission_rate',
        'commission_amount',
        'vendor_amount',
        'description',
        'metadata',
        'country_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new VendorScope);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeCommission($query)
    {
        return $query->where('type', 'commission');
    }

    public function scopeRefund($query)
    {
        return $query->where('type', 'refund');
    }

    public function scopeForVendor($query, $vendorId = null)
    {
        if ($vendorId) {
            return $query->where('vendor_id', $vendorId);
        }
        return $query;
    }

    protected function applyCustomSearch(\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder
    {
        $query->whereHas('order', function($subQ) use ($search) {
            $subQ->where('order_number', 'like', "%{$search}%");
        })->orWhereHas('vendor', function($subQ) use ($search) {
            $subQ->where('name', 'like', "%{$search}%");
        });
        
        return $query;
    }
}
