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
                        
                        // Total and Remaining
                        $productTotal = $productTotalWithShipping;
                        $productRemaining = $productTotal - $productCommissionAmount;
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
                                        <span class="fw-bold">{{ number_format($productTotal, 2) }} {{ currency() }}</span>
                                    </div>
                                    <div class="summary-row mb-2" style="color: #333;">
                                        <span class="fw-bold">{{ trans('order::order.bnaia_commission') }}</span>
                                        <span class="fw-bold" style="color: #dc3545;">({{ $productCommissionPercent }}%) -{{ number_format($productCommissionAmount, 2) }} {{ currency() }}</span>
                                    </div>
                                    <hr style="border-color: #ddd; margin: 10px 0;">
                                    <div class="summary-row" style="font-size: 15px; color: #333;">
                                        <span class="fw-bold">{{ trans('order::order.remaining') }}</span>
                                        <span class="fw-bold" style="color: {{ $colors[0] }};">{{ number_format($productRemaining, 2) }} {{ currency() }}</span>
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
            // Customer total = what customer actually paid (total - discounts)
            $customerTotal = $total - $promoCodeShare - $pointsShare;
            
            // Calculate total before remaining (after adding back discounts that Bnaia covers)
            // Both promo_code_share and points_share are added (Bnaia covers them)
            $totalBeforeRemaining = $total + $promoCodeShare + $pointsShare;
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
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.total_with_shipping') }}</span>
                <span class="fw-bold">{{ number_format($total, 2) }} {{ currency() }}</span>
            </div>
            @if ($promoCodeShare > 0 || $pointsShare > 0)
                {{-- Show customer total (what customer actually paid) --}}
                @if ($promoCodeShare > 0)
                    <div class="summary-row mb-12">
                        <span class="fw-bold">{{ trans('order::order.promo_code_discount') }}</span>
                        <span class="fw-bold" style="color: #dc3545;">-{{ number_format($promoCodeShare, 2) }} {{ currency() }}</span>
                    </div>
                @endif
                @if ($pointsShare > 0)
                    <div class="summary-row mb-12">
                        <span class="fw-bold">{{ trans('order::order.points_discount') }}</span>
                        <span class="fw-bold" style="color: #dc3545;">-{{ number_format($pointsShare, 2) }} {{ currency() }}</span>
                    </div>
                @endif
                <div class="summary-row mb-12" style="background: #f8f9fa; padding: 8px 12px; border-radius: 6px;">
                    <span class="fw-bold">{{ trans('order::order.customer_total') }}</span>
                    <span class="fw-bold" style="color: #5f63f2;">{{ number_format($customerTotal, 2) }} {{ currency() }}</span>
                </div>
                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
                {{-- Show total with shipping again before commission --}}
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.total_with_shipping') }}</span>
                    <span class="fw-bold">{{ number_format($total, 2) }} {{ currency() }}</span>
                </div>
            @endif
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.bnaia_commission') }}</span>
                <span class="fw-bold" style="color: #dc3545;">-{{ number_format($commissionAmount, 2) }} {{ currency() }}</span>
            </div>
            <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
            <div class="summary-row" style="font-size: 18px;">
                <span class="fw-bold">{{ trans('order::order.remaining') }}</span>
                <span class="fw-bold" style="color: {{ $colors[0] }};">{{ number_format($remaining, 2) }} {{ currency() }}</span>
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
