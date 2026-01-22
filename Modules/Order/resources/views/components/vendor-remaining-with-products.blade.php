@props([
    'vendorName' => 'Vendor',
    'products' => [],
    'subtotalBeforeTax' => 0,
    'taxAmount' => 0,
    'subtotalWithTax' => 0,
    'shipping' => 0,
    'total' => 0,
    'commissionPercentage' => 0,
    'commissionAmount' => 0,
    'remaining' => 0,
    'promoCodeShare' => 0,
    'pointsShare' => 0,
    'fees' => 0,
    'discounts' => 0,
    'refundedAmount' => 0,
    'refundedCommission' => 0,
    'finalCommission' => 0,
    'customerPromoAmount' => 0,
    'customerPointsCost' => 0,
    'orderCustomerPaid' => null,
    'colors' => ['#28a745', '#5dd879']
])

<div class="card border-0"
    style="background: white; color: #333; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-20 d-flex align-items-center" style="color: {{ $colors[0] }};">
            <i class="uil uil-store me-2" style="font-size: 20px;"></i>
            {{ $vendorName }} {{ trans('order::order.remaining_summary') }}
        </h6>
        
        {{-- Product Boxes Inside --}}
        @if(count($products) > 0)
            <div class="row mb-3">
                @foreach($products as $product)
                    @php
                        // Get product details
                        $productName = $product->vendorProduct->product->name ?? 'N/A';
                        
                        // Build variant path
                        $variantConfig = $product->vendorProductVariant?->variantConfiguration;
                        $variantKey = $variantConfig?->key?->getTranslation('name', app()->getLocale()) ?? null;
                        $variantValue = $variantConfig?->getTranslation('name', app()->getLocale()) ?? null;
                        $variantPath = null;
                        if ($variantKey && $variantValue) {
                            $variantPath = $variantKey . ' → ' . $variantValue;
                        } elseif ($variantValue) {
                            $variantPath = $variantValue;
                        }
                        
                        // Price calculations
                        $productTotalWithTax = $product->price;
                        $productTaxAmount = $product->taxes->sum('amount') ?? 0;
                        $productTotalBeforeTax = $productTotalWithTax - $productTaxAmount;
                        $productShippingCost = $product->shipping_cost ?? 0;
                        
                        // Commission (calculated on total with shipping)
                        $productCommissionPercent = $product->commission;
                        $productTotalWithShipping = $productTotalWithTax + $productShippingCost;
                        $productCommissionAmount = ($productTotalWithShipping * $productCommissionPercent) / 100;
                        
                        // Check if this product has been refunded
                        $productRefundedAmount = 0;
                        $productRefundedCommission = 0;
                        
                        // Get refund items for this order product
                        $refundItems = $product->refundItems()
                            ->whereHas('refundRequest', function($q) {
                                $q->where('status', 'refunded');
                            })
                            ->with('refundRequest')
                            ->get();
                        
                        foreach ($refundItems as $refundItem) {
                            // Calculate total refund amount for this item (total_price already includes tax)
                            $itemRefundAmount = ($refundItem->total_price ?? 0) + ($refundItem->shipping_amount ?? 0);
                            $productRefundedAmount += $itemRefundAmount;
                            
                            // Get commission percentage from order_products table
                            $orderProduct = $refundItem->orderProduct;
                            $itemCommissionPercent = $orderProduct ? ($orderProduct->commission ?? 0) : 0;
                            if ($itemRefundAmount > 0 && $itemCommissionPercent > 0) {
                                $productRefundedCommission += ($itemRefundAmount * $itemCommissionPercent) / 100;
                            }
                        }
                        
                        // Calculate final values after refunds
                        $finalProductCommission = $productCommissionAmount - $productRefundedCommission;
                        
                        // Remaining calculation:
                        // Start with: Total - Original Commission = Original Remaining
                        // Subtract: Net refund impact (Refunded Amount - Refunded Commission)
                        $remainingBeforeRefund = $productTotalWithShipping - $productCommissionAmount;
                        $netRefundImpact = $productRefundedAmount - $productRefundedCommission;
                        $productRemaining = $remainingBeforeRefund - $netRefundImpact;
                    @endphp
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="background: #f8f9fa; color: #333;">
                            <div class="card-body">
                                <h6 class="card-title fw-bold mb-3 d-flex align-items-start" style="font-size: 14px; color: #333;">
                                    <i class="uil uil-box me-2" style="font-size: 18px; flex-shrink: 0; color: {{ $colors[0] }};"></i>
                                    <div>
                                        <div>#{{ $loop->iteration }} - {{ $productName }}</div>
                                        @if($variantPath)
                                            <small style="font-size: 11px; color: #666;">{{ $variantPath }}</small>
                                        @endif
                                    </div>
                                </h6>
                                <div class="summary-details" style="font-size: 13px;">
                                    <div class="summary-row mb-2" style="color: #333;">
                                        <span class="fw-bold">{{ trans('order::order.subtotal') }}</span>
                                        <span class="fw-bold">{{ number_format($productTotalBeforeTax, 2) }} {{ currency() }}</span>
                                    </div>
                                    <div class="summary-row mb-2" style="color: #333;">
                                        <span class="fw-bold">{{ trans('order::order.taxes_price') }}</span>
                                        <span class="fw-bold">+{{ number_format($productTaxAmount, 2) }} {{ currency() }}</span>
                                    </div>
                                    <div class="summary-row mb-2" style="color: #333;">
                                        <span class="fw-bold">{{ trans('order::order.subtotal_including_tax') }}</span>
                                        <span class="fw-bold">{{ number_format($productTotalWithTax, 2) }} {{ currency() }}</span>
                                    </div>
                                    @if($productShippingCost > 0)
                                        <div class="summary-row mb-2" style="color: #333;">
                                            <span class="fw-bold">{{ trans('order::order.shipping') }}</span>
                                            <span class="fw-bold">+{{ number_format($productShippingCost, 2) }} {{ currency() }}</span>
                                        </div>
                                    @endif
                                    <div class="summary-row mb-2" style="color: #333;">
                                        <span class="fw-bold">{{ trans('order::order.total_with_shipping') }}</span>
                                        <span class="fw-bold">{{ number_format($productTotalWithShipping, 2) }} {{ currency() }}</span>
                                    </div>
                                    <div class="summary-row mb-2" style="color: #333;">
                                        <span class="fw-bold">{{ trans('order::order.bnaia_commission') }}</span>
                                        <span class="fw-bold" style="color: #dc3545;">({{ $productCommissionPercent }}%) -{{ number_format($productCommissionAmount, 2) }} {{ currency() }}</span>
                                    </div>
                                    @if($productRefundedAmount > 0)
                                        <div class="summary-row mb-2" style="font-size: 12px; color: #666; padding-left: 15px;">
                                            <span class="fw-500">{{ trans('order::order.minus') }} {{ trans('order::order.refunded_commission') }}</span>
                                            <span class="fw-500" style="color: #28a745;">-{{ number_format($productRefundedCommission, 2) }} {{ currency() }}</span>
                                        </div>
                                        <div class="summary-row mb-2" style="background: #fff3cd; padding: 8px 12px; border-radius: 6px; color: #856404;">
                                            <span class="fw-bold">= {{ trans('order::order.net_commission') }}</span>
                                            <span class="fw-bold">{{ number_format($finalProductCommission, 2) }} {{ currency() }}</span>
                                        </div>
                                        <hr style="border-color: #ddd; margin: 10px 0;">
                                        <div class="summary-row mb-2" style="font-size: 13px; color: #666;">
                                            <span>{{ trans('order::order.calculation') }}: {{ number_format($productTotalWithShipping, 2) }} - {{ number_format($productCommissionAmount, 2) }}</span>
                                            <span></span>
                                        </div>
                                        <div class="summary-row mb-2" style="background: #e8f5e9; padding: 8px 12px; border-radius: 6px; color: #2e7d32;">
                                            <span class="fw-bold">= {{ trans('order::order.remaining_before_refund') }}</span>
                                            <span class="fw-bold">{{ number_format($remainingBeforeRefund, 2) }} {{ currency() }}</span>
                                        </div>
                                        <hr style="border-color: #ddd; margin: 10px 0;">
                                        <div class="summary-row mb-2" style="color: #333;">
                                            <span class="fw-bold">{{ trans('order::order.total_refunded') }}</span>
                                            <span class="fw-bold" style="color: #dc3545;">-{{ number_format($productRefundedAmount, 2) }} {{ currency() }}</span>
                                        </div>
                                        <div class="summary-row mb-2" style="font-size: 12px; color: #666; padding-left: 15px;">
                                            <span class="fw-500">{{ trans('order::order.plus') }} {{ trans('order::order.refunded_commission') }}</span>
                                            <span class="fw-500" style="color: #28a745;">+{{ number_format($productRefundedCommission, 2) }} {{ currency() }}</span>
                                        </div>
                                        <div class="summary-row mb-2" style="background: #ffe6e6; padding: 8px 12px; border-radius: 6px; color: #c62828;">
                                            <span class="fw-bold">= {{ trans('order::order.net_refund_impact') }}</span>
                                            <span class="fw-bold">{{ number_format($netRefundImpact, 2) }} {{ currency() }}</span>
                                        </div>
                                        <hr style="border-color: #ddd; margin: 10px 0;">
                                        <div class="summary-row mb-2" style="font-size: 13px; color: #666;">
                                            <span>{{ trans('order::order.calculation') }}: {{ number_format($remainingBeforeRefund, 2) }} - {{ number_format($netRefundImpact, 2) }}</span>
                                            <span></span>
                                        </div>
                                    @else
                                        <hr style="border-color: #ddd; margin: 10px 0;">
                                        <div class="summary-row mb-2" style="font-size: 13px; color: #666;">
                                            <span>{{ trans('order::order.calculation') }}: {{ number_format($productTotalWithShipping, 2) }} - {{ number_format($productCommissionAmount, 2) }}</span>
                                            <span></span>
                                        </div>
                                    @endif
                                    <hr style="border-color: #ddd; margin: 10px 0;">
                                    <div class="summary-row" style="font-size: 15px; color: #333;">
                                        <span class="fw-bold">= {{ trans('order::order.remaining') }}</span>
                                        <span class="fw-bold" style="color: {{ $productRemaining >= 0 ? $colors[0] : '#dc3545' }};">{{ number_format($productRemaining, 2) }} {{ currency() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        {{-- Vendor Total Summary --}}
        @php
            // Customer total = Total with Shipping - Promo - Points
            // (discounts applied AFTER shipping)
            $customerTotal = $total - $customerPromoAmount - $customerPointsCost;
            $customerTotal = max(0, $customerTotal); // Cannot be negative
        @endphp
        <div class="summary-details">
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.subtotal') }}</span>
                <span class="fw-bold">{{ number_format($subtotalBeforeTax, 2) }} {{ currency() }}</span>
            </div>
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.taxes_price') }}</span>
                <span class="fw-bold">+{{ number_format($taxAmount, 2) }} {{ currency() }}</span>
            </div>
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.subtotal_including_tax') }}</span>
                <span class="fw-bold">{{ number_format($subtotalWithTax, 2) }} {{ currency() }}</span>
            </div>
            @if ($shipping > 0)
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.shipping') }}</span>
                    <span class="fw-bold">+{{ number_format($shipping, 2) }} {{ currency() }}</span>
                </div>
            @endif
            @if ($fees > 0)
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.extra_fees') }}</span>
                    <span class="fw-bold" style="color: #28a745;">+{{ number_format($fees, 2) }} {{ currency() }}</span>
                </div>
            @endif
            @if ($discounts > 0)
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.extra_discounts') }}</span>
                    <span class="fw-bold" style="color: #dc3545;">-{{ number_format($discounts, 2) }} {{ currency() }}</span>
                </div>
            @endif
            {{-- Total with shipping (before customer discounts) --}}
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.total_with_shipping') }}</span>
                <span class="fw-bold">{{ number_format($total, 2) }} {{ currency() }}</span>
            </div>
            @if ($customerPromoAmount > 0 || $customerPointsCost > 0)
                {{-- Show promo code and points discounts AFTER total with shipping --}}
                @if ($customerPromoAmount > 0)
                    <div class="summary-row mb-12">
                        <span class="fw-bold">{{ trans('order::order.promo_code_discount') }}</span>
                        <span class="fw-bold" style="color: #dc3545;">-{{ number_format($customerPromoAmount, 2) }} {{ currency() }}</span>
                    </div>
                @endif
                @if ($customerPointsCost > 0)
                    <div class="summary-row mb-12">
                        <span class="fw-bold">{{ trans('order::order.points_discount') }}</span>
                        <span class="fw-bold" style="color: #dc3545;">-{{ number_format($customerPointsCost, 2) }} {{ currency() }}</span>
                    </div>
                @endif
                {{-- Show customer total (what customer actually paid) --}}
                <div class="summary-row mb-12" style="background: #e8f5e9; padding: 8px 12px; border-radius: 6px;">
                    <span class="fw-bold" style="color: #2e7d32;">{{ trans('order::order.customer_total') }}</span>
                    <span class="fw-bold" style="color: #2e7d32;">{{ number_format($customerTotal, 2) }} {{ currency() }}</span>
                </div>
                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
            @endif
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.bnaia_commission') }}</span>
                <span class="fw-bold" style="color: #dc3545;">({{ $commissionPercentage }}%) -{{ number_format($commissionAmount, 2) }} {{ currency() }}</span>
            </div>
            @if ($refundedAmount > 0)
                <div class="summary-row mb-12" style="font-size: 14px; color: #666; padding-left: 20px;">
                    <span class="fw-500">{{ trans('order::order.minus') }} {{ trans('order::order.refunded_commission') }}</span>
                    <span class="fw-500" style="color: #28a745;">-{{ number_format($refundedCommission, 2) }} {{ currency() }}</span>
                </div>
                <div class="summary-row mb-12" style="background: #fff3cd; padding: 10px 15px; border-radius: 6px;">
                    <span class="fw-bold" style="color: #856404;">= {{ trans('order::order.net_commission') }}</span>
                    <span class="fw-bold" style="color: #856404;">{{ number_format($finalCommission, 2) }} {{ currency() }}</span>
                </div>
                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
                <div class="summary-row mb-12" style="font-size: 14px; color: #666;">
                    <span>{{ trans('order::order.calculation') }}: {{ number_format($total, 2) }} - {{ number_format($commissionAmount, 2) }}</span>
                    <span></span>
                </div>
                <div class="summary-row mb-12" style="background: #e8f5e9; padding: 10px 15px; border-radius: 6px;">
                    <span class="fw-bold" style="color: #2e7d32;">= {{ trans('order::order.remaining_before_refund') }}</span>
                    <span class="fw-bold" style="color: #2e7d32;">{{ number_format($total - $commissionAmount, 2) }} {{ currency() }}</span>
                </div>
                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
                <div class="summary-row mb-12" style="background: #ffe6e6; padding: 10px 15px; border-radius: 6px;">
                    <span class="fw-bold" style="color: #dc3545;">{{ trans('order::order.total_refunded') }}</span>
                    <span class="fw-bold" style="color: #dc3545;">-{{ number_format($refundedAmount, 2) }} {{ currency() }}</span>
                </div>
                <div class="summary-row mb-12" style="font-size: 14px; color: #666; padding-left: 20px;">
                    <span class="fw-500">{{ trans('order::order.plus') }} {{ trans('order::order.refunded_commission') }}</span>
                    <span class="fw-500" style="color: #28a745;">+{{ number_format($refundedCommission, 2) }} {{ currency() }}</span>
                </div>
                <div class="summary-row mb-12" style="background: #ffcdd2; padding: 10px 15px; border-radius: 6px;">
                    <span class="fw-bold" style="color: #c62828;">= {{ trans('order::order.net_refund_impact') }}</span>
                    <span class="fw-bold" style="color: #c62828;">{{ number_format($refundedAmount - $refundedCommission, 2) }} {{ currency() }}</span>
                </div>
                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
                <div class="summary-row mb-12" style="font-size: 14px; color: #666;">
                    <span>{{ trans('order::order.calculation') }}: {{ number_format($total - $commissionAmount, 2) }} - {{ number_format($refundedAmount - $refundedCommission, 2) }}</span>
                    <span></span>
                </div>
            @else
                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
                <div class="summary-row mb-12" style="font-size: 14px; color: #666;">
                    <span>{{ trans('order::order.calculation') }}: {{ number_format($total, 2) }} - {{ number_format($commissionAmount, 2) }}</span>
                    <span></span>
                </div>
            @endif
            <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
            <div class="summary-row" style="font-size: 18px;">
                <span class="fw-bold">= {{ trans('order::order.remaining') }}</span>
                <span class="fw-bold" style="color: {{ $remaining >= 0 ? $colors[0] : '#dc3545' }};">{{ number_format($remaining, 2) }} {{ currency() }}</span>
            </div>
        </div>
    </div>
</div>

<style>
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
    }
</style>
