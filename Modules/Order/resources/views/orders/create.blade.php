@extends('layout.app')
@section('title', trans('order::order.create_order'))
@section('content')
    <div style="padding: 20px; display: flex; flex-direction: column;">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order::order.order_management'), 'url' => route('admin.orders.index')],
                    ['title' => trans('order::order.create_order')],
                ]" />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 20px;">
            <div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ trans('order::order.create_order') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <form id="createOrderForm" action="{{ route('admin.orders.store') }}" method="POST">
                            @csrf

                            {{-- Customer Selection Section --}}
                            <div class="mb-30">
                                <h6 class="fw-500 mb-20">
                                    <i class="uil uil-user me-2"></i>{{ trans('order::order.customer_information') }}
                                </h6>

                                {{-- Customer Type Selection --}}
                                <div class="row mb-20">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order::order.customer_type') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="customer_type"
                                                    id="existing_customer" value="existing" checked>
                                                <label class="btn btn-outline-primary" for="existing_customer">
                                                    <i
                                                        class="uil uil-database me-1"></i>{{ trans('order::order.existing_customer') }}
                                                </label>

                                                <input type="radio" class="btn-check" name="customer_type"
                                                    id="external_customer" value="external">
                                                <label class="btn btn-outline-primary" for="external_customer">
                                                    <i
                                                        class="uil uil-user-plus me-1"></i>{{ trans('order::order.external_customer') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Existing Customer Section --}}
                                <div id="existing_customer_section">
                                    <div class="row">
                                        <div class="col-md-12 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.select_customer') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="text"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                        id="customer_search"
                                                        placeholder="{{ __('common.search') }} {{ trans('order::order.customer_name') }}..."
                                                        autocomplete="off">
                                                    <div class="position-absolute w-100 bg-white border rounded-bottom shadow-sm"
                                                        id="customer_suggestions"
                                                        style="display: none; top: 100%; left: 0; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                                    </div>
                                                </div>
                                                <input type="hidden" id="selected_customer_id" value="">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Customer Address Selection --}}
                                    <div class="row" id="customer_address_section" style="display: none;">
                                        <div class="col-md-12 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_address') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                    id="customer_address_select" name="customer_address_id">
                                                    <option value="">{{ trans('order::order.select_address') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Auto-filled Customer Details --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_email') }}
                                                </label>
                                                <input type="email"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="customer_email" name="customer_email"
                                                    placeholder="{{ trans('order::order.customer_email') }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_phone') }}
                                                </label>
                                                <input type="tel"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="customer_phone" name="customer_phone"
                                                    placeholder="{{ trans('order::order.customer_phone') }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_address') }}
                                                </label>
                                                <input type="text"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="customer_address" name="customer_address"
                                                    placeholder="{{ trans('order::order.customer_address') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- External Customer Section --}}
                                <div id="external_customer_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_name') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="external_customer_name" name="external_customer_name"
                                                    placeholder="{{ trans('order::order.customer_name') }}">
                                                @error('external_customer_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_email') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="email"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="external_customer_email" name="external_customer_email"
                                                    placeholder="{{ trans('order::order.customer_email') }}">
                                                @error('external_customer_email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_phone') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="tel"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="external_customer_phone" name="external_customer_phone"
                                                    placeholder="{{ trans('order::order.customer_phone') }}">
                                                @error('external_customer_phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-25">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                    {{ trans('order::order.customer_address') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="external_customer_address" name="external_customer_address"
                                                    placeholder="{{ trans('order::order.customer_address') }}">
                                                @error('external_customer_address')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Order Details Section --}}
                            <div class="mb-30">
                                <h6 class="fw-500 mb-20">
                                    <i class="uil uil-receipt me-2"></i>{{ trans('order::order.order_details') }}
                                </h6>

                                <div class="row">
                                    {{-- Payment Type --}}
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order::order.payment_type') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="payment_type" name="payment_type" required>
                                                <option value="">{{ __('common.select') }}</option>
                                                <option value="cash_on_delivery">
                                                    {{ trans('order::order.cash_on_delivery') }}</option>
                                                <option value="online_payment">{{ trans('order::order.online_payment') }}
                                                </option>
                                            </select>
                                            @error('payment_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Shipping Cost --}}
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order::order.shipping') }}
                                            </label>
                                            <input type="number"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="shipping" name="shipping" placeholder="0.00" step="0.01"
                                                min="0" value="0">
                                            @error('shipping')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Products Section --}}
                            <div class="mb-30">
                                <h6 class="fw-500 mb-20">
                                    <i class="uil uil-shopping-bag me-2"></i>{{ trans('order::order.add_product') }}
                                </h6>

                                <div class="row mb-20">
                                    <div class="col-md-8 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order::order.add_product') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <input type="text"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="product_search"
                                                    placeholder="{{ __('common.search') }} {{ trans('order::order.product_name') }}..."
                                                    autocomplete="off">
                                                <div class="position-absolute w-100 bg-white border rounded-bottom shadow-sm"
                                                    id="product_suggestions"
                                                    style="display: none; top: 100%; left: 0; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order::order.items_count') }}
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="product_quantity" name="product_quantity" placeholder="1"
                                                    min="1" value="1">
                                                <button type="button" class="btn btn-primary" id="addProductBtn"
                                                    disabled>
                                                    <i
                                                        class="uil uil-plus me-1"></i>{{ trans('order::order.add_product') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden input for selected product --}}
                                <input type="hidden" id="selected_product_id" value="">
                                <input type="hidden" id="selected_product_name" value="">
                                <input type="hidden" id="selected_product_price" value="" {{-- Products List --}}
                                    <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="productsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ trans('order::order.product_name') }}</th>
                                            <th class="text-center">{{ trans('order::order.price') }}</th>
                                            <th class="text-center">{{ trans('order::order.items_count') }}</th>
                                            <th class="text-center">{{ trans('order::order.total') }}</th>
                                            <th class="text-center">{{ __('common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsTableBody">
                                    </tbody>
                                </table>
                            </div>

                            {{-- Hidden input for products --}}
                            <input type="hidden" id="productsData" name="products" value="[]">
                    </div>

                    {{-- Form Actions --}}
                    <div class="row mt-30">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2" style="margin: 15px;">
                                <a href="{{ route('admin.orders.index') }}"
                                    class="btn btn-light btn-default btn-squared">
                                    <i class="uil uil-arrow-left me-1"></i>
                                    {{ trans('main.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary btn-squared" id="submitBtn">
                                    <i class="uil uil-check me-1"></i>
                                    {{ trans('order::order.create_order') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="card border-0 shadow-sm" style="position: sticky; top: 20px; height: fit-content;">
                <div class="card-header bg-white border-bottom py-20">
                    <h5 class="mb-0 fw-500">
                        {{ trans('order::order.order_summary') }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Subtotal --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-receipt text-warning me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order::order.subtotal') }}</span>
                        </div>
                        <span class="fw-500" id="subtotal">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Shipping Cost --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-truck text-info me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order::order.shipping') }}</span>
                        </div>
                        <span class="fw-500" id="shippingDisplay">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Additional Fees Section --}}
                    <div class="mb-15 pb-15 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-10">
                            <div class="d-flex align-items-center">
                                <i class="uil uil-plus-circle text-success me-2" style="font-size: 18px;"></i>
                                <span class="fw-500">{{ trans('order::order.add_fee') }}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="addFeeBtn">
                                <i class="uil uil-plus me-1"></i>{{ trans('order::order.add_fee') }}
                            </button>
                        </div>
                        <div id="feesContainer"></div>
                        <span class="fw-500" id="totalFeesDisplay">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Total Tax --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-chart-pie text-info me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order::order.tax') }}</span>
                        </div>
                        <span class="fw-500" id="totalTax">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Additional Discounts Section --}}
                    <div class="mb-15 pb-15 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-10">
                            <div class="d-flex align-items-center">
                                <i class="uil uil-gift text-danger me-2" style="font-size: 18px;"></i>
                                <span class="fw-500">{{ trans('order::order.add_discount') }}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-warning" id="addDiscountBtn">
                                <i class="uil uil-plus me-1"></i>{{ trans('order::order.add_discount') }}
                            </button>
                        </div>
                        <div id="discountsContainer"></div>
                        <span class="fw-500" id="totalDiscountsDisplay">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Grand Total --}}
                    <div class="d-flex justify-content-between align-items-center pt-15">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-receipt text-primary me-2" style="font-size: 18px;"></i>
                            <span class="fw-500 fs-16">{{ trans('order::order.total') }}</span>
                        </div>
                        <span class="fw-bold fs-16 text-primary" id="grandTotal">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Hidden inputs for fees and discounts --}}
                    <input type="hidden" id="feesData" name="fees" value="[]">
                    <input type="hidden" id="discountsData" name="discounts" value="[]">
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let feeCounter = 0;
                let discountCounter = 0;
                let fees = [];
                let discounts = [];
                let products = [];
                let productCounter = 0;
                let allProducts = [];
                let allCustomers = [];

                // Load all products
                function loadAllProducts() {
                    $.ajax({
                        url: '/api/products', // Products API endpoint
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.data) {
                                allProducts = response.data;
                                console.log('Products loaded:', allProducts.length);
                                console.log('Sample product:', allProducts[0]);
                            } else if (response && Array.isArray(response)) {
                                allProducts = response;
                                console.log('Products loaded:', allProducts.length);
                            }
                        },
                        error: function(xhr) {
                            console.log('Failed to load products', xhr);
                            showAlert('danger', 'Failed to load products');
                        }
                    });
                }

                // Load all customers
                function loadAllCustomers() {
                    $.ajax({
                        url: '/api/customers', // Customers API endpoint
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.data) {
                                allCustomers = response.data;
                                console.log('Customers loaded:', allCustomers.length);
                            } else if (response && Array.isArray(response)) {
                                allCustomers = response;
                                console.log('Customers loaded:', allCustomers.length);
                            }
                        },
                        error: function(xhr) {
                            console.log('Failed to load customers', xhr);
                            showAlert('danger', 'Failed to load customers');
                        }
                    });
                }

                // Live product search
                let searchTimeout;
                $('#product_search').on('keyup', function() {
                    clearTimeout(searchTimeout);
                    const searchTerm = $(this).val().toLowerCase();
                    const suggestions = $('#product_suggestions');

                    if (searchTerm.length < 1) {
                        suggestions.hide();
                        return;
                    }

                    searchTimeout = setTimeout(function() {
                        // Filter products by name or SKU
                        const filteredProducts = allProducts.filter(product => {
                            const name = (product.name || product.title || '').toLowerCase();
                            const sku = (product.sku || '').toLowerCase();
                            return name.includes(searchTerm) || sku.includes(searchTerm);
                        }).slice(0, 10);

                        if (filteredProducts.length > 0) {
                            let html = '';

                            // For each product, show all its variants
                            filteredProducts.forEach(product => {
                                const productName = product.name || product.title || 'N/A';

                                if (product.variants && product.variants.length > 0) {
                                    product.variants.forEach(variant => {
                                        const price = parseFloat(variant.real_price) ||
                                            0;
                                        const variantName = variant.variant_name ||
                                            'Default';
                                        const variantSku = variant.sku || product.sku ||
                                            'N/A';

                                        html += `
                                        <div class="p-2 border-bottom cursor-pointer product-suggestion"
                                             data-id="${variant.id}"
                                             data-product-id="${product.id}"
                                             data-name="${productName} - ${variantName}"
                                             data-price="${price}"
                                             style="cursor: pointer;">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-500">${productName}</span>
                                                <span class="text-muted">${price.toFixed(2)} {{ __('common.currency') }}</span>
                                            </div>
                                            <small class="text-muted">${variantName} (SKU: ${variantSku})</small>
                                        </div>
                                    `;
                                    });
                                } else {
                                    // Fallback if no variants
                                    html += `
                                    <div class="p-2 border-bottom cursor-pointer product-suggestion"
                                         data-id="${product.id}"
                                         data-product-id="${product.id}"
                                         data-name="${productName}"
                                         data-price="0"
                                         style="cursor: pointer;">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-500">${productName}</span>
                                            <span class="text-muted">No variants</span>
                                        </div>
                                        <small class="text-muted">SKU: ${product.sku || 'N/A'}</small>
                                    </div>
                                `;
                                }
                            });
                            suggestions.html(html).show();
                        } else {
                            suggestions.html(
                                '<div class="p-2 text-muted">{{ trans('order::order.no_products_found') }}</div>'
                                ).show();
                        }
                    }, 300);
                });

                // Product suggestion click
                $(document).on('click', '.product-suggestion', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const price = $(this).data('price');

                    console.log('Product selected:', {
                        id,
                        name,
                        price
                    });

                    $('#product_search').val(name);
                    $('#selected_product_id').val(id);
                    $('#selected_product_name').val(name);
                    $('#selected_product_price').val(price);
                    $('#product_suggestions').hide();
                    $('#addProductBtn').prop('disabled', false);

                    console.log('Hidden fields set:', {
                        id: $('#selected_product_id').val(),
                        name: $('#selected_product_name').val(),
                        price: $('#selected_product_price').val()
                    });
                });

                // Hide suggestions on blur
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#product_search, #product_suggestions').length) {
                        $('#product_suggestions').hide();
                    }
                });

                // Customer type toggle
                $('input[name="customer_type"]').on('change', function() {
                    if ($(this).val() === 'existing') {
                        $('#existing_customer_section').show();
                        $('#external_customer_section').hide();
                    } else {
                        $('#existing_customer_section').hide();
                        $('#external_customer_section').show();
                    }
                });

                // Live customer search
                let customerSearchTimeout;
                $('#customer_search').on('keyup', function() {
                    clearTimeout(customerSearchTimeout);
                    const searchTerm = $(this).val().toLowerCase();
                    const suggestions = $('#customer_suggestions');

                    if (searchTerm.length < 1) {
                        suggestions.hide();
                        return;
                    }

                    customerSearchTimeout = setTimeout(function() {
                        // Check if customers are loaded
                        if (!allCustomers || allCustomers.length === 0) {
                            console.log('Customers not loaded yet, loading...');
                            suggestions.html(
                                '<div class="p-2 text-muted">{{ trans('order::order.loading_customers') }}</div>'
                                ).show();
                            return;
                        }

                        const filtered = allCustomers.filter(customer => {
                            const name = (customer.full_name || '').toLowerCase();
                            const email = (customer.email || '').toLowerCase();
                            const phone = (customer.phone || '').toString();

                            return name.includes(searchTerm) ||
                                email.includes(searchTerm) ||
                                phone.includes(searchTerm);
                        }).slice(0, 10);

                        if (filtered.length > 0) {
                            let html = '';
                            filtered.forEach(customer => {
                                html += `
                                <div class="p-2 border-bottom cursor-pointer customer-suggestion"
                                     data-id="${customer.id}"
                                     data-name="${customer.full_name || ''}"
                                     data-email="${customer.email || ''}"
                                     data-phone="${customer.phone || ''}"
                                     style="cursor: pointer;">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-500">${customer.full_name || 'N/A'}</span>
                                        <small class="text-muted">${customer.email || 'N/A'}</small>
                                    </div>
                                    <small class="text-muted">${customer.phone || 'N/A'}</small>
                                </div>
                            `;
                            });
                            suggestions.html(html).show();
                        } else {
                            suggestions.html(
                                '<div class="p-2 text-muted">{{ trans('order::order.no_customers_found') }}</div>'
                                ).show();
                        }
                    }, 300);
                });

                // Customer suggestion click
                $(document).on('click', '.customer-suggestion', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const email = $(this).data('email');
                    const phone = $(this).data('phone');

                    $('#customer_search').val(name);
                    $('#selected_customer_id').val(id);
                    $('#customer_suggestions').hide();

                    // Load customer addresses
                    loadCustomerAddresses(id, email, phone);
                });

                // Load customer addresses
                function loadCustomerAddresses(customerId, email, phone) {
                    $.ajax({
                        url: `/api/customers/${customerId}/addresses`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            const addressSelect = $('#customer_address_select');
                            addressSelect.empty();
                            addressSelect.append(
                                '<option value="">{{ trans('order::order.select_address') }}</option>');

                            if (response.data && response.data.length > 0) {
                                response.data.forEach(address => {
                                    addressSelect.append(
                                        `<option value="${address.id}" data-address="${address.address}">${address.title} - ${address.address}</option>`
                                        );
                                });
                                $('#customer_address_section').show();
                            } else {
                                $('#customer_address_section').hide();
                            }

                            $('#customer_email').val(email);
                            $('#customer_phone').val(phone);
                            $('#customer_address').val('');
                        }
                    });
                }

                // Customer address select change
                $('#customer_address_select').on('change', function() {
                    const address = $(this).find('option:selected').data('address');
                    $('#customer_address').val(address);
                });

                // Hide customer suggestions on blur
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#customer_search, #customer_suggestions').length) {
                        $('#customer_suggestions').hide();
                    }
                });

                // Alert function
                function showAlert(type, message) {
                    const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="uil uil-info-circle me-2"></i>${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                    $('#alertContainer').html(alertHtml);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'slow');
                }

                loadAllProducts();
                loadAllCustomers();

                // Add Fee
                $('#addFeeBtn').on('click', function() {
                    const feeId = `fee_${feeCounter++}`;
                    const feeHtml = `
                    <div class="fee-item mb-10 p-10 rounded" data-fee-id="${feeId}">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control fee-reason" placeholder="{{ trans('order::order.reason') }}" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control fee-amount" placeholder="0.00" step="0.01" min="0" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-fee w-100">
                                    <i class="uil uil-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                    $('#feesContainer').append(feeHtml);
                    updateSummary();
                });

                // Add Discount
                $('#addDiscountBtn').on('click', function() {
                    const discountId = `discount_${discountCounter++}`;
                    const discountHtml = `
                    <div class="discount-item mb-10 p-10 rounded" data-discount-id="${discountId}">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control discount-reason" placeholder="{{ trans('order::order.reason') }}" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control discount-amount" placeholder="0.00" step="0.01" min="0" required style="background-color: transparent; border: 1px solid #ddd;">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-discount w-100">
                                    <i class="uil uil-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                    $('#discountsContainer').append(discountHtml);
                    updateSummary();
                });

                // Remove Fee
                $(document).on('click', '.remove-fee', function() {
                    $(this).closest('.fee-item').remove();
                    updateSummary();
                });

                // Remove Discount
                $(document).on('click', '.remove-discount', function() {
                    $(this).closest('.discount-item').remove();
                    updateSummary();
                });

                // Add Product
                $('#addProductBtn').on('click', function() {
                    const productId = $('#selected_product_id').val();
                    const productName = $('#selected_product_name').val();
                    const priceValue = $('#selected_product_price').val();
                    const productPrice = parseFloat(priceValue) || 0;
                    const quantity = parseInt($('#product_quantity').val()) || 1;

                    console.log('Adding product:', {
                        productId,
                        productName,
                        priceValue,
                        productPrice,
                        quantity
                    });

                    if (!productId) {
                        showAlert('warning', '{{ trans('order::order.please_select_product') }}');
                        return;
                    }

                    const productTotal = productPrice * quantity;

                    // Check if product already exists
                    const existingProduct = products.find(p => p.id == productId);
                    if (existingProduct) {
                        existingProduct.quantity += quantity;
                        existingProduct.total = existingProduct.price * existingProduct.quantity;
                    } else {
                        products.push({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            quantity: quantity,
                            total: productTotal
                        });
                    }

                    renderProductsTable();
                    $('#product_search').val('');
                    $('#selected_product_id').val('');
                    $('#selected_product_name').val('');
                    $('#selected_product_price').val('');
                    $('#product_quantity').val(1);
                    $('#addProductBtn').prop('disabled', true);
                    updateSummary();
                });

                // Remove Product
                $(document).on('click', '.remove-product', function() {
                    const productId = $(this).data('product-id');
                    products = products.filter(p => p.id != productId);
                    renderProductsTable();
                    updateSummary();
                });

                // Render Products Table
                function renderProductsTable() {
                    const tbody = $('#productsTableBody');
                    tbody.empty();

                    products.forEach(product => {
                        const row = `
                        <tr>
                            <td>${product.name}</td>
                            <td class="text-center">${product.price.toFixed(2)} {{ __('common.currency') }}</td>
                            <td class="text-center">${product.quantity}</td>
                            <td class="text-center">${product.total.toFixed(2)} {{ __('common.currency') }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger remove-product" data-product-id="${product.id}">
                                    <i class="uil uil-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                        tbody.append(row);
                    });

                    // Update hidden input
                    $('#productsData').val(JSON.stringify(products));
                }

                // Update Summary
                function updateSummary() {
                    let totalFees = 0;
                    let totalDiscounts = 0;
                    let subtotal = 0;

                    // Calculate subtotal from products
                    products.forEach(product => {
                        subtotal += product.total;
                    });

                    $('.fee-item').each(function() {
                        const amount = parseFloat($(this).find('.fee-amount').val()) || 0;
                        totalFees += amount;
                    });

                    $('.discount-item').each(function() {
                        const amount = parseFloat($(this).find('.discount-amount').val()) || 0;
                        totalDiscounts += amount;
                    });

                    const shipping = parseFloat($('#shipping').val()) || 0;
                    const tax = 0; // Will be calculated based on products
                    const grandTotal = subtotal + shipping + totalFees + tax - totalDiscounts;

                    $('#subtotal').text(subtotal.toFixed(2) + ' {{ __('common.currency') }}');
                    $('#shippingDisplay').text(shipping.toFixed(2) + ' {{ __('common.currency') }}');
                    $('#totalFeesDisplay').text(totalFees.toFixed(2) + ' {{ __('common.currency') }}');
                    $('#totalDiscountsDisplay').text(totalDiscounts.toFixed(2) + ' {{ __('common.currency') }}');
                    $('#grandTotal').text(grandTotal.toFixed(2) + ' {{ __('common.currency') }}');

                    // Update hidden inputs
                    fees = [];
                    discounts = [];

                    $('.fee-item').each(function() {
                        fees.push({
                            reason: $(this).find('.fee-reason').val(),
                            amount: parseFloat($(this).find('.fee-amount').val()) || 0
                        });
                    });

                    $('.discount-item').each(function() {
                        discounts.push({
                            reason: $(this).find('.discount-reason').val(),
                            amount: parseFloat($(this).find('.discount-amount').val()) || 0
                        });
                    });

                    $('#feesData').val(JSON.stringify(fees));
                    $('#discountsData').val(JSON.stringify(discounts));
                }

                // Update summary on input change
                $(document).on('change keyup',
                    '.fee-reason, .fee-amount, .discount-reason, .discount-amount, #shipping',
                    function() {
                        updateSummary();
                    });

                // Form submission
                $('#createOrderForm').on('submit', function(e) {
                    e.preventDefault();

                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.show({
                            text: '{{ trans('order::order.create_order') }}',
                            subtext: '{{ trans('main.please wait') }}'
                        });
                    }

                    const formData = new FormData(this);
                    const url = $(this).attr('action');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                if (typeof LoadingOverlay !== 'undefined') {
                                    LoadingOverlay.showSuccess(
                                        response.message,
                                        '{{ trans('main.redirecting') }}'
                                    );
                                }

                                setTimeout(function() {
                                    window.location.href =
                                        '{{ route('admin.orders.index') }}';
                                }, 1500);
                            } else {
                                if (typeof LoadingOverlay !== 'undefined') {
                                    LoadingOverlay.hide();
                                }
                                showAlert('danger', response.message);
                            }
                        },
                        error: function(xhr) {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                let errorHtml = '<ul class="mb-0">';
                                $.each(errors, function(key, value) {
                                    errorHtml += '<li>' + value[0] + '</li>';
                                });
                                errorHtml += '</ul>';
                                showAlert('danger', errorHtml);
                            } else {
                                const message = xhr.responseJSON?.message ||
                                    '{{ trans('order::order.error_creating_order') }}';
                                showAlert('danger', message);
                            }
                        }
                    });
                });

                // Alert function
                function showAlert(type, message) {
                    const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                    $('#alertContainer').html(alertHtml);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'slow');
                }
            });
        </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component --}}
@push('after-body')
    <x-loading-overlay :loadingText="trans('order::order.create_order')" :loadingSubtext="trans('main.please wait')" />
@endpush
