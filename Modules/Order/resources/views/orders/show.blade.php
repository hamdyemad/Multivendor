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
                                        <div class="detail-row mb-15 flex-column">
                                            <span class="detail-label mb-2">{{ trans('order::order.vendor_stages') }}:</span>
                                            <div class="w-100">
                                                @php
                                                    $hasVendorStages = isset($order->vendorStages) && $order->vendorStages->count() > 0;
                                                @endphp
                                                
                                                @if($hasVendorStages)
                                                    @foreach($order->vendorStages as $vendorStage)
                                                        @php
                                                            $vendor = $vendorStage->vendor;
                                                            $stage = $vendorStage->stage;
                                                            
                                                            if (!$vendor || !$stage) {
                                                                continue;
                                                            }
                                                            
                                                            $isCurrentVendor = $isVendorUser && isset($currentVendorId) && $vendor->id == $currentVendorId;
                                                            
                                                            // If viewing as vendor, only show their own stage
                                                            if ($isVendorUser && !$isCurrentVendor) {
                                                                continue;
                                                            }
                                                        @endphp
                                                        <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded" style="background: #f8f9fa;">
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if($vendor->logo)
                                                                    <img src="{{ asset('storage/' . $vendor->logo->path) }}" 
                                                                         alt="{{ $vendor->name }}"
                                                                         class="rounded"
                                                                         style="width: 30px; height: 30px;">
                                                                @else
                                                                    <div class="rounded d-flex align-items-center justify-content-center" 
                                                                         style="width: 30px; height: 30px; background: #fff;">
                                                                        <i class="uil uil-store text-muted"></i>
                                                                    </div>
                                                                @endif
                                                                <span class="fw-500">{{ $vendor->getTranslation('name', app()->getLocale()) }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <x-protected-badge 
                                                                    :color="$stage->color ?? '#6c757d'"
                                                                    :text="$stage->getTranslation('name', app()->getLocale()) ?? 'N/A'"
                                                                    size="md"
                                                                    :id="'vendor-stage-badge-' . $vendor->id"
                                                                />
                                                                @if($isCurrentVendor && isset($orderStages) && count($orderStages) > 0)
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                            data-bs-toggle="modal" 
                                                                            data-bs-target="#changeVendorStageModal"
                                                                            style="padding: 2px 8px; font-size: 12px;">
                                                                        <i class="uil uil-exchange m-0" style="font-size: 14px;"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="uil uil-exclamation-triangle me-2"></i>
                                                        No vendor stages found for this order. Vendor stages are created automatically when the order is placed.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="detail-row ">
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
                                        <hr class="my-15">
                                        <h6 class="fw-bold mb-15 d-flex align-items-center justify-content-between">
                                            <span>
                                                <i class="uil uil-credit-card me-2" style="color: #5f63f2; font-size: 18px;"></i>
                                                {{ trans('order::order.payment_information') }}
                                            </span>
                                        </h6>
                                        <div class="detail-row mb-15">
                                            <span class="detail-label">{{ trans('order::order.payment_type') }}:</span>
                                            <span class="detail-value">
                                                @if ($order->payment_type === 'cash_on_delivery')
                                                    <x-protected-badge 
                                                        color="#28a745"
                                                        text="💵 {{ trans('order::order.cash_on_delivery') }}"
                                                        size="md"
                                                        id="payment-type-badge"
                                                    />
                                                @elseif($order->payment_type === 'visa')
                                                    <x-protected-badge 
                                                        color="#5f63f2"
                                                        text="💳 {{ trans('order::order.visa') }}"
                                                        size="md"
                                                        id="payment-type-badge"
                                                    />
                                                @elseif($order->payment_type === 'online')
                                                    <x-protected-badge 
                                                        color="#17a2b8"
                                                        text="🌐 {{ trans('order::order.online') }}"
                                                        size="md"
                                                        id="payment-type-badge"
                                                    />
                                                @else
                                                    <x-protected-badge 
                                                        color="#6c757d"
                                                        :text="$order->payment_type ?? 'N/A'"
                                                        size="md"
                                                        id="payment-type-badge"
                                                    />
                                                @endif
                                            </span>
                                        </div>
                                        @if($order->payment_type !== 'cash_on_delivery')
                                        <div class="detail-row mb-15">
                                            <span class="detail-label">{{ trans('order::order.payment_status') }}:</span>
                                            <span class="detail-value">
                                                @if ($order->payment_visa_status === 'paid' || $order->payment_visa_status === 'success')
                                                    <x-protected-badge 
                                                        color="#28a745"
                                                        text="✓ {{ trans('order::order.paid') }}"
                                                        size="md"
                                                        id="payment-status-badge"
                                                    />
                                                @elseif($order->payment_visa_status === 'pending' || empty($order->payment_visa_status))
                                                    <x-protected-badge 
                                                        color="#ffc107"
                                                        text="⏳ {{ trans('order::order.pending') }}"
                                                        size="md"
                                                        id="payment-status-badge"
                                                    />
                                                @elseif($order->payment_visa_status === 'failed')
                                                    <x-protected-badge 
                                                        color="#dc3545"
                                                        text="✗ {{ trans('order::order.failed') }}"
                                                        size="md"
                                                        id="payment-status-badge"
                                                    />
                                                @else
                                                    <x-protected-badge 
                                                        color="#6c757d"
                                                        :text="$order->payment_visa_status"
                                                        size="md"
                                                        id="payment-status-badge"
                                                    />
                                                @endif
                                            </span>
                                        </div>
                                        @if($order->payment_reference)
                                        <div class="detail-row">
                                            <span class="detail-label">{{ trans('order::order.payment_reference') }}:</span>
                                            <span class="detail-value">
                                                <x-protected-badge 
                                                    color="#5f63f2"
                                                    :text="$order->payment_reference"
                                                    size="md"
                                                    id="payment-reference-badge"
                                                />
                                            </span>
                                        </div>
                                        @endif
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
                            text-align: end;
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

                    <!-- Vendors and Their Stages -->

                    <!-- Products Table -->
                    <div class="mb-40">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">{{ trans('order::order.products') }}</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="uil uil-arrow-left me-1"></i>{{ trans('common.back') }}
                                </a>
                                <button type="button" class="btn btn-primary btn-sm" id="printAllBtn">
                                    <i class="uil uil-print me-1"></i>{{ trans('order::order.print_invoice') }}
                                </button>
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
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.shipping') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.total_with_shipping') }}
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
                                            
                                            // Shipping cost per product (must be defined before commission calculation)
                                            $productShippingCost = $product->shipping_cost ?? 0;
                                            
                                            // Total tax percentage
                                            $totalTaxPercentage = $product->taxes->sum('percentage') ?? 0;
                                            
                                            // Commission is stored as percentage
                                            $bnaiaCommission = $product->commission;
                                            $commissionPercent = $bnaiaCommission;
                                            
                                            // Calculate commission amount from percentage (on total with shipping)
                                            $productTotalWithShipping = $productTotalWithTax + $productShippingCost;
                                            $commissionAmount = ($productTotalWithShipping * $commissionPercent) / 100;
                                            
                                            // Remaining = Total with shipping - Commission amount
                                            $remaining = $productTotalWithShipping - $commissionAmount;
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
                                                            @php
                                                                $taxName = $tax->tax?->getTranslation('name', app()->getLocale()) ?? $tax->tax_title ?? '';
                                                            @endphp
                                                            <x-protected-badge 
                                                                color="#6c757d"
                                                                :text="$taxName . ' ' . $tax->percentage . '%'"
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
                                                {{ number_format($productShippingCost, 2) }}
                                                {{ currency() }}
                                            </td>
                                            <td class="text-center fw-bold" style="color: #28a745;">
                                                {{ number_format($productTotalWithTax + $productShippingCost, 2) }}
                                                {{ currency() }}
                                            </td>
                                            <td class="text-center text-danger">
                                                <div>{{ $commissionPercent }}%</div>
                                                <div class="fw-bold">{{ number_format($commissionAmount, 2) }} {{ currency() }}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-20">
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
                            
                            // Calculate total with shipping
                            $totalWithShippingOrder = $totalProductsPriceWithTax - $order->customer_promo_code_amount + $order->shipping;
                        @endphp
                        <div class="row mb-40">
                            <div class="col-12">
                                <div class="card border-0"
                                    style="background: white; color: #333; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-20 d-flex align-items-center" style="color: #5f63f2;">
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
                                                            <small style="color: #999;">({{ $order->customer_promo_code_title }})</small>
                                                        @endif
                                                    </span>
                                                    <span class="fw-bold" style="color: #dc3545;">-{{ number_format($order->customer_promo_code_amount, 2) }}
                                                        {{ currency() }}</span>
                                                </div>
                                            @endif
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold">{{ trans('order::order.taxes_price') }}</span>
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
                                                    <span class="fw-bold" style="color: #dc3545;">-{{ number_format($order->total_discounts, 2) }}
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
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold">{{ trans('order::order.total_with_shipping') }}</span>
                                                <span class="fw-bold">{{ number_format($totalWithShippingOrder, 2) }}
                                                    {{ currency() }}</span>
                                            </div>
                                            @if ($order->points_used > 0)
                                                <div class="summary-row mb-12">
                                                    <span class="fw-bold">{{ trans('order::order.points_used') }}</span>
                                                    <span class="fw-bold" style="color: #dc3545;">-{{ number_format($order->points_cost, 2) }}
                                                        {{ currency() }} ({{ number_format($order->points_used, 0) }} {{ trans('order::order.points') }})</span>
                                                </div>
                                            @endif
                                            <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
                                            <div class="summary-row" style="font-size: 18px;">
                                                <span class="fw-bold">{{ trans('order::order.total') }}</span>
                                                <span class="fw-bold" style="color: #5f63f2;">{{ number_format($order->total_price, 2) }}
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
                                $prodShippingCost = $prod->shipping_cost ?? 0;
                                
                                $totalProductsPriceBeforeTax += $prodTotalBeforeTax;
                                $totalProductsTax += $prodTax;
                                $vendorShippingCost += $prodShippingCost;
                                
                                // Calculate commission from each product (on total with shipping)
                                $commPercent = $prod->commission > 0 ? $prod->commission : ($prod->vendorProduct?->product?->department?->commission ?? 0);
                                $prodTotalWithShipping = $prodTotalWithTax + $prodShippingCost;
                                $prodCommissionAmount = ($prodTotalWithShipping * $commPercent) / 100;
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
                            
                            // Get vendor name for display
                            if (isset($isVendorUser) && $isVendorUser && isset($vendorProducts) && $vendorProducts->count() > 0) {
                                $currentVendorName = $vendorProducts->first()->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? 'Vendor';
                                $currentVendorId = $vendorProducts->first()->vendorProduct?->vendor_id;
                            } else {
                                $currentVendorName = 'Vendor';
                                $currentVendorId = null;
                            }
                            
                            // Get vendor_order_stage for discount shares (vendor view)
                            $vendorOrderStageForVendor = null;
                            $vendorPromoCodeShare = 0;
                            $vendorPointsShare = 0;
                            if ($currentVendorId) {
                                $vendorOrderStageForVendor = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                                    ->where('vendor_id', $currentVendorId)
                                    ->first();
                                $vendorPromoCodeShare = $vendorOrderStageForVendor?->promo_code_share ?? 0;
                                $vendorPointsShare = $vendorOrderStageForVendor?->points_share ?? 0;
                            }
                            
                            // Recalculate remaining: Total with Shipping - Commission
                            $totalRemaining = $totalProductsPriceWithTax + $shippingToUse - $totalCommission;
                            $totalWithShipping = $totalProductsPriceWithTax + $shippingToUse;
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
                                    :promoCodeShare="$vendorPromoCodeShare"
                                    :pointsShare="$vendorPointsShare"
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
                                
                                // Check if order has promo code or points discount
                                $hasPromoCode = $order->customer_promo_code_amount > 0;
                                $hasPointsDiscount = $order->points_cost > 0;
                            @endphp
                            
                            {{-- Explanation box for promo code and points share --}}
                            @if($hasPromoCode || $hasPointsDiscount)
                                <div class="col-12 mb-4">
                                    <div class="card border-0 shadow-sm" style="background: #f8f9fa; border-radius: 12px;">
                                        <div class="card-body p-4">
                                            <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #1565c0;">
                                                <i class="uil uil-info-circle me-2" style="font-size: 22px;"></i>
                                                {{ trans('order::order.discount_share_explanation_title') }}
                                            </h6>
                                            <p class="mb-3" style="color: #555; font-size: 14px; line-height: 1.6;">
                                                {{ trans('order::order.discount_share_explanation_text') }}
                                            </p>
                                            <div class="row">
                                                @if($hasPromoCode)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="d-flex align-items-start p-3" style="background: #e8f5e9; border-radius: 8px;">
                                                            <i class="uil uil-tag-alt me-2" style="color: #28a745; font-size: 18px; margin-top: 2px;"></i>
                                                            <div>
                                                                <strong style="color: #28a745;">{{ trans('order::order.promo_code_share') }}</strong>
                                                                <p class="mb-0 mt-1" style="color: #555; font-size: 13px;">{{ trans('order::order.promo_code_share_explanation') }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($hasPointsDiscount)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="d-flex align-items-start p-3" style="background: #e8f5e9; border-radius: 8px;">
                                                            <i class="uil uil-star me-2" style="color: #28a745; font-size: 18px; margin-top: 2px;"></i>
                                                            <div>
                                                                <strong style="color: #28a745;">{{ trans('order::order.points_share') }}</strong>
                                                                <p class="mb-0 mt-1" style="color: #555; font-size: 13px;">{{ trans('order::order.points_share_explanation') }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @foreach($productsByVendor as $vendorId => $vendorProducts)
                                @php
                                    // Get vendor name
                                    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? 'N/A';
                                    
                                    // Get vendor_order_stage for discount shares
                                    $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                                        ->where('vendor_id', $vendorId)
                                        ->first();
                                    $promoCodeShare = $vendorOrderStage?->promo_code_share ?? 0;
                                    $pointsShare = $vendorOrderStage?->points_share ?? 0;
                                    
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
                                        $prodShippingCost = $prod->shipping_cost ?? 0;
                                        
                                        $vendorSubtotalBeforeTax += $prodTotalBeforeTax;
                                        $vendorTotalTax += $prodTax;
                                        $vendorShipping += $prodShippingCost;
                                        
                                        // Calculate commission from each product (on total with shipping)
                                        $commPercent = $prod->commission > 0 ? $prod->commission : ($prod->vendorProduct?->product?->department?->commission ?? 0);
                                        $prodTotalWithShipping = $prodTotalWithTax + $prodShippingCost;
                                        $prodCommissionAmount = ($prodTotalWithShipping * $commPercent) / 100;
                                        $vendorTotalCommission += $prodCommissionAmount;
                                        $totalCommissionPercentage += $commPercent;
                                    }
                                    
                                    // Calculate average commission percentage for display
                                    $avgCommissionPercentage = $vendorProducts->count() > 0 ? $totalCommissionPercentage / $vendorProducts->count() : 0;
                                    
                                    $vendorSubtotalWithTax = $vendorSubtotalBeforeTax + $vendorTotalTax;
                                    $vendorTotalWithShipping = $vendorSubtotalWithTax + $vendorShipping;
                                    
                                    // Calculate remaining: Total with Shipping - Commission
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
                                        :promoCodeShare="$promoCodeShare"
                                        :pointsShare="$pointsShare"
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

    <!-- Change Vendor Stage Modal (Vendor Only) -->
    @if($isVendorUser && isset($currentVendorId) && isset($orderStages) && count($orderStages) > 0)
    <div class="modal fade" id="changeVendorStageModal" tabindex="-1" aria-labelledby="changeVendorStageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeVendorStageModalLabel">
                        <i class="uil uil-exchange me-2"></i>{{ trans('order::order.change_vendor_stage') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changeVendorStageForm">
                    <div class="modal-body">
                        <!-- Current Stage Info -->
                        @if(isset($currentVendorStage) && $currentVendorStage->stage)
                        <div class="alert alert-light border mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block mb-1">{{ trans('order::order.current_stage') }}</small>
                                    <x-protected-badge 
                                        :color="$currentVendorStage->stage->color ?? '#6c757d'"
                                        :text="$currentVendorStage->stage->getTranslation('name', app()->getLocale())"
                                        size="lg"
                                        id="current-stage-badge"
                                    />
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Stage Selection -->
                        <div class="mb-3">
                            <label for="vendorStageSelect" class="form-label fw-bold">
                                {{ trans('order::order.select_new_stage') }}
                                <span class="text-danger">*</span>
                            </label>
                            <select name="stage_id" id="vendorStageSelect" class="form-control form-control-lg" required>
                                <option value="">{{ trans('order::order.select_stage') }}</option>
                            </select>
                            <div id="vendorStageWarning" class="alert alert-warning mt-2" style="display: none;"></div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info mb-0">
                            <i class="uil uil-info-circle me-2"></i>
                            <strong>{{ trans('common.note') }}:</strong> {{ trans('order::order.vendor_stage_change_info') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="uil uil-times me-1"></i>{{ trans('common.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="uil uil-check me-1"></i>{{ trans('order::order.update_stage') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    @push('scripts')
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
            
            // Handle print all button
            $('#printAllBtn').on('click', function() {
                // Print all products without filtering
                const printUrl = '{{ route("admin.orders.print", $order->id) }}';
                window.open(printUrl, '_blank');
            });
            
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
        });
    </script>
    
    @if($isVendorUser && isset($currentVendorId) && isset($orderStages) && count($orderStages) > 0)
    <script>
        $(document).ready(function() {
            let orderStages = @json($orderStages);
            
            // Stage step mapping (must match OrderStage::STAGE_STEPS in PHP)
            const STAGE_STEPS = {
                'new': 1,
                'in_progress': 2,
                'deliver': 3,
                'cancel': 3,
                'refund': 4
            };

            // Final stages that cannot transition
            const FINAL_STAGES = ['deliver', 'cancel'];

            // Get step for a stage type (default to 0 if type is null/undefined)
            function getStageStep(type) {
                if (!type) return 0;
                return STAGE_STEPS[type] || 0;
            }

            // Check if stage is final
            function isFinalStage(type) {
                if (!type) return false;
                return FINAL_STAGES.includes(type);
            }

            // Check if transition is allowed
            function canTransitionTo(currentType, newType, currentId, newId) {
                // Cannot transition to same stage
                if (currentId == newId) {
                    return false;
                }

                // Cannot change from final stages
                if (isFinalStage(currentType)) {
                    return false;
                }

                // If current type is null/undefined, allow only to step 1 or 2
                if (!currentType) {
                    const newStep = getStageStep(newType);
                    return newStep <= 2; // Can go to new or in_progress
                }

                // If new type is null/undefined, allow transition
                if (!newType) {
                    return true;
                }

                const currentStep = getStageStep(currentType);
                const newStep = getStageStep(newType);

                // Cannot go backwards
                if (newStep < currentStep) {
                    return false;
                }

                // Can always cancel from any non-final stage
                if (newType === 'cancel') {
                    return true;
                }

                // Cannot skip steps - must go to next step only
                if (newStep > currentStep + 1) {
                    return false;
                }

                // Refund only after deliver
                if (newType === 'refund' && currentType !== 'deliver') {
                    return false;
                }

                return true;
            }

            // Populate vendor stage select dropdown with step-based restrictions
            function populateVendorStageSelect() {
                const vendorStageSelect = $('#vendorStageSelect');
                const stageWarning = $('#vendorStageWarning');
                
                @if(isset($currentVendorStage) && $currentVendorStage->stage)
                const currentStageId = {{ $currentVendorStage->stage_id }};
                const currentStageType = '{{ $currentVendorStage->stage->type }}';
                @else
                const currentStageId = null;
                const currentStageType = null;
                @endif
                
                vendorStageSelect.find('option:not(:first)').remove();
                stageWarning.hide();

                // Check if current stage is final
                if (isFinalStage(currentStageType)) {
                    stageWarning.text('{{ trans("order::order.cannot_change_final_stage") }}').show();
                    vendorStageSelect.prop('disabled', true);
                    return;
                }

                let hasOptions = false;
                orderStages.forEach(stage => {
                    const stageName = stage.name;
                    const stageType = stage.type;
                    
                    // Check if transition is allowed
                    if (canTransitionTo(currentStageType, stageType, currentStageId, stage.id)) {
                        vendorStageSelect.append(
                            `<option value="${stage.id}" data-type="${stageType}">${stageName}</option>`
                        );
                        hasOptions = true;
                    }
                });

                if (!hasOptions) {
                    stageWarning.text('{{ trans("order::order.no_available_stages") }}').show();
                    vendorStageSelect.prop('disabled', true);
                }
            }
            
            // Populate stages when modal is shown
            $('#changeVendorStageModal').on('shown.bs.modal', function() {
                populateVendorStageSelect();
            });
            
            // Handle vendor stage change submission
            $('#changeVendorStageForm').on('submit', function(e) {
                e.preventDefault();
                
                const stageId = $('#vendorStageSelect').val();
                const selectedOption = $('#vendorStageSelect option:selected');
                const stageType = selectedOption.data('type');
                
                if (!stageId) {
                    toastr.error('{{ trans("order::order.please_select_stage") }}');
                    return;
                }
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="uil uil-spinner-alt spin me-1"></i>{{ trans("order::order.updating_stage") }}');
                
                $.ajax({
                    url: '{{ route("admin.orders.vendor-stage.change", ["orderId" => $order->id, "vendorId" => $currentVendorId ?? 0]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stage_id: stageId
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            
                            // If stage changed to in_progress, redirect to allocate page
                            if (stageType === 'in_progress') {
                                setTimeout(() => {
                                    window.location.href = '{{ route("admin.order-fulfillments.show", $order->id) }}';
                                }, 1000);
                            } else {
                                // Otherwise reload page to show updated stage
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
                        $('#changeVendorStageModal').modal('hide');
                    }
                });
            });
        });
    </script>
    @endif
    @endpush
@endsection
