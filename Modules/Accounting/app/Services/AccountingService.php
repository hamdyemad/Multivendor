<?php

namespace Modules\Accounting\app\Services;

use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\VendorBalance;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;

class AccountingService
{
    public function processOrderStageChange(Order $order, OrderStage $newStage)
    {
        // Process delivered orders
        if ($newStage->type === 'deliver') {
            $this->processDeliveredOrder($order);
        }

        // Process refunded orders
        if ($newStage->type === 'refund') {
            $this->processRefundedOrder($order);
        }
    }

    private function processDeliveredOrder(Order $order)
    {
        // Group order products by vendor
        $vendorGroups = $order->products()->with(['vendorProduct.vendor.translations', 'vendorProduct.product.department'])->get()->groupBy('vendor_id');

        foreach ($vendorGroups as $vendorId => $products) {
            if (!$vendorId) continue;

            $vendorTotal = 0;
            $totalCommissionAmount = 0;
            $vendor = $products->first()->vendorProduct?->vendor;
            $vendorNameEn = $vendor?->getTranslation('name', 'en') ?? $vendor?->name ?? 'Unknown';
            $vendorNameAr = $vendor?->getTranslation('name', 'ar') ?? $vendor?->name ?? 'غير معروف';

            foreach ($products as $product) {
                $productTotal = $product->price * $product->quantity;
                $vendorTotal += $productTotal;
                
                // Get commission rate from product's department
                $commissionRate = $product->vendorProduct?->product?->department?->commission ?? 0;
                $totalCommissionAmount += $productTotal * ($commissionRate / 100);
            }

            $vendorAmount = $vendorTotal - $totalCommissionAmount;
            
            // Calculate average commission rate for display
            $avgCommissionRate = $vendorTotal > 0 ? ($totalCommissionAmount / $vendorTotal) * 100 : 0;

            // Create income entry for each vendor
            AccountingEntry::create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'type' => 'income',
                'amount' => $vendorTotal,
                'commission_rate' => $avgCommissionRate,
                'commission_amount' => $totalCommissionAmount,
                'vendor_amount' => $vendorAmount,
                'description' => json_encode([
                    'en' => __('accounting.order_delivered_description', ['order_number' => $order->order_number, 'vendor_name' => $vendorNameEn], 'en'),
                    'ar' => __('accounting.order_delivered_description', ['order_number' => $order->order_number, 'vendor_name' => $vendorNameAr], 'ar'),
                ]),
                'metadata' => [
                    'order_number' => $order->order_number,
                    'stage_changed_at' => now(),
                    'product_count' => $products->count()
                ]
            ]);

            // Update vendor balance
            $this->updateVendorBalance($vendorId, $vendorAmount, $totalCommissionAmount);
        }
    }

    private function processRefundedOrder(Order $order)
    {
        // Group order products by vendor
        $vendorGroups = $order->products()->with(['vendorProduct.vendor.translations'])->get()->groupBy('vendor_id');

        foreach ($vendorGroups as $vendorId => $products) {
            if (!$vendorId) continue;

            $vendor = $products->first()->vendorProduct?->vendor;
            $vendorNameEn = $vendor?->getTranslation('name', 'en') ?? $vendor?->name ?? 'Unknown';
            $vendorNameAr = $vendor?->getTranslation('name', 'ar') ?? $vendor?->name ?? 'غير معروف';
            $vendorTotal = $products->sum(function($product) {
                return $product->price * $product->quantity;
            });

            // Create refund entry for each vendor
            AccountingEntry::create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'type' => 'refund',
                'amount' => -$vendorTotal,
                'description' => json_encode([
                    'en' => __('accounting.order_refunded_description', ['order_number' => $order->order_number, 'vendor_name' => $vendorNameEn], 'en'),
                    'ar' => __('accounting.order_refunded_description', ['order_number' => $order->order_number, 'vendor_name' => $vendorNameAr], 'ar'),
                ]),
                'metadata' => [
                    'order_number' => $order->order_number,
                    'refunded_at' => now(),
                    'product_count' => $products->count()
                ]
            ]);

            // Reverse vendor balance if it was previously delivered
            $previousEntry = AccountingEntry::where('order_id', $order->id)
                ->where('vendor_id', $vendorId)
                ->where('type', 'income')
                ->first();

            if ($previousEntry) {
                $this->updateVendorBalance(
                    $vendorId,
                    -$previousEntry->vendor_amount,
                    -$previousEntry->commission_amount
                );
            }
        }
    }

    private function updateVendorBalance($vendorId, $earnings, $commission)
    {
        $balance = VendorBalance::firstOrCreate(['vendor_id' => $vendorId]);
        $balance->updateBalance($earnings, $commission);
    }

    private function getCommissionRate($vendorId)
    {
        // This method is kept for backward compatibility
        // Commission is now calculated per product from department
        return 0;
    }

    public function getAccountingSummary($filters = [])
    {
        $query = AccountingEntry::query();

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Get expenses from Expense model
        $expenseQuery = \Modules\Accounting\app\Models\Expense::query();
        if (!empty($filters['date_from'])) {
            $expenseQuery->whereDate('expense_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $expenseQuery->whereDate('expense_date', '<=', $filters['date_to']);
        }

        return [
            'total_income' => (clone $query)->income()->sum('amount'),
            'total_expenses' => $expenseQuery->sum('amount'),
            'total_commissions' => (clone $query)->income()->sum('commission_amount'),
            'total_refunds' => abs((clone $query)->refund()->sum('amount')),
            'net_profit' => (clone $query)->income()->sum('amount') - $expenseQuery->sum('amount')
        ];
    }
}
