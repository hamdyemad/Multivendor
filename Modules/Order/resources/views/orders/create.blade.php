@extends('layout.app')
@section('title', trans('order.create_order'))
@section('content')
    <div style="padding: 20px;">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order.order_management'), 'url' => route('admin.orders.index')],
                    ['title' => trans('order.create_order')],
                ]" />
            </div>
        </div>

        <div class="responsive-grid">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-20">
                    <h5 class="mb-0 fw-500">
                        {{ trans('order.create_order') }}
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
                                <i class="uil uil-user me-2"></i>{{ trans('order.customer_information') }}
                            </h6>

                            {{-- Customer Type Selection --}}
                            <div class="row mb-20">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.customer_type') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="customer_type"
                                                id="existing_customer" value="existing" checked>
                                            <label class="btn btn-outline-primary" for="existing_customer">
                                                <i class="uil uil-database me-1"></i>{{ trans('order.existing_customer') }}
                                            </label>

                                            <input type="radio" class="btn-check" name="customer_type"
                                                id="external_customer" value="external">
                                            <label class="btn btn-outline-primary" for="external_customer">
                                                <i class="uil uil-user-plus me-1"></i>{{ trans('order.external_customer') }}
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
                                                {{ trans('order.select_customer') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <input type="text"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    id="customer_search"
                                                    placeholder="{{ __('common.search') }} {{ trans('order.customer_name') }}..."
                                                    autocomplete="off">
                                                <div class="position-absolute w-100 bg-white border rounded-bottom shadow-sm"
                                                    id="customer_suggestions"
                                                    style="display: none; top: 100%; left: 0; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                                </div>
                                            </div>
                                            <input type="hidden" id="selected_customer_id" name="selected_customer_id"
                                                value="">
                                        </div>
                                    </div>
                                </div>

                                {{-- Customer Address Selection --}}
                                <div class="row" id="customer_address_section" style="display: none;">
                                    <div class="col-md-12 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_address') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-2">
                                                <select
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                    id="customer_address_select" name="customer_address_id">
                                                    <option value="">{{ trans('order.select_address') }}
                                                    </option>
                                                </select>
                                                <button type="button" class="btn btn-primary" id="addNewAddressBtn"
                                                    title="Add new address">
                                                    <i class="uil uil-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No Address Message --}}
                                <div class="row" id="no_address_section" style="display: none;">
                                    <div class="col-md-12 mb-25">
                                        <div class="alert alert-info" role="alert">
                                            <i class="uil uil-info-circle me-2"></i>
                                            {{ trans('order.customer_has_no_address') }}
                                            <button type="button" class="btn btn-sm btn-primary ms-2"
                                                id="createAddressBtn">
                                                <i class="uil uil-plus me-1"></i>{{ trans('order.create_address') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Auto-filled Customer Details --}}
                                <div class="row">
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_email') }}
                                            </label>
                                            <input type="email"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="customer_email" name="customer_email"
                                                placeholder="{{ trans('order.customer_email') }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_phone') }}
                                            </label>
                                            <input type="tel"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="customer_phone" name="customer_phone"
                                                placeholder="{{ trans('order.customer_phone') }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_address') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="customer_address" name="customer_address"
                                                placeholder="{{ trans('order.customer_address') }}" readonly>
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
                                                {{ trans('order.customer_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_name" name="external_customer_name"
                                                placeholder="{{ trans('order.customer_name') }}">
                                            @error('external_customer_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_email') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_email" name="external_customer_email"
                                                placeholder="{{ trans('order.customer_email') }}">
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
                                                {{ trans('order.customer_phone') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_phone" name="external_customer_phone"
                                                placeholder="{{ trans('order.customer_phone') }}">
                                            @error('external_customer_phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ trans('order.customer_address') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="external_customer_address" name="external_customer_address"
                                                placeholder="{{ trans('order.customer_address') }}">
                                            @error('external_customer_address')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- Products Section --}}
                        <div class="mb-30">
                            <h6 class="fw-500 mb-20">
                                <i class="uil uil-shopping-bag me-2"></i>{{ trans('order.add_product') }}
                            </h6>

                            <div class="row mb-20">
                                <div class="col-md-8 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.add_product') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="position-relative">
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="product_search"
                                                placeholder="{{ __('common.search') }} {{ trans('order.product_name') }}..."
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
                                            {{ trans('order.items_count') }}
                                            <small class="text-muted" id="limitationText"></small>
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="product_quantity" name="product_quantity" placeholder="1"
                                                min="1" value="1">
                                            <button type="button" class="btn btn-primary" id="addProductBtn" disabled>
                                                <i class="uil uil-plus me-1"></i>{{ trans('order.add_product') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden input for selected product --}}
                            <input type="hidden" id="selected_product_id" value="">
                            <input type="hidden" id="selected_product_variant_id" value="">
                            <input type="hidden" id="selected_product_name" value="">
                            <input type="hidden" id="selected_product_price" value="">
                            <input type="hidden" id="selected_product_limitation" value="">
                            <input type="hidden" id="selected_product_tax_rate" value="">
                            <input type="hidden" id="selected_product_category_id" value="">
                            <input type="hidden" id="selected_product_category_name" value="">
                            {{-- Products List --}}
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="productsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ trans('order.product_name') }}</th>
                                            <th class="text-center">{{ trans('order.price') }}</th>
                                            <th class="text-center">{{ trans('order.items_count') }}</th>
                                            <th class="text-center">{{ trans('order.tax') }}</th>
                                            <th class="text-center">{{ trans('order.total') }}</th>
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


                        {{-- Hidden inputs for fees, discounts, and shipping --}}
                        <input type="hidden" id="feesData" name="feesData" value="[]">
                        <input type="hidden" id="discountsData" name="discountsData" value="[]">
                        <input type="hidden" id="shipping" name="shipping" value="0">
                    </form>
                </div>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="card border-0 shadow-sm" style="position: sticky; top: 20px; height: fit-content;">
                <div class="card-header bg-white border-bottom py-20">
                    <h5 class="mb-0 fw-500">
                        {{ trans('order.order_summary') }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Subtotal --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-receipt text-warning me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order.subtotal') }}</span>
                        </div>
                        <span class="fw-500" id="subtotal">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Shipping Cost --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-truck text-info me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order.shipping') }}</span>
                        </div>
                        <span class="fw-500" id="shippingDisplay">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Total Tax --}}
                    <div class="d-flex justify-content-between align-items-center mb-15 pb-15 border-bottom"
                        id="taxSection" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-chart-pie text-info me-2" style="font-size: 18px;"></i>
                            <span class="fw-500">{{ trans('order.tax') }}</span>
                        </div>
                        <span class="fw-500" id="totalTax">0.00 {{ __('common.currency') }}</span>
                    </div>

                    {{-- Additional Fees Section --}}
                    <div class="mb-15 pb-15 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-10">
                            <div class="d-flex align-items-center">
                                <i class="uil uil-plus-circle text-success me-2" style="font-size: 18px;"></i>
                                <span class="fw-500">{{ trans('order.add_fee') }}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="addFeeBtn">
                                <i class="uil uil-plus me-1"></i>{{ trans('order.add_fee') }}
                            </button>
                        </div>
                        <div id="feesContainer"></div>
                        <span class="fw-500" id="totalFeesDisplay">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Additional Discounts Section --}}
                    <div class="mb-15 pb-15 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-10">
                            <div class="d-flex align-items-center">
                                <i class="uil uil-gift text-danger me-2" style="font-size: 18px;"></i>
                                <span class="fw-500">{{ trans('order.add_discount') }}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-warning" id="addDiscountBtn">
                                <i class="uil uil-plus me-1"></i>{{ trans('order.add_discount') }}
                            </button>
                        </div>
                        <div id="discountsContainer"></div>
                        <span class="fw-500" id="totalDiscountsDisplay">0.00 {{ currency() }}</span>
                    </div>

                    {{-- Grand Total --}}
                    <div class="d-flex justify-content-between align-items-center pt-15 mb-20 pb-15 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="uil uil-receipt text-primary me-2" style="font-size: 18px;"></i>
                            <span class="fw-500 fs-16">{{ trans('order.total') }}</span>
                        </div>
                        <span class="fw-bold fs-16 text-primary" id="grandTotal">0.00
                            {{ __('common.currency') }}</span>
                    </div>

                    {{-- Form Actions --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-default btn-squared">
                            <i class="uil uil-arrow-left me-1"></i>
                            {{ trans('main.cancel') }}
                        </a>
                        <button type="submit" form="createOrderForm" class="btn btn-primary btn-squared"
                            id="submitBtn">
                            <i class="uil uil-check me-1"></i>
                            {{ trans('order.create_order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- Add Address Modal --}}
        <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAddressModalLabel">
                            <i class="uil uil-map-pin me-2"></i>{{ trans('order.add_new_address') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addAddressForm" novalidate>
                            <div id="addressFormErrors" class="alert alert-danger" style="display: none;"></div>

                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.address_title') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 address-required"
                                            id="address_title" name="address_title" placeholder="e.g., Home, Office"
                                            data-field="title">
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.country') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select address-required"
                                            id="address_country_id" name="address_country_id" data-field="country_id">
                                            <option value="">{{ __('common.select') }}</option>
                                        </select>
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.city') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select address-required"
                                            id="address_city_id" name="address_city_id" disabled data-field="city_id">
                                            <option value="">{{ __('common.select') }}</option>
                                        </select>
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.region') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select address-required"
                                            id="address_region_id" name="address_region_id" disabled
                                            data-field="region_id">
                                            <option value="">{{ __('common.select') }}</option>
                                        </select>
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.sub_region') }}
                                        </label>
                                        <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                            id="address_subregion_id" name="address_subregion_id" disabled
                                            data-field="subregion_id">
                                            <option value="">{{ __('common.select') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('order.customer_address') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 address-required"
                                            id="address_address" name="address_address" placeholder="Enter full address"
                                            data-field="address">
                                        <small class="text-danger d-none error-message"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="address_is_primary"
                                            name="address_is_primary">
                                        <label class="form-check-label" for="address_is_primary">
                                            {{ trans('order.set_as_primary') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-default" data-bs-dismiss="modal">
                            <i class="uil uil-times me-1"></i>{{ trans('main.cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="saveAddressBtn">
                            <i class="uil uil-check me-1"></i>{{ trans('main.create') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(document).ready(function() {
                    // Get country code from session
                    const countryCode = '{{ strtoupper(session('country_code', 'EG')) }}';

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
                            url: '/api/products/variants-all', // Products API endpoint
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode
                            },
                            success: function(response) {
                                if (response.data) {
                                    allProducts = response.data;
                                    console.log('Products loaded:', allProducts.length);
                                    console.log('Sample product:', allProducts[0]);
                                    console.log('Sample product full structure:', JSON.stringify(allProducts[0],
                                        null, 2));
                                } else if (response && Array.isArray(response)) {
                                    allProducts = response;
                                    console.log('Products loaded:', allProducts.length);
                                    console.log('Sample product:', allProducts[0]);
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
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode
                            },
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
                                    const limitation = product.limitation || 0;
                                    const taxRate = product.tax && product.tax.tax_rate ? product
                                        .tax.tax_rate : 0;
                                    const productStock = product.stock || 0;

                                    // Extract category from product
                                    let categoryId = null;
                                    let categoryName = null;

                                    if (product.category) {
                                        categoryId = product.category.id;
                                        categoryName = product.category.name;
                                    }

                                    console.log('Product category:', {
                                        productName,
                                        categoryId,
                                        categoryName,
                                        fullCategory: product.category
                                    });

                                    if (product.variants && product.variants.length > 0) {
                                        product.variants.forEach(variant => {
                                            const price = parseFloat(variant.real_price) ||
                                                0;
                                            const variantKey = variant.variant_key;
                                            const variantValue = variant.variant_value;
                                            const variantName = variantKey ?
                                                `${variantKey}: ${variantValue}` : (variant
                                                    .variant_name || 'Default');
                                            const variantSku = variant.sku || product.sku ||
                                                'N/A';
                                            const variantStock = variant.stock ?? 0;

                                            html += `
                                        <div class="p-2 border-bottom cursor-pointer product-suggestion"
                                             data-id="${variant.id}"
                                             data-product-id="${product.id}"
                                             data-name="${productName} - ${variantName}"
                                             data-price="${price}"
                                             data-limitation="${limitation}"
                                             data-tax-rate="${taxRate}"
                                             data-category-id="${categoryId}"
                                             data-category-name="${categoryName}"
                                             style="cursor: pointer;">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-500">${productName}</span>
                                                <span class="text-muted">${price.toFixed(2)} {{ currency() }}</span>
                                            </div>
                                            <small class="text-muted">${variantName} (SKU: ${variantSku} | Stock: ${variantStock}) ${limitation > 0 ? `- Max: ${limitation}` : ''}</small>
                                        </div>
                                    `;
                                        });
                                    } else {
                                        // Fallback if no variants - should not happen with current API
                                        // but if it does, use product.id as both product and variant ID
                                        html += `
                                    <div class="p-2 border-bottom cursor-pointer product-suggestion"
                                         data-id="${product.id}"
                                         data-product-id="${product.id}"
                                         data-name="${productName}"
                                         data-price="0"
                                         data-limitation="${limitation}"
                                         data-tax-rate="${taxRate}"
                                         data-category-id="${categoryId}"
                                         data-category-name="${categoryName}"
                                         style="cursor: pointer;">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-500">${productName}</span>
                                            <span class="text-muted">No variants</span>
                                        </div>
                                        <small class="text-muted">SKU: ${product.sku || 'N/A'} | Stock: ${productStock} ${limitation > 0 ? `- Max: ${limitation}` : ''}</small>
                                    </div>
                                `;
                                    }
                                });
                                suggestions.html(html).show();
                            } else {
                                suggestions.html(
                                    '<div class="p-2 text-muted">{{ trans('order.no_products_found') }}</div>'
                                ).show();
                            }
                        }, 300);
                    });

                    // Product suggestion click
                    $(document).on('click', '.product-suggestion', function() {
                        const productId = $(this).data('product-id');
                        const variantId = $(this).data('id');
                        const name = $(this).data('name');
                        const price = $(this).data('price');
                        const limitation = $(this).data('limitation') || 0;
                        const taxRate = $(this).data('tax-rate') || 0;
                        const categoryId = $(this).data('category-id');
                        const categoryName = $(this).data('category-name');

                        console.log('Product selected:', {
                            productId,
                            variantId,
                            name,
                            price,
                            limitation,
                            taxRate,
                            categoryId,
                            categoryName
                        });

                        $('#product_search').val(name);
                        $('#selected_product_id').val(productId);
                        $('#selected_product_variant_id').val(variantId);
                        $('#selected_product_name').val(name);
                        $('#selected_product_price').val(price);
                        $('#selected_product_limitation').val(limitation);
                        $('#selected_product_tax_rate').val(taxRate);
                        $('#selected_product_category_id').val(categoryId);
                        $('#selected_product_category_name').val(categoryName);
                        $('#product_suggestions').hide();
                        $('#addProductBtn').prop('disabled', false);

                        // Update quantity input max and show limitation text
                        if (limitation > 0) {
                            $('#product_quantity').attr('max', limitation);
                            $('#limitationText').text(`(Max: ${limitation})`);
                        } else {
                            $('#product_quantity').removeAttr('max');
                            $('#limitationText').text('');
                        }

                        console.log('Hidden fields set:', {
                            id: $('#selected_product_id').val(),
                            name: $('#selected_product_name').val(),
                            price: $('#selected_product_price').val(),
                            limitation: $('#selected_product_limitation').val(),
                            taxRate: $('#selected_product_tax_rate').val()
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
                                    '<div class="p-2 text-muted">{{ trans('order.loading_customers') }}</div>'
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
                                    '<div class="p-2 text-muted">{{ trans('order.no_customers_found') }}</div>'
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

                        console.log('Customer selected:', {
                            id,
                            name,
                            email,
                            phone
                        });

                        $('#customer_search').val(name);
                        $('#selected_customer_id').val(id);
                        $('#customer_suggestions').hide();

                        // Immediately populate email and phone fields
                        $('#customer_email').val(email || '');
                        $('#customer_phone').val(phone || '');

                        // Load customer addresses
                        loadCustomerAddresses(id);
                    });

                    // Load customer addresses
                    function loadCustomerAddresses(customerId) {
                        $.ajax({
                            url: `/api/customers/${customerId}/addresses`,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Country-Code': countryCode
                            },
                            success: function(response) {
                                const addressSelect = $('#customer_address_select');
                                addressSelect.empty();
                                addressSelect.append(
                                    '<option value="">{{ trans('order.select_address') }}</option>');

                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(address => {
                                        addressSelect.append(
                                            `<option value="${address.id}" data-address="${address.address}">${address.title} - ${address.address}</option>`
                                        );
                                    });
                                    $('#customer_address_section').show();
                                    $('#no_address_section').hide();
                                } else {
                                    $('#customer_address_section').hide();
                                    $('#no_address_section').show();
                                }

                                // Clear address field until user selects one
                                $('#customer_address').val('');

                                // Store current customer ID for address creation
                                $('#addAddressForm').data('customer-id', customerId);
                            },
                            error: function(xhr) {
                                console.error('Failed to load addresses:', xhr);
                                showAlert('danger', 'Failed to load customer addresses');
                            }
                        });
                    }

                    // Customer address select change
                    $('#customer_address_select').on('change', function() {
                        const address = $(this).find('option:selected').data('address');
                        $('#customer_address').val(address);
                    });

                    // Load countries for address modal
                    function loadCountries() {
                        $.ajax({
                            url: '/api/area/countries',
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                const countrySelect = $('#address_country_id');
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(country => {
                                        countrySelect.append(
                                            `<option value="${country.id}">${country.name || country.title}</option>`
                                        );
                                    });
                                }
                            }
                        });
                    }

                    // Load cities based on country
                    $('#address_country_id').on('change', function() {
                        const countryId = $(this).val();
                        const citySelect = $('#address_city_id');

                        citySelect.empty().append('<option value="">{{ __('common.select') }}</option>').prop(
                            'disabled', true);
                        $('#address_region_id').empty().append(
                            '<option value="">{{ __('common.select') }}</option>').prop('disabled', true);
                        $('#address_subregion_id').empty().append(
                            '<option value="">{{ __('common.select') }}</option>').prop('disabled', true);

                        if (!countryId) return;

                        $.ajax({
                            url: `/api/area/countries/${countryId}/cities`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(city => {
                                        citySelect.append(
                                            `<option value="${city.id}">${city.name || city.title}</option>`
                                        );
                                    });
                                    citySelect.prop('disabled', false);
                                }
                            }
                        });
                    });

                    // Load regions based on city
                    $('#address_city_id').on('change', function() {
                        const cityId = $(this).val();
                        const regionSelect = $('#address_region_id');

                        regionSelect.empty().append('<option value="">{{ __('common.select') }}</option>').prop(
                            'disabled', true);
                        $('#address_subregion_id').empty().append(
                            '<option value="">{{ __('common.select') }}</option>').prop('disabled', true);

                        if (!cityId) return;

                        $.ajax({
                            url: `/api/area/cities/${cityId}/regions`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(region => {
                                        regionSelect.append(
                                            `<option value="${region.id}">${region.name || region.title}</option>`
                                        );
                                    });
                                    regionSelect.prop('disabled', false);
                                }
                            }
                        });
                    });

                    // Load sub-regions based on region
                    $('#address_region_id').on('change', function() {
                        const regionId = $(this).val();
                        const subregionSelect = $('#address_subregion_id');

                        subregionSelect.empty().append('<option value="">{{ __('common.select') }}</option>').prop(
                            'disabled', true);

                        if (!regionId) return;

                        $.ajax({
                            url: `/api/area/regions/${regionId}/subregions`,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(subregion => {
                                        subregionSelect.append(
                                            `<option value="${subregion.id}">${subregion.name || subregion.title}</option>`
                                        );
                                    });
                                    subregionSelect.prop('disabled', false);
                                }
                            }
                        });
                    });

                    // Open add address modal
                    $('#addNewAddressBtn, #createAddressBtn').on('click', function() {
                        const customerId = $('#selected_customer_id').val();
                        if (!customerId) {
                            showAlert('warning', '{{ trans('order.please_select_customer') }}');
                            return;
                        }

                        // Pre-fill email and phone if available
                        const email = $('#customer_email').val();
                        const phone = $('#customer_phone').val();

                        $('#address_email').val(email);
                        $('#address_phone').val(phone);

                        const modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
                        modal.show();
                    });

                    // Validate address form
                    function validateAddressForm() {
                        let isValid = true;
                        const requiredFields = $('#addAddressForm').find('.address-required');

                        // Clear previous errors
                        $('#addAddressForm').find('.error-message').addClass('d-none').text('');
                        $('#addAddressForm').find('.address-required').removeClass('is-invalid');
                        $('#addressFormErrors').hide().html('');

                        requiredFields.each(function() {
                            const value = $(this).val();
                            if (!value || value === '') {
                                isValid = false;
                                $(this).addClass('is-invalid');
                                $(this).closest('.form-group').find('.error-message').removeClass('d-none').text(
                                    'This field is required');
                            }
                        });

                        return isValid;
                    }

                    // Real-time validation on input change
                    $('#addAddressForm').find('.address-required').on('change keyup', function() {
                        const value = $(this).val();
                        if (value && value !== '') {
                            $(this).removeClass('is-invalid');
                            $(this).closest('.form-group').find('.error-message').addClass('d-none').text('');
                        }
                    });

                    // Save new address - Remove previous handlers to prevent multiple submissions
                    $('#saveAddressBtn').off('click').on('click', function() {
                        // Validate form first
                        if (!validateAddressForm()) {
                            showAlert('warning', 'Please fill in all required fields');
                            return;
                        }

                        const customerId = $('#selected_customer_id').val();
                        const formData = {
                            title: $('#address_title').val(),
                            country_id: $('#address_country_id').val(),
                            city_id: $('#address_city_id').val(),
                            region_id: $('#address_region_id').val(),
                            subregion_id: $('#address_subregion_id').val() || null,
                            address: $('#address_address').val(),
                            phone: $('#address_phone').val(),
                            email: $('#address_email').val() || null,
                            is_primary: $('#address_is_primary').is(':checked') ? 1 : 0
                        };

                        $.ajax({
                            url: `/api/customers/${customerId}/addresses`,
                            type: 'POST',
                            dataType: 'json',
                            contentType: 'application/json',
                            data: JSON.stringify(formData),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status && response.data) {
                                    // Get the new address from response
                                    const newAddress = response.data;

                                    // Close modal - use multiple methods to ensure it closes
                                    const modalElement = document.getElementById('addAddressModal');
                                    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                                    modal.hide();
                                    console.log('Modal should close');

                                    // Remove modal backdrop if it exists
                                    setTimeout(function() {
                                        $('.modal-backdrop').remove();
                                        $('body').removeClass('modal-open');
                                    }, 300);

                                    // Fill the address field with the created address
                                    $('#customer_address').val(newAddress.address);

                                    // Add new address to dropdown
                                    const addressSelect = $('#customer_address_select');
                                    const addressOption =
                                        `<option value="${newAddress.id}" data-address="${newAddress.address}">${newAddress.title} - ${newAddress.address}</option>`;
                                    addressSelect.append(addressOption);

                                    // Select the new address in dropdown
                                    addressSelect.val(newAddress.id);

                                    // Show success message
                                    showAlert('success',
                                        '{{ trans('order.address_created_successfully') }}');

                                    // Reset form
                                    $('#addAddressForm')[0].reset();
                                    $('#address_country_id').val('');
                                    $('#address_city_id').empty().append(
                                            '<option value="">{{ __('common.select') }}</option>')
                                        .prop('disabled', true);
                                    $('#address_region_id').empty().append(
                                            '<option value="">{{ __('common.select') }}</option>')
                                        .prop('disabled', true);
                                    $('#address_subregion_id').empty().append(
                                            '<option value="">{{ __('common.select') }}</option>')
                                        .prop('disabled', true);
                                    $('#addressFormErrors').hide().html('');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    '{{ trans('order.error_creating_address') }}';

                                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                    const errors = xhr.responseJSON.errors;
                                    let errorHtml = '<ul class="mb-0">';
                                    $.each(errors, function(key, value) {
                                        errorHtml += '<li>' + value[0] + '</li>';
                                    });
                                    errorHtml += '</ul>';
                                    $('#addressFormErrors').html(errorHtml).show();
                                } else {
                                    errorMessage = xhr.responseJSON?.message || errorMessage;
                                    showAlert('danger', errorMessage);
                                }
                            }
                        });
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
                    loadCountries();

                    // Add Fee
                    $('#addFeeBtn').on('click', function() {
                        const feeId = `fee_${feeCounter++}`;
                        const feeHtml = `
                    <div class="fee-item mb-10 p-10 rounded" data-fee-id="${feeId}">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control fee-reason" placeholder="{{ trans('order.reason') }}" required style="background-color: transparent; border: 1px solid #ddd;">
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
                                <input type="text" class="form-control discount-reason" placeholder="{{ trans('order.reason') }}" required style="background-color: transparent; border: 1px solid #ddd;">
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

                    // Add Product to Order
                    $('#addProductBtn').click(function() {
                        const productId = $('#selected_product_id').val();
                        const variantId = $('#selected_product_variant_id').val() || null;
                        const productName = $('#selected_product_name').val();
                        const productPrice = parseFloat($('#selected_product_price').val()) || 0;
                        const quantity = parseInt($('#product_quantity').val()) || 1;
                        const limitation = parseInt($('#selected_product_limitation').val()) || 0;
                        const taxRate = parseFloat($('#selected_product_tax_rate').val()) || 0;
                        const categoryId = parseInt($('#selected_product_category_id').val()) || null;
                        const categoryName = $('#selected_product_category_name').val();

                        console.log('Adding product:', {
                            productId,
                            variantId,
                            productName,
                            productPrice,
                            quantity,
                            limitation,
                            taxRate,
                            categoryId,
                            categoryName
                        });

                        if (!productId) {
                            showAlert('warning', '{{ trans('order.please_select_product') }}');
                            return;
                        }

                        // Validate quantity against limitation
                        if (limitation > 0 && quantity > limitation) {
                            showAlert('warning', `Maximum quantity for this product is ${limitation}`);
                            return;
                        }

                        const productTotal = productPrice * quantity;

                        // Create unique ID for this product (same logic as when adding)
                        const uniqueProductId = productId + (variantId ? '_' + variantId : '');

                        // Check if product already exists
                        const existingProduct = products.find(p => p.id == uniqueProductId);
                        if (existingProduct) {
                            const newQuantity = existingProduct.quantity + quantity;

                            // Validate total quantity against limitation
                            if (limitation > 0 && newQuantity > limitation) {
                                showAlert('warning',
                                    `Total quantity for this product cannot exceed ${limitation}. Current: ${existingProduct.quantity}, Adding: ${quantity}`
                                );
                                return;
                            }

                            existingProduct.quantity = newQuantity;
                            existingProduct.total = existingProduct.price * existingProduct.quantity;
                        } else {
                            products.push({
                                // Minimal data for server (form submission)
                                vendor_product_id: productId,
                                vendor_product_variant_id: variantId,
                                quantity: quantity,
                                // Category data for shipping calculation
                                category_id: categoryId,
                                category_name: categoryName,
                                // Display data for UI (not sent to server)
                                id: productId + (variantId ? '_' + variantId : ''), // Unique ID for UI
                                name: productName,
                                price: productPrice,
                                total: productTotal,
                                taxRate: taxRate
                            });
                        }

                        renderProductsTable();
                        // Reset form fields
                        $('#product_search').val('');
                        $('#selected_product_id').val('');
                        $('#selected_product_variant_id').val('');
                        $('#selected_product_name').val('');
                        $('#selected_product_price').val('');
                        $('#selected_product_limitation').val('');
                        $('#selected_product_tax_rate').val('');
                        $('#product_quantity').val(1);
                        $('#limitationText').text('');
                        $('#product_quantity').removeAttr('max');
                        $('#addProductBtn').prop('disabled', true);
                        updateSummary();
                        // Trigger shipping calculation when product is added
                        $(document).trigger('productAdded');
                    });

                    // Remove Product
                    $(document).on('click', '.remove-product', function() {
                        const productId = $(this).data('product-id');
                        products = products.filter(p => p.id !== productId);
                        renderProductsTable();
                        updateSummary();
                        // Recalculate shipping when product is removed
                        calculateShipping();
                    });

                    // Render Products Table
                    function renderProductsTable() {
                        const tbody = $('#productsTableBody');
                        tbody.empty();

                        products.forEach(product => {
                            const taxRate = product.taxRate || 0;
                            const priceExcl = product.price / (1 + taxRate / 100);
                            const row = `
                        <tr>
                            <td>${product.name}</td>
                            <td class="text-center">${priceExcl.toFixed(2)} {{ currency() }}</td>
                            <td class="text-center">${product.quantity}</td>
                            <td class="text-center">${taxRate > 0 ? taxRate.toFixed(2) + '%' : '-'}</td>
                            <td class="text-center">${product.total.toFixed(2)} {{ currency() }}</td>
                            <td class="text-center d-flex">
                                <button type="button" class="btn btn-sm btn-danger remove-product" data-product-id="${product.id}">
                                    <i class="uil uil-trash m-0"></i>
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
                        let totalTax = 0;

                        // Calculate subtotal and tax from products
                        products.forEach(product => {
                            const taxRate = product.taxRate || 0;
                            const lineTotalIncl = product.total;
                            const lineTotalExcl = lineTotalIncl / (1 + taxRate / 100);
                            const lineTax = lineTotalIncl - lineTotalExcl;

                            subtotal += lineTotalExcl;
                            totalTax += lineTax;
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
                        const grandTotal = subtotal + shipping + totalFees + totalTax - totalDiscounts;

                        $('#subtotal').text(subtotal.toFixed(2) + ' {{ __('common.currency') }}');
                        $('#shippingDisplay').text(shipping.toFixed(2) + ' {{ __('common.currency') }}');

                        // Show tax only if > 0
                        if (totalTax > 0) {
                            $('#totalTax').text(totalTax.toFixed(2) + ' {{ __('common.currency') }}');
                            $('#taxSection').show();
                        } else {
                            $('#taxSection').hide();
                        }

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
                        '.fee-reason, .fee-amount, .discount-reason, .discount-amount',
                        function() {
                            updateSummary();
                        });

                    // Calculate shipping when product is added
                    $(document).on('productAdded', function() {
                        calculateShipping();
                    });

                    // Calculate shipping when address is changed
                    $('#customer_address_select').on('change', function() {
                        calculateShipping();
                    });

                    // Calculate shipping cost via API
                    function calculateShipping() {
                        const customerType = $('input[name="customer_type"]:checked').val();
                        const customerId = $('#selected_customer_id').val();
                        const addressId = $('#customer_address_select').val();

                        // Only calculate for existing customers with selected address and at least one product
                        if (customerType !== 'existing' || !customerId || !addressId || products.length === 0) {
                            $('#shipping').val(0);
                            updateSummary();
                            return;
                        }

                        // Format cart items for shipping calculation
                        const cartItems = products.map(p => ({
                            category_id: p.category_id,
                            category_name: p.category_name,
                            product_id: p.vendor_product_id,
                            quantity: p.quantity
                        }));

                        // Call shipping calculation endpoint
                        $.ajax({
                            url: '{{ route('admin.shipping.calculate') }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Content-Type': 'application/json'
                            },
                            data: JSON.stringify({
                                customer_id: customerId,
                                customer_address_id: addressId,
                                cart_items: cartItems
                            }),
                            success: function(response) {
                                console.log('Shipping response:', response);
                                if (response.success && response.data) {
                                    const shippingCost = parseFloat(response.data.shipping_cost) || 0;
                                    $('#shipping').val(shippingCost);
                                    updateSummary();
                                } else {
                                    $('#shipping').val(0);
                                    updateSummary();
                                }
                            },
                            error: function(xhr) {
                                console.error('Shipping calculation error:', xhr);
                                console.log('Response:', xhr.responseJSON);
                                $('#shipping').val(0);
                                updateSummary();
                            }
                        });
                    }

                    // Form validation function
                    function validateForm() {
                        let errors = [];

                        // Check customer selection
                        const customerType = $('input[name="customer_type"]:checked').val();

                        if (customerType === 'existing') {
                            const customerId = $('#selected_customer_id').val();
                            const addressId = $('#customer_address_select').val();

                            if (!customerId || customerId.trim() === '') {
                                errors.push('{{ trans('order.select_customer') }} is required');
                            }
                            if (!addressId || addressId.trim() === '') {
                                errors.push('{{ trans('order.customer_address') }} is required');
                            }
                        } else if (customerType === 'external') {
                            const name = $('#external_customer_name').val();
                            const email = $('#external_customer_email').val();
                            const phone = $('#external_customer_phone').val();
                            const address = $('#external_customer_address').val();

                            if (!name || name.trim() === '') {
                                errors.push('{{ trans('order.customer_name') }} is required');
                            }
                            if (!email || email.trim() === '') {
                                errors.push('{{ trans('order.customer_email') }} is required');
                            } else if (!isValidEmail(email)) {
                                errors.push('{{ trans('order.customer_email') }} must be valid');
                            }
                            if (!phone || phone.trim() === '') {
                                errors.push('{{ trans('order.customer_phone') }} is required');
                            }
                            if (!address || address.trim() === '') {
                                errors.push('{{ trans('order.customer_address') }} is required');
                            }
                        }

                        // Check products
                        if (!products || products.length === 0) {
                            errors.push('{{ trans('order.add_product') }} - At least one product is required');
                        }

                        return errors;
                    }

                    // Email validation helper
                    function isValidEmail(email) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return emailRegex.test(email);
                    }

                    // Form submission
                    $('#createOrderForm').on('submit', function(e) {
                        e.preventDefault();

                        // Validate form first
                        const validationErrors = validateForm();
                        if (validationErrors.length > 0) {
                            let errorHtml =
                                '<strong>{{ trans('order.validation_error') }}:</strong><ul class="mb-0 mt-2">';
                            validationErrors.forEach(function(error) {
                                errorHtml += '<li>' + error + '</li>';
                            });
                            errorHtml += '</ul>';
                            showAlert('danger', errorHtml);
                            return;
                        }

                        // Collect fees and discounts BEFORE creating FormData
                        let fees = [];
                        let discounts = [];

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

                        // Prepare products with required fields including category data
                        const productsForServer = products.map(p => ({
                            vendor_product_id: p.vendor_product_id,
                            vendor_product_variant_id: p.vendor_product_variant_id,
                            quantity: p.quantity,
                            category_id: p.category_id,
                            category_name: p.category_name
                        }));

                        // Update hidden inputs with collected data
                        $('#feesData').val(JSON.stringify(fees));
                        $('#discountsData').val(JSON.stringify(discounts));
                        $('#productsData').val(JSON.stringify(productsForServer));

                        // Debug logging
                        console.log('Products for server:', productsForServer);
                        console.log('Fees collected:', fees);
                        console.log('Discounts collected:', discounts);
                        console.log('Shipping value:', $('#shipping').val());

                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.show({
                                text: '{{ trans('order.create_order') }}',
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
                                if (response.status) {
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

                                let errorMessage = '{{ trans('order.error_creating_order') }}';
                                let errorDetails = [];

                                // Check if we have a response
                                if (!xhr.responseJSON) {
                                    // Network error or server didn't respond
                                    if (xhr.status === 0) {
                                        errorMessage =
                                            'Network error. Please check your connection and try again.';
                                    } else if (xhr.status === 500) {
                                        errorMessage = 'Server error. Please try again later.';
                                    } else if (xhr.status === 403) {
                                        errorMessage = 'You do not have permission to create orders.';
                                    } else if (xhr.status === 404) {
                                        errorMessage = 'The requested resource was not found.';
                                    }
                                    showAlert('danger', errorMessage);
                                    return;
                                }

                                // Handle validation errors (422)
                                if (xhr.status === 422) {
                                    if (xhr.responseJSON?.errors) {
                                        const errors = xhr.responseJSON.errors;
                                        $.each(errors, function(key, value) {
                                            if (Array.isArray(value)) {
                                                errorDetails.push(value[0]);
                                            } else {
                                                errorDetails.push(value);
                                            }
                                        });
                                    }
                                }
                                // Handle other errors
                                else if (xhr.responseJSON?.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                // Handle errors array (from backend exceptions)
                                else if (xhr.responseJSON?.errors && Array.isArray(xhr.responseJSON
                                        .errors)) {
                                    errorDetails = xhr.responseJSON.errors;
                                }

                                // Build error HTML
                                let errorHtml = errorMessage;
                                if (errorDetails.length > 0) {
                                    errorHtml += '<ul class="mb-0 mt-2">';
                                    errorDetails.forEach(function(error) {
                                        errorHtml += '<li>' + error + '</li>';
                                    });
                                    errorHtml += '</ul>';
                                }

                                console.error('Order creation error:', {
                                    status: xhr.status,
                                    message: errorMessage,
                                    details: errorDetails,
                                    response: xhr.responseJSON
                                });

                                showAlert('danger', errorHtml);
                            }
                        });
                    });

                    // Alert function
                    function showAlert(type, message) {
                        let icon = 'uil-info-circle';
                        if (type === 'danger') {
                            icon = 'uil-exclamation-circle';
                        } else if (type === 'success') {
                            icon = 'uil-check-circle';
                        } else if (type === 'warning') {
                            icon = 'uil-alert-triangle';
                        }

                        const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="uil ${icon} me-2"></i>
                        <span>${message}</span>
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

    {{-- Responsive Grid CSS --}}
    @push('styles')
        <style>
            .responsive-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 1.5rem !important;
                margin-top: 20px !important;
            }

            /* Tablet screens (768px and below) */
            @media (max-width: 991px) {
                .responsive-grid {
                    grid-template-columns: 1fr !important;
                }
            }

            /* Mobile screens (576px and below) */
            @media (max-width: 575px) {
                .responsive-grid {
                    grid-template-columns: 1fr !important;
                    gap: 1rem !important;
                }
            }
        </style>
    @endpush


    {{-- Include Loading Overlay Component --}}
    @push('after-body')
        <x-loading-overlay :loadingText="trans('order.create_order')" :loadingSubtext="trans('main.please wait')" />
    @endpush
