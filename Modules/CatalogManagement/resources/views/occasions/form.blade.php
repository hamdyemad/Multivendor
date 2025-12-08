@extends('layout.app')
@section('title', isset($occasion) ? trans('catalogmanagement::occasion.edit_occasion') :
    trans('catalogmanagement::occasion.add_occasion'))

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
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
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
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('catalogmanagement::occasion.occasions_management'),
                        'url' => route('admin.occasions.index'),
                    ],
                    [
                        'title' => isset($occasion)
                            ? trans('catalogmanagement::occasion.edit_occasion')
                            : trans('catalogmanagement::occasion.add_occasion'),
                    ],
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
                            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="uil uil-exclamation-triangle me-2"></i>
                                    <strong>{{ __('common.validation_errors') }}</strong>
                                </div>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="occasionForm"
                            action="{{ isset($occasion) ? route('admin.occasions.update', $occasion->id) : route('admin.occasions.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($occasion))
                                @method('PUT')
                            @endif
                            {{-- Occasion Name Fields --}}
                            <x-multilingual-input name="name" :label="trans('catalogmanagement::occasion.name')" :labelAr="'اسم العرض'" :placeholder="trans('catalogmanagement::occasion.enter_occasion_name')"
                                :placeholderAr="'اسم العرض'" :languages="$languages" :model="$occasion ?? null" :required="true" />

                            {{-- Occasion Title Fields --}}
                            <x-multilingual-input name="title" :label="trans('catalogmanagement::occasion.title')" :labelAr="'العنوان'" :placeholder="trans('catalogmanagement::occasion.enter_occasion_title')"
                                :placeholderAr="'العنوان'" :languages="$languages" :model="$occasion ?? null" />

                            {{-- Occasion Sub Title Fields --}}
                            <x-multilingual-input name="sub_title" :label="trans('catalogmanagement::occasion.sub_title')" :labelAr="'العنوان الفرعى'" :placeholder="trans('catalogmanagement::occasion.enter_occasion_sub_title')"
                                :placeholderAr="'العنوان الفرعى'" :languages="$languages" :model="$occasion ?? null" />
                            {{-- Date Fields --}}
                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="start_date" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.start_date') }}
                                        </label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            id="start_date" name="start_date"
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
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            id="end_date" name="end_date"
                                            value="{{ old('end_date', isset($occasion) ? $occasion->end_date?->format('Y-m-d') : '') }}">
                                        @error('end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- Occasion Image --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <x-image-upload id="occasion_image" name="image" :placeholder="trans('catalogmanagement::occasion.image')"
                                            :recommendedSize="trans('catalogmanagement::occasion.recommended_size')" :existingImage="isset($occasion) && $occasion->image
                                                ? asset('storage/' . $occasion->image)
                                                : null" aspectRatio="16:9" :required="true" />
                                        @error('image')
                                            <div class="invalid-feedback d-block" style="display: block !important;">
                                                {{ $message }}</div>
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
                                                <input type="checkbox" class="form-check-input" id="is_active"
                                                    name="is_active" value="1"
                                                    {{ old('is_active', $occasion->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('is_active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Vendor Selection --}}
                            @if(isAdmin())
                                <div class="row">
                                    <div class="col-md-12 mb-25">
                                        <div class="form-group">
                                            <label for="vendor_id" class="il-gray fs-14 fw-500 mb-10">
                                                {{ trans('catalogmanagement::occasion.vendor') }} <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <select name="vendor_id" id="vendor_id" class="form-control select2">
                                                <option value="">{{ trans('catalogmanagement::occasion.select_vendor') }}
                                                </option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}"
                                                        {{ old('vendor_id', $occasion->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
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
                            @else
                                {{-- Hidden vendor input for vendor users --}}
                                @php
                                    $userVendorId = auth()->user()->vendor_id ?? (auth()->user()->vendor ? auth()->user()->vendor->id : null);
                                @endphp
                                <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $userVendorId }}">
                            @endif

                            {{-- Product Variants Section --}}
                            <div class="row" id="variantsSection"
                            @if(isAdmin())
                                style="display: none;"
                            @endif
                            >
                                <div class="col-12">
                                    <h6 class="mb-20 fw-500">{{ trans('catalogmanagement::occasion.product_variants') }}
                                    </h6>
                                </div>
                                {{-- Search Input --}}
                                <div class="col-12 mb-25">
                                    <div class="form-group">
                                        <label for="product_search" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::occasion.search_products') }}
                                        </label>
                                        <input type="text" id="product_search" class="form-control"
                                               placeholder="{{ trans('catalogmanagement::occasion.type_to_search_products') }}"
                                               style="width: 100%;">
                                        <small class="text-muted">{{ trans('catalogmanagement::occasion.search_products_help') }}</small>
                                    </div>
                                </div>

                                {{-- Products Grid Container --}}
                                <div class="col-12 mb-25">
                                    <div id="products-grid" class="row" style="max-height: 500px; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 0.375rem; padding: 15px;">
                                        <div class="col-12 text-center text-muted py-5">
                                            <i class="uil uil-search fs-1 mb-2"></i>
                                            <p>{{ trans('catalogmanagement::occasion.search_products_help') }}</p>
                                        </div>
                                    </div>
                                </div>


                                {{-- Selected Products Container --}}
                                <div class="col-12 mb-25">
                                    <h6 class="mb-3 fw-500">{{ trans('catalogmanagement::occasion.selected_products') }}</h6>
                                    <div id="selected-products" class="row" style="min-height: 100px;">
                                        <div class="col-12 text-center text-muted py-3">
                                            <p>{{ trans('catalogmanagement::occasion.no_products_selected') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Variants Validation Errors --}}
                                @if ($errors->has('variants') || $errors->has('variants.*'))
                                    <div class="col-12">
                                        <div class="alert alert-danger mt-3" role="alert">
                                            <div class="d-flex align-items-center">
                                                <i class="uil uil-exclamation-triangle me-2"></i>
                                                <strong>{{ __('catalogmanagement::occasion.product_variants') }}
                                                    {{ __('common.errors') }}</strong>
                                            </div>
                                            <ul class="mb-0 mt-2">
                                                @error('variants')
                                                    <li>{{ $message }}</li>
                                                @enderror
                                                @foreach ($errors->get('variants.*') as $variantErrors)
                                                    @foreach ($variantErrors as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($occasion) && $occasion->occasionProducts->count() > 0)
                                    {{-- Existing Products Table (for edit mode) --}}
                                    @include('catalogmanagement::occasions.occasion-products-table', ['occasion' => $occasion, 'showDragHandle' => true, 'showActions' => true])
                                @endif

                            </div>

                            {{-- SEO Information Section --}}
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="p-0 fw-500 fw-bold">
                                        {{ trans('catalogmanagement::occasion.seo_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- SEO Title Fields --}}
                                        <x-multilingual-input name="seo_title" :label="trans('catalogmanagement::occasion.seo_title')" :labelAr="'عنوان ال SEO'"
                                            :placeholder="trans('catalogmanagement::occasion.enter_seo_title')" :placeholderAr="'عنوان ال SEO'" :languages="$languages" :model="$occasion ?? null" />

                                        {{-- SEO Description Fields --}}
                                        <x-multilingual-input name="seo_description" :label="trans('catalogmanagement::occasion.seo_description')" :labelAr="'وصف SEO'"
                                            :placeholder="trans('catalogmanagement::occasion.enter_seo_description')" :placeholderAr="'وصف SEO'" type="textarea" rows="3"
                                            :languages="$languages" :model="$occasion ?? null" />

                                        {{-- SEO Keywords Fields --}}
                                        <x-multilingual-input name="seo_keywords" :label="trans('catalogmanagement::occasion.seo_keywords')" :labelAr="'الكلمات المفتاحية'"
                                            :placeholder="trans('catalogmanagement::occasion.type_keyword_press_enter')" :placeholderAr="'الكلمات المفتاحية'" :tags="true" :languages="$languages"
                                            :model="$occasion ?? null" />
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.occasions.index') }}"
                                    class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ trans('common.cancel') }}
                                </a>
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-default btn-squared text-capitalize"
                                    style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span>{{ isset($occasion) ? trans('common.update') : trans('common.save') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
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
            // Check if current user is a vendor
            const currentUserType = {{ auth()->user()->user_type_id ?? 'null' }};
            const vendorUserTypes = [3, 4]; // VENDOR_TYPE = 3, VENDOR_USER_TYPE = 4
            const isVendorUser = vendorUserTypes.includes(currentUserType);

            // Get vendor ID from hidden input (for vendor users) or will be selected (for admin users)
            let userVendorId = null;
            if (isVendorUser) {
                userVendorId = $('#vendor_id').val();
                console.log('Vendor user detected. Vendor ID:', userVendorId);
            }

            // Initialize Select2 for vendor dropdown (only if not vendor user)
            if (!isVendorUser) {
                $('#vendor_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: '{{ trans('common.select') }}',
                    allowClear: true,
                    width: '100%'
                });
            }

            // If vendor user, show variants section immediately with their vendor ID
            if (isVendorUser && userVendorId) {
                $('#variantsSection').show();
                console.log('Vendor user - variants section shown');
            }

            // Show/hide variants section based on vendor selection (only for admin users)
            if (!isVendorUser) {
                $('#vendor_id').on('change', function() {
                    const vendorId = $(this).val();
                    if (vendorId) {
                        $('#variantsSection').slideDown();
                        // Clear search and products grid when vendor changes
                        $('#product_search').val('');
                        $('#products-grid').html(`
                            <div class="col-12 text-center text-muted py-5">
                                <i class="uil uil-search fs-1 mb-2"></i>
                                <p>{{ trans('catalogmanagement::occasion.search_products_help') }}</p>
                            </div>
                        `);
                    } else {
                        $('#variantsSection').slideUp();
                        $('#product_search').val('');
                        $('#products-grid').html(`
                            <div class="col-12 text-center text-muted py-5">
                                <i class="uil uil-search fs-1 mb-2"></i>
                                <p>{{ trans('catalogmanagement::occasion.search_products_help') }}</p>
                            </div>
                        `);
                    }
                });
            }

            // Global variables for product selection
            let selectedProducts = [];
            let selectedProductsDetails = {};
            let allProducts = [];

            // Search products function
            function searchProducts(searchTerm = '', page = 1) {
                const vendorId = $('#vendor_id').val();
                console.log('Vendor ID:', vendorId);

                // For admin users, vendor_id is required
                if (!isVendorUser && !vendorId) {
                    alert('{{ trans('catalogmanagement::occasion.select_vendor') }}');
                    return;
                }

                // For vendor users, use their vendor_id
                if (isVendorUser && !vendorId) {
                    console.error('Vendor user but no vendor_id found');
                    alert('{{ trans('catalogmanagement::occasion.select_vendor') }}');
                    return;
                }

                $.ajax({
                    url: '/api/products',
                    type: 'GET',
                    headers: {
                        'lang': "{{ app()->getLocale() }}"
                    },
                    data: {
                        search: searchTerm,
                        vendor_id: vendorId,
                        country_id: $("meta[name='current_country_id']").attr("content"),
                        page: page,
                        per_page: 15,
                        paginated: 'ok',
                    },
                    success: function(response) {
                        console.log('Products response:', response);

                        if (response.status && response.data && response.data.length > 0) {
                            allProducts = [];
                            let productsHtml = '';

                            response.data.forEach(function(vendorProduct) {
                                const productImage = vendorProduct.image || '{{ asset('assets/img/logo.png') }}';
                                const productName = vendorProduct.name || 'N/A';
                                const variants = vendorProduct.variants || [];

                                if (variants.length > 0) {
                                    variants.forEach(function(variant) {
                                        const variantId = variant.id;
                                        const variantName = variant.variant_name || variant.name || '';
                                        const variantSku = variant.sku || 'N/A';
                                        const stock = variant.stock || 0;
                                        const price = variant.real_price;
                                        const priceBeforeDiscount = variant.fake_price;
                                        const isSelected = selectedProducts.includes(variantId);

                                        allProducts.push({
                                            id: variantId,
                                            productName: productName,
                                            variantName: variantName,
                                            sku: variantSku,
                                            stock: stock,
                                            price: price,
                                            priceBeforeDiscount: priceBeforeDiscount,
                                            image: productImage
                                        });

                                        productsHtml += `
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100 product-card" data-product-id="${variantId}">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex gap-2 mb-2">
                                                            <img src="${productImage}" alt="${productName}"
                                                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover; flex-shrink: 0;">
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1 fw-semibold text-truncate">${productName}</h6>
                                                                ${variantName ? `<small class="text-primary d-block">${variantName}</small>` : ''}
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column gap-1 mb-2">
                                                            <small class="text-muted"><strong>{{ trans('catalogmanagement::occasion.sku') }}:</strong> ${variantSku}</small>
                                                            <small class="text-muted"><strong>{{ trans('common.stock') }}:</strong> ${stock}</small>
                                                            <small class="text-muted"><strong>{{ trans('catalogmanagement::occasion.original_price') }}:</strong> ${price} {{ currency() }}</small>
                                                            ${priceBeforeDiscount ? `<small class="text-muted"><strong>{{ trans('common.before_discount') }}:</strong> ${priceBeforeDiscount} {{ currency() }}</small>` : ''}
                                                        </div>
                                                        <button type="button" class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-primary'} w-100 add-product-btn"
                                                                data-product-id="${variantId}" ${isSelected ? 'disabled' : ''}>
                                                            <i class="uil ${isSelected ? 'uil-check' : 'uil-plus'} me-1"></i>${isSelected ? '{{ trans('common.added') }}' : '{{ trans('common.add') }}'}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                }
                            });

                            $('#products-grid').html(productsHtml);
                        } else {
                            $('#products-grid').html(`
                                <div class="col-12 text-center text-muted py-5">
                                    <i class="uil uil-inbox fs-1 mb-2"></i>
                                    <p>{{ trans('catalogmanagement::occasion.no_products_found') }}</p>
                                </div>
                            `);
                        }
                    },
                    error: function(error) {
                        console.error('Error loading products:', error);
                        $('#products-grid').html(`
                            <div class="col-12 text-center text-danger py-5">
                                <i class="uil uil-exclamation-triangle fs-1 mb-2"></i>
                                <p>{{ trans('catalogmanagement::occasion.error_loading_data') }}</p>
                            </div>
                        `);
                    }
                });
            }

            // Search input handler
            $('#product_search').on('keyup', function() {
                const searchTerm = $(this).val();
                searchProducts(searchTerm, 1);
            });

            // Add product button handler
            $(document).on('click', '.add-product-btn', function(e) {
                e.preventDefault();
                const productId = parseInt($(this).data('product-id'));
                const product = allProducts.find(p => p.id === productId);

                if (product && !selectedProducts.includes(productId)) {
                    selectedProducts.push(productId);
                    selectedProductsDetails[productId] = product;
                    updateSelectedProductsDisplay();

                    // Update button state
                    $(this).addClass('btn-success').removeClass('btn-primary').prop('disabled', true);
                    $(this).html('<i class="uil uil-check me-1"></i>Added');
                }
            });

            // Update selected products display
            function updateSelectedProductsDisplay() {
                const count = selectedProducts.length;

                if (count > 0) {
                    let selectedHtml = '';
                    selectedProducts.forEach(function(variantId, index) {
                        const variant = selectedProductsDetails[variantId];
                        if (variant) {
                            selectedHtml += `
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-0 shadow-sm h-100 product-card" data-product-id="${variantId}">
                                        <div class="card-body p-3">
                                            <div class="d-flex gap-2 mb-2">
                                                <img src="${variant.image}" alt="${variant.productName}"
                                                     class="rounded" style="width: 50px; height: 50px; object-fit: cover; flex-shrink: 0;">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold text-truncate">${variant.productName}</h6>
                                                    ${variant.variantName ? `<small class="text-primary d-block">${variant.variantName}</small>` : ''}
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column gap-1 mb-2">
                                                <small class="text-muted"><strong>{{ trans('catalogmanagement::occasion.sku') }}:</strong> ${variant.sku}</small>
                                                <small class="text-muted"><strong>{{ trans('common.stock') }}:</strong> ${variant.stock}</small>
                                                <small class="text-muted"><strong>{{ trans('catalogmanagement::occasion.original_price') }}:</strong> ${variant.price} {{ currency() }}</small>
                                                ${variant.priceBeforeDiscount ? `<small class="text-muted"><strong>{{ trans('common.before_discount') }}:</strong> ${variant.priceBeforeDiscount} {{ currency() }}</small>` : ''}
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fs-13 fw-500">{{ trans('catalogmanagement::occasion.special_price') }}</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm special-price-input"
                                                       placeholder="{{ trans('catalogmanagement::occasion.special_price') }}" data-product-id="${variantId}"
                                                       value="${variant.specialPrice || ''}">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger w-100 remove-selected-btn"
                                                    data-product-id="${variantId}">
                                                <i class="uil uil-trash-alt me-1"></i>{{ trans('common.remove') }}
                                            </button>
                                            <input type="hidden" name="variants[${index}][vendor_product_variant_id]" value="${variantId}">
                                            <input type="hidden" name="variants[${index}][special_price]" class="special-price-hidden-${variantId}" value="">
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });

                    $('#selected-products').html(selectedHtml);
                } else {
                    $('#selected-products').html(`
                        <div class="col-12 text-center text-muted py-3">
                            <p>{{ trans('catalogmanagement::occasion.no_products_selected') }}</p>
                        </div>
                    `);
                }
            }

            // Remove product from selected list
            $(document).on('click', '.remove-selected-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productId = parseInt($(this).data('product-id'));
                console.log('Removing product:', productId);

                // Remove from selected products array
                selectedProducts = selectedProducts.filter(id => id !== productId);
                delete selectedProductsDetails[productId];

                // Update button state in products grid
                $(`.add-product-btn[data-product-id="${productId}"]`)
                    .removeClass('btn-success').addClass('btn-primary')
                    .prop('disabled', false)
                    .html('<i class="uil uil-plus me-1"></i>Add');

                updateSelectedProductsDisplay();
                console.log('Updated selected products:', selectedProducts);
            });

            // Handle special price input change
            $(document).on('change keyup', '.special-price-input', function() {
                const productId = parseInt($(this).data('product-id'));
                const specialPrice = $(this).val();

                // Update the hidden input
                $(`.special-price-hidden-${productId}`).val(specialPrice);

                // Update the stored product details
                if (selectedProductsDetails[productId]) {
                    selectedProductsDetails[productId].specialPrice = specialPrice;
                }

                console.log('Updated special price for product', productId, ':', specialPrice);
            });



                // Trigger change on page load if vendor is already selected
                if ($('#vendor_id').val()) {
                    $('#variantsSection').show();
                }

                @if (isset($occasion) && $occasion->occasionProducts->count() > 0)
                    // Load existing occasion products for edit mode
                    $(document).ready(function() {
                        @php
                            $existingVariantsData = $occasion->occasionProducts->map(function ($item) {
                                return [
                                    'id' => $item->vendor_product_variant_id,
                                    'special_price' => $item->special_price,
                                    'position' => $item->position,
                                    'variant' => $item->vendorProductVariant
                                        ? [
                                            'id' => $item->vendorProductVariant->id,
                                            'sku' => $item->vendorProductVariant->sku,
                                            'variant_name' => $item->vendorProductVariant->variant_name,
                                            'stock' => $item->vendorProductVariant->total_stock,
                                            'real_price' => $item->vendorProductVariant->price,
                                            'fake_price' => $item->vendorProductVariant->price_before_discount,
                                        ]
                                        : null,
                                    'product' =>
                                        $item->vendorProductVariant && $item->vendorProductVariant->vendorProduct
                                            ? [
                                                'name' => $item->vendorProductVariant->vendorProduct->product->title,
                                                'image' => $item->vendorProductVariant->vendorProduct->product->mainImage ? asset('storage/' . $item->vendorProductVariant->vendorProduct->product->mainImage->path) : null,
                                            ]
                                            : null,
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
                                    image: item.product.image ||
                                        '{{ asset('assets/img/default.png') }}',
                                    specialPrice: item
                                        .special_price // Store the saved special price
                                };
                            }
                        });

                        // Update the display with existing variants
                        updateSelectedProductsDisplay();

                        // Set the special price values for existing products
                        existingVariants.forEach(function(item) {
                            const variantId = item.variant.id;
                            const specialPrice = item.special_price;
                            if (specialPrice) {
                                $(`.special-price-input[data-product-id="${variantId}"]`).val(specialPrice);
                                $(`.special-price-hidden-${variantId}`).val(specialPrice);
                            }
                        });
                    });
                @endif


                $("#submitBtn").on("click", function(e) {
                    e.preventDefault();
                    const form = $("#occasionForm")[0];
                    const submitBtn = $("#submitBtn");
                    const alertContainer = $("#alertContainer");
                    const spinner = submitBtn.find(".spinner-border");
                    spinner.removeClass("d-none");
                    submitBtn.prop("disabled", true);

                    // Use FormData to include file uploads
                    const formData = new FormData(form);

                    $.ajax({
                        url: $(form).attr("action"),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            spinner.addClass("d-none");
                            submitBtn.prop("disabled", false);

                            // Show success message with toastr
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message, 'Success');
                            } else {
                                alertContainer.html('<div class="alert alert-success">' + response.message + '</div>');
                            }

                            // Redirect after 2 seconds
                            setTimeout(function() {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            }, 2000);
                        },
                        error: function(xhr, status, error) {
                            spinner.addClass("d-none");
                            submitBtn.prop("disabled", false);

                            const response = xhr.responseJSON;
                            const errors = response.errors || {};

                            // Show error message with toastr
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'An error occurred', 'Error');
                            }

                            // Always show detailed error list
                            let errorHtml = "<div class='alert alert-danger alert-dismissible fade show d-block' role='alert'>";
                            errorHtml += "<strong><i class='uil uil-exclamation-triangle me-2'></i>Validation Errors:</strong>";
                            errorHtml += "<ul class='mb-0 mt-2'>";

                            for (const key in errors) {
                                const errorMessages = errors[key];
                                if (Array.isArray(errorMessages)) {
                                    errorMessages.forEach(function(msg) {
                                        errorHtml += "<li>" + msg + "</li>";
                                    });
                                } else {
                                    errorHtml += "<li>" + errorMessages + "</li>";
                                }
                            }

                            errorHtml += "</ul>";
                            errorHtml += "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
                            errorHtml += "</div>";

                            alertContainer.html(errorHtml);
                            window.scrollTo(0, 0);
                        }
                    });


                });
        </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay :loadingText="trans('loading.processing')" :loadingSubtext="trans('loading.please_wait')" />
@endpush
