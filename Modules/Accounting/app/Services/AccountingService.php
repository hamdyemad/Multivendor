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

    /**
     * Process vendor-specific stage changes
     * Called when a vendor changes their order stage (not the main order stage)
     */
    public function processVendorOrderStageChange(Order $order, int $vendorId, OrderStage $newStage, ?OrderStage $previousStage = null)
    {
        // Process delivered vendor orders
        if ($newStage->type === 'deliver') {
            $this->processDeliveredVendorOrder($order, $vendorId);
        }

        // Process refunded vendor orders
        if ($newStage->type === 'refund') {
            $this->processRefundedVendorOrder($order, $vendorId);
        }
    }

    private function processDeliveredOrder(Order $order)
    {
        // Group order products by vendor
        $vendorGroups = $order->products()->with(['vendorProduct.vendor.translations', 'vendorProduct.product.department'])->get()->groupBy('vendor_id');

        foreach ($vendorGroups as $vendorId => $products) {
            if (!$vendorId) continue;

            // Check if accounting entry already exists for this vendor and order
            $existingEntry = AccountingEntry::where('order_id', $order->id)
                ->where('vendor_id', $vendorId)
                ->where('type', 'income')
                ->first();

            if ($existingEntry) {
                // Already processed, skip
                continue;
            }

            $vendor = $products->first()->vendorProduct?->vendor;
            $vendorNameEn = $vendor?->getTranslation('name', 'en') ?? $vendor?->name ?? 'Unknown';
            $vendorNameAr = $vendor?->getTranslation('name', 'ar') ?? $vendor?->name ?? 'غير معروف';

            // Calculate totals - price already includes (unit_price * quantity)
            $vendorTotal = $products->sum('price');
            
            // Add shipping cost
            $vendorShipping = $products->sum('shipping_cost');
            
            // Get vendor's fees and discounts from order_extra_fees_discounts
            $extrasResult = \Illuminate\Support\Facades\DB::table('order_extra_fees_discounts')
                ->where('order_id', $order->id)
                ->where('vendor_id', $vendorId)
                ->select(
                    \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN type = "fee" THEN cost ELSE 0 END) as fees_total'),
                    \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN type = "discount" THEN cost ELSE 0 END) as discounts_total')
                )
                ->first();

            $feesTotal = $extrasResult->fees_total ?? 0;
            $discountsTotal = $extrasResult->discounts_total ?? 0;

            // Get vendor's promo code and points shares from vendor_order_stages
            $vendorStage = \Illuminate\Support\Facades\DB::table('vendor_order_stages')
                ->where('order_id', $order->id)
                ->where('vendor_id', $vendorId)
                ->select('promo_code_share', 'points_share')
                ->first();

            $promoCodeShare = $vendorStage->promo_code_share ?? 0;
            $pointsShare = $vendorStage->points_share ?? 0;

            // Total for accounting = products + shipping + fees - discounts
            // (Don't subtract customer promo/points because vendor receives this from Bnaia)
            $vendorTotalWithExtras = $vendorTotal + $vendorShipping + $feesTotal - $discountsTotal;
            
            // Calculate vendor's share of customer promo/points for metadata only
            $orderGrandTotal = $order->products->sum(function($p) {
                return $p->price + ($p->shipping_cost ?? 0);
            });
            
            $vendorGrandTotal = $vendorTotal + $vendorShipping;
            $vendorPercentage = $orderGrandTotal > 0 
                ? ($vendorGrandTotal / $orderGrandTotal) 
                : 0;
            
            $customerPromoShare = ($order->customer_promo_code_amount ?? 0) * $vendorPercentage;
            $customerPointsShare = ($order->points_cost ?? 0) * $vendorPercentage;

            // Calculate commission on total with extras
            // Commission = 0 is valid (means no commission), don't fallback to department
            $totalCommissionAmount = $products->sum(function($product) {
                $productTotal = $product->price + ($product->shipping_cost ?? 0);
                $commissionPercent = $product->commission ?? 0;
                return $productTotal * ($commissionPercent / 100);
            });

            $vendorAmount = $vendorTotalWithExtras - $totalCommissionAmount;

            // Calculate average commission rate for display
            $avgCommissionRate = $vendorTotalWithExtras > 0 ? ($totalCommissionAmount / $vendorTotalWithExtras) * 100 : 0;

            // Create income entry for each vendor
            AccountingEntry::create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'type' => 'income',
                'amount' => $vendorTotalWithExtras, // Now includes fees, discounts, promo codes, and points
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
                    'product_count' => $products->count(),
                    'fees' => $feesTotal,
                    'discounts' => $discountsTotal,
                    'promo_code_share' => $promoCodeShare,
                    'points_share' => $pointsShare,
                    'customer_promo_share' => $customerPromoShare,
                    'customer_points_share' => $customerPointsShare
                ]
            ]);

            // Update vendor balance - pass full amount (with extras) as earnings, not vendor_amount
            $this->updateVendorBalance($vendorId, $vendorTotalWithExtras, $totalCommissionAmount);
        }
    }

    private function processRefundedOrder(Order $order)
    {
        // Group order products by vendor
        $vendorGroups = $order->products()->with(['vendorProduct.vendor.translations'])->get()->groupBy('vendor_id');

        foreach ($vendorGroups as $vendorId => $products) {
            if (!$vendorId) continue;

            // Check if refund entry already exists
            $existingRefund = AccountingEntry::where('order_id', $order->id)
                ->where('vendor_id', $vendorId)
                ->where('type', 'refund')
                ->first();

            if ($existingRefund) {
                // Already processed, skip
                continue;
            }

            $vendor = $products->first()->vendorProduct?->vendor;
            $vendorNameEn = $vendor?->getTranslation('name', 'en') ?? $vendor?->name ?? 'Unknown';
            $vendorNameAr = $vendor?->getTranslation('name', 'ar') ?? $vendor?->name ?? 'غير معروف';
            
            // Calculate total - price already includes (unit_price * quantity)
            $vendorTotal = $products->sum('price');
            $vendorShipping = $products->sum('shipping_cost');
            $vendorTotalWithShipping = $vendorTotal + $vendorShipping;

            // Create refund entry for each vendor
            AccountingEntry::create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'type' => 'refund',
                'amount' => -$vendorTotalWithShipping,
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

    /**
     * Process delivered order for a specific vendor
     * Called when vendor changes their stage to deliver
     */
    private function processDeliveredVendorOrder(Order $order, int $vendorId)
    {
        // Check if accounting entry already exists for this vendor and order
        $existingEntry = AccountingEntry::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'income')
            ->first();

        if ($existingEntry) {
            // Already processed, skip
            return;
        }

        // Get products for this specific vendor
        $products = $order->products()
            ->where('vendor_id', $vendorId)
            ->with(['vendorProduct.vendor.translations', 'vendorProduct.product.department'])
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        $vendor = $products->first()->vendorProduct?->vendor;
        $vendorNameEn = $vendor?->getTranslation('name', 'en') ?? $vendor?->name ?? 'Unknown';
        $vendorNameAr = $vendor?->getTranslation('name', 'ar') ?? $vendor?->name ?? 'غير معروف';

        // Calculate totals - price already includes (unit_price * quantity)
        $vendorTotal = $products->sum('price');
        
        // Add shipping cost
        $vendorShipping = $products->sum('shipping_cost');
        
        // Get vendor's fees and discounts from order_extra_fees_discounts
        $extrasResult = \Illuminate\Support\Facades\DB::table('order_extra_fees_discounts')
            ->where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->select(
                \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN type = "fee" THEN cost ELSE 0 END) as fees_total'),
                \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN type = "discount" THEN cost ELSE 0 END) as discounts_total')
            )
            ->first();

        $feesTotal = $extrasResult->fees_total ?? 0;
        $discountsTotal = $extrasResult->discounts_total ?? 0;

        // Get vendor's promo code and points shares from vendor_order_stages
        $vendorStage = \Illuminate\Support\Facades\DB::table('vendor_order_stages')
            ->where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->select('promo_code_share', 'points_share')
            ->first();

        $promoCodeShare = $vendorStage->promo_code_share ?? 0;
        $pointsShare = $vendorStage->points_share ?? 0;

        // Total for accounting = products + shipping + fees - discounts
        // (Don't subtract customer promo/points because vendor receives this from Bnaia)
        $vendorTotalWithExtras = $vendorTotal + $vendorShipping + $feesTotal - $discountsTotal;
        
        // Calculate vendor's share of customer promo/points for metadata only
        $orderGrandTotal = $order->products->sum(function($p) {
            return $p->price + ($p->shipping_cost ?? 0);
        });
        
        $vendorGrandTotal = $vendorTotal + $vendorShipping;
        $vendorPercentage = $orderGrandTotal > 0 
            ? ($vendorGrandTotal / $orderGrandTotal) 
            : 0;
        
        $customerPromoShare = ($order->customer_promo_code_amount ?? 0) * $vendorPercentage;
        $customerPointsShare = ($order->points_cost ?? 0) * $vendorPercentage;

        // Calculate commission on total with extras
        // Commission = 0 is valid (means no commission), don't fallback to department
        $totalCommissionAmount = $products->sum(function($product) {
            $productTotal = $product->price + ($product->shipping_cost ?? 0);
            $commissionPercent = $product->commission ?? 0;
            return $productTotal * ($commissionPercent / 100);
        });

        $vendorAmount = $vendorTotalWithExtras - $totalCommissionAmount;

        // Calculate average commission rate for display
        $avgCommissionRate = $vendorTotalWithExtras > 0 ? ($totalCommissionAmount / $vendorTotalWithExtras) * 100 : 0;

        // Create income entry for this vendor
        AccountingEntry::create([
            'order_id' => $order->id,
            'vendor_id' => $vendorId,
            'type' => 'income',
            'amount' => $vendorTotalWithExtras, // Now includes fees, discounts, promo codes, and points
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
                'product_count' => $products->count(),
                'vendor_stage_change' => true,
                'fees' => $feesTotal,
                'discounts' => $discountsTotal,
                'promo_code_share' => $promoCodeShare,
                'points_share' => $pointsShare,
                'customer_promo_share' => $customerPromoShare,
                'customer_points_share' => $customerPointsShare
            ]
        ]);

        // Update vendor balance - pass full amount (with extras) as earnings, not vendor_amount
        $this->updateVendorBalance($vendorId, $vendorTotalWithExtras, $totalCommissionAmount);
    }

    /**
     * Process refunded order for a specific vendor
     * Called when vendor changes their stage to refund
     */
    private function processRefundedVendorOrder(Order $order, int $vendorId)
    {
        // Get products for this specific vendor
        $products = $order->products()
            ->where('vendor_id', $vendorId)
            ->with(['vendorProduct.vendor.translations'])
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        $vendor = $products->first()->vendorProduct?->vendor;
        $vendorNameEn = $vendor?->getTranslation('name', 'en') ?? $vendor?->name ?? 'Unknown';
        $vendorNameAr = $vendor?->getTranslation('name', 'ar') ?? $vendor?->name ?? 'غير معروف';

        // Calculate total - price already includes (unit_price * quantity)
        $vendorTotal = $products->sum('price');
        $vendorShipping = $products->sum('shipping_cost');
        $vendorTotalWithShipping = $vendorTotal + $vendorShipping;

        // Check if refund entry already exists
        $existingRefund = AccountingEntry::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'refund')
            ->first();

        if ($existingRefund) {
            // Already processed, skip
            return;
        }

        // Create refund entry for this vendor
        AccountingEntry::create([
            'order_id' => $order->id,
            'vendor_id' => $vendorId,
            'type' => 'refund',
            'amount' => -$vendorTotalWithShipping,
            'description' => json_encode([
                'en' => __('accounting.order_refunded_description', ['order_number' => $order->order_number, 'vendor_name' => $vendorNameEn], 'en'),
                'ar' => __('accounting.order_refunded_description', ['order_number' => $order->order_number, 'vendor_name' => $vendorNameAr], 'ar'),
            ]),
            'metadata' => [
                'order_number' => $order->order_number,
                'refunded_at' => now(),
                'product_count' => $products->count(),
                'vendor_stage_change' => true
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

    private function updateVendorBalance($vendorId, $earnings, $commission)
    {
        $balance = VendorBalance::firstOrCreate(['vendor_id' => $vendorId]);
        $balance->updateBalance($earnings, $commission);
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
            // Net profit = Income - Commissions - Expenses
            'net_profit' => (clone $query)->income()->sum('amount') - (clone $query)->income()->sum('commission_amount') - $expenseQuery->sum('amount')
        ];
    }
}
