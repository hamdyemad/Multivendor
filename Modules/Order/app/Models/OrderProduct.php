<?php

namespace Modules\Order\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\database\factories\OrderProductFactory;

// use Modules\Order\Database\Factories\OrderProductFactory;

class OrderProduct extends Model
{
    use HasFactory;

    public static function newFactory()
    {
        return OrderProductFactory::new();
    }

    protected $fillable = [
        "order_id",
        "vendor_id",
        "price",
        "commission"
    ];
}
