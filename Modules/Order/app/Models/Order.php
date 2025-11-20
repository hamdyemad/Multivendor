<?php

namespace Modules\Order\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\database\factories\OrderFactory;

// use Modules\Order\Database\Factories\OrderFactory;

class Order extends Model
{
    use HasFactory;

    public static function newFactory()
    {
        return OrderFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "order_number"
    ];
}
