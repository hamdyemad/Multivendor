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

        // Get vendor's total balance from delivered orders
        $vendor_order_prices = $vendor->total_balance;

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

        // Commission from Bnaia
        $commission = $vendor->bnaia_commission;
        $bnaia_balance = $commission;
        
        // Total Vendor's Transactions = delivered orders total - sent money
        $total_transactions = $vendor_order_prices - $total_sent_money;
        
        // Remaining credit after commission
        $remaining_credit = $vendor_order_prices - $bnaia_balance;
        
        // Remaining after sent money
        $remaining = $remaining_credit - $total_sent_money;
        
        return [
            "orders_price" => number_format($total_transactions, 2),
            "vendor_commission" => $commission,
            "bnaia_balance" => number_format($bnaia_balance, 2),
            "total_vendor_balance" => number_format($remaining_credit, 2),
            "total_sent_money" => number_format($total_sent_money, 2),
            "remaining" => number_format($remaining, 2),
            "waiting_approve_requests" => $waiting_approve_requests
        ];
    }
}
