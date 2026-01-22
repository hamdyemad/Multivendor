@props(['order'])

@php
    // Get all refund requests for this order with their items
    $refundRequests = $order->refunds()->with(['items.orderProduct.vendorProduct.product', 'items.orderProduct.vendorProductVariant.variantConfiguration.key', 'vendor'])->get();
    $hasRefunds = $refundRequests->count() > 0;
    
    // Group refunds by vendor
    $refundsByVendor = [];
    foreach($refundRequests as $refund) {
        $vendorId = $refund->vendor_id;
        $vendorName = $refund->vendor?->getTranslation('name', app()->getLocale()) ?? 'N/A';
        
        if (!isset($refundsByVendor[$vendorId])) {
            $refundsByVendor[$vendorId] = [
                'vendor_name' => $vendorName,
                'refunds' => [],
                'total_amount' => 0,
                'total_commission' => 0,
                'total_fees' => 0,
                'total_discounts' => 0,
            ];
        }
        
        $refundsByVendor[$vendorId]['refunds'][] = $refund;
        
        // Calculate totals for completed refunds only
        if ($refund->status === 'refunded') {
            $refundsByVendor[$vendorId]['total_amount'] += $refund->total_refund_amount;
            $refundsByVendor[$vendorId]['total_fees'] += $refund->vendor_fees_amount ?? 0;
            $refundsByVendor[$vendorId]['total_discounts'] += $refund->vendor_discounts_amount ?? 0;
            
            // Calculate commission using commission from order_products
            foreach ($refund->items as $item) {
                $orderProduct = $item->orderProduct;
                if ($orderProduct) {
                    $itemRefundAmount = $item->total_price + $item->shipping_amount;
                    // Get commission percentage from order_products table
                    $commPercent = $orderProduct->commission ?? 0;
                    $refundsByVendor[$vendorId]['total_commission'] += ($itemRefundAmount * $commPercent) / 100;
                }
            }
        }
    }
@endphp

