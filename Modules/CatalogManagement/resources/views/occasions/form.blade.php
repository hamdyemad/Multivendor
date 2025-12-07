@extends('layout.app')
@section('title', (isset($occasion)) ? trans('catalogmanagement::occasion.edit_occasion') : trans('catalogmanagement::occasion.add_occasion'))

@push('styles')
<style>
    /* Search Results Dropdown */
    .variant-search-container {
        position: relative;
    }

    .variant-search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 350px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .variant-search-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .variant-search-item:hover {
        background: #f8f9fa;
    }

    .variant-search-item:last-child {
        border-bottom: none;
    }

    .variant-search-item .variant-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .variant-search-item .variant-info {
        flex: 1;
        min-width: 0;
    }

    .variant-search-item .variant-title {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .variant-search-item .variant-details {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 12px;
    }

    .variant-search-item .variant-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .variant-search-item .variant-badge.variant-name {
        background: #e3f2fd;
        color: #1565c0;
    }

    .variant-search-item .variant-badge.price {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .variant-search-item .variant-badge.price-discount {
        background: #fff3e0;
        color: #e65100;
    }

    .variant-search-item .variant-badge.stock {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    /* Selected Products Cards */
    .product-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .product-card:hover {
        border-color: var(--color-primary);
        box-shadow: 0 4px 12px rgba(0,123,255,0.15);
    }
    .product-card.selected {
        border-color: #28a745;
        background-color: #f8fff8;
    }
    .product-card .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .product-card .product-checkbox {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .product-card .product-info h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
    .product-card .product-info .product-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* No results message */
    .no-results-message {
        padding: 20px;
        text-align: center;
        color: #6c757d;
    }

    /* Selected Variants Container */
    .selected-variants-container {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Products Container */
    #products-container {
        max-height: 500px;
        overflow-y: auto;
    }

    #products-container::-webkit-scrollbar {
        width: 6px;
    }

    #products-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    #products-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    #products-container::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    .selected-variant-item {
        transition: background 0.2s ease;
    }

    .selected-variant-item:hover {
        background: #f8f9fa;
    }

    /* Gradient backgrounds */
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    /* Soft badges */
    .badge-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .badge-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15);
        color: #cc9a06;
    }

    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }

    /* Remove button hover */
    .remove-variant-btn:hover {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
    }

    /* Custom price input styling */
    .custom-price-input {
        border-radius: 6px 0 0 6px !important;
        border-color: #e0e0e0;
    }

    .custom-price-input:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--color-primary-rgb), 0.15);
    }

    .custom-price-input::placeholder {
        color: #adb5bd;
        font-style: italic;
    }

    /* Validation states */
    .custom-price-input.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .alert {
        border: none;
        border-radius: 8px;
    }

    .alert-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    /* Scrollbar styling for selected variants */
    .selected-variants-container::-webkit-scrollbar {
        width: 6px;
    }

    .selected-variants-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .selected-variants-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .selected-variants-container::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::occasion.occasions_management'), 'url' => route('admin.occasions.index')],
                    ['title' => isset($occasion) ? trans('catalogmanagement::occasion.edit_occasion') : trans('catalogmanagement::occasion.add_occasion')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($occasion) ? trans('catalogmanagement::occasion.edit_occasion') : trans('catalogmanagement::occasion.add_occasion') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <!-- Laravel Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="uil uil-exclamation-triangle me-2"></i>
                                    <strong>{{ __('common.validation_errors') }}</strong>
                                </div>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="occasionForm"
                              action="{{ isset($occasion) ? route('admin.occasions.update', $occasion->id) : route('admin.occasions.store') }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($occasion))
                                @method('PUT')
                            @endif
                            {{-- Occasion Name Fields --}}
                            <x-multilingual-input
                                name="name"
                                :label="trans('catalogmanagement::occasion.name')"
                                :labelAr="'اسم العرض'"
                                :placeholder="trans('catalogmanagement::occasion.enter_occasion_name')"
                                :placeholderAr="'اسم العرض'"
                                :languages="$languages"
                                :model="$occasion ?? null"
                                :required="true"
                            />

                            {{-- Occasion Title Fields --}}
                            <x-multilingual-input
                                name="title"
                                :label="trans('catalogmanagement::occasion.title')"
                                :labelAr="'العنوان'"
                                :placeholder="trans('catalogmanagement::occasion.enter_occasion_title')"
                                :placeholderAr="'العنوان'"
                                :languages="$languages"
                                :model="$occasion ?? null"
                            />

                            {{-- Occasion Sub Title Fields --}}
                            <x-multilingual-input
                                name="sub_title"
                                :label="trans('catalogmanagement::occasion.sub_title')"
                                :labelAr="'العنوان الفرعى'"
                                :placeholder="trans('catalogmanagement::occasion.enter_occasion_sub_title')"
                                :placeholderAr="'العنوان الفرعى'"
                                :languages="$languages"
                                :model="$occasion ?? null"
                            />

                            <div class="row">
                                {{-- Occasion Image --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <x-image-upload
                                            id="occasion_image"
                                            name="image"
                                            :placeholder="trans('catalogmanagement::occasion.image')"
                                            :recommendedSize="trans('catalogmanagement::occasion.recommended_size')"
                                            :existingImage="isset($occasion) && $occasion->image ? asset('storage/' . $occasion->image) : null"
                                            aspectRatio="16:9"
                                            :required="true"
                                        />
                                        @error('image')
                                            <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Activation Switcher --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('catalogmanagement::occasion.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="is_active"
                                                       name="is_active"
                                                       value="1"
                                                       {{ old('is_active', $occasion->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('is_active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Date Fields --}}
                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="start_date" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.start_date') }}
                                        </label>
                                        <input type="date"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="start_date"
                                               name="start_date"
                                               value="{{ old('start_date', isset($occasion) ? $occasion->start_date?->format('Y-m-d') : '') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="end_date" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.end_date') }}
                                        </label>
                                        <input type="date"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="end_date"
                                               name="end_date"
                                               value="{{ old('end_date', isset($occasion) ? $occasion->end_date?->format('Y-m-d') : '') }}">
                                        @error('end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Vendor Selection --}}
                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <div class="form-group">
                                        <label for="vendor_id" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.vendor') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                                            <option value="">{{ trans('catalogmanagement::occasion.select_vendor') }}</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" {{ old('vendor_id', $occasion->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            {{-- Product Variants Section --}}
                            <div class="row" id="variantsSection" style="display: none;">
                                <div class="col-12">
                                    <h6 class="mb-20 fw-500">{{ trans('catalogmanagement::occasion.product_variants') }}</h6>
                                </div>
                                <div class="col-12 mb-25">
                                    <div class="form-group">
                                        <label for="product_search" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.search_products') }}
                                        </label>
                                        <input type="text"
                                               id="product_search"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               :placeholder="trans('catalogmanagement::occasion.type_to_search_products')">
                                        <small class="text-muted">{{ trans('catalogmanagement::occasion.search_products_help') }}</small>
                                    </div>

                                    {{-- Search Loading Indicator --}}
                                    <div id="products-loading" style="display: none;">
                                        <div class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">{{ trans('common.loading') }}...</span>
                                            </div>
                                            <p class="mt-2 mb-0 text-muted small">{{ trans('catalogmanagement::occasion.searching_products') }}...</p>
                                        </div>
                                    </div>

                                    {{-- Selected Products Alert --}}
                                    <div id="selected-products-alert" class="mt-3" style="display: none;">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-gradient-success text-white py-3 d-block">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i class="uil uil-check-circle fs-4 me-2"></i>
                                                        <div>
                                                            <h6 class="mb-0 text-white">{{ trans('catalogmanagement::occasion.selected_products') }}</h6>
                                                            <small class="opacity-75"><span id="selected-count">0</span> {{ trans('catalogmanagement::occasion.products_selected') }}</small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-light" id="clear-selections">
                                                        <i class="uil uil-trash-alt me-1"></i>{{ trans('common.clear_all') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div id="selected-products-list" class="selected-variants-container"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Variants Validation Errors --}}
                                    @if($errors->has('variants') || $errors->has('variants.*'))
                                        <div class="alert alert-danger mt-3" role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="uil uil-exclamation-triangle me-2"></i>
                                                <strong>{{ __('catalogmanagement::occasion.product_variants') }} {{ __('common.errors') }}</strong>
                                            </div>
                                            <ul class="mb-0 mt-2">
                                                @error('variants')
                                                    <li>{{ $message }}</li>
                                                @enderror
                                                @foreach($errors->get('variants.*') as $variantErrors)
                                                    @foreach($variantErrors as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Products Container --}}
                                    <div id="products-container" class="mt-3">
                                        <div id="products-list"></div>
                                        <div id="no-products" class="text-center text-muted py-4" style="display: none;">
                                            {{ trans('catalogmanagement::occasion.no_products_found') }}
                                        </div>
                                    </div>
                                </div>
                            </div>




                            {{-- SEO Information Section --}}
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="p-0 fw-500 fw-bold">{{ trans('catalogmanagement::occasion.seo_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- SEO Title Fields --}}
                                        <x-multilingual-input
                                            name="seo_title"
                                            :label="trans('catalogmanagement::occasion.seo_title')"
                                            :labelAr="trans('catalogmanagement::occasion.seo_title')"
                                            :placeholder="trans('catalogmanagement::occasion.enter_seo_title')"
                                            :placeholderAr="trans('catalogmanagement::occasion.enter_seo_title')"
                                            :languages="$languages"
                                            :model="$occasion ?? null"
                                        />

                                        {{-- SEO Description Fields --}}
                                        <x-multilingual-input
                                            name="seo_description"
                                            :label="trans('catalogmanagement::occasion.seo_description')"
                                            :labelAr="trans('catalogmanagement::occasion.seo_description')"
                                            :placeholder="trans('catalogmanagement::occasion.enter_seo_description')"
                                            :placeholderAr="trans('catalogmanagement::occasion.enter_seo_description')"
                                            type="textarea"
                                            rows="3"
                                            :languages="$languages"
                                            :model="$occasion ?? null"
                                        />

                                        {{-- SEO Keywords Fields --}}
                                        <x-multilingual-input
                                            name="seo_keywords"
                                            :label="trans('catalogmanagement::occasion.seo_keywords')"
                                            :labelAr="trans('catalogmanagement::occasion.seo_keywords')"
                                            :placeholder="trans('catalogmanagement::occasion.type_keyword_press_enter')"
                                            :placeholderAr="trans('catalogmanagement::occasion.type_keyword_press_enter')"
                                            :tags="true"
                                            :languages="$languages"
                                            :model="$occasion ?? null"
                                        />
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.occasions.index') }}"
                                   class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ trans('common.cancel') }}
                                </a>
                                <button type="submit" id="submitBtn"
                                        class="btn btn-primary btn-default btn-squared text-capitalize"
                                        style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span>{{ isset($occasion) ? trans('common.update') : trans('common.save') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialize Select2 for vendor dropdown
        $('#vendor_id').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ trans("common.select") }}',
            allowClear: true,
            width: '100%'
        });

        // Show/hide variants section based on vendor selection
        $('#vendor_id').on('change', function() {
            const vendorId = $(this).val();
            if (vendorId) {
                $('#variantsSection').slideDown();
                // Clear previous selections only when vendor changes
                $('#product_search').val('');
                $('#products-list').empty();
                $('#no-products').hide();
                selectedProducts = []; // Clear selections when vendor changes
            } else {
                $('#variantsSection').slideUp();
                $('#product_search').val('');
                $('#products-list').empty();
                selectedProducts = [];
            }
        });

        document.addEventListener('DOMContentLoaded', function() {

            let searchTimeout;
            let selectedProducts = [];

            // Product search functionality
            $('#product_search').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().trim();
                const vendorId = $('#vendor_id').val();

                if (!vendorId) {
                    return;
                }

                if (searchTerm.length < 2) {
                    $('#products-list').empty();
                    $('#no-products').hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    searchProducts(searchTerm, vendorId);
                }, 500);
            });

            // Search products via AJAX
            function searchProducts(search, vendorId) {
                $('#products-loading').show();
                $('#products-list').empty();
                $('#no-products').hide();

                $.ajax({
                    url: '/api/products',
                    method: 'GET',
                    headers: {
                        'lang' : "{{ app()->getLocale() }}"
                    },
                    data: {
                        search: search,
                        vendor_id: vendorId,
                        country_id: $("meta[name='current_country_id']").attr("content"),
                        paginated: 'ok',
                    },
                    success: function(response) {
                        console.log('response:', response);
                        $('#products-loading').hide();

                        if (response.status && response.data && response.data.length > 0) {
                            renderProducts(response.data);
                        } else {
                            $('#no-products').show();
                        }
                    },
                    error: function() {
                        $('#products-loading').hide();
                        $('#no-products').show();
                    }
                });
            }

            // Render product cards with variants
            function renderProducts(products) {
                const container = $('#products-list');
                container.empty();

                // Store product details for the alert
                storeProductDetails(products);

                products.forEach(function(vendorProduct) {
                    // Get product image
                    const productImage = vendorProduct.image || '{{ asset("assets/img/logo.png") }}';
                    const productName = vendorProduct.name || 'N/A';

                    // Check if product has variants
                    const variants = vendorProduct.variants || [];

                    if (variants.length > 0) {
                        // Render each variant as a separate card
                        variants.forEach(function(variant) {
                            const variantId = variant.id;
                            const isSelected = selectedProducts.includes(parseInt(variantId));
                            const variantName = variant.variant_name || variant.name || '';
                            const variantSku = variant.sku || 'N/A';
                            const stock = variant.stock;
                            const price = variant.real_price;
                            const priceBeforeDiscount = variant.fake_price;

                            const card = `
                                <div class="product-card ${isSelected ? 'selected' : ''}" data-product-id="${variantId}" style="cursor: pointer;">
                                    <div class="d-flex align-items-center">
                                        <img src="${productImage}" alt="${productName}" class="product-image me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">${productName}</h6>
                                            ${variantName ? `<div class="text-primary small mb-1"><i class="uil uil-tag-alt me-1"></i>{{ __('catalogmanagement::occasion.variant_name') }}: ${variantName}</div>` : ''}
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <span class="badge badge-primary badge-round badge-lg"><i class="uil uil-box me-1"></i>{{ __('catalogmanagement::occasion.sku') }}: ${variantSku}</span>
                                                <span class="badge ${stock > 0 ? 'badge-success' : 'badge-danger'} badge-round badge-lg"><i class="uil uil-package me-1 text-white"></i>{{ __('common.stock') }}: ${stock}</span>
                                                ${priceBeforeDiscount ?
                                                    `<span class="badge badge-warning text-white badge-round badge-lg"><del>${priceBeforeDiscount}</del> → ${price} {{ __('common.egp') }}</span>` :
                                                    `<span class="badge badge-info text-white badge-round badge-lg">${price} {{ __('common.egp') }}</span>`
                                                }
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            ${isSelected ? '<i class="select-icon fas fa-check-circle text-success fa-lg"></i>' : '<i class="select-icon far fa-circle text-muted fa-lg"></i>'}
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.append(card);
                        });
                    } else {
                        // No variants - show product without variants (fallback)
                        const isSelected = selectedProducts.includes(parseInt(vendorProduct.id));
                        const sku = vendorProduct.sku || 'N/A';

                        const card = `
                            <div class="product-card ${isSelected ? 'selected' : ''}" data-product-id="${vendorProduct.id}" style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <img src="${productImage}" alt="${productName}" class="product-image me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${productName}</h6>
                                        <small class="text-muted">{{ __('catalogmanagement::occasion.sku') }}: ${sku}</small>
                                    </div>
                                    <div class="ms-2">
                                        ${isSelected ? '<i class="select-icon fas fa-check-circle text-success fa-lg"></i>' : '<i class="select-icon far fa-circle text-muted fa-lg"></i>'}
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(card);
                    }
                });
            }

            // Handle product toggle - click entire card
            $(document).on('click', '.product-card', function() {
                const productId = parseInt($(this).data('product-id'));
                const selectIcon = $(this).find('.select-icon');
                const isCurrentlySelected = selectedProducts.includes(productId);

                if (isCurrentlySelected) {
                    // Remove from selected products (unselect)
                    selectedProducts = selectedProducts.filter(id => id !== productId);
                    $(this).removeClass('selected');
                    // Change icon from checked to unchecked
                    selectIcon.attr('class', 'select-icon far fa-circle text-muted fa-lg');
                } else {
                    // Add to selected products (select)
                    selectedProducts.push(productId);
                    $(this).addClass('selected');
                    // Change icon from unchecked to checked
                    selectIcon.attr('class', 'select-icon fas fa-check-circle text-success fa-lg');
                }

                console.log('Selected products:', selectedProducts);
                updateSelectedProductsAlert();
            });

            // Store selected product details for the alert
            let selectedProductsDetails = {};

            // Update selected products alert
            function updateSelectedProductsAlert() {
                const count = selectedProducts.length;

                if (count > 0) {
                    $('#selected-products-alert').show();
                    $('#selected-count').text(count);

                    // Update the selected products list with variant cards
                    let selectedHtml = '';
                    selectedProducts.forEach(function(variantId, index) {
                        const variant = selectedProductsDetails[variantId];
                        if (variant) {
                            selectedHtml += `
                                <div class="selected-variant-item ${index !== selectedProducts.length - 1 ? 'border-bottom' : ''}">
                                    <div class="d-flex align-items-center p-3">
                                        <img src="${variant.image}" alt="${variant.productName}"
                                             class="rounded-3 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">${variant.productName}</h6>
                                            ${variant.variantName ? `<div class="text-primary small mb-1"><i class="uil uil-tag-alt me-1"></i>{{ __('catalogmanagement::occasion.variant_name') }}: ${variant.variantName}</div>` : ''}
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                <span class="badge badge-soft-primary badge-lg badge-round"><i class="uil uil-box me-1"></i>{{ __('catalogmanagement::occasion.sku') }}: ${variant.sku}</span>
                                                <span class="badge ${variant.stock > 0 ? 'badge-soft-success' : 'badge-soft-danger'} badge-lg badge-round">
                                                    <i class="uil uil-package me-1 text-muted"></i>{{ __('common.stock') }}: ${variant.stock}
                                                </span>
                                                ${variant.priceBeforeDiscount ?
                                                    `<span class="badge badge-soft-warning badge-lg badge-round"><del class="me-1">${variant.priceBeforeDiscount}</del>${variant.price} {{ __('common.egp') }}</span>` :
                                                    `<span class="badge badge-soft-info badge-lg badge-round">${variant.price} {{ __('common.egp') }}</span>`
                                                }
                                            </div>
                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                <label class="small text-muted mb-0">{{ __('catalogmanagement::occasion.custom_price') }}:</label>
                                                <div class="input-group input-group-sm" style="width: 150px;">
                                                    <input type="hidden"
                                                           name="variants[${index}][vendor_product_variant_id]"
                                                           value="${variantId}">
                                                    <input type="number"
                                                           class="form-control custom-price-input"
                                                           name="variants[${index}][special_price]"
                                                           placeholder="${variant.price}"
                                                           value="${variant.specialPrice || ''}"
                                                           step="0.01"
                                                           min="0">
                                                    <span class="input-group-text">EGP</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle remove-variant-btn"
                                                data-product-id="${variantId}" style="width: 32px; height: 32px; padding: 0; margin:0;">
                                            <i class="uil uil-times m-0"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        }
                    });
                    $('#selected-products-list').html(selectedHtml);
                } else {
                    $('#selected-products-alert').hide();
                }
            }

            // Store product details when rendering (now stores variant details)
            function storeProductDetails(products) {
                products.forEach(function(product) {
                    const variants = product.variants || [];
                    const productImage = product.image || '{{ asset("assets/img/logo.png") }}';
                    const productName = product.name || 'N/A';

                    if (variants.length > 0) {
                        variants.forEach(function(variant) {
                            selectedProductsDetails[variant.id] = {
                                productName: productName,
                                variantName: variant.variant_name || variant.name || '',
                                sku: variant.sku || 'N/A',
                                stock: variant.stock || 0,
                                price: variant.real_price || '0.00',
                                priceBeforeDiscount: variant.fake_price || null,
                                image: productImage
                            };
                        });
                    } else {
                        selectedProductsDetails[product.id] = {
                            productName: productName,
                            variantName: '',
                            sku: product.sku || 'N/A',
                            stock: 0,
                            price: '0.00',
                            priceBeforeDiscount: null,
                            image: productImage
                        };
                    }
                });
            }

            // Clear all selections
            $(document).on('click', '#clear-selections', function() {
                selectedProducts = [];
                // Don't clear selectedProductsDetails - keep the data for re-selection
                updateSelectedProductsAlert();

                // Update all product cards - remove selected state and update icons
                $('.product-card').removeClass('selected');
                $('.product-card .select-icon').attr('class', 'select-icon far fa-circle text-muted fa-lg');
            });

            // Remove individual product from selected list
            $(document).on('click', '#selected-products-list .remove-variant-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productId = parseInt($(this).data('product-id'));
                console.log('Removing product:', productId);

                // Remove from selected products array only (keep details for re-selection)
                selectedProducts = selectedProducts.filter(id => id !== productId);
                // Don't delete from selectedProductsDetails - keep the data for re-selection

                // Update the card if it's visible - remove selected state and update icon
                const card = $(`.product-card[data-product-id="${productId}"]`);
                card.removeClass('selected');
                card.find('.select-icon').attr('class', 'select-icon far fa-circle text-muted fa-lg');

                updateSelectedProductsAlert();
                console.log('Updated selected products:', selectedProducts);
            });



            // Trigger change on page load if vendor is already selected
            if ($('#vendor_id').val()) {
                $('#variantsSection').show();
            }

            @if(isset($occasion) && $occasion->occasionProducts->count() > 0)
            // Load existing occasion products for edit mode
            $(document).ready(function() {
                @php
                    $existingVariantsData = $occasion->occasionProducts->map(function ($item) {
                        return [
                            'id' => $item->vendor_product_variant_id,
                            'special_price' => $item->special_price,
                            'position' => $item->position,
                            'variant' => $item->vendorProductVariant ? [
                                'id' => $item->vendorProductVariant->id,
                                'sku' => $item->vendorProductVariant->sku,
                                'variant_name' => $item->vendorProductVariant->variant_name,
                                'stock' => $item->vendorProductVariant->total_stock,
                                'real_price' => $item->vendorProductVariant->price,
                                'fake_price' => $item->vendorProductVariant->price_before_discount,
                            ] : null,
                            'product' => ($item->vendorProductVariant && $item->vendorProductVariant->vendorProduct) ? [
                                'name' => $item->vendorProductVariant->vendorProduct->product->title,
                                'image' => $item->vendorProductVariant->vendorProduct->product->mainImage
                                    ? asset('storage/' . $item->vendorProductVariant->vendorProduct->product->mainImage->path)
                                    : null,
                            ] : null,
                        ];
                    });
                @endphp
                const existingVariants = @json($existingVariantsData);

                // Add existing variants to selected products
                existingVariants.forEach(function(item) {
                    if (item.variant && item.product) {
                        const variantId = item.variant.id;

                        // Add to selected products array
                        if (!selectedProducts.includes(variantId)) {
                            selectedProducts.push(variantId);
                        }

                        // Store variant details
                        selectedProductsDetails[variantId] = {
                            productName: item.product.name,
                            variantName: item.variant.variant_name || '',
                            sku: item.variant.sku,
                            stock: item.variant.stock || 0,
                            price: item.variant.real_price || '0.00',
                            priceBeforeDiscount: item.variant.fake_price || null,
                            image: item.product.image || '{{ asset("images/placeholder.png") }}',
                            specialPrice: item.special_price // Store the saved special price
                        };
                    }
                });

                // Update the alert to show existing variants
                updateSelectedProductsAlert();
            });
            @endif

            // AJAX Form Submission
            const occasionForm = document.getElementById('occasionForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');

            // Clear validation errors on input
            occasionForm.querySelectorAll('input, textarea, select').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.remove();
                        }
                    }
                });
            });

            occasionForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Disable submit button and show loading state
                submitBtn.disabled = true;
                const btnIcon = submitBtn.querySelector('i');
                const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                if (btnIcon) btnIcon.classList.add('d-none');
                if (btnText) btnText.classList.add('d-none');
                submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                // Update loading text dynamically
                const loadingText = @json(isset($occasion) ? trans('loading.updating') : trans('loading.creating'));
                const loadingSubtext = '{{ trans("loading.please_wait") }}';
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.querySelector('.loading-text').textContent = loadingText;
                    overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
                }

                // Show loading overlay
                LoadingOverlay.show();

                // Clear previous alerts
                alertContainer.innerHTML = '';

                // Remove previous validation errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                // Start progress bar animation
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                    // Prepare form data
                    const formData = new FormData(occasionForm);

                    // Send AJAX request
                    return fetch(occasionForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                })
                .then(response => {
                    // Progress to 60%
                    LoadingOverlay.animateProgressBar(60, 200);

                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Progress to 90%
                    return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
                })
                .then(data => {
                    // Complete progress bar
                    return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                        // Show success animation with dynamic message
                        const successMessage = @json(isset($occasion) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                        LoadingOverlay.showSuccess(
                            successMessage,
                            '{{ trans("loading.redirecting") }}'
                        );

                        // Success - redirect after delay
                        if (data.success) {
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            }, 2000);
                        }
                    });
                })
                .catch(error => {
                    // Hide loading overlay and reset progress bar
                    LoadingOverlay.hide();

                    // Show error message with all validation errors
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show d-block';

                    let errorHtml = `
                        <div class="d-flex align-items-center mb-2">
                            <i class="uil uil-exclamation-triangle me-2"></i>
                            <strong>${error.message || 'Validation Error'}</strong>
                        </div>
                    `;


                    errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    alert.innerHTML = errorHtml;
                    alertContainer.appendChild(alert);

                    // Scroll to top to show errors
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    // Re-enable submit button
                    submitBtn.disabled = false;
                    if (btnIcon) btnIcon.classList.remove('d-none');
                    if (btnText) btnText.classList.remove('d-none');
                    submitBtn.querySelector('.spinner-border').classList.add('d-none');
                });
            });
        });
    </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay
        :loadingText="trans('loading.processing')"
        :loadingSubtext="trans('loading.please_wait')"
    />
@endpush
