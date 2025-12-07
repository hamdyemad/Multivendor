<?php

namespace Modules\Withdraw\app\Repositories;

use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Vendor\app\Models\Vendor;
use Modules\Withdraw\app\Interfaces\WithdrawRepositoryInterface;
use Modules\Withdraw\app\Models\Withdraw;

class WithdrawRepository implements WithdrawRepositoryInterface
{
    /**
     * Get all departments with filters and pagination
     */
    public function getVendor()
    {
        return Vendor::latest()
            ->with(['translations' => function ($query) {
                $query->where('lang_key', 'name');
            }])
            ->get()
            ->map(function ($vendor) {
                $vendor->translation_name = $vendor->translations->first();
                return $vendor;
            });
    }

    public function getVendorBalance($vendor_id)
    {
        // Get the vendor to access the user_id
        $vendor = Vendor::find($vendor_id);

        if (!$vendor || !$vendor->user_id) {
            return [
                "orders_price" => "0.000",
                "vendor_commission" => 0,
                "total_vendor_balance" => "0.000",
                "total_sent_money" => "0.000",
                "remaining" => "0.000",
                "bnaia_balance" => "0.000",
                "waiting_approve_requests" => "0.000"
            ];
        }

        // $orders = OrderProduct::where("vendor_id", $vendor_id)->get();

        $vendor_order_prices = $vendor->total_balance;

        $total_sent_money = Withdraw::where(function ($q) use ($vendor) {
            $q->where('reciever_id', $vendor->id);
        })
            ->where('status', 'accepted')
            ->sum('sent_amount');

        $waiting_approve_requests = Withdraw::where(function ($q) use ($vendor) {
            $q->where('reciever_id', $vendor->id);
        })
            ->where('status', 'new')
            ->sum('sent_amount');

        $commission = 10;
        $bnaia_balance = $vendor_order_prices - ($vendor_order_prices * $commission / 100);
        $remaining = $vendor_order_prices - $bnaia_balance;
        return [
            "orders_price" => number_format($vendor_order_prices, 2),
            "vendor_commission" => $commission,
            "bnaia_balance" => number_format($bnaia_balance, 2),
            "total_vendor_balance" => number_format($remaining, 2),
            "total_sent_money" => number_format($total_sent_money, 2),
            "remaining" => number_format($remaining - $total_sent_money, 2),
            "waiting_approve_requests" => $waiting_approve_requests
        ];
    }
}