@if($hasRefunds)
    <div class="mb-40">
        {{-- Modern Info Banner --}}
        <div class="alert mb-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: none; border-left: 4px solid #2196F3; border-radius: 12px; box-shadow: 0 2px 8px rgba(33, 150, 243, 0.1);">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="uil uil-info-circle" style="font-size: 28px; color: #1976D2;"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fw-bold mb-1" style="color: #1565C0;">{{ trans('order::order.refunded_products_notice') }}</h6>
                    <p class="mb-0" style="color: #424242; font-size: 14px;">{{ trans('order::order.refunded_products_description') }}</p>
                </div>
            </div>
        </div>

        {{-- Section Title --}}
        <div class="mb-4">
            <h5 class="fw-bold d-flex align-items-center" style="color: #1a1a1a;">
                <span class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #f44336 0%, #e91e63 100%); border-radius: 10px; box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);">
                    <i class="uil uil-redo" style="color: white; font-size: 20px;"></i>
                </span>
                {{ trans('order::order.refunded_products') }}
            </h5>
        </div>

        @php 
            $globalRefundItemIndex = 1;
        @endphp

        @foreach($refundsByVendor as $vendorId => $vendorData)
            {{-- Modern Vendor Card --}}
            <div class="card border-0 mb-4" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">
                {{-- Vendor Header --}}
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border: none;">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                            <i class="uil uil-store" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <small class="d-block opacity-75" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.vendor') }}</small>
                            <h6 class="mb-0 fw-bold text-white" style="font-size: 18px;">{{ $vendorData['vendor_name'] }}</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 0; background: #fafafa;">
                    @foreach($vendorData['refunds'] as $refund)
                        {{-- Modern Refund Request Header --}}
                        <div style="background: white; padding: 16px 24px; border-bottom: 1px solid #e0e0e0;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px;">
                                        <i class="uil uil-file-alt" style="color: white; font-size: 18px;"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.refund_number') }}</small>
                                        <a href="{{ route('admin.refunds.show', $refund->id) }}" 
                                           class="fw-bold" 
                                           target="_blank"
                                           style="font-size: 15px; color: #667eea; text-decoration: none;">
                                            {{ $refund->refund_number }}
                                            <i class="uil uil-external-link-alt ms-1" style="font-size: 14px;"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-center">
                                    @php
                                        $statusColors = [
                                            'pending' => '#FF9800',
                                            'approved' => '#00BCD4',
                                            'in_progress' => '#667eea',
                                            'picked_up' => '#9E9E9E',
                                            'refunded' => '#4CAF50',
                                            'rejected' => '#F44336',
                                            'cancelled' => '#757575',
                                        ];
                                        $statusColor = $statusColors[$refund->status] ?? '#9E9E9E';
                                    @endphp
                                    <x-protected-badge 
                                        :color="$statusColor" 
                                        :text="trans('refund::refund.statuses.' . $refund->status)" 
                                        size="md" 
                                        :id="'refund-status-' . $refund->id" />
                                    <small class="text-muted" style="font-size: 13px;">
                                        <i class="uil uil-calendar-alt me-1"></i>{{ $refund->created_at }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Modern Financial Details --}}
                        <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 24px; margin: 0;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px;">
                                    <i class="uil uil-money-bill" style="color: white; font-size: 16px;"></i>
                                </div>
                                <h6 class="mb-0 fw-bold" style="color: #2c3e50;">{{ trans('order::order.financial_details') }}</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card border-0 h-100" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-2">
                                                <i class="uil uil-box" style="font-size: 24px; color: #667eea;"></i>
                                            </div>
                                            <small class="d-block text-muted mb-1" style="font-size: 12px;">{{ trans('order::order.products_amount') }}</small>
                                            <strong style="color: #2c3e50; font-size: 18px;">{{ number_format($refund->total_products_amount, 2) }}</strong>
                                            <small class="text-muted ms-1">{{ currency() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 h-100" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-2">
                                                <i class="uil uil-truck" style="font-size: 24px; color: #00BCD4;"></i>
                                            </div>
                                            <small class="d-block text-muted mb-1" style="font-size: 12px;">{{ trans('order::order.shipping_amount') }}</small>
                                            <strong style="color: #2c3e50; font-size: 18px;">{{ number_format($refund->total_shipping_amount, 2) }}</strong>
                                            <small class="text-muted ms-1">{{ currency() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 h-100" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-2">
                                                <i class="uil uil-tag-alt" style="font-size: 24px; color: #4CAF50;"></i>
                                            </div>
                                            <small class="d-block text-muted mb-1" style="font-size: 12px;">{{ trans('order::order.discount_amount') }}</small>
                                            <strong style="color: #2c3e50; font-size: 18px;">{{ number_format($refund->total_discount_amount, 2) }}</strong>
                                            <small class="text-muted ms-1">{{ currency() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 h-100" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-2">
                                                <i class="uil uil-plus-circle" style="font-size: 24px; color: #FF9800;"></i>
                                            </div>
                                            <small class="d-block text-muted mb-1" style="font-size: 12px;">{{ trans('order::order.vendor_fees') }}</small>
                                            <strong style="color: #2c3e50; font-size: 18px;">{{ number_format($refund->vendor_fees_amount ?? 0, 2) }}</strong>
                                            <small class="text-muted ms-1">{{ currency() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 h-100" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-2">
                                                <i class="uil uil-minus-circle" style="font-size: 24px; color: #F44336;"></i>
                                            </div>
                                            <small class="d-block text-muted mb-1" style="font-size: 12px;">{{ trans('order::order.vendor_discounts') }}</small>
                                            <strong style="color: #2c3e50; font-size: 18px;">{{ number_format($refund->vendor_discounts_amount ?? 0, 2) }}</strong>
                                            <small class="text-muted ms-1">{{ currency() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-2">
                                                <i class="uil uil-receipt" style="font-size: 24px; color: white;"></i>
                                            </div>
                                            <small class="d-block text-white mb-1 opacity-75" style="font-size: 12px;">{{ trans('order::order.total_refund_amount') }}</small>
                                            <strong class="text-white" style="font-size: 20px;">{{ number_format($refund->total_refund_amount, 2) }}</strong>
                                            <small class="text-white ms-1 opacity-75">{{ currency() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modern Items Table --}}
                        <div class="table-responsive" style="background: white;">
                            <table class="table mb-0" style="border: none;">
                                <thead style="background: linear-gradient(135deg, #f44336 0%, #e91e63 100%); color: white;">
                                    <tr>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">#</th>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.product') }}</th>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.unit_price') }}</th>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.refunded_quantity') }}</th>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.products_total') }}</th>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.shipping') }}</th>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.refund_amount') }}</th>
                                        <th class="text-white fw-bold text-center" style="padding: 16px; border: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">{{ trans('order::order.refund_commission') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($refund->items as $refundItem)
                                        @php
                                            $orderProduct = $refundItem->orderProduct;
                                            if (!$orderProduct) continue;
                                            
                                            $productImage = $orderProduct->vendorProduct?->product?->mainImage?->path;
                                            
                                            // Build variant path
                                            $variantConfig = $orderProduct->vendorProductVariant?->variantConfiguration;
                                            $variantKey = $variantConfig?->key?->getTranslation('name', app()->getLocale()) ?? null;
                                            $variantValue = $variantConfig?->getTranslation('name', app()->getLocale()) ?? null;
                                            $variantPath = null;
                                            if ($variantKey && $variantValue) {
                                                $variantPath = $variantKey . ' → ' . $variantValue;
                                            } elseif ($variantValue) {
                                                $variantPath = $variantValue;
                                            }
                                            
                                            // Calculate refund amount (total_price already includes tax)
                                            $itemRefundAmount = $refundItem->total_price + $refundItem->shipping_amount;
                                            // Get commission percentage from order_products table
                                            $commissionPercent = $orderProduct->commission ?? 0;
                                            $itemRefundCommission = ($itemRefundAmount * $commissionPercent) / 100;
                                        @endphp
                                        <tr style="background: white; border-bottom: 1px solid #f0f0f0;">
                                            <td class="text-center" style="padding: 16px; color: #667eea; font-weight: 600;">{{ $globalRefundItemIndex++ }}</td>
                                            <td class="text-center" style="padding: 16px;">
                                                <div class="d-flex align-items-center justify-content-center gap-3">
                                                    @if($productImage)
                                                        <img src="{{ asset('storage/' . $productImage) }}" 
                                                             alt="{{ $orderProduct->vendorProduct->product->name ?? 'Product' }}"
                                                             style="width: 56px; height: 56px; border-radius: 12px; object-fit: cover; border: 2px solid #f0f0f0;">
                                                    @else
                                                        <img src="{{ asset('assets/img/default.png') }}" 
                                                             alt="{{ $orderProduct->vendorProduct->product->name ?? 'Product' }}"
                                                             style="width: 56px; height: 56px; border-radius: 12px; object-fit: cover; border: 2px solid #f0f0f0;">
                                                    @endif
                                                    <div class="text-start">
                                                        <p class="fw-bold mb-1" style="color: #2c3e50; font-size: 14px;">{{ $orderProduct->vendorProduct->product->name ?? 'N/A' }}</p>
                                                        <small class="d-block mb-1" style="color: #7f8c8d; font-size: 12px;">
                                                            <strong>{{ trans('order::order.sku') }}:</strong> 
                                                            {{ $orderProduct->vendorProductVariant?->sku ?? ($orderProduct->vendorProduct?->sku ?? 'N/A') }}
                                                        </small>
                                                        @if($variantPath)
                                                            <small class="d-block" style="color: #7f8c8d; font-size: 12px;">
                                                                <i class="uil uil-tag me-1"></i>
                                                                <strong>{{ trans('order::order.variant') }}:</strong> {{ $variantPath }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center" style="padding: 16px;">
                                                <div>
                                                    <span class="fw-bold" style="color: #2c3e50; font-size: 14px;">{{ number_format($refundItem->unit_price, 2) }}</span>
                                                    <small class="text-muted ms-1">{{ currency() }}</small>
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ trans('order::order.per_unit') }}</small>
                                            </td>
                                            <td class="text-center" style="padding: 16px;">
                                                <span class="badge" style="background: linear-gradient(135deg, #f44336 0%, #e91e63 100%); font-size: 14px; padding: 6px 12px; border-radius: 8px;">
                                                    {{ $refundItem->quantity }}
                                                </span>
                                            </td>
                                            <td class="text-center" style="padding: 16px;">
                                                <div>
                                                    <span class="fw-bold" style="color: #667eea; font-size: 15px;">{{ number_format($refundItem->total_price, 2) }}</span>
                                                    <small class="text-muted ms-1">{{ currency() }}</small>
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ trans('order::order.with_tax') }}</small>
                                            </td>
                                            <td class="text-center" style="padding: 16px;">
                                                <div>
                                                    <span class="fw-bold" style="color: #00BCD4; font-size: 14px;">{{ number_format($refundItem->shipping_amount, 2) }}</span>
                                                    <small class="text-muted ms-1">{{ currency() }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center" style="padding: 16px;">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="fw-bold" style="color: #f44336; font-size: 16px;">{{ number_format($itemRefundAmount, 2) }}</span>
                                                    <small class="text-muted">{{ currency() }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center" style="padding: 16px;">
                                                <div class="fw-bold" style="color: #4CAF50; font-size: 15px;">{{ number_format($itemRefundCommission, 2) }} <small class="text-muted">{{ currency() }}</small></div>
                                                <small class="text-muted" style="font-size: 11px;">({{ number_format($commissionPercent, 2) }}%)</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach

                    {{-- Modern Vendor Total Footer --}}
                    <div style="background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%); border-top: 3px solid #FFA726; padding: 20px 24px;">
                        <div class="row align-items-center g-3">
                            <div class="col-md-6">
                                <div class="d-flex flex-wrap gap-3">
                                    @if($vendorData['total_fees'] > 0)
                                        <div class="d-flex align-items-center" style="background: white; padding: 8px 16px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                            <i class="uil uil-plus-circle me-2" style="color: #4CAF50; font-size: 18px;"></i>
                                            <div>
                                                <small class="d-block text-muted" style="font-size: 11px;">{{ trans('order::order.refunded_fees') }}</small>
                                                <strong style="color: #4CAF50; font-size: 14px;">+{{ number_format($vendorData['total_fees'], 2) }} {{ currency() }}</strong>
                                            </div>
                                        </div>
                                    @endif
                                    @if($vendorData['total_discounts'] > 0)
                                        <div class="d-flex align-items-center" style="background: white; padding: 8px 16px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                            <i class="uil uil-minus-circle me-2" style="color: #F44336; font-size: 18px;"></i>
                                            <div>
                                                <small class="d-block text-muted" style="font-size: 11px;">{{ trans('order::order.refunded_discounts') }}</small>
                                                <strong style="color: #F44336; font-size: 14px;">-{{ number_format($vendorData['total_discounts'], 2) }} {{ currency() }}</strong>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex align-items-center justify-content-center" style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                    <div>
                                        <small class="d-block text-muted mb-1" style="font-size: 11px;">{{ trans('order::order.vendor_total_refunded') }}</small>
                                        <div style="font-size: 22px; color: #FF6F00; font-weight: 700;">
                                            {{ number_format($vendorData['total_amount'], 2) }} <small style="font-size: 14px; color: #7f8c8d;">{{ currency() }}</small>
                                        </div>
                                        @if($vendorData['total_amount'] == 0)
                                            <small class="d-block text-muted" style="font-size: 11px;">({{ trans('order::order.pending') }})</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex align-items-center justify-content-center" style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                    <div>
                                        <small class="d-block text-muted mb-1" style="font-size: 11px;">{{ trans('order::order.refunded_commission') }}</small>
                                        <div style="font-size: 20px; color: #4CAF50; font-weight: 700;">
                                            {{ number_format($vendorData['total_commission'], 2) }} <small style="font-size: 14px; color: #7f8c8d;">{{ currency() }}</small>
                                        </div>
                                        @if($vendorData['total_commission'] == 0)
                                            <small class="d-block text-muted" style="font-size: 11px;">({{ trans('order::order.pending') }})</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Grand Total for All Refunds --}}
        @php
            $grandTotalAmount = 0;
            $grandTotalCommission = 0;
            $grandTotalFees = 0;
            $grandTotalDiscounts = 0;
            
            foreach($refundsByVendor as $vendorData) {
                $grandTotalAmount += $vendorData['total_amount'];
                $grandTotalCommission += $vendorData['total_commission'];
                $grandTotalFees += $vendorData['total_fees'];
                $grandTotalDiscounts += $vendorData['total_discounts'];
            }
        @endphp

        @if($grandTotalAmount > 0)
            {{-- Modern Grand Total Card --}}
            <div class="card border-0 mt-4" style="background: linear-gradient(135deg, #f44336 0%, #e91e63 100%); border-radius: 16px; box-shadow: 0 8px 24px rgba(244, 67, 54, 0.3); overflow: hidden;">
                <div class="card-body" style="padding: 28px;">
                    <div class="row align-items-center g-4">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                                    <i class="uil uil-calculator-alt" style="color: white; font-size: 24px;"></i>
                                </div>
                                <h5 class="text-white mb-0 fw-bold" style="font-size: 20px;">{{ trans('order::order.grand_total_refunds') }}</h5>
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                @if($grandTotalFees > 0)
                                    <div style="background: rgba(255, 255, 255, 0.15); padding: 12px 20px; border-radius: 10px; backdrop-filter: blur(10px);">
                                        <small class="d-block text-white opacity-75 mb-1" style="font-size: 11px;">{{ trans('order::order.total_fees') }}</small>
                                        <strong class="text-white" style="font-size: 16px;">+{{ number_format($grandTotalFees, 2) }} {{ currency() }}</strong>
                                    </div>
                                @endif
                                @if($grandTotalDiscounts > 0)
                                    <div style="background: rgba(255, 255, 255, 0.15); padding: 12px 20px; border-radius: 10px; backdrop-filter: blur(10px);">
                                        <small class="d-block text-white opacity-75 mb-1" style="font-size: 11px;">{{ trans('order::order.total_discounts') }}</small>
                                        <strong class="text-white" style="font-size: 16px;">-{{ number_format($grandTotalDiscounts, 2) }} {{ currency() }}</strong>
                                    </div>
                                @endif
                                <div style="background: rgba(255, 255, 255, 0.15); padding: 12px 20px; border-radius: 10px; backdrop-filter: blur(10px);">
                                    <small class="d-block text-white opacity-75 mb-1" style="font-size: 11px;">{{ trans('order::order.total_commission') }}</small>
                                    <strong class="text-white" style="font-size: 16px;">{{ number_format($grandTotalCommission, 2) }} {{ currency() }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div style="background: rgba(255, 255, 255, 0.2); padding: 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                                <small class="d-block text-white opacity-75 mb-2" style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">{{ trans('order::order.total_refunded') }}</small>
                                <div style="font-size: 36px; font-weight: 800; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.2); line-height: 1;">
                                    {{ number_format($grandTotalAmount, 2) }}
                                </div>
                                <small class="text-white opacity-75" style="font-size: 14px;">{{ currency() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
