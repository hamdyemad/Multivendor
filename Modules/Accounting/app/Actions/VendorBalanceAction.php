<?php

namespace Modules\Accounting\app\Actions;

use Modules\Accounting\app\Services\VendorBalanceService;

class VendorBalanceAction
{
    public function __construct(
        protected VendorBalanceService $vendorBalanceService
    ) {}

    public function getDataTable(array $data)
    {
        $filters = [
            'search' => $data['search'] ?? null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
        ];

        // Add vendor filter if user is vendor
        if (isVendor()) {
            $filters['vendor_id'] = auth()->user()->vendor->id ?? null;
        }

        $perPage = $data['per_page'] ?? 10;
        $page = $data['page'] ?? 1;
        $orderDirection = $data['orderDirection'] ?? 'desc';

        $dataPaginated = $this->vendorBalanceService->getAllVendorBalances($filters, $perPage, $page, $orderDirection);

        $formattedData = $dataPaginated->map(function ($balance) {
            // Calculate actual values from order_products table
            $orderProductsData = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $balance->vendor_id)
                ->selectRaw('SUM(price * quantity) as total_earnings, SUM(commission) as total_commission')
                ->first();

            $totalEarnings = $orderProductsData->total_earnings ?? 0;
            $totalCommission = $orderProductsData->total_commission ?? 0;
            $availableBalance = $totalEarnings - $totalCommission;

            $totalWithdrawn = $balance->withdraws()->where('status', 'accepted')->sum('sent_amount');
            $actualAvailableBalance = $availableBalance - $totalWithdrawn;

            return [
                'id' => $balance->id,
                'vendor_name' => $balance->vendor->user->name ?? $balance->vendor->name ?? 'N/A',
                'vendor_email' => $balance->vendor->user->email ?? $balance->vendor->email ?? 'N/A',
                'total_earnings' => number_format($totalEarnings, 2) . ' ' . currency(),
                'commission_deducted' => number_format($totalCommission, 2) . ' ' . currency(),
                'available_balance' => number_format($availableBalance, 2) . ' ' . currency(),
                'total_withdrawn' => number_format($totalWithdrawn, 2) . ' ' . currency(),
                'actual_available_balance' => number_format($actualAvailableBalance, 2) . ' ' . currency(),
                'updated_at' => $balance->updated_at,
            ];
        });

        return [
            'data' => $formattedData,
            'totalRecords' => $dataPaginated->total(),
            'filteredRecords' => $dataPaginated->total(),
            'dataPaginated' => $dataPaginated
        ];
    }
}


