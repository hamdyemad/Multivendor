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
                "orders_price" => "0.00",
                "vendor_commission" => 0,
                "total_vendor_balance" => "0.00",
                "total_sent_money" => "0.00",
                "remaining" => "0.00",
                "bnaia_balance" => "0.00",
                "waiting_approve_requests" => "0.00"
            ];
        }

        // Get vendor's total balance from delivered orders (product price * quantity)
        $delivered_orders_total = $vendor->total_balance;

        // Get total sent money (accepted withdrawals)
        $total_sent_money = Withdraw::where(function ($q) use ($vendor) {
            $q->where('reciever_id', $vendor->id);
        })
            ->where('status', 'accepted')
            ->sum('sent_amount');

        // Get waiting approve requests
        $waiting_approve_requests = Withdraw::where(function ($q) use ($vendor) {
            $q->where('reciever_id', $vendor->id);
        })
            ->where('status', 'new')
            ->sum('sent_amount');

        // Commission from Bnaia (calculated from delivered orders)
        $bnaia_balance = $vendor->bnaia_commission;
        
        // Total Vendor's Credit = delivered orders total - bnaia commission
        $total_vendor_balance = $delivered_orders_total - $bnaia_balance;
        
        // Remaining = Total Vendor's Credit - sent money
        $remaining = $total_vendor_balance - $total_sent_money;
        
        return [
            "orders_price" => number_format($delivered_orders_total, 2),
            "vendor_commission" => $bnaia_balance,
            "bnaia_balance" => number_format($bnaia_balance, 2),
            "total_vendor_balance" => number_format($total_vendor_balance, 2),
            "total_sent_money" => number_format($total_sent_money, 2),
            "remaining" => number_format($remaining, 2),
            "waiting_approve_requests" => $waiting_approve_requests
        ];
    }
}
