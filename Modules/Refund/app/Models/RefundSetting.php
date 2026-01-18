<?php

namespace Modules\Refund\app\Models;

use Illuminate\Database\Eloquent\Model;

class RefundSetting extends Model
{
    protected $fillable = [
        'customer_pays_return_shipping',
        'refund_processing_days',
    ];

    protected $casts = [
        'customer_pays_return_shipping' => 'boolean',
        'refund_processing_days' => 'integer',
    ];

    /**
     * Get the singleton instance
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([
            'customer_pays_return_shipping' => false,
            'refund_processing_days' => 7,
        ]);
    }
}
