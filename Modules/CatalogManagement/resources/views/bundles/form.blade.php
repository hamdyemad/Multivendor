@extends('layout.app')
@section('title', isset($bundle) ? trans('catalogmanagement::bundle.edit_bundle') :
    trans('catalogmanagement::bundle.add_bundle'))

    @push('styles')
        <style>
            /* Alert styling */
            .alert {
                border: none;
                border-radius: 8px;
            }

            .alert-danger {
                background-color: rgba(220, 53, 69, 0.1);
                color: #721c24;
                border-left: 4px solid #dc3545;
            }

            /* Product Card Styling */
            .product-card {
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                padding: 15px;
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

            /* Products Container */
            #products-grid {
                max-height: 500px;
                overflow-y: auto;
            }

            #products-grid::-webkit-scrollbar {
                width: 6px;
            }

            #products-grid::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            #products-grid::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }

            #products-grid::-webkit-scrollbar-thumb:hover {
                background: #a1a1a1;
            }

            /* Disabled/Out of Stock Card */
            .product-card.disabled-card {
                cursor: not-allowed;
                background-color: #f8f9fa;
                border-color: #dee2e6;
                opacity: 0.7;
            }

            .product-card.disabled-card:hover {
                border-color: #dee2e6;
                box-shadow: none;
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
                        'title' => trans('catalogmanagement::bundle.bundles_management'),
                        'url' => route('admin.bundles.index'),
                    ],
                    [
                        'title' => isset($bundle)
                            ? trans('catalogmanagement::bundle.edit_bundle')
                            : trans('catalogmanagement::bundle.add_bundle'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($bundle) ? trans('catalogmanagement::bundle.edit_bundle') : trans('catalogmanagement::bundle.add_bundle') }}
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

                        <form id="bundleForm"
                            action="{{ isset($bundle) ? route('admin.bundles.update', $bundle->id) : route('admin.bundles.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($bundle))
                                @method('PUT')
                            @endif

                            {{-- Bundle Name Fields --}}
                            <x-multilingual-input name="name" :label="trans('catalogmanagement::bundle.name')" :labelAr="'اسم الحزمة'" :placeholder="trans('catalogmanagement::bundle.enter_name')"
                                :placeholderAr="'اسم الحزمة'" :languages="$languages" :model="$bundle ?? null" :required="true" />

                            {{-- Bundle Description Fields --}}
                            <x-multilingual-input name="description" :label="trans('catalogmanagement::bundle.description')" :labelAr="'الوصف'" :placeholder="trans('catalogmanagement::bundle.enter_description')"
                                :placeholderAr="'الوصف'" type="textarea" rows="3" :languages="$languages" :model="$bundle ?? null" />

                            {{-- SKU and Image --}}
                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="sku" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::bundle.sku') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            id="sku" name="sku"
                                            value="{{ old('sku', isset($bundle) ? $bundle->sku : '') }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="bundle_category_id" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::bundle.category') }} <span
                                                class="text-danger">*</span>
                                        </label>
                                        <select name="bundle_category_id" id="bundle_category_id"
                                            class="form-control select2" required>
                                            <option value="">{{ trans('catalogmanagement::bundle.select_category') }}
                                            </option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('bundle_category_id', $bundle->bundle_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->getTranslation('name', 'en') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bundle_category_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <x-image-upload id="bundle_image" name="image" :placeholder="trans('catalogmanagement::bundle.image')"
                                            :recommendedSize="trans('catalogmanagement::bundle.recommended_size')" :existingImage="isset($bundle) && $bundle->image
                                                ? asset('storage/' . $bundle->image)
                                                : null" aspectRatio="16:9" />
                                        @error('image')
                                            <div class="invalid-feedback d-block" style="display: block !important;">
                                                {{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('catalogmanagement::bundle.is_active') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" class="form-check-input" id="is_active"
                                                    name="is_active" value="1"
                                                    {{ old('is_active', $bundle->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('is_active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                            {{-- SEO Information Section --}}
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="p-0 fw-500 fw-bold">
                                        {{ trans('catalogmanagement::bundle.seo_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- SEO Title Fields --}}
                                        <x-multilingual-input name="seo_title" :label="trans('catalogmanagement::bundle.seo_title')" :labelAr="'عنوان ال SEO'"
                                            :placeholder="trans('catalogmanagement::bundle.enter_seo_title')" :placeholderAr="'عنوان ال SEO'" :languages="$languages" :model="$bundle ?? null" />

                                        {{-- SEO Description Fields --}}
                                        <x-multilingual-input name="seo_description" :label="trans('catalogmanagement::bundle.seo_description')" :labelAr="'وصف SEO'"
                                            :placeholder="trans('catalogmanagement::bundle.enter_seo_description')" :placeholderAr="'وصف SEO'" type="textarea" rows="3"
                                            :languages="$languages" :model="$bundle ?? null" />

                                        {{-- SEO Keywords Fields --}}
                                        <x-multilingual-input name="seo_keywords" :label="trans('catalogmanagement::bundle.seo_keywords')" :labelAr="'كلمات مفتاحية SEO'"
                                            :placeholder="trans('catalogmanagement::bundle.enter_seo_keywords')" :placeholderAr="'كلمات مفتاحية SEO'" :tags="true" :languages="$languages"
                                            :model="$bundle ?? null" />
                                    </div>
                                </div>
                            </div>

                            {{-- Vendor and Category Selection --}}
                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <div class="form-group">
                                        @if ($isAdmin)
                                            {{-- Admin: Show vendor dropdown --}}
                                            <label for="vendor_id" class="il-gray fs-14 fw-500 mb-10">
                                                {{ trans('catalogmanagement::bundle.vendor') }} <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <select name="vendor_id" id="vendor_id" class="form-control select2" required>
                                                <option value="">
                                                    {{ trans('catalogmanagement::bundle.select_vendor') }}</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}"
                                                        {{ old('vendor_id', $bundle->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
                                                        {{ $vendor->getTranslation('name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vendor_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        @else
                                            {{-- Vendor: Auto-set vendor ID and hide dropdown --}}
                                            <input type="hidden" name="vendor_id" id="vendor_id"
                                                value="{{ $userVendorId }}">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Bundle Products Section --}}
                            <div class="row" id="productsSection" style="display: none;">
                                <div class="col-12">
                                    <h6 class="mb-20 fw-500">{{ trans('catalogmanagement::bundle.bundle_products') }}</h6>
                                </div>

                                {{-- Search Input --}}
                                <div class="col-12 mb-25">
                                    <div class="form-group">
                                        <label for="product_search" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::bundle.search_products') }}
                                        </label>
                                        <input type="text" id="product_search" class="form-control"
                                            placeholder="{{ trans('catalogmanagement::bundle.type_to_search_products') }}"
                                            style="width: 100%;">
                                        <small
                                            class="text-muted">{{ trans('catalogmanagement::bundle.search_products_help') }}</small>
                                    </div>
                                </div>

                                {{-- Products Grid Container --}}
                                <div class="col-12 mb-25">
                                    <div id="products-grid" class="row"
                                        style="max-height: 500px; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 0.375rem; padding: 15px;">
                                        <div class="col-12 text-center text-muted py-5">
                                            <i class="uil uil-search fs-1 mb-2"></i>
                                            <p>{{ trans('catalogmanagement::bundle.search_products_help') }}</p>
                                        </div>
                                    </div>

                                    {{-- Loading Spinner --}}
                                    <div id="products-loader" style="display: none; text-align: center; padding: 20px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">{{ __('common.loading') }}</span>
                                        </div>
                                        <p class="text-muted mt-2">
                                            {{ trans('catalogmanagement::bundle.loading_products') }}</p>
                                    </div>
                                </div>

                                {{-- Selected Products Container --}}
                                <div class="col-12 mb-25">
                                    <h6 class="mb-3 fw-500">{{ trans('catalogmanagement::bundle.selected_products') }}
                                    </h6>
                                    <div id="selected-products" class="row" style="min-height: 100px;">
                                        <div class="col-12 text-center text-muted py-3">
                                            <p>{{ trans('catalogmanagement::bundle.no_products_selected') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            {{-- Form Actions --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button id="submitBtn" class="btn btn-primary btn-default btn-squared">
                                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                                aria-hidden="true"></span>
                                            {{ isset($bundle) ? trans('catalogmanagement::bundle.update_bundle') : trans('catalogmanagement::bundle.create_bundle') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const form = $('#bundleForm');
            const submitBtn = $('#submitBtn');
            const alertContainer = $('#alertContainer');

            // Global variables for product selection
            let selectedProducts = [];
            let selectedProductsDetails = {};
            let allProducts = [];

            // Pagination variables
            let currentPage = 1;
            let lastPage = 1;
            let isLoadingMore = false;
            let currentSearchTerm = '';

            // Initialize form with existing bundle data (on edit)
            $(document).ready(function() {
                // Check if user is vendor (not admin)
                const isAdmin = @json($isAdmin ?? true);
                const userVendorId = @json($userVendorId ?? null);

                // If vendor user, automatically show products section (but don't load products yet)
                if (!isAdmin && userVendorId) {
                    $('#productsSection').show();
                }

                @if (isset($bundleResource) &&
                        isset($bundleResource['bundle_products']) &&
                        count($bundleResource['bundle_products']) > 0)
                    // Show products section
                    $('#productsSection').show();

                    // Load existing bundle products from resource
                    const existingProducts = @json($bundleResource['bundle_products']);
                    const currencySymbol = '{{ currency() }}';
                    const defaultImage = '{{ asset('assets/img/default.png') }}';
                    const defaultVendorLogo = '{{ asset('assets/img/default-vendor.png') }}';

                    existingProducts.forEach(function(bundleProduct) {
                        const variantId = bundleProduct.vendor_product_variant_id;

                        // Access clean data from BundleResource
                        const vpv = bundleProduct.vendor_product_variant;
                        console.log('Bundle Product Variant:', vpv);
                        
                        let productName = vpv?.product?.name || '-';
                        let variantName = vpv?.variant_configuration?.name || '-';
                        let variantKey = vpv?.variant_configuration?.key?.name || '';
                        let sku = vpv?.sku || '-';
                        let productImage = vpv?.product?.image || defaultImage;
                        let remainingStock = vpv?.remaining_stock ?? 0;
                        let vendorName = vpv?.vendor?.name || 'N/A';
                        let vendorLogo = vpv?.vendor?.logo || defaultVendorLogo;

                        selectedProducts.push(variantId);
                        selectedProductsDetails[variantId] = {
                            id: variantId,
                            name: productName + ' - ' + variantName,
                            variantKey: variantKey,
                            image: productImage,
                            sku: sku,
                            stock: remainingStock,
                            price: bundleProduct.price,
                            vendorName: vendorName,
                            vendorLogo: vendorLogo,
                            limit_quantity: bundleProduct.limitation_quantity,
                            min_quantity: bundleProduct.min_quantity
                        };
                    });

                    // Update display
                    updateSelectedProducts();
                @endif
            });

            // Show/hide products section based on vendor selection
            $('#vendor_id').on('change', function() {
                const vendorId = $(this).val();
                if (vendorId) {
                    $('#productsSection').slideDown();
                    $('#product_search').val('');
                    $('#products-grid').html(`
                        <div class="col-12 text-center text-muted py-5">
                            <i class="uil uil-search fs-1 mb-2"></i>
                            <p>{{ trans('catalogmanagement::bundle.search_products_help') }}</p>
                        </div>
                    `);
                } else {
                    $('#productsSection').slideUp();
                    $('#product_search').val('');
                    selectedProducts = [];
                    selectedProductsDetails = {};
                    $('#selected-products').html(`
                        <div class="col-12 text-center text-muted py-3">
                            <p>{{ trans('catalogmanagement::bundle.no_products_selected') }}</p>
                        </div>
                    `);
                }
            });

            // Search products function
            function searchProducts(searchTerm = '', page = 1) {
                const vendorId = $('#vendor_id').val();

                if (!vendorId) {
                    alert('{{ trans('catalogmanagement::bundle.select_vendor_first') }}');
                    return;
                }

                // Reset pagination on new search
                if (page === 1) {
                    currentPage = 1;
                    currentSearchTerm = searchTerm;
                    $('#products-grid').html('');
                }

                // Show loader
                if (page === 1) {
                    $('#products-loader').show();
                    $('#products-grid').hide();
                }

                isLoadingMore = true;

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
                        per_page: 10,
                        page: page,
                        paginated: 'ok'
                    },
                    success: function(response) {
                        // Hide loader
                        $('#products-loader').hide();
                        $('#products-grid').show();

                        if (response.status && response.data && response.data.length > 0) {
                            // Store pagination info
                            lastPage = response.last_page || 1;
                            currentPage = response.current_page || page;

                            let productsHtml = '';

                            response.data.forEach(function(vendorProduct) {
                                const productImage = vendorProduct.image ||
                                    '{{ asset('assets/img/logo.png') }}';
                                const productName = vendorProduct.name || 'N/A';
                                const variants = vendorProduct.variants || [];
                                
                                // Get vendor info
                                const vendor = vendorProduct.vendor || {};
                                const vendorName = vendor.name || 'N/A';
                                const vendorLogo = vendor.logo || '{{ asset('assets/img/default-vendor.png') }}';

                                if (variants.length > 0) {
                                    variants.forEach(function(variant) {
                                        const variantId = variant.id;
                                        const variantName = variant.variant_name || variant.name || '';
                                        const variantKey = variant.variant_key || '';
                                        const variantValue = variant.variant_value || variantName || '';
                                        const variantSku = variant.sku || 'N/A';
                                        // Use remaining_stock instead of stock
                                        const remainingStock = variant.remaining_stock ?? variant.stock ?? 0;
                                        const price = variant.real_price;
                                        const isSelected = selectedProducts.includes(variantId);
                                        const isOutOfStock = remainingStock <= 0;

                                        // Only add to allProducts if not already there
                                        if (!allProducts.find(p => p.id == variantId)) {
                                            allProducts.push({
                                                id: variantId,
                                                productName: productName,
                                                variantName: variantName,
                                                variantKey: variantKey,
                                                variantValue: variantValue,
                                                sku: variantSku,
                                                stock: remainingStock,
                                                price: price,
                                                image: productImage,
                                                vendorName: vendorName,
                                                vendorLogo: vendorLogo
                                            });
                                        }

                                        const selectedClass = isSelected ? 'selected' : '';
                                        const disabledClass = isOutOfStock ? 'disabled-card' : '';
                                        const disabledAttr = isOutOfStock ? 'data-disabled="true"' : '';
                                        const currencySymbol = '{{ currency() }}';
                                        
                                        productsHtml += `
                                            <div class="col-md-6 mb-3">
                                                <div class="product-card ${selectedClass} ${disabledClass}" data-variant-id="${variantId}" ${disabledAttr}>
                                                    <div class="d-flex">
                                                        <img src="${productImage}" alt="${productName}" class="product-image me-3" style="${isOutOfStock ? 'opacity: 0.5;' : ''}">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1" style="${isOutOfStock ? 'opacity: 0.5;' : ''}">${productName}</h6>
                                                            <small class="text-muted d-block"><strong>${variantKey}:</strong> ${variantValue}</small>
                                                            <small class="text-muted d-block">SKU: ${variantSku}</small>
                                                            <small class="text-muted d-block">{{ trans('catalogmanagement::bundle.remaining_stock') }}: <span class="${remainingStock > 0 ? 'text-success' : 'text-danger fw-bold'}">${remainingStock}${isOutOfStock ? ' ({{ trans("catalogmanagement::bundle.out_of_stock") }})' : ''}</span></small>
                                                            <small class="text-success d-block">{{ trans('catalogmanagement::bundle.price') }}: ${price} ${currencySymbol}</small>
                                                            <div class="d-flex align-items-center mt-2 pt-2 border-top">
                                                                <img src="${vendorLogo}" alt="${vendorName}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                                                <small class="text-primary fw-500">${vendorName}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                }
                            });

                            // Append products to grid (not replace)
                            if (page === 1) {
                                $('#products-grid').html(productsHtml);
                            } else {
                                $('#products-grid').append(productsHtml);
                            }

                            // Attach click handlers to product cards (use off() to prevent duplicate handlers)
                            $('#products-grid').off('click', '.product-card').on('click', '.product-card', function() {
                                // Ignore clicks on disabled (out of stock) cards
                                if ($(this).data('disabled') === true) {
                                    return;
                                }
                                
                                const variantId = parseInt($(this).data('variant-id'));
                                const product = allProducts.find(p => parseInt(p.id) === variantId);
                                
                                if (!product) {
                                    console.error('Product not found for variant ID:', variantId);
                                    return;
                                }

                                if (selectedProducts.includes(variantId)) {
                                    selectedProducts = selectedProducts.filter(id => id != variantId);
                                    delete selectedProductsDetails[variantId];
                                    $(this).removeClass('selected');
                                } else {
                                    selectedProducts.push(variantId);
                                    selectedProductsDetails[variantId] = {
                                        id: variantId,
                                        name: product.productName + ' - ' + product.variantName,
                                        variantKey: product.variantKey,
                                        image: product.image,
                                        sku: product.sku,
                                        stock: product.stock,
                                        price: product.price,
                                        vendorName: product.vendorName,
                                        vendorLogo: product.vendorLogo,
                                        limit_quantity: null,
                                        min_quantity: 1
                                    };
                                    $(this).addClass('selected');
                                }

                                updateSelectedProducts();
                            });
                        } else if (page === 1) {
                            $('#products-loader').hide();
                            $('#products-grid').show();
                            $('#products-grid').html(`
                                <div class="col-12 text-center text-muted py-5">
                                    <i class="uil uil-inbox fs-1 mb-2"></i>
                                    <p>{{ trans('catalogmanagement::bundle.no_products_found') }}</p>
                                </div>
                            `);
                        }

                        isLoadingMore = false;
                    },
                    error: function() {
                        $('#products-loader').hide();
                        $('#products-grid').show();
                        if (currentPage === 1) {
                            $('#products-grid').html(`
                                <div class="col-12 text-center text-danger py-5">
                                    <i class="uil uil-exclamation-triangle fs-1 mb-2"></i>
                                    <p>{{ trans('catalogmanagement::bundle.error_loading_products') }}</p>
                                </div>
                            `);
                        }
                        isLoadingMore = false;
                    }
                });
            }

            // Infinite scroll functionality
            $('#products-grid').on('scroll', function() {
                // Check if scrolled to bottom
                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 50) {
                    // Load next page if available and not already loading
                    if (currentPage < lastPage && !isLoadingMore) {
                        searchProducts(currentSearchTerm, currentPage + 1);
                    }
                }
            });

            // Update selected products display
            function updateSelectedProducts() {
                if (selectedProducts.length === 0) {
                    $('#selected-products').html(`
                        <div class="col-12 text-center text-muted py-3">
                            <p>{{ trans('catalogmanagement::bundle.no_products_selected') }}</p>
                        </div>
                    `);
                    return;
                }

                let html = '';
                selectedProducts.forEach(function(variantId) {
                    const product = selectedProductsDetails[variantId];
                    const vendorLogo = product.vendorLogo || '{{ asset('assets/img/default-vendor.png') }}';
                    const vendorName = product.vendorName || 'N/A';
                    const stockClass = product.stock > 0 ? 'text-success' : 'text-danger';
                    
                    html += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-0 shadow-sm h-100 product-card">
                                <div style="width: 100%; height: 180px; overflow: hidden; border-radius: 8px 8px 0 0;">
                                    <img src="${product.image}" alt="${product.name}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title mb-2 fw-semibold">${product.name}</h6>
                                    
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="${vendorLogo}" alt="${vendorName}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                        <small class="text-primary fw-500">${vendorName}</small>
                                    </div>

                                    <div class="mb-3 pb-3 border-bottom">
                                        <small class="text-muted d-block"><strong>SKU:</strong> ${product.sku}</small>
                                        <small class="d-block"><strong>{{ trans('catalogmanagement::bundle.remaining_stock') }}:</strong> <span class="${stockClass}">${product.stock}</span></small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-500">{{ trans('catalogmanagement::bundle.price') }}</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm product-price"
                                               data-variant-id="${variantId}" value="${product.price}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-500">{{ trans('catalogmanagement::bundle.limit_quantity') }}</label>
                                        <input type="number" class="form-control form-control-sm product-limit-qty"
                                               data-variant-id="${variantId}" value="${product.limit_quantity || 1}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-500">{{ trans('catalogmanagement::bundle.min_quantity') }}</label>
                                        <input type="number" class="form-control form-control-sm product-min-qty"
                                               data-variant-id="${variantId}" value="${product.min_quantity}">
                                    </div>

                                    <button type="button" class="btn btn-danger btn-sm w-100 remove-product"
                                            data-variant-id="${variantId}">
                                        <i class="uil uil-trash-alt me-1"></i> {{ trans('common.remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#selected-products').html(html);

                // Attach event handlers
                $('.product-price').on('change', function() {
                    const variantId = $(this).data('variant-id');
                    selectedProductsDetails[variantId].price = $(this).val();
                });

                $('.product-limit-qty').on('change', function() {
                    const variantId = $(this).data('variant-id');
                    selectedProductsDetails[variantId].limit_quantity = $(this).val() || null;
                });

                $('.product-min-qty').on('change', function() {
                    const variantId = $(this).data('variant-id');
                    selectedProductsDetails[variantId].min_quantity = $(this).val();
                });

                $('.remove-product').on('click', function() {
                    const variantId = $(this).data('variant-id');
                    selectedProducts = selectedProducts.filter(id => id != variantId);
                    delete selectedProductsDetails[variantId];
                    $('.product-card[data-variant-id="' + variantId + '"]').removeClass('selected');
                    updateSelectedProducts();
                });
            }

            // Search input handler
            $('#product_search').on('keyup', function() {
                const searchTerm = $(this).val();
                searchProducts(searchTerm);
            });

            // Form submission
            submitBtn.on('click', function(e) {
                e.preventDefault();
                const spinner = submitBtn.find('.spinner-border');
                spinner.removeClass('d-none');
                submitBtn.prop('disabled', true);

                // Sync CKEditor data to textareas before form submission
                if (typeof CKEDITOR !== 'undefined') {
                    for (let instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                }

                const formData = new FormData(form[0]);

                // Ensure image is included if it exists
                const imageInput = form.find('input[name="image"]')[0];
                if (imageInput && imageInput.files.length > 0) {
                    formData.set('image', imageInput.files[0]);
                }

                // Add selected products to form data
                selectedProducts.forEach(function(variantId, index) {
                    const product = selectedProductsDetails[variantId];
                    if (!product) {
                        console.error('Product details not found for variant ID:', variantId);
                        return;
                    }
                    formData.append(`bundle_products[${index}][vendor_product_variant_id]`, variantId);
                    formData.append(`bundle_products[${index}][price]`, product.price || 0);
                    formData.append(`bundle_products[${index}][limitation_quantity]`, product.limit_quantity || 1);
                    formData.append(`bundle_products[${index}][min_quantity]`, product.min_quantity || 1);
                });

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        spinner.addClass('d-none');
                        submitBtn.prop('disabled', false);

                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message, 'Success');
                        }

                        setTimeout(function() {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }, 1500);
                    },
                    error: function(xhr) {
                        spinner.addClass('d-none');
                        submitBtn.prop('disabled', false);

                        const response = xhr.responseJSON;
                        const errors = response.errors || {};

                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'An error occurred', 'Error');
                        }

                        let errorHtml =
                            "<div class='alert alert-danger alert-dismissible fade show d-block' role='alert'>";
                        errorHtml += "<div class='d-flex align-items-center'>";
                        errorHtml += "<i class='uil uil-exclamation-triangle me-2'></i>";
                        errorHtml += "<strong>Validation Errors</strong>";
                        errorHtml += "</div>";
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
                        errorHtml +=
                            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
                        errorHtml += "</div>";

                        alertContainer.html(errorHtml);
                        window.scrollTo(0, 0);
                    }
                });
            });
        </script>
    @endpush
@endsection
