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
                                        <div class="detail-row mb-15 justify-content-between">
                                            <span class="detail-label">{{ trans('order::order.stage') }}:</span>
                                            <span class="badge badge-lg badge-round"
                                                style="background: {{ $order->stage?->color ?? '#6c757d' }}; color: white">{{ $order->stage?->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">{{ trans('order::order.order_from') }}:</span>
                                            <span class="detail-value">
                                                @if ($order->order_from === 'web')
                                                    <span class="badge badge-lg badge-round bg-info"><i
                                                            class="uil uil-globe me-1"></i>{{ trans('order::order.web') }}</span>
                                                @elseif($order->order_from === 'ios')
                                                    <span class="badge badge-lg badge-round bg-dark"><i
                                                            class="uil uil-apple me-1"></i>{{ trans('order::order.ios') }}</span>
                                                @elseif($order->order_from === 'android')
                                                    <span class="badge badge-lg badge-round bg-success"><i
                                                            class="uil uil-android me-1"></i>{{ trans('order::order.android') }}</span>
                                                @else
                                                    <span
                                                        class="badge badge-lg badge-round bg-secondary">{{ $order->order_from }}</span>
                                                @endif
                                            </span>
                                        </div>
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
                                        <div class="detail-row">
                                            <span class="detail-label"><i
                                                    class="uil uil-map-pin me-1"></i>{{ trans('order::order.customer_address') }}:</span>
                                            <span class="detail-value">{{ $order->customer_address }}</span>
                                        </div>
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
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover" style="border-color: #dee2e6;">
                                <thead class="userDatatable-header" style="background-color: #003d82; color: white;">
                                    <tr>
                                        <th class="text-white fw-bold">#</th>
                                        <th class="text-white fw-bold">{{ trans('order::order.product') }}</th>
                                        <th class="text-white fw-bold text-end">{{ trans('order::order.price_per_unit') }}
                                        </th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.quantity') }}
                                        </th>
                                        <th class="text-white fw-bold text-end">{{ trans('order::order.total_price') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->products as $key => $product)
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
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $key + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($productImage)
                                                        <img src="{{ asset('storage/' . $productImage) }}" 
                                                             alt="{{ $product->vendorProduct->product->name ?? 'Product' }}"
                                                             class="rounded"
                                                             style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #dee2e6;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 60px; height: 60px; border: 1px solid #dee2e6;">
                                                            <i class="uil uil-image text-muted" style="font-size: 24px;"></i>
                                                        </div>
                                                    @endif
                                                    <div>
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
                                            <td class="text-end">{{ number_format($product->price, 2) }}
                                                {{ currency() }}</td>
                                            <td class="text-center">{{ $product->quantity }}</td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($product->price * $product->quantity, 2) }}
                                                {{ currency() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-20">
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
                                                        <span
                                                            class="badge badge-lg badge-round bg-danger">{{ trans('order::order.fee') }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-lg badge-round bg-success">{{ trans('order::order.discount') }}</span>
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

                    <!-- Summary Section -->
                    <div class="row mb-40">
                        <!-- Location Card -->
                        @if ($order->country || $order->city || $order->region)
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-20 d-flex align-items-center">
                                            <i class="uil uil-map-pin me-2" style="color: #5f63f2; font-size: 20px;"></i>
                                            {{ trans('order::order.location') }}
                                        </h6>
                                        <div class="location-details">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Order Summary Card -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100"
                                style="background: linear-gradient(135deg, #5f63f2 0%, #8e92f7 100%); color: white;">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-20 d-flex align-items-center text-white">
                                        <i class="uil uil-receipt me-2" style="font-size: 20px;"></i>
                                        {{ trans('order::order.order_summary') }}
                                    </h6>
                                    <div class="summary-details">
                                        <div class="summary-row mb-12">
                                            <span class="fw-bold">{{ trans('order::order.subtotal') }}</span>
                                            <span class="fw-bold">{{ number_format($order->total_product_price, 2) }}
                                                {{ currency() }}</span>
                                        </div>
                                        @if ($order->total_discounts > 0)
                                            <div class="summary-row mb-12">
                                                <span class="fw-bold">{{ trans('order::order.discounts') }}</span>
                                                <span class="fw-bold">-{{ number_format($order->total_discounts, 2) }}
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
                                            <span class="fw-bold">{{ trans('order::order.tax') }}</span>
                                            <span class="fw-bold">+{{ number_format($order->total_tax, 2) }}
                                                {{ currency() }}</span>
                                        </div>
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

                    <style>
                        .summary-row {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 8px 0;
                            font-size: 14px;
                        }
                    </style>
                </div>

                <!-- Action Buttons Section (Not Printable) -->
                <div class="mt-30 mb-40 no-print">
                    <div class="bg-white p-20 radius-xl global-shadow border-light-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-500">{{ trans('order::order.actions') }}</h6>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back') }}
                                </a>
                                @php
                                    $finalStages = ['deliver', 'cancel', 'refund'];
                                    $isFinalStage = in_array($order->stage?->slug, $finalStages);
                                @endphp
                                @if (!$isFinalStage)
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#changeStageModal">
                                        <i
                                            class="uil uil-check-circle me-2"></i>{{ trans('order::order.change_order_stage') }}
                                    </button>
                                @endif
                                <button class="btn btn-info btn-sm" onclick="printInvoice()">
                                    <i class="uil uil-print me-2"></i>{{ trans('order::order.print_invoice') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Change Stage Modal Component -->
    <x-order::change-stage-modal :order-id="$order->id" :current-stage-id="$order->stage_id" />

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
@endsection
