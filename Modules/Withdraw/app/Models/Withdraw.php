<?php

namespace Modules\Withdraw\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Withdraw\Database\Factories\WithdrawFactory;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        "request_from",
        "sender_id",
        "reciever_id",
        "before_sending_money",
        "sent_amount",
        "after_sending_amount"
    ];

    // protected static function newFactory(): WithdrawFactory
    // {
    //     // return WithdrawFactory::new();
    // }
}
