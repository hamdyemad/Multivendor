@extends('layout.app')
@section('title')
    {{ trans('order::order.order_details') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order::order.order_management'), 'url' => route('admin.orders.index')],
                    ['title' => trans('order::order.order_details')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div id="printableArea" class="bg-white p-40 radius-xl">
                    <!-- Order Info & Customer Details with QR Code -->
                    <div class="row mb-40">
                        <!-- Order Details Card -->
                        <div class="col-lg-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-20 d-flex align-items-center">
                                        <i class="uil uil-receipt me-2" style="color: #5f63f2; font-size: 20px;"></i>
                                        {{ trans('order::order.order_information') }}
                                    </h6>
                                    <div class="order-details">
                                        <div class="detail-row mb-15">
                                            <span class="detail-label">{{ trans('order::order.order_id') }}:</span>
                                            <span
                                                class="detail-value fw-bold text-primary">{{ $order->order_number }}</span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label">{{ trans('order::order.created_at') }}:</span>
                                            <span class="detail-value">{{ $order->created_at }}</span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label mb-2">{{ trans('order::order.product_stages') }}:</span>
                                            <div class="d-flex flex-wrap gap-2">
                                                @php
                                                    // For vendors: get stages from their products only
                                                    if ($isVendorUser && isset($vendorProducts)) {
                                                        $productStagesData = $vendorProducts->groupBy('stage_id')->map(function($group) {
                                                            $stage = $group->first()->stage;
                                                            return [
                                                                'id' => $stage?->id,
                                                                'name' => $stage?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                                                                'color' => $stage?->color ?? '#6c757d',
                                                                'count' => $group->count()
                                                            ];
                                                        })->values();
                                                    } else {
                                                        // For admins: use the order's product_stages getter
                                                        $productStagesData = collect($order->product_stages);
                                                    }
                                                @endphp
                                                @forelse($productStagesData as $productStage)
                                                    <x-protected-badge 
                                                        :color="$productStage['color']"
                                                        :text="$productStage['name'] . ' (' . $productStage['count'] . ')'"
                                                        size="lg"
                                                        :id="'order-info-stage-' . $productStage['id']"
                                                    />
                                                @empty
                                                    <x-protected-badge 
                                                        color="#6c757d"
                                                        text="N/A"
                                                        size="lg"
                                                        id="order-info-stage-empty"
                                                    />
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">{{ trans('order::order.order_from') }}:</span>
                                            <span class="detail-value">
                                                @if ($order->order_from === 'web')
                                                    <x-protected-badge 
                                                        color="#17a2b8"
                                                        text="🌐 {{ trans('order::order.web') }}"
                                                        size="lg"
                                                        id="order-from-badge"
                                                    />
                                                @elseif($order->order_from === 'ios')
                                                    <x-protected-badge 
                                                        color="#343a40"
                                                        text="🍎 {{ trans('order::order.ios') }}"
                                                        size="lg"
                                                        id="order-from-badge"
                                                    />
                                                @elseif($order->order_from === 'android')
                                                    <x-protected-badge 
                                                        color="#28a745"
                                                        text="🤖 {{ trans('order::order.android') }}"
                                                        size="lg"
                                                        id="order-from-badge"
                                                    />
                                                @else
                                                    <x-protected-badge 
                                                        color="#6c757d"
                                                        :text="$order->order_from"
                                                        size="lg"
                                                        id="order-from-badge"
                                                    />
                                                @endif
                                            </span>
                                        </div>
                                        @if($order->requestQuotation)
                                        <div class="detail-row">
                                            <span class="detail-label">{{ trans('order::request-quotation.request_quotations') }}:</span>
                                            <span class="detail-value">
                                                <a target="_blank" href="{{ route('admin.request-quotations.index') }}?search={{ $order->order_number }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="uil uil-file-question-alt me-1"></i>{{ trans('common.view') }}
                                                </a>
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details Card -->
                        <div class="col-lg-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-20 d-flex align-items-center">
                                        <i class="uil uil-user me-2" style="color: #5f63f2; font-size: 20px;"></i>
                                        {{ trans('order::order.customer_information') }}
                                    </h6>
                                    <div class="customer-details">
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-user-circle me-1"></i>{{ trans('order::order.customer_name') }}:</span>
                                            <span class="detail-value fw-bold">{{ $order->customer_name }}</span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-envelope me-1"></i>{{ trans('order::order.customer_email') }}:</span>
                                            <span class="detail-value"><a
                                                    href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-phone me-1"></i>{{ trans('order::order.customer_phone') }}:</span>
                                            <span class="detail-value"><a
                                                    href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></span>
                                        </div>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label"><i
                                                    class="uil uil-map-pin me-1"></i>{{ trans('order::order.customer_address') }}:</span>
                                            <span class="detail-value">{{ $order->customer_address }}</span>
                                        </div>
                                        @if ($order->country || $order->city || $order->region)
                                            <hr class="my-15">
                                            <h6 class="fw-bold mb-15 d-flex align-items-center">
                                                <i class="uil uil-location-point me-2" style="color: #5f63f2; font-size: 18px;"></i>
                                                {{ trans('order::order.location') }}
                                            </h6>
                                            <div class="detail-row mb-15">
                                                <span class="detail-label">{{ trans('order::order.country') }}:</span>
                                                <span class="detail-value">{{ $order->country?->name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="detail-row mb-15">
                                                <span class="detail-label">{{ trans('order::order.city') }}:</span>
                                                <span class="detail-value">{{ $order->city?->name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">{{ trans('order::order.region') }}:</span>
                                                <span class="detail-value">{{ $order->region?->name ?? 'N/A' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .detail-row {
                            display: flex;
                            justify-content: space-between;
                            align-items: flex-start;
                            padding: 10px 0;
                            border-bottom: 1px solid #f0f0f0;
                        }
                        
                        .detail-row.flex-column {
                            flex-direction: column;
                            align-items: flex-start !important;
                        }

                        .detail-row:last-child {
                            border-bottom: none;
                        }

                        .detail-label {
                            font-weight: 600;
                            color: #666;
                            min-width: 150px;
                        }

                        .detail-value {
                            color: #333;
                            text-align: right;
                            flex: 1;
                        }

                        .detail-value a {
                            color: #5f63f2;
                            text-decoration: none;
                        }

                        .detail-value a:hover {
                            text-decoration: underline;
                        }
                    </style>

                    <!-- Products Table -->
                    <div class="mb-40">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">{{ trans('order::order.products') }}</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="uil uil-arrow-left me-1"></i>{{ trans('common.back') }}
                                </a>
                                <button type="button" class="btn btn-info btn-sm" id="printSelectedBtn" disabled>
                                    <i class="uil uil-print me-1"></i>{{ trans('order::order.print_selected') }} (<span id="selectedCount">0</span>)
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover" style="border-color: #dee2e6;">
                                <thead class="userDatatable-header" style="background-color: #003d82; color: white;">
                                    <tr>
                                        <th class="text-white fw-bold text-center">
                                            <input type="checkbox" id="selectAllProducts" class="form-check-input" style="cursor: pointer;">
                                        </th>
                                        <th class="text-white fw-bold text-center">#</th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.product') }}</th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.price_before_taxes') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.taxes') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.price_including_taxes') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.quantity') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.total_price') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.stage') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.shipping') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.bnaia_commission') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Use vendor-filtered products if available, otherwise use all products
                                        $displayProducts = isset($vendorProducts) && $vendorProducts !== null ? $vendorProducts : $order->products;
                                    @endphp
                                    @forelse($displayProducts as $key => $product)
                                        @php
                                            $productImage = $product->vendorProduct?->product?->mainImage?->path;
                                            $vendorName = $product->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? 'N/A';
                                            
                                            // Build variant path: Key → Value
                                            $variantConfig = $product->vendorProductVariant?->variantConfiguration;
                                            $variantKey = $variantConfig?->key?->getTranslation('name', app()->getLocale()) ?? null;
                                            $variantValue = $variantConfig?->getTranslation('name', app()->getLocale()) ?? null;
                                            $variantPath = null;
                                            if ($variantKey && $variantValue) {
                                                $variantPath = $variantKey . ' → ' . $variantValue;
                                            } elseif ($variantValue) {
                                                $variantPath = $variantValue;
                                            }
                                            
                                            // Price stored is total price INCLUDING tax
                                            $productTotalWithTax = $product->price;
                                            
                                            // Get tax amount (sum of all taxes)
                                            $taxAmount = $product->taxes->sum('amount') ?? 0;
                                            
                                            // Calculate price before tax
                                            $productTotalBeforeTax = $productTotalWithTax - $taxAmount;
                                            
                                            // Unit prices
                                            $unitPriceWithTax = $product->quantity > 0 ? $productTotalWithTax / $product->quantity : 0;
                                            $unitPriceBeforeTax = $product->quantity > 0 ? $productTotalBeforeTax / $product->quantity : 0;
                                            $unitTaxAmount = $product->quantity > 0 ? $taxAmount / $product->quantity : 0;
                                            
                                            // Commission is stored as percentage
                                            $bnaiaCommission = $product->commission;
                                            $commissionPercent = $bnaiaCommission;
                                            
                                            // Calculate commission amount from percentage
                                            $commissionAmount = ($productTotalWithTax * $commissionPercent) / 100;
                                            
                                            // Remaining = Total with tax - Commission amount
                                            $remaining = $productTotalWithTax - $commissionAmount;
                                            
                                            // Total tax percentage
                                            $totalTaxPercentage = $product->taxes->sum('percentage') ?? 0;
                                            
                                            // Shipping cost per product
                                            $productShippingCost = $product->shipping_cost ?? 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input product-checkbox" 
                                                       data-product-id="{{ $product->id }}" 
                                                       style="cursor: pointer;">
                                            </td>
                                            <td class="fw-bold text-center">{{ $key + 1 }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-3">
                                                    @if($productImage)
                                                        <img src="{{ asset('storage/' . $productImage) }}" 
                                                             alt="{{ $product->vendorProduct->product->name ?? 'Product' }}"
                                                             class="rounded"
                                                             style="width: 60px; height: 60px;  border: 1px solid #dee2e6;">
                                                    @else
                                                        <img src="{{ asset('assets/img/default.png') }}" 
                                                             alt="{{ $product->vendorProduct->product->name ?? 'Product' }}"
                                                             class="rounded"
                                                             style="width: 60px; height: 60px;  border: 1px solid #dee2e6;">
                                                    @endif
                                                    <div class="text-start">
                                                        <p class="fw-bold mb-2">
                                                            {{ $product->vendorProduct->product->name ?? 'N/A' }}</p>
                                                        <small class="text-muted d-block mb-1">
                                                            <strong>{{ trans('order::order.sku') }}:</strong>
                                                            {{ $product->vendorProductVariant?->sku ?? $product->vendorProduct?->sku ?? 'N/A' }}
                                                        </small>
                                                        @if ($variantPath)
                                                            <small class="text-muted d-block mb-1">
                                                                <i class="uil uil-tag me-1"></i>
                                                                <strong>{{ trans('order::order.variant') }}:</strong>
                                                                {{ $variantPath }}
                                                            </small>
                                                        @endif
                                                        <small class="text-muted d-block">
                                                            <i class="uil uil-store me-1"></i>
                                                            <strong>{{ trans('order::order.vendor') }}:</strong>
                                                            {{ $vendorName }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($unitPriceBeforeTax, 2) }}
                                                {{ currency() }}
                                            </td>
                                            <td class="text-center">
                                                @if($product->taxes && $product->taxes->count() > 0)
                                                    <x-protected-badge 
                                                        color="#5f63f2"
                                                        :text="trans('order::order.total') . ': ' . $totalTaxPercentage . '%'"
                                                        size="lg"
                                                        :id="'tax-total-' . $product->id"
                                                        class="mb-1"
                                                    />
                                                    <div>
                                                        @foreach($product->taxes as $tax)
                                                            <x-protected-badge 
                                                                color="#6c757d"
                                                                :text="$tax->name . ' ' . $tax->percentage . '%'"
                                                                size="md"
                                                                :id="'tax-' . $tax->id . '-' . $product->id"
                                                                class="me-1 mb-1"
                                                            />
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($unitPriceWithTax, 2) }}
                                                {{ currency() }}
                                            </td>
                                            <td class="text-center">{{ $product->quantity }}</td>
                                            <td class="text-center fw-bold">
                                                {{ number_format($productTotalWithTax, 2) }}
                                                {{ currency() }}</td>
                                            <td class="text-center">
                                                @if($product->stage)
                                                    <div class="d-flex flex-column align-items-center gap-2">
                                                        <x-protected-badge 
                                                            :color="$product->stage->color ?? '#6c757d'"
                                                            :text="$product->stage->getTranslation('name', app()->getLocale()) ?? 'N/A'"
                                                            size="lg"
                                                            :id="'stage-badge-' . $product->id"
                                                        />
                                                        @if(!isAdmin() && isset($orderStages) && count($orderStages) > 0)
                                                            <div class="d-flex gap-1">
                                                                <button type="button" class="btn btn-sm btn-outline-primary change-product-stage-btn" 
                                                                        data-product-id="{{ $product->id }}"
                                                                        data-current-stage="{{ $product->stage_id }}"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#changeProductStageModal"
                                                                        title="{{ trans('order::order.change_product_stage') }}">
                                                                    <i class="uil uil-exchange m-0"></i>
                                                                </button>
                                                                @if($product->stage->type === 'in_progress')
                                                                    <a href="{{ route('admin.order-fulfillments.show', $order->id) }}" 
                                                                       class="btn btn-sm btn-outline-success"
                                                                       title="{{ trans('order::order.allocate') }}">
                                                                        <i class="uil uil-box m-0"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <x-protected-badge 
                                                        color="#6c757d"
                                                        :text="trans('order::order.pending')"
                                                        size="lg"
                                                        :id="'stage-badge-' . $product->id"
                                                    />
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($productShippingCost, 2) }}
                                                {{ currency() }}
                                            </td>
                                            <td class="text-center text-danger">
                                                <div>{{ $commissionPercent }}%</div>
                                                <div class="fw-bold">{{ number_format($commissionAmount, 2) }} {{ currency() }}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-20">
                                                {{ trans('common.no_data') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Fees & Discounts Details -->
                    @if ($order->extraFeesDiscounts->count() > 0)
                        <div class="mb-40">
                            <div class="table-responsive">
                                <table class="table mb-0 table-hover" style="border-color: #dee2e6;">
                                    <thead class="userDatatable-header" style="background-color: #003d82; color: white;">
                                        <tr>
                                            <th class="text-white fw-bold">{{ trans('order::order.type') }}</th>
                                            <th class="text-white fw-bold">{{ trans('order::order.reason') }}</th>
                                            <th class="text-white fw-bold text-end">{{ trans('order::order.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->extraFeesDiscounts as $extra)
                                            <tr>
                                                <td>
                                                    @if ($extra->type === 'fee')
                                                        <x-protected-badge 
                                                            color="#dc3545"
                                                            :text="trans('order::order.fee')"
                                                            size="lg"
                                                            :id="'extra-' . $extra->id"
                                                        />
                                                    @else
                                                        <x-protected-badge 
                                                            color="#28a745"
                                                            :text="trans('order::order.discount')"
                                                            size="lg"
                                                            :id="'extra-' . $extra->id"
                                                        />
                                                    @endif
                                                </td>
                                                <td>{{ $extra->reason }}</td>
                                                <td class="text-end fw-bold">
                                                    {{ $extra->type === 'fee' ? '+' : '-' }}{{ number_format($extra->cost, 2) }}
                                                    {{ currency() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Order Summary - Full Width (Admin Only) --}}
                    @if(!isset($isVendorUser) || !$isVendorUser)
                        @php
                            // Calculate total commission and remaining for all displayed products
                            $totalProductsPriceBeforeTax = 0;
                            $totalCommission = 0;
                            $totalRemaining = 0;
                            $totalProductsTax = 0;
                            
                            $productsToCalculate = isset($vendorProducts) && $vendorProducts !== null ? $vendorProducts : $order->products;
                            
                            foreach ($productsToCalculate as $prod) {
                                // Price stored is total price INCLUDING tax
                                $prodTotalWithTax = $prod->price;
                                
                                // Get tax amount (sum of all taxes)
                                $prodTax = $prod->taxes->sum('amount') ?? 0;
                                
                                // Calculate price before tax
                                $prodTotalBeforeTax = $prodTotalWithTax - $prodTax;
                                
                                // Commission is stored directly (calculated from price with tax)
                                $commAmount = $prod->commission;
                                
                                $totalProductsPriceBeforeTax += $prodTotalBeforeTax;
                                $totalProductsTax += $prodTax;
                                $totalCommission += $commAmount;
                                $totalRemaining += ($prodTotalWithTax - $commAmount);
                            }
                            
                            // Total with tax for vendor remaining calculation
                            $totalProductsPriceWithTax = $totalProductsPriceBeforeTax + $totalProductsTax;
                        @endphp
                        <div class="row mb-40">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm"
                                    style="background: linear-gradient(135deg, #5f63f2 0%, #8e92f7 100%); color: white;">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-20 d-flex align-items-center text-white">
                                            <i class="uil uil-receipt me-2" style="font-size: 20px;"></i>
                                            {{ trans('order::order.order_summary') }}
                                        </h6>
                                        <div class="summary-details">
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold">{{ trans('order::order.subtotal') }}</span>
                                                <span class="fw-bold">{{ number_format($totalProductsPriceBeforeTax, 2) }}
                                                    {{ currency() }}</span>
                                            </div>
                                            @if ($order->customer_promo_code_amount > 0)
                                                <div class="summary-row mb-12">
                                                    <span class="fw-bold">
                                                        {{ trans('order::order.promo_discount') }}
                                                        @if($order->customer_promo_code_title)
                                                            <small class="text-white-50">({{ $order->customer_promo_code_title }})</small>
                                                        @endif
                                                    </span>
                                                    <span class="fw-bold" style="color: #ffcccc;">-{{ number_format($order->customer_promo_code_amount, 2) }}
                                                        {{ currency() }}</span>
                                                </div>
                                            @endif
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold">{{ trans('order::order.tax') }}</span>
                                                <span class="fw-bold">+{{ number_format($order->total_tax, 2) }}
                                                    {{ currency() }}</span>
                                            </div>
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold">{{ trans('order::order.subtotal_including_tax') }}</span>
                                                <span class="fw-bold">{{ number_format($totalProductsPriceWithTax - $order->customer_promo_code_amount, 2) }}
                                                    {{ currency() }}</span>
                                            </div>
                                            @if ($order->total_discounts > 0)
                                                <div class="summary-row mb-12">
                                                    <span class="fw-bold">{{ trans('order::order.discounts') }}</span>
                                                    <span class="fw-bold" style="color: #ffcccc;">-{{ number_format($order->total_discounts, 2) }}
                                                        {{ currency() }}</span>
                                                </div>
                                            @endif
                                            @if ($order->total_fees > 0)
                                                <div class="summary-row mb-12">
                                                    <span class="fw-bold">{{ trans('order::order.fees') }}</span>
                                                    <span class="fw-bold">+{{ number_format($order->total_fees, 2) }}
                                                        {{ currency() }}</span>
                                                </div>
                                            @endif
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold">{{ trans('order::order.shipping') }}</span>
                                                <span class="fw-bold">+{{ number_format($order->shipping, 2) }}
                                                    {{ currency() }}</span>
                                            </div>
                                            @if ($order->points_used > 0)
                                                <div class="summary-row mb-12">
                                                    <span class="fw-bold">{{ trans('order::order.points_used') }}</span>
                                                    <span class="fw-bold" style="color: #ffcccc;">-{{ number_format($order->points_cost, 2) }}
                                                        {{ currency() }} ({{ number_format($order->points_used, 0) }} {{ trans('order::order.points') }})</span>
                                                </div>
                                            @endif
                                            <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
                                            <div class="summary-row" style="font-size: 18px;">
                                                <span class="fw-bold">{{ trans('order::order.total') }}</span>
                                                <span class="fw-bold">{{ number_format($order->total_price, 2) }}
                                                    {{ currency() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Summary Section -->
                    <div class="row mb-40">
                        @php
                            // Calculate totals from products
                            $productsToCalculate = isset($vendorProducts) && $vendorProducts !== null ? $vendorProducts : $order->products;
                            
                            $totalProductsPriceBeforeTax = 0;
                            $totalProductsTax = 0;
                            $vendorShippingCost = 0;
                            $totalCommission = 0;
                            $totalCommissionPercentage = 0;
                            $productCount = $productsToCalculate->count();
                            
                            foreach ($productsToCalculate as $prod) {
                                $prodTotalWithTax = $prod->price;
                                $prodTax = $prod->taxes->sum('amount') ?? 0;
                                $prodTotalBeforeTax = $prodTotalWithTax - $prodTax;
                                
                                $totalProductsPriceBeforeTax += $prodTotalBeforeTax;
                                $totalProductsTax += $prodTax;
                                $vendorShippingCost += $prod->shipping_cost ?? 0;
                                
                                // Calculate commission from each product
                                $commPercent = $prod->commission > 0 ? $prod->commission : ($prod->vendorProduct?->product?->department?->commission ?? 0);
                                $prodCommissionAmount = ($prodTotalWithTax * $commPercent) / 100;
                                $totalCommission += $prodCommissionAmount;
                                $totalCommissionPercentage += $commPercent;
                            }
                            
                            // Calculate average commission percentage for display
                            if ($productCount > 0) {
                                $totalCommissionPercentage = $totalCommissionPercentage / $productCount;
                            }
                            
                            // Total with tax for vendor remaining calculation
                            $totalProductsPriceWithTax = $totalProductsPriceBeforeTax + $totalProductsTax;
                            
                            // Use vendor-specific shipping if vendor user, otherwise use total order shipping
                            $shippingToUse = (isset($isVendorUser) && $isVendorUser) ? $vendorShippingCost : $order->shipping;
                            
                            $totalRemaining = $totalProductsPriceWithTax + $shippingToUse - $totalCommission - $order->customer_promo_code_amount;
                            $totalWithShipping = $totalProductsPriceWithTax + $shippingToUse - $order->customer_promo_code_amount;
                            
                            // Get vendor name for display
                            if (isset($isVendorUser) && $isVendorUser && isset($vendorProducts) && $vendorProducts->count() > 0) {
                                $currentVendorName = $vendorProducts->first()->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? 'Vendor';
                            } else {
                                $currentVendorName = 'Vendor';
                            }
                        @endphp

                        @if(isset($isVendorUser) && $isVendorUser && isset($vendorProductTotal))
                            {{-- Vendor view: show Vendor Remaining Summary with products inside --}}
                            <div class="col-12 mb-3">
                                <x-order::vendor-remaining-with-products
                                    :vendorName="$currentVendorName"
                                    :products="$displayProducts"
                                    :subtotalBeforeTax="$totalProductsPriceBeforeTax"
                                    :taxAmount="$totalProductsTax"
                                    :subtotalWithTax="$totalProductsPriceWithTax"
                                    :shipping="$vendorShippingCost"
                                    :total="$totalWithShipping"
                                    :commissionPercentage="$totalCommissionPercentage"
                                    :commissionAmount="$totalCommission"
                                    :remaining="$totalRemaining"
                                    :promoDiscount="$order->customer_promo_code_amount"
                                    :colors="['#28a745', '#5dd879']"
                                />
                            </div>
                        @else
                            {{-- Admin view: show per-vendor boxes with products inside --}}
                            
                            @php
                                // Group products by vendor
                                $productsByVendor = $order->products->groupBy(function($product) {
                                    return $product->vendorProduct?->vendor_id;
                                });
                                
                                // Use single green color for all vendors
                                $vendorColors = [
                                    ['#28a745', '#5dd879'], // Green
                                ];
                                $colorIndex = 0;
                            @endphp
                            
                            @foreach($productsByVendor as $vendorId => $vendorProducts)
                                @php
                                    // Get vendor name
                                    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? 'N/A';
                                    
                                    // Calculate totals for this vendor
                                    $vendorSubtotalBeforeTax = 0;
                                    $vendorTotalTax = 0;
                                    $vendorShipping = 0;
                                    $vendorTotalCommission = 0;
                                    $totalCommissionPercentage = 0;
                                    
                                    foreach ($vendorProducts as $prod) {
                                        $prodTotalWithTax = $prod->price;
                                        $prodTax = $prod->taxes->sum('amount') ?? 0;
                                        $prodTotalBeforeTax = $prodTotalWithTax - $prodTax;
                                        
                                        $vendorSubtotalBeforeTax += $prodTotalBeforeTax;
                                        $vendorTotalTax += $prodTax;
                                        $vendorShipping += $prod->shipping_cost ?? 0;
                                        
                                        // Calculate commission from each product
                                        $commPercent = $prod->commission > 0 ? $prod->commission : ($prod->vendorProduct?->product?->department?->commission ?? 0);
                                        $prodCommissionAmount = ($prodTotalWithTax * $commPercent) / 100;
                                        $vendorTotalCommission += $prodCommissionAmount;
                                        $totalCommissionPercentage += $commPercent;
                                    }
                                    
                                    // Calculate average commission percentage for display
                                    $avgCommissionPercentage = $vendorProducts->count() > 0 ? $totalCommissionPercentage / $vendorProducts->count() : 0;
                                    
                                    $vendorSubtotalWithTax = $vendorSubtotalBeforeTax + $vendorTotalTax;
                                    $vendorTotalWithShipping = $vendorSubtotalWithTax + $vendorShipping;
                                    $vendorTotalRemaining = $vendorTotalWithShipping - $vendorTotalCommission;
                                    
                                    // Get color for this vendor
                                    $colors = $vendorColors[$colorIndex % count($vendorColors)];
                                    $colorIndex++;
                                @endphp
                                
                                {{-- Per-Vendor Remaining Summary Box with Products Inside --}}
                                <div class="col-12 mb-3">
                                    <x-order::vendor-remaining-with-products
                                        :vendorName="$vendorName"
                                        :products="$vendorProducts"
                                        :subtotalBeforeTax="$vendorSubtotalBeforeTax"
                                        :taxAmount="$vendorTotalTax"
                                        :subtotalWithTax="$vendorSubtotalWithTax"
                                        :shipping="$vendorShipping"
                                        :total="$vendorTotalWithShipping"
                                        :commissionPercentage="$avgCommissionPercentage"
                                        :commissionAmount="$vendorTotalCommission"
                                        :remaining="$vendorTotalRemaining"
                                        :promoDiscount="0"
                                        :colors="$colors"
                                    />
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Change Stage Modal Component (Vendor Only) -->
    @if(!isAdmin())
        <x-order::change-stage-modal :order-id="$order->id" :current-stage-id="$order->stage_id" :order-stages="$orderStages ?? []" />
    @endif

    <style>
        .no-print {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        @media print {

            /* Hide non-printable elements */
            .sidebar,
            .header,
            .navbar,
            .no-print,
            .breadcrumb,
            nav,
            .btn,
            button,
            footer {
                display: none !important;
            }

            .badge {
                background-color: white !important;
                color: black !important;
            }

            /* Reset body and container */
            body * {
                visibility: hidden;
            }

            #printableArea,
            #printableArea * {
                visibility: visible;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                font-size: 11pt;
            }

            .container-fluid {
                margin: 0 !important;
                padding: 10mm !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 15px !important;
                background: white !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }

            /* Card styling for print */
            .card {
                border: 1pt solid #ddd !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                margin-bottom: 8pt !important;
            }

            .card-body {
                padding: 8pt !important;
            }

            .card-title {
                font-size: 10pt !important;
                margin-bottom: 8pt !important;
                border-bottom: 1pt solid #ddd !important;
                padding-bottom: 6pt !important;
            }

            /* Hide icons in print */
            .uil {
                display: none !important;
            }

            /* Header section - Order Info & Customer Details */
            .row.mb-40 {
                page-break-inside: avoid;
                margin-bottom: 10pt !important;
                padding-bottom: 0 !important;
                display: block !important;
            }

            /* Fix column layout for print */
            .row {
                display: block !important;
                page-break-inside: avoid;
            }

            .col-lg-6,
            .col-md-6,
            .col-md-3 {
                width: 100% !important;
                float: none !important;
                display: block !important;
                margin-bottom: 10pt !important;
            }

            /* Detail rows styling */
            .detail-row {
                display: flex !important;
                justify-content: space-between !important;
                padding: 4pt 0 !important;
                border-bottom: none !important;
                font-size: 9pt !important;
                margin-bottom: 3pt !important;
            }

            .detail-label {
                font-weight: 600 !important;
                color: #333 !important;
                min-width: 100pt !important;
            }

            .detail-value {
                color: #333 !important;
                text-align: right !important;
                flex: 1;
            }

            /* Text styling */
            p {
                margin-bottom: 4pt !important;
                line-height: 1.3 !important;
            }

            .text-primary {
                color: #003d82 !important;
            }

            .fw-bold {
                font-weight: bold !important;
            }

            /* Text alignment */
            .text-end {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            /* Table styling */
            .table-responsive {
                width: 100% !important;
                overflow: visible !important;
                page-break-inside: avoid;
            }

            .table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 10pt !important;
                page-break-inside: auto;
                font-size: 10pt !important;
            }

            .table thead {
                background-color: #003d82 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color: white !important;
                page-break-inside: avoid;
                page-break-after: avoid;
            }

            .table th {
                padding: 6pt 4pt !important;
                border: 0.5pt solid #003d82 !important;
                font-weight: bold !important;
                color: white !important;
                background-color: #003d82 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table td {
                padding: 5pt !important;
                border: 0.5pt solid #ddd !important;
                line-height: 1.2 !important;
            }

            .table tbody tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .table tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Badge styling */
            .badge {
                padding: 2pt 4pt !important;
                font-size: 8pt !important;
                border-radius: 2pt !important;
                display: inline-block !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-lg {
                padding: 3pt 6pt !important;
                font-size: 9pt !important;
            }

            .bg-info {
                background-color: #17a2b8 !important;
                color: white !important;
            }

            .bg-dark {
                background-color: #343a40 !important;
                color: white !important;
            }

            .bg-success {
                background-color: #28a745 !important;
                color: white !important;
            }

            .bg-secondary {
                background-color: #6c757d !important;
                color: white !important;
            }

            .bg-danger {
                background-color: #dc3545 !important;
                color: white !important;
            }

            /* Summary card with gradient */
            .card[style*="gradient"] {
                background: #003d82 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border: 1pt solid #003d82 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                margin-bottom: 8pt !important;
            }

            .card[style*="gradient"] .card-body {
                padding: 8pt !important;
            }

            .card[style*="gradient"] .card-title {
                color: white !important;
                border-bottom: 1pt solid rgba(255, 255, 255, 0.3) !important;
                font-size: 10pt !important;
                margin-bottom: 8pt !important;
                padding-bottom: 6pt !important;
            }

            .card[style*="gradient"] * {
                color: white !important;
            }

            .card[style*="gradient"] .summary-row {
                color: white !important;
            }

            .card[style*="gradient"] .summary-row span {
                color: white !important;
            }

            .summary-row {
                display: flex !important;
                justify-content: space-between !important;
                padding: 4pt 0 !important;
                font-size: 9pt !important;
                margin-bottom: 2pt !important;
            }

            .summary-row span {
                color: white !important;
            }

            /* Summary details section */
            .summary-details {
                width: 100% !important;
            }

            /* Horizontal rule */
            hr {
                border: 0 !important;
                border-top: 0.5pt solid #ddd !important;
                margin: 4pt 0 !important;
                page-break-inside: avoid;
            }

            hr.bg-white {
                border-top: 0.5pt solid rgba(255, 255, 255, 0.3) !important;
                margin: 4pt 0 !important;
            }

            .card[style*="gradient"] hr {
                border-top: 0.5pt solid rgba(255, 255, 255, 0.3) !important;
            }

            /* Flex utilities for print */
            .d-flex {
                display: flex !important;
            }

            .justify-content-between {
                justify-content: space-between !important;
            }

            .justify-content-start {
                justify-content: flex-start !important;
            }

            .justify-content-end {
                justify-content: flex-end !important;
            }

            .align-items-center {
                align-items: center !important;
            }

            .mb-10 {
                margin-bottom: 8pt !important;
            }

            .mb-15 {
                margin-bottom: 10pt !important;
            }

            .mb-5 {
                margin-bottom: 4pt !important;
            }

            .mb-0 {
                margin-bottom: 0 !important;
            }

            /* Font sizes */
            .fs-16 {
                font-size: 14pt !important;
            }

            h6 {
                font-size: 12pt !important;
                margin-bottom: 10pt !important;
            }

            small {
                font-size: 9pt !important;
            }

            /* Text utilities */
            .text-muted {
                color: #6c757d !important;
            }

            /* Page settings */
            @page {
                size: A4;
                margin: 15mm 10mm;
            }

            /* Prevent orphans and widows */
            p,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                orphans: 3;
                widows: 3;
            }

            /* Color adjustment for all colored elements */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <script>
        function printInvoice() {
            // Get order number for filename
            const orderNumber = '{{ $order->order_number }}';

            // Set document title for print dialog
            const originalTitle = document.title;
            document.title = `Invoice_${orderNumber}`;

            // Trigger print dialog
            window.print();

            // Restore original title after print dialog closes
            setTimeout(() => {
                document.title = originalTitle;
            }, 100);
        }
    </script>

    <!-- Change Product Stage Modal -->
    @if(isset($orderStages) && count($orderStages) > 0)
    <div class="modal fade" id="changeProductStageModal" tabindex="-1" aria-labelledby="changeProductStageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeProductStageModalLabel">{{ trans('order::order.change_product_stage') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changeProductStageForm">
                    <div class="modal-body">
                        <div id="productStageSelectContainer"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('order::order.update_stage') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    @push('scripts')
    @if(isset($orderStages) && count($orderStages) > 0)
    <script>
        $(document).ready(function() {
            // ===== PRINT FUNCTIONALITY =====
            let selectedProductsForPrint = [];
            
            // Handle select all checkbox
            $('#selectAllProducts').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.product-checkbox').prop('checked', isChecked);
                updateSelectedProductsForPrint();
            });
            
            // Handle individual product checkbox
            $('.product-checkbox').on('change', function() {
                updateSelectedProductsForPrint();
                
                // Update select all checkbox state
                const totalCheckboxes = $('.product-checkbox').length;
                const checkedCheckboxes = $('.product-checkbox:checked').length;
                $('#selectAllProducts').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
            
            // Update selected products for print
            function updateSelectedProductsForPrint() {
                selectedProductsForPrint = [];
                $('.product-checkbox:checked').each(function() {
                    selectedProductsForPrint.push($(this).data('product-id'));
                });
                
                // Update button state and count
                $('#selectedCount').text(selectedProductsForPrint.length);
                $('#printSelectedBtn').prop('disabled', selectedProductsForPrint.length === 0);
            }
            
            // Handle print selected button
            $('#printSelectedBtn').on('click', function() {
                if (selectedProductsForPrint.length === 0) {
                    toastr.warning('{{ trans('order::order.please_select_products_to_print') }}');
                    return;
                }
                
                // Build URL with selected product IDs
                const productIds = selectedProductsForPrint.join(',');
                const printUrl = '{{ route("admin.orders.print", $order->id) }}?products=' + productIds;
                
                // Open in new window
                window.open(printUrl, '_blank');
            });
            
            // ===== STAGE MANAGEMENT FUNCTIONALITY =====
            console.log('Initializing stage management...');
            
            // Store all stages data with step values
            const allStages = @json($orderStages);
            console.log('All stages:', allStages);
            
            // Add step values to stages if not present
            const STAGE_STEPS = {
                'new': 1,
                'in_progress': 2,
                'deliver': 3,
                'cancel': 3,
                'refund': 4
            };
            
            allStages.forEach(stage => {
                if (!stage.step) {
                    stage.step = STAGE_STEPS[stage.type] || 0;
                }
            });
            
            const FINAL_STAGES = ['deliver', 'cancel'];
            
            let currentProductId = null;
            
            // Handle click on change stage button
            $('.change-product-stage-btn').on('click', function() {
                currentProductId = $(this).data('product-id');
                const currentStageId = $(this).data('current-stage');
                console.log('Button clicked - Product ID:', currentProductId, 'Current Stage ID:', currentStageId);
            });
            
            // Filter stages based on current stage
            function getAvailableStages(currentStageId) {
                const currentStage = allStages.find(s => s.id == currentStageId);
                const currentType = currentStage ? currentStage.type : null;
                const currentStep = currentStage ? (currentStage.step || 0) : 0;
                
                console.log('Current stage:', currentStage, 'Type:', currentType, 'Step:', currentStep);
                
                // If current stage is final, no transitions allowed
                if (currentType && FINAL_STAGES.includes(currentType)) {
                    console.log('Current stage is final, no transitions allowed');
                    return [];
                }
                
                const filtered = allStages.filter(stage => {
                    // Can't transition to same stage
                    if (stage.id == currentStageId) return false;
                    
                    const newType = stage.type;
                    const newStep = stage.step || 0;
                    
                    // Can always cancel from any non-final stage
                    if (newType === 'cancel') return true;
                    
                    // Can't go backwards
                    if (newStep < currentStep) return false;
                    
                    // Can't skip steps (must go to next step only)
                    if (newStep > currentStep + 1) return false;
                    
                    // Refund only after deliver
                    if (newType === 'refund' && currentType !== 'deliver') return false;
                    
                    return true;
                });
                
                console.log('Available stages after filtering:', filtered);
                return filtered;
            }
            
            // When modal opens, filter and render available stages
            $('#changeProductStageModal').on('show.bs.modal', function(e) {
                console.log('Modal opening...');
                const button = $(e.relatedTarget);
                const currentStageId = button.data('current-stage');
                console.log('Current stage ID from button:', currentStageId);
                
                const availableStages = getAvailableStages(currentStageId);
                
                if (availableStages.length === 0) {
                    console.log('No available stages');
                    $('#productStageSelectContainer').html(`
                        <div class="alert alert-warning">
                            <i class="uil uil-exclamation-triangle me-2"></i>
                            {{ trans('order::order.no_available_stages') }}
                        </div>
                    `);
                    $('#changeProductStageForm button[type="submit"]').prop('disabled', true);
                    return;
                }
                
                // Render simple select dropdown
                let selectHtml = `
                    <div class="form-group">
                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                            {{ trans('order::order.select_new_stage') }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="stage_id" id="productStageSelect" class="form-control" required>
                            <option value="">{{ trans('order::order.select_stage') }}</option>
                `;
                
                availableStages.forEach(stage => {
                    selectHtml += `<option value="${stage.id}">${stage.name}</option>`;
                });
                
                selectHtml += `
                        </select>
                    </div>
                `;
                
                console.log('Rendering select HTML');
                $('#productStageSelectContainer').html(selectHtml);
                $('#changeProductStageForm button[type="submit"]').prop('disabled', false);
            });
            
            // Handle stage change submission
            $('#changeProductStageForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!currentProductId) {
                    toastr.error('{{ trans("order::order.error_updating_stage") }}');
                    return;
                }
                
                const stageId = $('#productStageSelect').val();
                if (!stageId) {
                    toastr.error('{{ trans("order::order.please_select_stage") }}');
                    return;
                }
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="uil uil-spinner-alt spin me-1"></i>{{ trans("order::order.updating_stage") }}');
                
                $.ajax({
                    url: '{{ route("admin.orders.products.change-stage", ["orderProductId" => "__PRODUCT_ID__"]) }}'.replace('__PRODUCT_ID__', currentProductId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stage_id: stageId
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            
                            // Check if the new stage is "in_progress"
                            const newStageType = response.data?.stage?.type;
                            if (newStageType === 'in_progress') {
                                // Redirect to allocate page
                                setTimeout(() => {
                                    window.location.href = '{{ route("admin.order-fulfillments.show", $order->id) }}';
                                }, 1000);
                            } else {
                                // Reload page to show updated stage
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }
                        } else {
                            toastr.error(response.message || '{{ trans("order::order.error_updating_stage") }}');
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || '{{ trans("order::order.error_updating_stage") }}';
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                        $('#changeProductStageModal').modal('hide');
                    }
                });
            });
        });
    </script>
    @endif
    @endpush
@endsection
