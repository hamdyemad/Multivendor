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
    'promoDiscount' => 0,
    'colors' => ['#28a745', '#5dd879']
])

<div class="card border-0 shadow-sm"
    style="background: linear-gradient(135deg, {{ $colors[0] }} 0%, {{ $colors[1] }} 100%); color: white;">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-20 d-flex align-items-center text-white">
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
                        
                        // Commission
                        $productCommissionPercent = $product->commission;
                        $productCommissionAmount = ($productTotalWithTax * $productCommissionPercent) / 100;
                        
                        // Total and Remaining
                        $productTotal = $productTotalWithTax + $productShippingCost;
                        $productRemaining = $productTotal - $productCommissionAmount;
                    @endphp
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="background: rgba(255, 255, 255, 0.95); color: #333;">
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
                                        <span class="fw-bold">{{ trans('order::order.total') }}</span>
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
            @if ($promoDiscount > 0)
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.promo_discount') }}</span>
                    <span class="fw-bold" style="color: #ffcccc;">-{{ number_format($promoDiscount, 2) }} {{ currency() }}</span>
                </div>
            @endif
            @if ($shipping > 0)
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.shipping') }}</span>
                    <span class="fw-bold">+{{ number_format($shipping, 2) }} {{ currency() }}</span>
                </div>
            @endif
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.total') }}</span>
                <span class="fw-bold">{{ number_format($total, 2) }} {{ currency() }}</span>
            </div>
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.bnaia_commission') }}</span>
                <span class="fw-bold" style="color: #ffcccc;">-{{ number_format($commissionAmount, 2) }} {{ currency() }}</span>
            </div>
            <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
            <div class="summary-row" style="font-size: 18px;">
                <span class="fw-bold">{{ trans('order::order.remaining') }}</span>
                <span class="fw-bold" style="color: #ffffcc;">{{ number_format($remaining, 2) }} {{ currency() }}</span>
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
