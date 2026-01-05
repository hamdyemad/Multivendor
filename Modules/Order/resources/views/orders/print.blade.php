<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bnaia - Invoice #{{ $order->order_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #1a1a2e;
            min-height: 100vh;
            padding: 20px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        
        /* Header */
        .invoice-title-section {
            text-align: center;
            padding: 20px 30px 10px;
        }
        
        .invoice-order-info {
            margin-top: 10px;
        }
        
        .invoice-order-number {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        
        .invoice-order-date {
            font-size: 12px;
            color: #666;
        }
        
        .invoice-header {
            padding: 10px 30px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .logo-section img {
            height: 40px;
        }
        
        .invoice-badge {
            background: linear-gradient(to right, #0056B7, #cb1037);
            padding: 8px 20px;
            border-radius: 50px;
        }
        
        .invoice-badge h1 {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        /* Content */
        .invoice-content {
            padding: 25px 30px;
        }
        
        /* Combined Info Box */
        .info-box {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(102, 126, 234, 0.1);
            margin-bottom: 20px;
        }
        
        .info-box-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        
        .info-section {
            border-right: 1px dashed rgba(102, 126, 234, 0.2);
            padding-right: 20px;
        }
        
        .info-section:last-child {
            border-right: none;
            padding-right: 0;
        }
        
        .info-section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.15);
        }
        
        .info-icon {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        
        .info-icon svg {
            width: 14px;
            height: 14px;
        }
        
        .info-section-title {
            font-size: 10px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.05);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-size: 11px;
            color: #666;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 11px;
            color: #1a1a2e;
            font-weight: 600;
            text-align: right;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        
        /* Products Table - Compact */
        .products-section {
            margin: 15px 0;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.3) 0%, transparent 100%);
        }
        
        .products-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            table-layout: fixed;
        }
        
        .products-table thead {
            background: linear-gradient(to right, #0056B7, #cb1037);
        }
        
        .products-table th {
            padding: 10px 12px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .products-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 11px;
        }
        
        .products-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .product-name {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        .product-sku {
            display: inline-block;
            background: rgba(102, 126, 234, 0.1);
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
            color: #667eea;
            font-weight: 500;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Summary - Compact */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.06);
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            padding: 2px;
            font-size: 11px;
            color: #666;
        }
        
        .summary-value {
            padding: 2px;
            font-size: 11px;
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .summary-row.total {
            margin-top: 8px;
            padding-top: 10px;
            border-top: 2px solid #667eea;
            border-bottom: none;
        }
        
        .summary-row.total .summary-label {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
        }
        
        .summary-row.total .summary-value {
            font-size: 15px;
            font-weight: 700;
            color: #667eea;
        }
        
        .discount-value {
            color: #e74c3c;
        }
        
        /* Footer */
        .invoice-footer {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            padding: 15px 30px;
            text-align: center;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .footer-text {
            font-size: 12px;
            color: #666;
        }
        
        /* Action Buttons */
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .action-btn {
            padding: 12px 20px;
            color: #fff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        
        .action-btn svg {
            width: 16px;
            height: 16px;
        }
        
        .print-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .download-btn {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .download-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Print Styles */
        @media print {
            @page {
                margin: 10mm;
                size: A4;
            }
            
            body {
                background: #fff !important;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                border-radius: 0;
                background: #fff !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            /* Remove gradient backgrounds for print */
            .invoice-badge {
                background: none !important;
                border: 2px solid #0056B7 !important;
            }
            
            .invoice-badge h1 {
                color: #0056B7 !important;
            }
            
            .products-table thead {
                background: none !important;
                border-bottom: 2px solid #0056B7 !important;
            }
            
            .products-table th {
                color: #0056B7 !important;
                border-bottom: 2px solid #0056B7 !important;
            }
            
            .info-box {
                background: none !important;
                border: 1px solid #ddd !important;
            }
            
            .info-icon {
                background: none !important;
                border: 1px solid #0056B7 !important;
                color: #0056B7 !important;
            }
            
            .info-icon svg {
                fill: #0056B7 !important;
            }
            
            .status-badge {
                background: none !important;
                border: 1px solid #0056B7 !important;
                color: #0056B7 !important;
            }
            
            .summary-card {
                background: none !important;
                border: 1px solid #ddd !important;
            }
            
            .summary-row.total .summary-value {
                color: #0056B7 !important;
                -webkit-text-fill-color: #0056B7 !important;
                background: none !important;
            }
            
            .invoice-footer {
                background: none !important;
            }
        }
        
        /* Prevent page breaks */
        .summary-card, .summary-section, .invoice-footer, .info-box {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        /* RTL */
        [dir="rtl"] .info-value { text-align: left; }
        [dir="rtl"] .text-right { text-align: left; }
        [dir="rtl"] .info-section { border-right: none; border-left: 1px dashed rgba(102, 126, 234, 0.2); padding-right: 0; padding-left: 20px; }
        [dir="rtl"] .info-section:last-child { border-left: none; padding-left: 0; }
        [dir="rtl"] .summary-section { justify-content: flex-start; }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button class="action-btn print-btn" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            {{ trans('common.print') }}
        </button>
        <button class="action-btn download-btn" onclick="downloadPDF()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Download PDF
        </button>
    </div>

    <div class="invoice-container">
        <!-- Invoice Title -->
        <div class="invoice-title-section">
            <div class="invoice-badge">
                <h1>Invoice</h1>
            </div>
            <div class="invoice-order-info">
                <div class="invoice-order-number">Bnaia - Invoice #{{ $order->order_number }}</div>
                <div class="invoice-order-date">{{ $order->created_at }}</div>
            </div>
        </div>
        
        <!-- Header -->
        <div class="invoice-header">
            <div class="logo-section">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Bnaia">
            </div>
        </div>

        <!-- Content -->
        <div class="invoice-content">
            <!-- Combined Info Box -->
            <div class="info-box">
                <div class="info-box-grid">
                    <!-- Order Details -->
                    <div class="info-section">
                        <div class="info-section-header">
                            <div class="info-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                                </svg>
                            </div>
                            <span class="info-section-title">Order Details</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Invoice No.</span>
                            <span class="info-value">#{{ $order->order_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date</span>
                            <span class="info-value">{{ $order->created_at }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="status-badge">{{ $order->stage?->name ?? 'N/A' }}</span>
                        </div>
                        @if(!isset($isVendorUser) || !$isVendorUser)
                            @if($order->customer_promo_code_title)
                                <div class="info-row">
                                    <span class="info-label">Promo Code</span>
                                    <span class="info-value" style="color: #667eea;">{{ $order->customer_promo_code_title }}</span>
                                </div>
                            @endif
                            @if($order->points_used > 0)
                                <div class="info-row">
                                    <span class="info-label">Points Used</span>
                                    <span class="info-value" style="color: #667eea;">{{ number_format($order->points_used, 0) }} pts</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Customer -->
                    <div class="info-section">
                        <div class="info-section-header">
                            <div class="info-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                </svg>
                            </div>
                            <span class="info-section-title">Customer</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Name</span>
                            <span class="info-value">{{ $order->customer_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone</span>
                            <span class="info-value">{{ $order->customer_phone }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value" style="font-size: 10px;">{{ $order->customer_email ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="info-section">
                        <div class="info-section-header">
                            <div class="info-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                </svg>
                            </div>
                            <span class="info-section-title">Shipping</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Address</span>
                            <span class="info-value" style="font-size: 10px;">{{ Str::limit($order->customer_address ?? '-', 20) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">City</span>
                            <span class="info-value">{{ $order->city?->name ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Region</span>
                            <span class="info-value">{{ $order->region?->name ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Country</span>
                            <span class="info-value">{{ $order->country?->name ?? 'Egypt' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="products-section">
                <div class="section-title">Order Items</div>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">Product</th>
                            <th class="text-center" style="width: 12%;">Price</th>
                            <th class="text-center" style="width: 15%;">Taxes</th>
                            <th class="text-center" style="width: 13%;">Price Inc. Tax</th>
                            <th class="text-center" style="width: 8%;">Qty</th>
                            <th class="text-right" style="width: 12%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Use vendor-filtered products if available, otherwise use all products
                            $displayProducts = isset($vendorProducts) && $vendorProducts !== null ? $vendorProducts : $order->products;
                        @endphp
                        @foreach($displayProducts as $key => $product)
                            @php
                                $productTotalWithTax = $product->price;
                                $taxAmount = $product->taxes->sum('amount') ?? 0;
                                $productTotalBeforeTax = $productTotalWithTax - $taxAmount;
                                $totalTaxPercentage = $product->taxes->sum('percentage') ?? 0;
                                
                                $unitPriceWithTax = $product->quantity > 0 ? $productTotalWithTax / $product->quantity : 0;
                                $unitPriceBeforeTax = $product->quantity > 0 ? $productTotalBeforeTax / $product->quantity : 0;
                                $unitTaxAmount = $product->quantity > 0 ? $taxAmount / $product->quantity : 0;
                                
                                $productName = $product->vendorProduct?->product?->getTranslation('name', app()->getLocale()) ?? ($product->name ?? '-');
                                $vendorName = $product->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()) ?? '-';
                                $productSku = $product->vendorProduct?->sku ?? '-';
                                $variantSku = $product->vendorProductVariant?->sku ?? null;
                                
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
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    <div class="product-name">{{ $productName }}</div>
                                    <div style="font-size: 9px; color: #667eea; margin-bottom: 2px;">{{ $vendorName }}</div>
                                    <span class="product-sku">{{ $variantSku ?? $productSku }}</span>
                                    @if($variantPath)
                                        <div style="font-size: 9px; color: #888; margin-top: 3px;">
                                            <span style="color: #0056B7;">▸</span> {{ $variantPath }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($unitPriceBeforeTax, 2) }} {{ currency() }}</td>
                                <td class="text-center">
                                    @if($product->taxes && $product->taxes->count() > 0)
                                        <div style="font-size: 10px;">{{ number_format($unitTaxAmount, 2) }} {{ currency() }}</div>
                                        <div style="margin-top: 2px;">
                                            @foreach($product->taxes as $tax)
                                                @php
                                                    $taxName = $tax->tax?->getTranslation('name', app()->getLocale()) ?? $tax->tax_title ?? '';
                                                @endphp
                                                <span style="display: inline-block; background: #e8e8e8; padding: 1px 4px; border-radius: 3px; font-size: 8px; color: #666; margin: 1px;">{{ $taxName }} {{ $tax->percentage }}%</span>
                                            @endforeach
                                        </div>
                                        <span style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1px 6px; border-radius: 3px; font-size: 8px; color: #fff; margin-top: 2px;">Total: {{ $totalTaxPercentage }}%</span>
                                    @else
                                        <span style="color: #ccc;">0.00 {{ currency() }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($unitPriceWithTax, 2) }} {{ currency() }}</td>
                                <td class="text-center">{{ $product->quantity }}</td>
                                <td class="text-right">{{ number_format($productTotalWithTax, 2) }} {{ currency() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="summary-section">
                <div class="summary-card">
                    @php
                        // Use vendor-filtered products if available, otherwise use all products
                        $productsForSummary = isset($vendorProducts) && $vendorProducts !== null ? $vendorProducts : $order->products;
                        
                        $totalWithTax = $productsForSummary->sum('price');
                        $totalTaxes = $productsForSummary->sum(function($product) {
                            return $product->taxes->sum('amount') ?? 0;
                        });
                        $subtotalBeforeTax = $totalWithTax - $totalTaxes;
                        
                        // Calculate shipping based on selected products only
                        $shippingCost = $productsForSummary->sum('shipping_cost');
                        
                        // Get promo_code_share and points_share from vendor_order_stages
                        $promoDiscount = 0;
                        $pointsDiscount = 0;
                        $vendorPromoShare = 0;
                        $vendorPointsShare = 0;
                        
                        if (isset($isVendorUser) && $isVendorUser) {
                            // For vendor: get their specific share from vendor_order_stages
                            $currentVendorId = auth()->user()->vendor?->id;
                            if ($currentVendorId) {
                                $vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                                    ->where('vendor_id', $currentVendorId)
                                    ->first();
                                $vendorPromoShare = $vendorStage->promo_code_share ?? 0;
                                $vendorPointsShare = $vendorStage->points_share ?? 0;
                            }
                            
                            // For vendor: products total + shipping - shares (deducted from total)
                            $finalTotal = ($vendorProductTotal ?? $totalWithTax) + $shippingCost - $vendorPromoShare - $vendorPointsShare;
                            $showPromoDiscount = true;
                            $showPointsDiscount = true;
                        } else {
                            // For admin: get total shares from all vendors
                            $vendorStages = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)->get();
                            $totalPromoCodeShare = $vendorStages->sum('promo_code_share') ?? 0;
                            $totalPointsShare = $vendorStages->sum('points_share') ?? 0;
                            
                            // Use order fields if available, otherwise use vendor_order_stages shares
                            $promoDiscount = ($order->customer_promo_code_amount ?? 0) > 0 
                                ? $order->customer_promo_code_amount 
                                : $totalPromoCodeShare;
                            $pointsDiscount = ($order->points_cost ?? 0) > 0 
                                ? $order->points_cost 
                                : $totalPointsShare;
                            
                            $finalTotal = $totalWithTax + $shippingCost - $promoDiscount - $pointsDiscount;
                            $showPromoDiscount = true;
                            $showPointsDiscount = true;
                        }
                    @endphp
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value">{{ number_format($subtotalBeforeTax, 2) }} {{ currency() }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Taxes</span>
                        <span class="summary-value">{{ number_format($totalTaxes, 2) }} {{ currency() }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Prices Inc. Tax</span>
                        <span class="summary-value">{{ number_format($totalWithTax, 2) }} {{ currency() }}</span>
                    </div>
                    @if($shippingCost > 0)
                        <div class="summary-row">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value">{{ number_format($shippingCost, 2) }} {{ currency() }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Total with Shipping</span>
                            <span class="summary-value">{{ number_format($totalWithTax + $shippingCost, 2) }} {{ currency() }}</span>
                        </div>
                    @endif
                    @if(isset($isVendorUser) && $isVendorUser)
                        {{-- Vendor view: show their promo_code_share and points_share as deductions --}}
                        @if($vendorPromoShare > 0)
                            <div class="summary-row">
                                <span class="summary-label">Promo Code Share</span>
                                <span class="summary-value discount-value">-{{ number_format($vendorPromoShare, 2) }} {{ currency() }}</span>
                            </div>
                        @endif
                        @if($vendorPointsShare > 0)
                            <div class="summary-row">
                                <span class="summary-label">Points Share</span>
                                <span class="summary-value discount-value">-{{ number_format($vendorPointsShare, 2) }} {{ currency() }}</span>
                            </div>
                        @endif
                    @else
                        {{-- Admin view: show discounts as subtractions --}}
                        @if($showPromoDiscount && $promoDiscount > 0)
                            <div class="summary-row">
                                <span class="summary-label">
                                    Promo Code
                                    @if($order->customer_promo_code_title)
                                        <span style="font-size: 9px; color: #667eea;">({{ $order->customer_promo_code_title }})</span>
                                    @endif
                                </span>
                                <span class="summary-value discount-value">-{{ number_format($promoDiscount, 2) }} {{ currency() }}</span>
                            </div>
                        @endif
                        @if($showPointsDiscount && $pointsDiscount > 0)
                            <div class="summary-row">
                                <span class="summary-label">
                                    Points Used
                                    @if($order->points_used > 0)
                                        <span style="font-size: 9px; color: #667eea;">({{ number_format($order->points_used, 0) }} pts)</span>
                                    @endif
                                </span>
                                <span class="summary-value discount-value">-{{ number_format($pointsDiscount, 2) }} {{ currency() }}</span>
                            </div>
                        @endif
                    @endif
                    <div class="summary-row total">
                        <span class="summary-label">Total</span>
                        <span class="summary-value">{{ number_format($finalTotal, 2) }} {{ currency() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <span class="footer-text">Thank you for your order!</span>
        </div>
    </div>

    <!-- html2pdf Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        function downloadPDF() {
            const btn = document.querySelector('.download-btn');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = `<svg style="animation: spin 1s linear infinite; width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Generating...`;
            
            const element = document.querySelector('.invoice-container');
            const orderNumber = '{{ $order->order_number }}';
            
            const opt = {
                margin: [5, 5, 10, 5],
                filename: `Bnaia-Invoice-${orderNumber}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true, 
                    allowTaint: true,
                    scrollY: 0,
                    windowHeight: element.scrollHeight
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
            };
            
            html2pdf().set(opt).from(element).save().then(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }).catch((error) => {
                console.error('PDF generation failed:', error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
    
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
