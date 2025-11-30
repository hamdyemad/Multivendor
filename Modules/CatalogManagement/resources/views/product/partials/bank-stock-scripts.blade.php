<script>
(function($) {
    'use strict';

    const config = {
        routes: {
            bankProductsApi: '{{ route("admin.products.bank.api.products") }}',
            taxesApi: '{{ route("admin.products.bank.api.taxes") }}',
            regionsApi: '/api/area/regions',
            saveStock: '{{ route("admin.products.bank.save-stock") }}'
        }
    };

    // Translations
    const translations = {
        pricing_and_details: '{{ __("catalogmanagement::product.pricing_and_details") }}',
        stock_management: '{{ __("catalogmanagement::product.stock_management") }}',
        vendor_sku: '{{ __("catalogmanagement::product.vendor_sku") }}',
        price: '{{ __("catalogmanagement::product.price") }}',
        has_discount: '{{ __("catalogmanagement::product.has_discount") }}',
        price_before_discount: '{{ __("catalogmanagement::product.price_before_discount") }}',
        discount: '{{ __("catalogmanagement::product.discount") }}',
        discount_end_date: '{{ __("catalogmanagement::product.discount_end_date") }}',
        tax: '{{ __("catalogmanagement::product.tax") }}',
        region: '{{ __("catalogmanagement::product.region") }}',
        quantity: '{{ __("catalogmanagement::product.quantity") }}',
        alert_quantity: '{{ __("catalogmanagement::product.alert_quantity") }}',
        add_stock_entry: '{{ __("catalogmanagement::product.add_stock_entry") }}',
        select_region: '{{ __("catalogmanagement::product.select_region") }}',
        select_tax: '{{ __("catalogmanagement::product.select_tax") }}',
        enter_variant_sku: '{{ __("catalogmanagement::product.enter_variant_sku") }}',
        enter_sku: '{{ __("catalogmanagement::product.enter_sku") }}',
        brand: '{{ __("catalogmanagement::product.brand") }}',
        category: '{{ __("catalogmanagement::product.category") }}',
        select_product: '{{ __("catalogmanagement::product.select_product") }}',
        product_selected: '{{ __("catalogmanagement::product.product_selected") }}',
        manage_variants_stock: '{{ __("catalogmanagement::product.manage_variants_stock") }}',
        product_variants_stock: '{{ __("catalogmanagement::product.product_variants_stock") }}'
    };

    const isVendorUser = {{ $isVendorUser ? 'true' : 'false' }};
    let selectedVendorId = {{ $isVendorUser ? ($vendors->first()['id'] ?? 'null') : 'null' }};
    let selectedProduct = null;
    let regionsData = [];
    let stockRowCounter = 0;

    $(document).ready(function() {
        console.log('Initializing bank stock management...');

        // Add a small delay to ensure all other scripts have loaded
        setTimeout(function() {
            // Hide any loading overlays that might be showing
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }
            $('.loading-overlay').hide();
            $('#loading-overlay').hide();
            $('[data-loading]').hide();

            // Hide any toastr messages that might be showing
            if (typeof toastr !== 'undefined') {
                toastr.clear();
            }

            if (!isVendorUser) {
                initVendorSelect();
            } else {
                // For vendor users, just show the product search step without triggering any loading
                $('#step-products').addClass('completed');
                $('#products-container').show();
                console.log('Vendor user - product search enabled');
            }
            initEventHandlers();
            loadRegions();
            loadTaxes();

            console.log('Bank stock management initialized successfully');
        }, 100); // Small delay to let other scripts finish
    });

    // Reset function to clear all form data when vendor changes
    function resetAllFormData() {
        console.log('🔄 Resetting all form data due to vendor change');

        // Reset global variables
        selectedProduct = null;

        // Clear product search and selection
        $('#product-search').val('');
        $('#products-list').empty();
        $('#selected-product-summary').hide();
        $('#selected-product-name').text('');
        $('#selected_product_id').val('');
        $('#selected_vendor_id').val(selectedVendorId || '');

        // Reset product step
        $('#step-products').removeClass('completed');
        $('#products-container').hide();
        $('#no-products').hide();

        // Clear and hide variant management
        $('#variants-container').empty();
        $('#step-variant-stock-management').hide();
        $('#variants-loading').hide();
        $('#no-variants-state').hide();

        // Reset form
        $('#vendor-product-form')[0].reset();

        // Reset global vendor product fields
        $('#tax_id').val('').trigger('change');
        $('#max_per_order').val('');

        // Clear any validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.error-message').hide();

        console.log('✅ All form data reset successfully');
    }

    // Step 1: Vendor Selection
    function initVendorSelect() {
        const $vendorSelect = $('#vendor_select');
        $vendorSelect.select2({ theme: 'bootstrap-5', width: '100%' });

        $vendorSelect.on('change', function() {
            selectedVendorId = $(this).val();
            console.log('🏪 Vendor selected:', selectedVendorId);

            // Always reset everything when vendor changes
            resetAllFormData();

            if (selectedVendorId) {
                console.log('✅ Enabling product search for vendor:', selectedVendorId);
                showVendorInfo();
                enableProductSearch();
            } else {
                console.log('❌ No vendor selected, disabling steps');
                hideVendorInfo();
                hideProductSearch();
                hideVariantManagement();
            }
        });
    }

    function showVendorInfo() {
        const vendorName = $('#vendor_select option:selected').text();
        $('#vendor-name').text(vendorName);
        $('#vendor-info').show();
        $('#step-vendor').addClass('completed');
    }

    function hideVendorInfo() {
        $('#vendor-info').hide();
        $('#step-vendor').removeClass('completed');
    }

    // Step 2: Product Search
    function enableProductSearch() {
        $('#step-products').css({
            opacity: 1,
            pointerEvents: 'auto'
        }).addClass('completed');
        $('#products-container').show();
    }

    function hideProductSearch() {
        $('#step-products').css({
            opacity: 0.5,
            pointerEvents: 'none'
        }).removeClass('completed');
        $('#products-container').hide();
        selectedProduct = null;
    }

    function hideVariantManagement() {
        $('#step-variant-stock-management').hide();
    }

    // Product Search Functions
    function searchProducts(searchTerm) {
        console.log('🔍 Searching products with term:', searchTerm);
        console.log('📦 Selected vendor ID:', selectedVendorId);
        console.log('🔗 Search URL:', config.routes.getBankProducts);

        if (!searchTerm || searchTerm.length < 2) {
            $('#products-list').html('<div class="col-12 text-center py-4"><p class="text-muted">Enter product name to search...</p></div>');
            return;
        }

        if (!selectedVendorId) {
            console.error('❌ No vendor selected');
            $('#products-list').html('<div class="col-12 text-center py-4"><p class="text-danger">Please select a vendor first</p></div>');
            return;
        }

        $('#products-loading').show();
        $('#products-list').hide();

        const requestData = {
            search: searchTerm,
            vendor_id: selectedVendorId
        };

        console.log('📤 Request data:', requestData);

        $.ajax({
            url: config.routes.bankProductsApi,
            type: 'GET',
            data: {
                type: 'search',
                search: searchTerm,
                vendor_id: selectedVendorId
            },
            success: function(response) {
                console.log('✅ Search response:', response);
                console.log('📊 Response structure check:', {
                    hasSuccess: !!response.success,
                    hasData: !!response.data,
                    hasProducts: !!(response.data && response.data.products),
                    productsLength: response.data && response.data.products ? response.data.products.length : 0,
                    productsListElement: $('#products-list').length
                });

                $('#products-loading').hide();

                if (response.success && response.data && response.data.products && response.data.products.length > 0) {
                    console.log('📋 Found products:', response.data.products.length);
                    console.log('🎯 Calling displayProducts with:', response.data.products);

                    // Store products globally for later use
                    window.currentProducts = response.data.products;

                    displayProducts(response.data.products);
                    console.log('✅ Products displayed, showing products list');
                } else {
                    console.log('❌ No products found or invalid response structure');
                    $('#products-list').html('<div class="col-12 text-center py-4"><p class="text-muted">No products found</p></div>');
                }
                $('#products-list').show();
                console.log('👁️ Products list visibility:', $('#products-list').is(':visible'));
            },
            error: function(xhr, status, error) {
                console.error('❌ Search error:', xhr, status, error);
                console.error('Response text:', xhr.responseText);
                $('#products-loading').hide();
                $('#products-list').html('<div class="col-12 text-center py-4"><p class="text-danger">Error loading products: ' + error + '</p></div>').show();
            }
        });
    }

    function displayProducts(products) {
        console.log('🎨 displayProducts called with:', products);
        console.log('📍 Products list element exists:', $('#products-list').length > 0);

        let html = '';
        products.forEach(function(product, index) {
            console.log(`🔍 Processing product ${index + 1}:`, {
                id: product.id,
                name: product.name,
                brand: product.brand,
                department: product.department,
                category: product.category,
                sub_category: product.sub_category,
                image: product.image
            });

            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="product-card" data-product-id="${product.id}">
                        <div class="d-flex align-items-start">
                            <img src="${product.image || '/images/default-product.png'}" alt="${product.name}" class="product-image me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            <div class="product-info flex-grow-1">
                                <h6 class="mb-1">${product.name}</h6>
                                <div class="product-details">
                                    ${product.brand ? `<small class="text-muted d-block"><i class="uil uil-tag-alt me-1"></i><strong>Brand:</strong> ${product.brand}</small>` : ''}
                                    ${product.department ? `<small class="text-muted d-block"><i class="uil uil-building me-1"></i><strong>Department:</strong> ${product.department}</small>` : ''}
                                    ${product.category ? `<small class="text-muted d-block"><i class="uil uil-layer-group me-1"></i><strong>Category:</strong> ${product.category}</small>` : ''}
                                    ${product.sub_category ? `<small class="text-muted d-block"><i class="uil uil-sitemap me-1"></i><strong>Sub Category:</strong> ${product.sub_category}</small>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        console.log('📝 Generated HTML length:', html.length);
        console.log('🎯 Setting HTML to products-list');
        $('#products-list').html(html);
        console.log('✅ HTML set, products-list children count:', $('#products-list').children().length);
    }

    function selectProduct(productId) {
        console.log('🔄 Selecting new product, clearing previous data...');

        // Clear previous product data
        clearPreviousProductData();

        $('.product-card').removeClass('selected');
        $(`.product-card[data-product-id="${productId}"]`).addClass('selected');

        selectedProduct = productId;
        $('#selected_product_id').val(productId);

        // Find the product data from the stored products
        const product = window.currentProducts?.find(p => p.id == productId);

        if (product) {
            $('#selected-product-name').text(product.name);
            $('#selected-product-summary').show();

            console.log('🔍 Selected product configuration type:', product.configuration_type);
            console.log('📦 Product data:', product);

            // Check if product is simple or has variants
            if (product.configuration_type === 'simple') {
                console.log('📦 Simple product - showing direct stock management');
                showSimpleProductStockManagement(product);
            } else if (product.configuration_type === 'variants') {
                console.log('🎛️ Variant product - showing variant management');

                // Show variant management section
                $('#step-variant-stock-management').show();

                // Check if product has variants data from search
                if (product.variants && product.variants.length > 0) {
                    console.log('✅ Using variants from search data:', product.variants);
                    displayVariantsFromSearch(product);
                } else {
                    console.log('⚠️ No variants in search data, loading from API...');
                    loadProductVariants(productId);
                }
            } else {
                console.log('⚠️ Unknown configuration type, loading variants from API...');
                loadProductVariants(productId);
            }
        } else {
            console.error('❌ Product data not found for ID:', productId);
            loadProductVariants(productId); // Fallback to variant loading
        }
    }

    function clearPreviousProductData() {
        console.log('🧹 Clearing previous product data...');

        // Clear variants container
        $('#variants-container').empty();

        // Hide variant management section
        $('#step-variant-stock-management').hide();

        // Hide product info
        $('#selected-product-info').hide();

        // Clear product info fields
        $('#product-image').attr('src', '');
        $('#product-title').text('');
        $('#product-brand').text('');
        $('#product-department').text('');
        $('#product-category').text('');
        $('#product-sub-category').text('');

        // Clear validation errors
        clearValidationErrors();

        // Hide and clear selected product summary
        $('#selected-product-summary').hide();
        $('#selected-product-name').text('');

        // Hide no variants state
        $('#no-variants-state').hide();

        // Hide variants loading state
        $('#variants-loading').hide();

        // Reset stock row counter
        stockRowCounter = 0;

        // Clear any Select2 instances to prevent memory leaks
        $('.tax-select').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                try {
                    $(this).select2('destroy');
                } catch (e) {
                    console.warn('Could not destroy tax select2:', e);
                }
            }
        });

        $('.region-select').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                try {
                    $(this).select2('destroy');
                } catch (e) {
                    console.warn('Could not destroy region select2:', e);
                }
            }
        });

        // Clear any form data
        $('#vendor-product-form').trigger('reset');

        // Clear hidden inputs
        $('#selected_product_id').val('');

        console.log('✅ Previous product data cleared successfully');
    }

    // Step 3: Variant Management
    function loadProductVariants(productId) {
        $('#step-variant-stock-management').show();
        $('#variants-loading').show();
        $('#variants-container').empty();

        $.ajax({
            url: config.routes.bankProductsApi,
            type: 'GET',
            data: {
                type: 'vendor_product',
                product_id: productId,
                vendor_id: selectedVendorId
            },
            success: function(response) {
                $('#variants-loading').hide();
                if (response.success && response.data && response.data.variants && response.data.variants.length > 0) {
                    displayVariants(response.data.variants);
                    $('#selected-product-info').show();
                    populateProductInfo(response.data.product);
                } else {
                    $('#no-variants-state').show();
                }
            },
            error: function() {
                $('#variants-loading').hide();
                $('#variants-container').html('<div class="alert alert-danger">Error loading variants</div>');
            }
        });
    }

    function showSimpleProductStockManagement(product) {
        console.log('producttttttttttttttttttttttttttttttt', product);
        console.log('📦 Setting up simple product stock management for:', product.name);

        $('#step-variant-stock-management').show();
        $('#variants-loading').hide();

        // Show product info card and populate it
        $('#selected-product-info').show();
        populateProductInfo(product);

        // Create simple product stock form
        const simpleProductHtml = `
            <div class="simple-product-stock">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Stock Management Form -->
                            <div class="col-md-12">
                                <form id="simple-product-form">
                                    <input type="hidden" name="product_id" value="${product.id}">
                                    <input type="hidden" name="vendor_id" value="${selectedVendorId}">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SKU</label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="sku"
                                                   placeholder="Enter SKU" value="${product.sku || ''}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Price <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="price"
                                                   placeholder="0.00" step="0.01" min="0" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <label class="form-label text-dark fw-medium mb-0">${translations.has_discount}</label>
                                                    <div class="form-check form-switch form-switch-lg">
                                                        <input class="form-check-input discount-switch" type="checkbox"
                                                               id="hasDiscountSimple" name="has_discount"
                                                               onchange="toggleSimpleDiscountFields()">
                                                        <label class="form-check-label" for="hasDiscountSimple"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row discount-fields" id="simpleDiscountFields" style="display: none;">
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-3">
                                                <label class="form-label">${translations.price_before_discount}</label>
                                                <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="price_before_discount"
                                                        placeholder="0.00" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-3">
                                                <label class="form-label">${translations.discount_end_date}</label>
                                                <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="discount_end_date">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock Management -->
                                    <div class="stock-section mt-4">
                                        <h6 class="mb-3">
                                            <i class="uil uil-cube me-2"></i>
                                            ${translations.stock_management}
                                        </h6>
                                        <div id="simple-stock-rows">
                                            <!-- Stock rows will be added here -->
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="addSimpleStockRow()">
                                            <i class="uil uil-plus me-1"></i>
                                            ${translations.add_stock_entry}
                                        </button>
                                    </div>

                                    <!-- Save/Cancel buttons removed as requested -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#variants-container').html(simpleProductHtml);

        // Populate tax options first
        populateTaxOptions();

        // Add initial stock row
        addSimpleStockRow();

        // Initialize Select2 after population
        setTimeout(() => {
            $('select[name="tax_id"]').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select Tax'
            });
        }, 500);

        // Setup form submission
        setupSimpleProductForm();
    }

    function populateProductInfo(product) {
        console.log('🏷️ Product info data:', product);

        $('#product-image').attr('src', product.image || '/images/default-product.png');
        $('#product-title').text(product.name || 'Product Name');
        $('#product-brand').text(product.brand || '-');

        // Try different possible field names for department
        const department = product.department;
        $('#product-department').text(department);
        console.log('🏢 Department:', department);

        $('#product-category').text(product.category || '-');

        // Try different possible field names for sub-category
        const subCategory = product.sub_category || product.subCategory || product.subcategory ||
                           product.sub_category_name || product.subCategoryName || '-';
        $('#product-sub-category').text(subCategory);
        console.log('📂 Sub-category:', subCategory);
    }

    function displayVariantsFromSearch(product) {
        console.log('🎨 Displaying variants from search data:', product.variants);

        // Hide loading
        $('#variants-loading').hide();

        // Show product info
        $('#selected-product-info').show();
        populateProductInfo(product);

        let html = '<div class="variants-management">';

        product.variants.forEach(function(variant, index) {
            // Build variant tree name
            let variantTreeName = buildVariantTreeName(variant.variant_tree);

            // Create variant box with tree name
            html += createVariantBoxWithTree(variant, variantTreeName, index);
        });

        html += '</div>';

        $('#variants-container').html(html);

        // Populate region and tax options
        populateRegionOptions();
        populateAllTaxOptions();

        // Initialize Select2 for region and tax selects
        setTimeout(() => {
            $('.region-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select Region'
            });
            $('.tax-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select Tax'
            });
        }, 500);

        // Add initial stock row for each variant
        product.variants.forEach(function(variant, index) {
            addVariantStockRow(index);
        });

        console.log('✅ Variants from search displayed successfully');
    }

    function buildVariantTreeName(variantTree) {
        if (!variantTree) return 'Unknown Variant';

        let names = [];
        let current = variantTree;

        // Build path from root to current (traverse parents first)
        while (current) {
            names.unshift(current.name); // Add to beginning
            current = current.parent;
        }

        return names.join(' > ');
    }

    function createVariantBoxWithTree(variant, variantTreeName, index) {
        const keyName = variant.key?.name || 'Variant';

        return `
            <div class="variant-management-box mb-4" data-variant-index="${index}">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="uil uil-tag-alt me-2"></i>
                                <strong>${keyName}:</strong> ${variantTreeName}
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="variant-form" data-variant-id="${variant.id}">
                            <input type="hidden" name="variants[${index}][id]" value="${variant.id}">
                            <input type="hidden" name="variants[${index}][variant_configuration_id]" value="${variant.variant_configuration_id || ''}">
                            <input type="hidden" name="product_id" value="${selectedProduct}">
                            <input type="hidden" name="vendor_id" value="${selectedVendorId}">

                            <!-- Pricing Section -->
                            <div class="pricing-section mb-4">
                                <h6 class="section-title">
                                    <i class="uil uil-money-bill me-2"></i>
                                    ${translations.pricing_and_details}
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">${translations.vendor_sku} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][sku]"
                                               placeholder="${translations.enter_variant_sku}" value="" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">${translations.price} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][price]"
                                               placeholder="0.00" step="0.01" min="0" value="" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <label class="form-label text-dark fw-medium mb-0">${translations.has_discount}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input discount-switch" type="checkbox"
                                                           id="hasDiscount${index}" name="variants[${index}][has_discount]"
                                                           onchange="toggleDiscountFields(${index})">
                                                    <label class="form-check-label" for="hasDiscount${index}"></label>
                                                </div>
                                            </div>
                                            <div class="discount-fields" id="discountFields${index}" style="display: none;">
                                                <div class="mb-3">
                                                    <label class="form-label">${translations.price_before_discount}</label>
                                                    <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][price_before_discount]"
                                                           placeholder="0.00" step="0.01" min="0">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">${translations.discount_end_date}</label>
                                                    <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][discount_end_date]">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Management Section -->
                            <div class="stock-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="section-title mb-0">
                                        <i class="uil uil-package me-2"></i>
                                        ${translations.stock_management}
                                    </h6>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addVariantStockRow(${index})">
                                        <i class="uil uil-plus me-1"></i>
                                        ${translations.add_stock_entry}
                                    </button>
                                </div>
                                <div class="variant-stock-rows" id="variant-stock-rows-${index}">
                                    <!-- Stock rows will be added here -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    function displayVariants(variants) {
        console.log('🎛️ Displaying variants for variant product:', variants);

        let html = '<div class="variants-management">';

        variants.forEach(function(variant, index) {
            html += createVariantManagementBox(variant, index);
        });

        html += '</div>';

        // No save/cancel buttons needed - removed as requested

        $('#variants-container').html(html);

        // Populate region and tax options first
        populateRegionOptions();
        populateAllTaxOptions();

        // Initialize Select2 for region and tax selects after population
        setTimeout(() => {
            $('.region-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select Region'
            });
            $('.tax-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select Tax'
            });
        }, 500);

        // Add initial stock row for each variant
        variants.forEach(function(variant, index) {
            addVariantStockRow(index);
        });
    }

    function createVariantManagementBox(variant, index) {
        const keyName = variant.key?.name || 'Variant';
        const variantName = variant.name || `Variant ${variant.id}`;

        return `
            <div class="variant-management-box mb-4" data-variant-index="${index}">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="uil uil-tag-alt me-2"></i>
                                ${variant.key ? `<strong>${keyName}:</strong> ${variantName}` : variantName}
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="variant-form" data-variant-id="${variant.id}">
                            <input type="hidden" name="variants[${index}][id]" value="${variant.id}">
                            <input type="hidden" name="variants[${index}][variant_configuration_id]" value="${variant.variant_configuration_id || ''}">
                            <input type="hidden" name="product_id" value="${selectedProduct}">
                            <input type="hidden" name="vendor_id" value="${selectedVendorId}">

                            <!-- Pricing Section -->
                            <div class="pricing-section mb-4">
                                <h6 class="section-title">
                                    <i class="uil uil-money-bill me-2"></i>
                                    ${translations.pricing_and_details}
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">${translations.vendor_sku} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][sku]"
                                               placeholder="${translations.enter_variant_sku}" value="${variant.sku || ''}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">${translations.price} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][price]"
                                               placeholder="0.00" step="0.01" min="0" value="${variant.price || ''}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <label class="form-label text-dark fw-medium mb-0">${translations.has_discount}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input discount-switch" type="checkbox"
                                                           id="hasDiscount${index}" name="variants[${index}][has_discount]"
                                                           onchange="toggleDiscountFields(${index})">
                                                    <label class="form-check-label" for="hasDiscount${index}"></label>
                                                </div>
                                            </div>
                                            <div class="discount-fields" id="discountFields${index}" style="display: none;">
                                                <div class="mb-3">
                                                    <label class="form-label">${translations.price_before_discount}</label>
                                                    <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][price_before_discount]"
                                                           placeholder="0.00" step="0.01" min="0">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">${translations.discount_end_date}</label>
                                                    <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][discount_end_date]">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Stock Management Section -->
                            <div class="stock-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="section-title mb-0">
                                        <i class="uil uil-package me-2"></i>
                                        ${translations.stock_management}
                                    </h6>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addVariantStockRow(${index})">
                                        <i class="uil uil-plus me-1"></i>
                                        ${translations.add_stock_entry}
                                    </button>
                                </div>
                                <div class="variant-stock-rows" id="variant-stock-rows-${index}">
                                    <!-- Stock rows will be added here -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    function createVariantHtml(variant, totalStock) {
        // Keep old function for backward compatibility if needed
        const template = $('#existing-variant-template').html();
        let html = template
            .replace(/__VARIANT_ID__/g, variant.id)
            .replace(/__VARIANT_NAME__/g, variant.name || `Variant ${variant.id}`)
            .replace(/__VARIANT_SKU__/g, variant.sku || '')
            .replace(/__VARIANT_PRICE__/g, variant.price || 0)
            .replace(/__VARIANT_CONFIG_ID__/g, variant.variant_configuration_id || '')
            .replace(/__TOTAL_STOCK__/g, totalStock);

        // Add existing stock rows
        const $temp = $(html);
        const $stockTBody = $temp.find(`#variant-${variant.id}-stock-rows`);

        if (variant.stocks && variant.stocks.length > 0) {
            variant.stocks.forEach(function(stock, index) {
                const stockHtml = createStockRowHtml(variant.id, stock, index);
                $stockTBody.append(stockHtml);
            });
        }

        return $temp[0].outerHTML;
    }

    function createStockRowHtml(variantId, stock, index) {
        const template = $('#stock-row-template').html();
        return template
            .replace(/__VARIANT_ID__/g, variantId)
            .replace(/__STOCK_INDEX__/g, index)
            .replace(/__STOCK_ID__/g, stock.id || '')
            .replace(/__STOCK_QUANTITY__/g, stock.quantity || 0);
    }

    // Event Handlers
    function initEventHandlers() {
        // Product search
        let searchTimeout;
        $('#product-search').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();

            searchTimeout = setTimeout(() => {
                searchProducts(searchTerm);
            }, 500);
        });

        // Product selection
        $(document).on('click', '.product-card', function() {
            const productId = $(this).data('product-id');
            selectProduct(productId);
        });

        // Stock management
        $(document).on('click', '.add-variant-stock-row', function() {
            const variantId = $(this).data('variant-id');
            addStockRow(variantId);
        });

        $(document).on('click', '.remove-stock-row', function() {
            $(this).closest('tr').remove();
            updateTotalStock();
        });

        $(document).on('input', '.stock-quantity', function() {
            updateTotalStock();
        });

        // Save button
        $('#save-form').on('click', function() {
            saveForm();
        });

        // Clear validation errors on global vendor product fields
        $('#tax_id').on('change', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
                $('#error-tax_id').hide();
            }
        });

        $('#max_per_order').on('input', function() {
            if ($(this).val() && parseInt($(this).val()) >= 1) {
                $(this).removeClass('is-invalid');
                $('#error-max_per_order').hide();
            }
        });

        // Clear validation errors on focus/input for any form field
        $(document).on('focus input change', 'input, select, textarea', function() {
            clearFieldError($(this));
        });

        // Also clear on blur for select elements (for better UX)
        $(document).on('blur', 'select', function() {
            if ($(this).val()) {
                clearFieldError($(this));
            }
        });

        // Clear validation errors for variant SKU fields
        $(document).on('input', 'input[name*="[sku]"]', function() {
            const $input = $(this);
            if ($input.val() && $input.val().trim()) {
                $input.removeClass('is-invalid');
                $input.next('.error-message').hide();
            }
        });

        // Clear validation errors for variant price fields
        $(document).on('input', 'input[name*="[price]"]', function() {
            const $input = $(this);
            if ($input.val() && parseFloat($input.val()) > 0) {
                $input.removeClass('is-invalid');
                $input.next('.error-message').hide();
            }
        });

        // Clear validation errors for stock quantity fields
        $(document).on('input', 'input[name*="[quantity]"], .stock-quantity', function() {
            const $input = $(this);
            if ($input.val() && parseInt($input.val()) >= 0) {
                $input.removeClass('is-invalid');
                $input.next('.error-message').hide();
            }
        });

        // Clear validation errors for region select fields
        $(document).on('change', 'select[name*="[region_id]"]', function() {
            const $select = $(this);
            if ($select.val()) {
                $select.removeClass('is-invalid');
            }
        });
    }

    // Stock Management Functions
    function addStockRow(variantId) {
        const stockHtml = createStockRowHtml(variantId, {}, stockRowCounter++);
        $(`#variant-${variantId}-stock-rows`).append(stockHtml);

        // Initialize Select2 for new row
        $(`#variant-${variantId}-stock-rows tr:last .region-select`).select2({ theme: 'bootstrap-5', width: '100%' });
        populateRegionOptions();
    }

    function updateTotalStock() {
        $('.existing-variant-card').each(function() {
            const $card = $(this);
            let total = 0;

            $card.find('.stock-quantity').each(function() {
                total += parseInt($(this).val()) || 0;
            });

            $card.find('.total-stock-display').text(total);
        });
    }

    function validateRequiredFields() {
        console.log('🔍 Validating required fields...');

        let isValid = true;
        let errors = [];

        // Validate global vendor product fields
        const taxId = $('#tax_id').val();
        if (!taxId || taxId === '') {
            errors.push('Tax selection is required');
            $('#tax_id').addClass('is-invalid');
            $('#error-tax_id').text('Tax is required').show();
            isValid = false;
        } else {
            $('#tax_id').removeClass('is-invalid');
            $('#error-tax_id').hide();
        }

        const maxPerOrder = $('#max_per_order').val();
        if (!maxPerOrder || parseInt(maxPerOrder) < 1) {
            errors.push('Max per order must be at least 1');
            $('#max_per_order').addClass('is-invalid');
            $('#error-max_per_order').text('Max per order is required and must be at least 1').show();
            isValid = false;
        } else {
            $('#max_per_order').removeClass('is-invalid');
            $('#error-max_per_order').hide();
        }

        // Debug: Check what variant elements exist
        const variantElements = $('.variant-management-box, .existing-variant-card');
        console.log('🔍 Found variant elements:', variantElements.length);
        console.log('🔍 Variant elements:', variantElements);

        // Also check for variants container content
        const variantsContainer = $('#variants-container');
        console.log('🔍 Variants container HTML:', variantsContainer.html());

        // Check if there are any products selected
        if (!selectedProduct) {
            errors.push('Please select a product first');
            isValid = false;
            console.log('❌ No product selected');
        } else {
            console.log('✅ Product selected:', selectedProduct);
        }

        // Validate each variant
        $('.variant-management-box, .existing-variant-card').each(function(index) {
            const $variant = $(this);
            const variantName = $variant.find('h6 .variant-name').text() || $variant.find('h6').text() || `Variant ${index + 1}`;

            // Check SKU
            const $skuInput = $variant.find('input[name*="[sku]"]');
            const sku = $skuInput.val();
            if (!sku || sku.trim() === '') {
                errors.push(`${variantName}: SKU is required`);
                $skuInput.addClass('is-invalid');
                // Find or create error message element
                let $errorElement = $skuInput.next('.error-message');
                if ($errorElement.length === 0) {
                    $errorElement = $('<div class="error-message text-danger" style="font-size: 0.875rem; margin-top: 0.25rem;"></div>');
                    $skuInput.after($errorElement);
                }
                $errorElement.text('SKU is required').show();
                isValid = false;
            } else {
                $skuInput.removeClass('is-invalid');
                $skuInput.next('.error-message').hide();
            }

            // Check Price
            const $priceInput = $variant.find('input[name*="[price]"]');
            const price = $priceInput.val();
            if (!price || parseFloat(price) <= 0) {
                errors.push(`${variantName}: Price must be greater than 0`);
                $priceInput.addClass('is-invalid');
                // Find or create error message element
                let $errorElement = $priceInput.next('.error-message');
                if ($errorElement.length === 0) {
                    $errorElement = $('<div class="error-message text-danger" style="font-size: 0.875rem; margin-top: 0.25rem;"></div>');
                    $priceInput.after($errorElement);
                }
                $errorElement.text('Price must be greater than 0').show();
                isValid = false;
            } else {
                $priceInput.removeClass('is-invalid');
                $priceInput.next('.error-message').hide();
            }

            // Check Stock Management (at least one stock entry with region and quantity)
            const stockRows = $variant.find('.stock-row, .variant-stock-row');
            let hasValidStock = false;
            let stockErrors = [];

            stockRows.each(function() {
                const $row = $(this);
                const $regionSelect = $row.find('select[name*="[region_id]"]');
                const $quantityInput = $row.find('input[name*="[quantity]"], .stock-quantity');
                const region = $regionSelect.val();
                const quantity = $quantityInput.val();

                // Validate region selection
                if (!region || region === '') {
                    $regionSelect.addClass('is-invalid');
                    stockErrors.push('Region is required');
                } else {
                    $regionSelect.removeClass('is-invalid');
                }

                // Validate quantity
                if (!quantity || parseInt(quantity) < 0) {
                    $quantityInput.addClass('is-invalid');
                    // Find or create error message element for quantity
                    let $errorElement = $quantityInput.next('.error-message');
                    if ($errorElement.length === 0) {
                        $errorElement = $('<div class="error-message text-danger" style="font-size: 0.875rem; margin-top: 0.25rem;"></div>');
                        $quantityInput.after($errorElement);
                    }
                    $errorElement.text('Quantity is required and must be 0 or greater').show();
                    stockErrors.push('Quantity is required');
                } else {
                    $quantityInput.removeClass('is-invalid');
                    $quantityInput.next('.error-message').hide();
                    if (region && region !== '') {
                        hasValidStock = true;
                    }
                }
            });

            if (!hasValidStock && stockRows.length > 0) {
                errors.push(`${variantName}: At least one valid stock entry with region and quantity is required`);
                isValid = false;
            } else if (stockRows.length === 0) {
                errors.push(`${variantName}: At least one stock entry is required`);
                isValid = false;
            }
        });

        // Debug: Final validation summary
        console.log('🔍 Final validation summary:');
        console.log('  - Total errors:', errors.length);
        console.log('  - Errors:', errors);
        console.log('  - Is valid:', isValid);
        console.log('  - Variant elements processed:', $('.variant-management-box, .existing-variant-card').length);

        // Show validation errors
        if (!isValid) {
            const errorMessage = errors.join('<br>');
            showBootstrapAlert('danger', `Please fix the following errors:<br>${errorMessage}`);
            console.error('❌ Validation failed:', errors);
        } else {
            console.log('✅ All required fields validated successfully');
        }

        return isValid;
    }

    function saveForm() {
        console.log('💾 Saving form...');

        // Validate required fields first
        if (!validateRequiredFields()) {
            return false;
        }

        // Get the form
        const form = $('#vendor-product-form');
        const formData = new FormData(form[0]);

        // Add CSRF token
        formData.append('_token', '{{ csrf_token() }}');

        // Log form data for debugging
        console.log('📋 Form data:', Object.fromEntries(formData));

        // Show loading state
        const saveButton = $('#save-form');
        const originalText = saveButton.html();
        saveButton.prop('disabled', true);
        saveButton.html('<i class="spinner-border spinner-border-sm me-1"></i> {{ __("common.saving") ?? "Saving..." }}');

        $.ajax({
            url: config.routes.saveStock,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('✅ Save response:', response);

                if (response.success) {
                    // Clear validation errors on success
                    clearValidationErrors();
                    showBootstrapAlert('success', response.message || 'Stocks saved successfully!');
                    window.location.href = '{{ route("admin.products.index") }}';
                } else {
                    showBootstrapAlert('danger', response.message || 'Error saving stocks');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Save error:', {
                    status: xhr.status,
                    error: error,
                    response: xhr.responseText
                });

                // Clear previous validation errors
                clearValidationErrors();

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors
                    displayValidationErrors(xhr.responseJSON.errors);
                } else {
                    // Handle other errors
                    let errorMessage = 'Error saving stocks';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showBootstrapAlert('danger', errorMessage);
                }
            },
            complete: function() {
                // Restore button state
                saveButton.prop('disabled', false);
                saveButton.html(originalText);
            }
        });
    }

    // Validation Error Handling Functions
    function displayValidationErrors(errors) {
        console.log('🚨 Displaying validation errors:', errors);

        // Create validation alert with all error messages
        const errorMessages = [];

        // Process each error field
        Object.keys(errors).forEach(field => {
            const fieldErrors = errors[field];

            // Add field errors to alert message
            fieldErrors.forEach(error => {
                errorMessages.push(`• ${error}`);
            });

            // Add is-invalid class and error message to specific fields
            highlightFieldError(field, fieldErrors[0]);
        });

        // Show BOTH: validation alert at top AND individual field errors
        showValidationAlert(errorMessages);
    }

    function highlightFieldError(field, errorMessage) {
        console.log(`🔴 Highlighting field error: ${field} - ${errorMessage}`);

        let $input = null;
        let $container = null;

        // First, try to find the input using multiple strategies

        // Handle different field types
        if (field === 'sku') {
            $input = $('input[name="sku"]');
        } else if (field === 'price') {
            $input = $('input[name="price"]');
        } else if (field === 'tax_id') {
            $input = $('select[name="tax_id"]');
        } else if (field === 'has_discount') {
            $input = $('input[name="has_discount"]');
        } else if (field === 'price_before_discount') {
            $input = $('input[name="price_before_discount"]');
        } else if (field === 'discount_end_date') {
            $input = $('input[name="discount_end_date"]');
        } else if (field.startsWith('stocks.')) {
            // Handle stock field errors (e.g., stocks.0.region_id, stocks.0.quantity)
            const matches = field.match(/stocks\.(\d+)\.(.+)/);
            if (matches) {
                const stockIndex = matches[1];
                const stockField = matches[2];

                if (stockField === 'region_id') {
                    $input = $(`select[name="stocks[${stockIndex}][region_id]"]`);
                } else if (stockField === 'quantity') {
                    $input = $(`input[name="stocks[${stockIndex}][quantity]"]`);
                }
            }
        } else if (field.startsWith('variants.')) {
            // Handle variant field errors (e.g., variants.0.sku, variants.0.stocks.0.region_id)
            const matches = field.match(/variants\.(\d+)\.(.+)/);
            if (matches) {
                const variantIndex = matches[1];
                const variantField = matches[2];

                if (variantField === 'sku') {
                    $input = $(`input[name="variants[${variantIndex}][sku]"]`);
                } else if (variantField === 'price') {
                    $input = $(`input[name="variants[${variantIndex}][price]"]`);
                } else if (variantField === 'has_discount') {
                    $input = $(`input[name="variants[${variantIndex}][has_discount]"]`);
                } else if (variantField === 'price_before_discount') {
                    $input = $(`input[name="variants[${variantIndex}][price_before_discount]"]`);
                } else if (variantField === 'discount_end_date') {
                    $input = $(`input[name="variants[${variantIndex}][discount_end_date]"]`);
                } else if (variantField.startsWith('stocks.')) {
                    const stockMatches = variantField.match(/stocks\.(\d+)\.(.+)/);
                    if (stockMatches) {
                        const stockIndex = stockMatches[1];
                        const stockField = stockMatches[2];

                        if (stockField === 'region_id') {
                            $input = $(`select[name="variants[${variantIndex}][stocks][${stockIndex}][region_id]"]`);
                        } else if (stockField === 'quantity') {
                            $input = $(`input[name="variants[${variantIndex}][stocks][${stockIndex}][quantity]"]`);
                        } else if (stockField === 'alert_quantity') {
                            $input = $(`input[name="variants[${variantIndex}][stocks][${stockIndex}][alert_quantity]"]`);
                        }
                    }
                }
            }
        } else {
            // Generic field selector for any other fields
            $input = $(`input[name="${field}"], select[name="${field}"], textarea[name="${field}"]`);
            if ($input.length === 0) {
                // Try with array notation
                $input = $(`input[name="${field}[]"], select[name="${field}[]"], textarea[name="${field}[]"]`);
            }
            if ($input.length === 0) {
                // Try to find by partial name match (for complex field names)
                $input = $(`input[name*="${field}"], select[name*="${field}"], textarea[name*="${field}"]`);
            }
        }

        console.log(`🔍 Field: ${field}, Found input:`, $input.length > 0 ? $input.attr('name') : 'NOT FOUND');

        if ($input && $input.length > 0) {
            // Add is-invalid class
            $input.addClass('is-invalid');

            // Find the best container for the error message
            $container = $input.parent();

            // Handle different input wrapper types
            if ($container.hasClass('input-group') || $container.hasClass('form-control-wrapper') || $container.hasClass('select2-selection')) {
                $container = $container.parent();
            }

            // For Select2 elements, find the Select2 container
            if ($input.hasClass('select2-hidden-accessible')) {
                const select2Container = $input.next('.select2-container');
                if (select2Container.length > 0) {
                    $container = select2Container.parent();
                }
            }

            // Remove existing error message for this field
            $container.find('.invalid-feedback').remove();
            $input.siblings('.invalid-feedback').remove();

            // Add error message directly after the input/container with better styling
            const errorHtml = `<div class="invalid-feedback d-block mt-1" style="color: #dc3545; font-size: 0.875rem;">
                <i class="uil uil-exclamation-triangle me-1"></i>${errorMessage}
            </div>`;

            $container.append(errorHtml);

            console.log(`✅ Added error to field: ${field} (${$input.attr('name') || $input.attr('id')})`);
        } else {
            console.warn(`⚠️ Could not find input for field: ${field}`);
        }
    }

    function showValidationAlert(errorMessages) {
        // Remove existing validation alert from anywhere
        $('#validation-alert').remove();

        const alertHtml = `
            <div id="validation-alert" class="alert d-block alert-danger alert-dismissible fade show mb-4" role="alert">
                <h6 class="alert-heading mb-2">
                    <i class="uil uil-exclamation-triangle me-2"></i>
                    Please fix the following validation errors:
                </h6>
                <div class="mb-2">
                    ${errorMessages.join('<br>')}
                </div>
                <div class="mb-0">
                    <small class="text-muted"><i class="uil uil-info-circle me-1"></i>Fields with errors are highlighted in red with specific error messages below each field.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Insert alert at the top of the stock management section (works for both simple and variant products)
        const $stockManagement = $('#step-variant-stock-management');
        if ($stockManagement.length > 0) {
            // Insert at the very beginning of the stock management section
            $stockManagement.prepend(alertHtml);
        } else {
            // Fallback: insert at the top of variants container
            $('#variants-container').prepend(alertHtml);
        }

        // Scroll to the alert
        $('html, body').animate({
            scrollTop: $('#validation-alert').offset().top - 100
        }, 500);
    }

    function clearValidationErrors() {
        console.log('🧹 Clearing validation errors');

        // Remove is-invalid classes
        $('.is-invalid').removeClass('is-invalid');

        // Remove error messages
        $('.invalid-feedback').remove();

        // Remove validation alert
        $('#validation-alert').remove();

        // Remove stock management alert
        $('#stock-management-alert').remove();
    }

    // Clear error for a specific field
    function clearFieldError($field) {
        if (!$field || $field.length === 0) return;

        // Remove is-invalid class from the field
        $field.removeClass('is-invalid');

        // Find and remove the error message
        const $container = $field.parent();

        // Remove error message from immediate parent
        $container.find('.invalid-feedback').remove();

        // Also check siblings (in case error is placed differently)
        $field.siblings('.invalid-feedback').remove();

        // If input is in a wrapper, check parent's parent too
        if ($container.hasClass('input-group') || $container.hasClass('form-control-wrapper')) {
            $container.parent().find('.invalid-feedback').remove();
        }

        console.log('🧹 Cleared error for field:', $field.attr('name') || $field.attr('id') || 'unknown');

        // If no more invalid fields exist, remove the validation alert
        setTimeout(function() {
            if ($('.is-invalid').length === 0) {
                $('#validation-alert').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        }, 100);
    }

    // Load regions
    function loadRegions() {
        // Mock regions data - replace with actual API call
        regionsData = [
            { id: 1, name: 'Region 1' },
            { id: 2, name: 'Region 2' },
            { id: 3, name: 'Region 3' }
        ];
        populateRegionOptions();
    }

    function populateRegionOptions() {
        // Fetch vendor-specific regions
        if (selectedVendorId) {
            $.ajax({
                url: config.routes.regionsApi,
                type: 'GET',
                data: {
                    vendor_id: selectedVendorId
                },
                success: function(response) {
                    const regions = response.data || response.regions || response;

                    const options = regions.map(region =>
                        `<option value="${region.id}">${region.name}</option>`
                    ).join('');

                    $('.region-select').each(function() {
                        const currentValue = $(this).val();
                        $(this).html('<option value="">Select Region</option>' + options);
                        if (currentValue) {
                            $(this).val(currentValue);
                        }
                    });

                    console.log('✅ Region options populated for vendor:', selectedVendorId, regions.length);
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error fetching regions:', error);
                    // Fallback to static regions or blade data
                    populateRegionsFromBladeData();
                }
            });
        } else {
            // Fallback if no vendor selected
            populateRegionsFromBladeData();
        }
    }

    function populateRegionsFromBladeData() {
        // Use regions from regionsData or blade template
        let regions = regionsData || [];

        // If regionsData is empty, try to get from blade
        if (regions.length === 0) {
            // You can pass regions from blade template like taxes
            regions = @json($regions ?? []);
        }

        const options = regions.map(region =>
            `<option value="${region.id}">${region.name}</option>`
        ).join('');

        $('.region-select').each(function() {
            const currentValue = $(this).val();
            $(this).html('<option value="">Select Region</option>' + options);
            if (currentValue) {
                $(this).val(currentValue);
            }
        });

        console.log('✅ Region options populated from fallback data:', regions.length);
    }

     // Variant value selection (tree navigation)
    $(document).on('change', '.variant-value-select', function() {
        const $select = $(this);
        const variantId = $select.val();
        const variantIndex = $select.data('variant-index');
        const level = $select.data('level');

        // Get the stored key ID
        const keyId = $(`#variant-${variantIndex}`).data('current-key-id');
        const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

        // Clear all child levels after the current level
        $levelsContainer.find('.variant-level').each(function() {
            if (parseInt($(this).data('level')) > level) {
                $(this).remove();
            }
        });

        // Hide pricing/stock when changing selection
        $(`#variant-${variantIndex}-pricing-stock`).hide().empty();
        $(`#variant-${variantIndex} .selected-variant-path`).hide();

        if (!variantId) {
            console.log('🗑️ Variant deselected at level:', level);
            return;
        }

        // Build selected path
        const selectedPath = [];
        $(`#variant-${variantIndex} .variant-value-select`).each(function(index) {
            if (index <= level && $(this).val()) {
                const selectedText = $(this).find('option:selected').text();
                selectedPath.push(selectedText);
            }
        });

        const $selectedOption = $select.find('option:selected');
        const hasChildren = $selectedOption.data('has-children');

        console.log('🌳 Variant selected:', variantId, 'Has children:', hasChildren);

        if (hasChildren) {
            // Load children
            loadChildVariants(variantIndex, variantId, level, selectedPath, keyId);
        } else {
            // This is a leaf node - finalize selection
            finalizeVariantSelection(variantIndex, variantId, selectedPath);
        }
    });

    // Discount checkbox toggle (for both simple and variant products)
    $(document).on('change', 'input[name*="has_discount"], input[name="has_discount"]', function() {
        const index = $(this).attr('id').replace('discount_', '');
        const discountFields = $('#discount_fields_' + index);

        if ($(this).is(':checked')) {
            discountFields.show();
        } else {
            discountFields.hide();
            discountFields.find('input').val('');
        }
    });

    // Add stock row button
    $(document).on('click', '.add-stock-row', function() {
        const productIndex = $(this).data('product-index');
        const stockContainer = $('#stock_rows_' + productIndex);
        const currentRows = stockContainer.find('.stock-row').length;

        const selectRegionText = '{{ __("catalogmanagement::product.select_region") }}';
        const quantityText = '{{ __("catalogmanagement::product.quantity") }}';

        const newRowHtml = `
            <div class="row stock-row mt-2">
                <div class="col-md-4">
                    <select name="region_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                        <option value="">${selectRegionText}</option>
                        <!-- Regions will be loaded dynamically -->
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="number" name="stock" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="${quantityText}" min="0">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-stock-row">
                        <i class="uil uil-minus m-0"></i>
                    </button>
                </div>
            </div>
        `;

        stockContainer.append(newRowHtml);
        stockContainer.find('.select2').last().select2({ theme: 'bootstrap-5', width: '100%' });
        updateRegionDropdowns();
    });

    // Remove stock row button
    $(document).on('click', '.remove-stock-row', function() {
        $(this).closest('.stock-row').remove();
    });

    // Add variant button
    $(document).on('click', '.add-variant-btn', function() {
        const productIndex = $(this).data('product-index');
        addVariant(productIndex);
    });

    // Remove variant button
    $(document).on('click', '.remove-variant-btn', function() {
        const productIndex = $(this).data('product-index');
        const variantIndex = $(this).data('variant-index');

        $(this).closest('.variant-box').remove();

        // Show empty state if no variants left
        if ($(`#variants-container-${productIndex} .variant-box`).length === 0) {
            $(`#variants-empty-state-${productIndex}`).show();
        }
    });

    // Variant discount toggle
    $(document).on('change', '.variant-discount-toggle', function() {
        const productIndex = $(this).closest('.variant-box').find('input[name*="[variant_key_id]"]').attr('name').match(/\[(\d+)\]/)[1];
        const variantIndex = $(this).closest('.variant-box').data('variant-index');
        const discountFields = $(`#variant_discount_fields_${productIndex}_${variantIndex}`);

        if ($(this).is(':checked')) {
            discountFields.show();
        } else {
            discountFields.hide();
            discountFields.find('input').val('');
        }
    });

    // Variant key change handler
    $(document).on('change', '[id^="variant_key_"]', function() {
        const keyId = $(this).val();
        const productIndex = $(this).attr('id').match(/variant_key_(\d+)_(\d+)/)[1];
        const variantIndex = $(this).attr('id').match(/variant_key_(\d+)_(\d+)/)[2];

        if (keyId) {
            loadVariantValues(keyId, productIndex, variantIndex);
        } else {
            const selectVariantValueText = '{{ __("catalogmanagement::product.select_variant_value") }}';
            $(`#variant_value_${productIndex}_${variantIndex}`).empty()
                .append(`<option value="">${selectVariantValueText}</option>`);
        }
    });

    // Add variant stock row
    $(document).on('click', '.add-variant-stock-row', function() {
        const productIndex = $(this).data('product-index');
        const variantIndex = $(this).data('variant-index');
        const stockContainer = $(`#variant_stock_rows_${productIndex}_${variantIndex}`);
        const currentRows = stockContainer.find('.stock-row').length;

        const selectRegionText = '{{ __("catalogmanagement::product.select_region") }}';
        const quantityText = '{{ __("catalogmanagement::product.quantity") }}';

        const newRowHtml = `
            <div class="row stock-row mb-2">
                <div class="col-md-4">
                    <select name="variants[${variantIndex}][stocks][${currentRows}][region_id]" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                        <option value="">${selectRegionText}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="number" name="variants[${variantIndex}][stocks][${currentRows}][stock]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="${quantityText}" min="0">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-variant-stock-row">
                        <i class="uil uil-minus m-0"></i>
                    </button>
                </div>
            </div>
        `;

        stockContainer.append(newRowHtml);
        stockContainer.find('.select2').last().select2({ theme: 'bootstrap-5', width: '100%' });
        updateRegionDropdowns();
    });

    // Remove variant stock row
    $(document).on('click', '.remove-variant-stock-row', function() {
        $(this).closest('.stock-row').remove();
    });

    // Save vendor products button
    $(document).on('click', '#save-vendor-products', function() {
        saveVendorProducts();
    });


    function updateSelectedProductsCount() {
        const count = selectedProducts.length;
        $('#selected-count').text(count);

        if (count > 0) {
            $('#selected-products-summary').show();
            $('#step-products').addClass('completed');
            enableVendorProductDataStep();
        } else {
            $('#selected-products-summary').hide();
            $('#step-products').removeClass('completed');
            hideVendorProductDataStep();
            hideStockManagement();
        }
    }

    // Step 3: VendorProduct Data
    function enableVendorProductDataStep() {
        // Store selected product data
        selectedProductsData = selectedProducts.map(id => {
            return availableProducts.find(p => p.id === id);
        });

        // Update hidden fields
        $('#selected_vendor_id').val(selectedVendorId);
        $('#selected_product_ids').val(JSON.stringify(selectedProducts));

        // Show vendor product data step
        $('#step-vendor-product-data').show();
        console.log('VendorProduct data step enabled for products:', selectedProductsData);
        showStockManagement(); // This will now hide Step 4 since stock is integrated in Step 3
    }

    function hideVendorProductDataStep() {
        $('#step-vendor-product-data').hide();
        hideStockManagement();
    }

    // Step 4: Stock Management (Only for Simple Products)
    function showStockManagement() {
        const productType = $('input[name="product_type"]:checked').val();

        if (productType === 'simple') {
            console.log('Stock management is integrated in Step 3 for simple products');
            $('#step-stock-management').hide(); // Hide Step 4 for simple products
            return; // Stock management is already in the simple product forms
        } else if (productType === 'variants') {
            console.log('Stock management skipped for variant products');
            $('#step-stock-management').hide(); // Hide Step 4 for variant products
            return; // Variants handle their own stock in Step 3
        }
    }

    function hideStockManagement() {
        $('#step-stock-management').hide();
    }

    function generateStockManagementForms() {
        const container = $('#stock-management-container');
        container.empty();

        selectedProductsData.forEach((product, index) => {
            const formHtml = createStockManagementForm(product, index);
            container.append(formHtml);
        });

        // Initialize Select2 and populate regions
        container.find('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

        // Ensure regions are loaded and populate dropdowns
        if (regionsData && regionsData.length > 0) {
            updateRegionDropdowns();
        } else {
            // Load regions if not already loaded
            loadRegions().then(() => {
                updateRegionDropdowns();
            });
        }
    }

    function createStockManagementForm(product, index) {
        // Get the appropriate title based on current locale
        const currentLocale = '{{ app()->getLocale() }}';
        let productTitle = '';

        if (currentLocale === 'ar') {
            productTitle = product.title_ar || product.title_en || 'Product ' + (index + 1);
        } else {
            productTitle = product.title_en || product.title_ar || 'Product ' + (index + 1);
        }

        // Get selected product type
        const productType = $('input[name="product_type"]:checked').val() || 'simple';

        if (productType === 'simple') {
            return createSimpleProductForm(product, index, productTitle);
        } else {
            return createVariantProductForm(product, index, productTitle);
        }
    }

    function createSimpleProductForm(product, index, productTitle) {
        const translations = {
            simple_product: '{{ __("catalogmanagement::product.simple_product") }}',
            vendor_sku: '{{ __("catalogmanagement::product.vendor_sku") }}',
            price: '{{ __("catalogmanagement::product.price") }}',
            enable_discount: '{{ __("catalogmanagement::product.enable_discount") }}',
            price_before_discount: '{{ __("catalogmanagement::product.price_before_discount") }}',
            discount_end_date: '{{ __("catalogmanagement::product.discount_end_date") }}',
            regional_stock: '{{ __("catalogmanagement::product.regional_stock") }}',
            select_region: '{{ __("catalogmanagement::product.select_region") }}',
            quantity: '{{ __("catalogmanagement::product.quantity") }}'
        };

        return `
            <div class="card mb-4" data-product-id="${product.id}">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="uil uil-cube me-2"></i>
                        ${productTitle}
                        <small class="text-muted ms-2">${translations.simple_product}</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>${translations.vendor_sku} <span class="text-danger">*</span></label>
                                <input type="text" name="sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="Enter vendor SKU" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>${translations.price} <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label mb-2">${translations.enable_discount}</label>
                            <div class="form-check form-switch form-switch-lg mb-3">
                                <input class="form-check-input" type="checkbox" name="has_discount" id="discount_${index}">
                            </div>
                        </div>
                    </div>

                    <div class="discount-fields" id="discount_fields_${index}" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>${translations.price_before_discount}</label>
                                    <input type="number" name="price_before_discount" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>${translations.discount_end_date}</label>
                                    <input type="date" name="offer_end_date" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stock-section">
                        <h6 class="mb-3">${translations.regional_stock}</h6>
                        <div class="stock-rows" id="stock_rows_${index}">
                            <div class="row stock-row">
                                <div class="col-md-4">
                                    <select name="region_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                        <option value="">${translations.select_region}</option>
                                        <!-- Regions will be loaded dynamically -->
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="stock" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="${translations.quantity}" min="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-success btn-sm add-stock-row" data-product-index="${index}">
                                        <i class="uil uil-plus me-0"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="product_id" value="${product.id}">
                </div>
            </div>
        `;
    }

    function createVariantProductForm(product, index, productTitle) {
        const translations = {
            variant_product: '{{ __("catalogmanagement::product.variant_product") }}',
            product_variants: '{{ __("catalogmanagement::product.product_variants") }}',
            add_variant: '{{ __("catalogmanagement::product.add_variant") }}',
            no_variants_added: '{{ __("catalogmanagement::product.no_variants_added") }}',
            click_add_variant_to_start: '{{ __("catalogmanagement::product.click_add_variant_to_start") }}'
        };

        return `
            <div class="card mb-4" data-product-id="${product.id}">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="uil uil-layer-group me-2"></i>
                        ${productTitle}
                        <small class="text-muted ms-2">${translations.variant_product}</small>
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Variant Management Section -->
                    <div class="variants-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">${translations.product_variants}</h6>
                            <button type="button" class="btn btn-primary btn-sm add-variant-btn" data-product-index="${index}">
                                <i class="uil uil-plus me-1"></i>${translations.add_variant}
                            </button>
                        </div>

                        <!-- Empty state message -->
                        <div id="variants-empty-state-${index}" class="text-center py-4 border rounded">
                            <i class="uil uil-layer-group text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mb-0">${translations.no_variants_added}</p>
                            <small class="text-muted">${translations.click_add_variant_to_start}</small>
                        </div>

                        <!-- Variants Container -->
                        <div id="variants-container-${index}" class="variants-container">
                            <!-- Variant boxes will be added here dynamically -->
                        </div>
                    </div>

                    <input type="hidden" name="products[${index}][product_id]" value="${product.id}">
                    <input type="hidden" name="products[${index}][product_type]" value="variants">
                </div>
            </div>
        `;
    }

    // Add variant functionality
    function addVariant(productIndex) {
        const variantIndex = Date.now(); // Use timestamp for unique ID
        const variantHtml = createVariantBox(productIndex, variantIndex);

        $(`#variants-container-${productIndex}`).append(variantHtml);
        $(`#variants-empty-state-${productIndex}`).hide();

        // Initialize Select2 for the new variant
        $(`#variant_key_${productIndex}_${variantIndex}`).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '{{ __("catalogmanagement::product.select_variant_key") }}'
        });

        // Load variant keys
        loadVariantKeysForSelect(`#variant_key_${productIndex}_${variantIndex}`);

        // Update region dropdowns for this variant
        updateRegionDropdowns();
    }

    function createVariantBox(productIndex, variantIndex) {
        return `
            <div class="variant-box border rounded p-3 mb-3" data-variant-index="${variantIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">{{ __('catalogmanagement::product.variant') }} #${variantIndex}</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-variant-btn" data-product-index="${productIndex}" data-variant-index="${variantIndex}">
                        <i class="uil uil-trash-alt m-0"></i>
                    </button>
                </div>

                <!-- Variant Configuration -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('catalogmanagement::product.variant_key') }} <span class="text-danger">*</span></label>
                        <select name="products[${productIndex}][variants][${variantIndex}][variant_key_id]"
                                id="variant_key_${productIndex}_${variantIndex}" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" required>
                            <option value="">{{ __('catalogmanagement::product.select_variant_key') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('catalogmanagement::product.variant_value') }} <span class="text-danger">*</span></label>
                        <select name="products[${productIndex}][variants][${variantIndex}][variant_value_id]"
                                id="variant_value_${productIndex}_${variantIndex}" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" required>
                            <option value="">{{ __('catalogmanagement::product.select_variant_value') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('catalogmanagement::product.vendor_sku') }} <span class="text-danger">*</span></label>
                        <input type="text" name="products[${productIndex}][variants][${variantIndex}][vendor_sku]"
                               class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="Enter variant SKU" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('catalogmanagement::product.price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="products[${productIndex}][variants][${variantIndex}][price]"
                               class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                </div>

                <!-- Discount -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label mb-2">{{ __('catalogmanagement::product.enable_discount') }}</label>
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input variant-discount-toggle" type="checkbox"
                                   name="products[${productIndex}][variants][${variantIndex}][has_discount]"
                                   id="variant_discount_${productIndex}_${variantIndex}">
                        </div>
                    </div>
                </div>

                <!-- Discount Fields -->
                <div class="discount-fields" id="variant_discount_fields_${productIndex}_${variantIndex}" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">{{ __('catalogmanagement::product.price_before_discount') }}</label>
                            <input type="number" name="products[${productIndex}][variants][${variantIndex}][price_before_discount]"
                                   class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('catalogmanagement::product.discount_end_date') }}</label>
                            <input type="date" name="products[${productIndex}][variants][${variantIndex}][discount_end_date]"
                                   class="form-control ih-medium ip-gray radius-xs b-light px-15">
                        </div>
                    </div>
                </div>

                <!-- Regional Stock -->
                <div class="stock-section">
                    <h6 class="mb-3">{{ __('catalogmanagement::product.regional_stock') }}</h6>
                    <div class="variant-stock-rows" id="variant_stock_rows_${productIndex}_${variantIndex}">
                        <div class="row stock-row mb-2">
                            <div class="col-md-4">
                                <select name="products[${productIndex}][variants][${variantIndex}][stocks][0][region_id]"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                    <option value="">{{ __('catalogmanagement::product.select_region') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="products[${productIndex}][variants][${variantIndex}][stocks][0][quantity]"
                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ __('catalogmanagement::product.quantity') }}" min="0">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-success btn-sm add-variant-stock-row"
                                        data-product-index="${productIndex}" data-variant-index="${variantIndex}">
                                    <i class="uil uil-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="products[${productIndex}][variants][${variantIndex}][variant_configuration_id]"
                       id="variant_config_id_${productIndex}_${variantIndex}" value="">
            </div>
        `;
    }

    // Load variant keys for select dropdown
    function loadVariantKeysForSelect(selector) {
        if (variantKeysData && variantKeysData.length > 0) {
            const select = $(selector);
            select.empty().append('<option value="">{{ __("catalogmanagement::product.select_variant_key") }}</option>');

            variantKeysData.forEach(key => {
                select.append(`<option value="${key.id}">${key.name}</option>`);
            });
        }
    }

    // Load variant values based on selected key
    function loadVariantValues(keyId, productIndex, variantIndex) {
        $.ajax({
            url: '/api/variant-configurations/by-key/' + keyId,
            type: 'GET',
            success: function(response) {
                const select = $(`#variant_value_${productIndex}_${variantIndex}`);
                select.empty().append('<option value="">{{ __("catalogmanagement::product.select_variant_value") }}</option>');

                if (response.data && response.data.length > 0) {
                    response.data.forEach(value => {
                        select.append(`<option value="${value.id}">${value.name}</option>`);
                    });
                }

                // Update variant configuration ID when both key and value are selected
                updateVariantConfigurationId(productIndex, variantIndex);
            },
            error: function(xhr) {
                console.error('Error loading variant values:', xhr);
            }
        });
    }

    // Update variant configuration ID
    function updateVariantConfigurationId(productIndex, variantIndex) {
        const keyId = $(`#variant_key_${productIndex}_${variantIndex}`).val();
        const valueId = $(`#variant_value_${productIndex}_${variantIndex}`).val();

        if (keyId && valueId) {
            // Find or create variant configuration
            $.ajax({
                url: '/api/variant-configurations/find-or-create',
                type: 'POST',
                data: {
                    key_id: keyId,
                    value_id: valueId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.configuration) {
                        $(`#variant_config_id_${productIndex}_${variantIndex}`).val(response.configuration.id);
                    }
                },
                error: function(xhr) {
                    console.error('Error updating variant configuration:', xhr);
                }
            });
        }
    }

    // Form validation function
    function validateForm() {
        let isValid = true;
        const errors = [];

        // Clear previous validation states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Validate vendor product form
        const vendorForm = $('#vendor-product-form');

        // Check tax selection
        const taxId = vendorForm.find('#tax_id').val();
        if (!taxId) {
            vendorForm.find('#tax_id').addClass('is-invalid');
            vendorForm.find('#tax_id').next('.invalid-feedback').text('{{ __("catalogmanagement::product.tax_required") }}');
            errors.push('{{ __("catalogmanagement::product.tax_required") }}');
            isValid = false;
        }

        // Check points
        const points = vendorForm.find('#points').val();
        if (!points || points < 0) {
            vendorForm.find('#points').addClass('is-invalid');
            vendorForm.find('#points').next('.invalid-feedback').text('{{ __("catalogmanagement::product.points_required") }}');
            errors.push('{{ __("catalogmanagement::product.points_required") }}');
            isValid = false;
        }

        // Check max per order
        const maxPerOrder = vendorForm.find('#max_per_order').val();
        if (!maxPerOrder || maxPerOrder < 1) {
            vendorForm.find('#max_per_order').addClass('is-invalid');
            vendorForm.find('#max_per_order').next('.invalid-feedback').text('{{ __("catalogmanagement::product.max_per_order_required") }}');
            errors.push('{{ __("catalogmanagement::product.max_per_order_required") }}');
            isValid = false;
        }

        // Validate product type specific fields
        const productType = $('input[name="product_type"]:checked').val();

        if (productType === 'simple') {
            // Validate simple product fields
            const sku = $('input[name="sku"]').val();
            const price = $('input[name="price"]').val();

            if (!sku || sku.trim() === '') {
                $('input[name="sku"]').addClass('is-invalid');
                $('input[name="sku"]').siblings('.invalid-feedback').text('{{ __("catalogmanagement::product.sku_required") }}');
                errors.push('{{ __("catalogmanagement::product.sku_required") }}');
                isValid = false;
            }

            if (!price || price <= 0) {
                $('input[name="price"]').addClass('is-invalid');
                $('input[name="price"]').siblings('.invalid-feedback').text('{{ __("catalogmanagement::product.price_required") }}');
                errors.push('{{ __("catalogmanagement::product.price_required") }}');
                isValid = false;
            }

            // Validate discount fields if discount is enabled
            if ($('input[name="has_discount"]').is(':checked')) {
                const priceBeforeDiscount = $('input[name="price_before_discount"]').val();
                const offerEndDate = $('input[name="offer_end_date"]').val();

                if (!priceBeforeDiscount || priceBeforeDiscount <= 0) {
                    $('input[name="price_before_discount"]').addClass('is-invalid');
                    $('input[name="price_before_discount"]').siblings('.invalid-feedback').text('{{ __("catalogmanagement::product.price_before_discount_required") }}');
                    errors.push('{{ __("catalogmanagement::product.price_before_discount_required") }}');
                    isValid = false;
                }

                if (!offerEndDate) {
                    $('input[name="offer_end_date"]').addClass('is-invalid');
                    $('input[name="offer_end_date"]').siblings('.invalid-feedback').text('{{ __("catalogmanagement::product.offer_end_date_required") }}');
                    errors.push('{{ __("catalogmanagement::product.offer_end_date_required") }}');
                    isValid = false;
                }
            }

            // Validate simple product stock management
            $('.stock-row').each(function() {
                const row = $(this);
                const regionId = row.find('select[name="region_id"], select[name*="[region_id]"]').val();
                const quantity = row.find('input[name="stock"], input[name*="[stock]"]').val();

                if (!regionId) {
                    row.find('select[name="region_id"], select[name*="[region_id]"]').addClass('is-invalid');
                    row.find('select[name="region_id"], select[name*="[region_id]"]').siblings('.invalid-feedback').text('{{ __("catalogmanagement::product.region_required") }}');
                    errors.push('{{ __("catalogmanagement::product.region_required") }}');
                    isValid = false;
                }

                if (!quantity || quantity < 0) {
                    row.find('input[name="stock"], input[name*="[stock]"]').addClass('is-invalid');
                    row.find('input[name="stock"], input[name*="[stock]"]').siblings('.invalid-feedback').text('{{ __("catalogmanagement::product.stock_required") }}');
                    errors.push('{{ __("catalogmanagement::product.stock_required") }}');
                    isValid = false;
                }
            });
        } else if (productType === 'variants') {
            // Validate variants
            $('.variant-box').each(function() {
                const variantBox = $(this);
                const variantKey = variantBox.find('.variant-key-select').val();
                const variantId = variantBox.find('.selected-variant-id').val();

                if (!variantKey) {
                    variantBox.find('.variant-key-select').addClass('is-invalid');
                    errors.push('{{ __("catalogmanagement::product.variant_key_required") }}');
                    isValid = false;
                }

                if (!variantId) {
                    errors.push('{{ __("catalogmanagement::product.variant_selection_required") }}');
                    isValid = false;
                }

                // Validate variant stock
                variantBox.find('.stock-row').each(function() {
                    const row = $(this);
                    const regionId = row.find('select[name*="[region_id]"]').val();
                    const quantity = row.find('input[name*="[quantity]"]').val();

                    if (!regionId) {
                        row.find('select[name*="[region_id]"]').addClass('is-invalid');
                        errors.push('{{ __("catalogmanagement::product.region_required") }}');
                        isValid = false;
                    }

                    if (!quantity || quantity < 0) {
                        row.find('input[name*="[quantity]"]').addClass('is-invalid');
                        errors.push('{{ __("catalogmanagement::product.quantity_required") }}');
                        isValid = false;
                    }
                });
            });
        }

        // Show validation errors
        if (!isValid) {
            // Create detailed error message
            let errorMessage = '{{ __("catalogmanagement::product.please_fill_required_fields") }}';
            if (errors.length > 0) {
                errorMessage += '\n\n{{ __("catalogmanagement::product.errors_found") }}:\n';
                errors.forEach((error, index) => {
                    errorMessage += `${index + 1}. ${error}\n`;
                });
            }

            // Show Bootstrap modal alert with detailed errors
            showBootstrapAlert('danger', errorMessage);

            // Scroll to first error field
            const firstErrorField = $('.is-invalid').first();
            if (firstErrorField.length > 0) {
                $('html, body').animate({
                    scrollTop: firstErrorField.offset().top - 100
                }, 500);
                firstErrorField.focus();
            }
        }

        return isValid;
    }

    // Save vendor products function
    function saveVendorProducts() {
        // Check if products are selected
        if (!selectedProductsData || selectedProductsData.length === 0) {
            showBootstrapAlert('warning', '{{ __("catalogmanagement::product.please_select_products_first") }}');
            return;
        }

        // Check if vendor is selected
        if (!selectedVendorId) {
            showBootstrapAlert('warning', '{{ __("catalogmanagement::product.please_select_vendor_first") }}');
            return;
        }

        // Validate form before saving
        if (!validateForm()) {
            return;
        }
        const formData = new FormData();

        // Add CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Add vendor ID
        formData.append('vendor_id', selectedVendorId);

        // Add selected product IDs
        const selectedProductIds = selectedProductsData.map(product => product.id);
        formData.append('product_ids', JSON.stringify(selectedProductIds));

        // Add vendor product data
        const vendorProductData = $('#vendor-product-form').serializeArray();
        vendorProductData.forEach(item => {
            formData.append(item.name, item.value);
        });

        // Ensure product_id is included in the main form data
        if (selectedProductsData.length > 0) {
            formData.append('product_id', selectedProductsData[0].id);
        }

        // Add product-specific data with product IDs
        selectedProductsData.forEach((product, index) => {
            // Add product ID for each product
            formData.append(`products[${index}][product_id]`, product.id);

            const productForm = $(`.card[data-product-id="${product.id}"]`);
            const productData = productForm.find('input, select').serializeArray();

            productData.forEach(item => {
                formData.append(item.name, item.value);
            });
        });

        // Debug: Log the form data being sent
        console.log('Form data being sent:');
        console.log('Selected vendor ID:', selectedVendorId);
        console.log('Selected products:', selectedProductsData);
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        // Show loading overlay
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.show({
                loadingText: '{{ __("catalogmanagement::product.saving_vendor_products") }}',
                loadingSubtext: '{{ __("common.please_wait") }}'
            });
        } else {
            // Fallback: Show simple loading overlay
            $('body').append(`
                <div id="simple-loading-overlay" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.7);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 18px;
                ">
                    <div class="text-center">
                        <div class="spinner-border text-light mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>{{ __("catalogmanagement::product.saving_vendor_products") }}...</div>
                        <div class="mt-2"><small>{{ __("common.please_wait") }}</small></div>
                    </div>
                </div>
            `);
        }

        // Disable save button and show loading state
        const $saveBtn = $('#save-vendor-products');
        const originalBtnText = $saveBtn.html();
        $saveBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            {{ __("common.saving") }}...
        `);

        // Send AJAX request
        $.ajax({
            url: config.routes.saveStock,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Hide loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.hide();
                } else {
                    $('#simple-loading-overlay').remove();
                }

                // Restore save button
                $saveBtn.prop('disabled', false).html(originalBtnText);

                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || '{{ __("catalogmanagement::product.vendor_products_saved_successfully") }}');
                    }

                    // Redirect to products page
                    setTimeout(function() {
                        window.location.href = '{{ route("admin.products.index") }}';
                    }, 1500);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || '{{ __("common.error") }}');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error saving bank stock:', xhr);
                console.error('Response:', xhr.responseJSON);
                console.error('Status:', xhr.status);
                console.error('Status Text:', xhr.statusText);

                let errorMessage = '{{ __("catalogmanagement::product.error_saving_bank_stock") }}';
                let detailedErrors = [];

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.error) {
                        errorMessage += '\n\n' + xhr.responseJSON.error;
                    }
                    if (xhr.responseJSON.errors) {
                        console.error('Validation errors:', xhr.responseJSON.errors);
                        errorMessage += '\n\n{{ __("catalogmanagement::product.validation_errors") }}:';

                        // Process Laravel validation errors
                        Object.keys(xhr.responseJSON.errors).forEach(field => {
                            const fieldErrors = xhr.responseJSON.errors[field];
                            fieldErrors.forEach(error => {
                                detailedErrors.push(`• ${field}: ${error}`);
                            });
                        });

                        if (detailedErrors.length > 0) {
                            errorMessage += '\n' + detailedErrors.join('\n');
                        }
                    }
                }

                // Hide loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.hide();
                } else {
                    $('#simple-loading-overlay').remove();
                }

                // Restore save button
                $saveBtn.prop('disabled', false).html(originalBtnText);

                showBootstrapAlert('danger', errorMessage);
            }
        });
    }

    // Utility functions
    function loadVariantKeys() {
        $.ajax({
            url: config.routes.variantKeys,
            type: 'GET',
            success: function(response) {
                variantKeysData = response.data || [];
            },
            error: function(xhr) {
                console.error('Error loading variant keys:', xhr);
                variantKeysData = [];
            }
        });
    }

    // Load regions for dropdowns (vendor-specific)
    function loadRegions() {
        if (!selectedVendorId) {
            console.log('No vendor selected, skipping region loading');
            return Promise.resolve();
        }

        console.log('Loading regions for vendor:', selectedVendorId);

        return $.ajax({
            url: '/api/area/regions',
            type: 'GET',
            data: {
                length: 1000,
                vendor_id: selectedVendorId  // Add vendor filter
            },
            success: function(response) {
                console.log('Regions response:', response);
                regionsData = (response.data || []).map(function(region) {
                    return {
                        id: region.id,
                        name: region.name || region.name_en || '-'
                    };
                });

                console.log('Processed regions data:', regionsData);

                // Update all region dropdowns
                updateRegionDropdowns();
            },
            error: function(xhr) {
                console.error('Error loading regions:', xhr);
                regionsData = [];
            }
        });
    }

    // Load taxes for the tax dropdown from controller data
    function loadTaxes() {
        console.log('Loading taxes from controller data...');

        try {
            // Get taxes from the controller data passed via window.bankStockConfig
            const taxes = window.bankStockConfig?.taxes || [];
            console.log('Taxes from controller:', taxes);

            // Update tax dropdown
            const $taxSelect = $('#tax_id');
            $taxSelect.empty().append('<option value="">{{ __("common.select_option") }}</option>');

            taxes.forEach(tax => {
                const taxName = tax.name || `Tax ${tax.id}`;
                const percentage = tax.percentage || 0;
                $taxSelect.append(`<option value="${tax.id}">${taxName}</option>`);
            });

            // Reinitialize Select2 for tax dropdown
            $taxSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __("catalogmanagement::product.select_tax") }}'
            });

            console.log('Taxes loaded successfully from controller data');
            return Promise.resolve();
        } catch (error) {
            console.error('Error loading taxes from controller data:', error);
            // Initialize empty dropdown with Select2 as fallback
            $('#tax_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __("catalogmanagement::product.select_tax") }}'
            });
            return Promise.reject(error);
        }
    }

    function updateRegionDropdowns() {
        const selectRegionText = '{{ __("catalogmanagement::product.select_region") }}';

        $('select[name*="[region_id]"]').each(function() {
            const currentValue = $(this).val();
            $(this).empty().append(`<option value="">${selectRegionText}</option>`);

            regionsData.forEach(region => {
                const selected = region.id == currentValue ? 'selected' : '';
                $(this).append(`<option value="${region.id}" ${selected}>${region.name}</option>`);
            });
        });
    }

    // Reset workflow function
    function resetWorkflow() {
        console.log('Resetting workflow...');

        // Reset all data variables
        selectedProducts = [];
        availableProducts = [];
        selectedProductsData = [];

        // Clear product selection
        $('input[name="selected_product"]').prop('checked', false);
        $('.product-card').removeClass('selected');

        // Reset step states
        $('#step-vendor').removeClass('completed');
        $('#step-products').removeClass('completed');

        // Hide all subsequent steps
        hideProductsStep();
        hideVendorProductDataStep();
        hideStockManagement();

        // Clear product search and list
        $('#product-search').val(''); // Clear search input
        $('#products-list').empty();
        $('#products-container').hide();
        $('#no-products').hide();
        $('#products-loading').hide();

        // Hide selected products summary
        $('#selected-products-summary').hide();
        $('#selected-count').text('0');

        // Reset vendor product form
        $('#vendor-product-form')[0]?.reset();

        // Clear stock management container
        $('#stock-management-container').empty();

        console.log('Workflow reset complete');
    }

    // ============================================
    // Variant Tree Functions
    // ============================================

    // Add new variant box
    function addVariantBox() {
        const template = $('#variant-box-template').html();

        if (!template) {
            console.error('❌ Variant box template not found! Make sure #variant-box-template exists in the DOM.');
            return;
        }

        const html = template
            .replace(/__VARIANT_INDEX__/g, variantCounter)
            .replace(/__VARIANT_NUMBER__/g, variantCounter + 1);

        $('#variants-container').append(html);
        $('#variants-empty-state').hide();

        // Populate variant keys
        const $keySelect = $(`#variant-${variantCounter} .variant-key-select`);
        if (variantKeysData && variantKeysData.length > 0) {
            variantKeysData.forEach(function(key) {
                $keySelect.append(`<option value="${key.id}">${key.name}</option>`);
            });
        }

        // Initialize Select2
        setTimeout(function() {
            $keySelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
            });
        }, 100);

        variantCounter++;
        console.log('✅ Variant box added');
    }

    // Load variants by key (root level - no parent)
    function loadVariantsByKey(variantIndex, keyId) {
        console.log('🌳 Loading root variants for key:', keyId);

        const $container = $(`#variant-${variantIndex} .variant-tree-container`);
        const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

        // Clear previous tree and pricing/stock
        $levelsContainer.empty();
        $container.hide();
        $(`#variant-${variantIndex}-pricing-stock`).hide().empty();
        $(`#variant-${variantIndex} .selected-variant-path`).hide();

        // Store keyId in the variant box for later use
        $(`#variant-${variantIndex}`).data('current-key-id', keyId);

        $.ajax({
            url: '{{ route("admin.api.variants-by-key") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                key_id: keyId,
            },
            success: function(response) {
                const variants = response.data || response;
                console.log('✅ Root variants loaded:', variants.length);

                if (variants.length > 0) {
                    $container.show();
                    addVariantLevel($levelsContainer, variants, variantIndex, 0, []);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading variants:', error);
            }
        });
    }

    // Add a level to the variant tree
    function addVariantLevel($container, variants, variantIndex, level, selectedPath) {
        const levelDiv = $('<div>', {
            class: 'variant-level mb-3',
            'data-level': level
        });

        const select = $('<select>', {
            class: 'form-control select2 variant-value-select',
            'data-variant-index': variantIndex,
            'data-level': level
        });

        const selectOptionText = '{{ __("common.select_option") }}';
        select.append(`<option value="">${selectOptionText}</option>`);

        variants.forEach(function(variant) {
            const hasChildren = variant.has_children || false;
            const treeIcon = hasChildren ? ' 🌳' : '';
            select.append(`<option value="${variant.id}" data-has-children="${hasChildren}">${variant.name}${treeIcon}</option>`);
        });

        levelDiv.append(select);
        $container.append(levelDiv);

        // Initialize Select2
        setTimeout(function() {
            select.select2({
                theme: 'bootstrap-5',
                width: '100%',
            });
        }, 100);
    }

    // Load child variants
    function loadChildVariants(variantIndex, parentId, level, selectedPath, keyId) {
        console.log('🌳 Loading child variants for parent:', parentId, 'at level:', level);

        const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

        // Remove all levels after current level
        $levelsContainer.find('.variant-level').each(function() {
            if (parseInt($(this).data('level')) > level) {
                $(this).remove();
            }
        });

        $.ajax({
            url: '{{ route("admin.api.variants-by-key") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                key_id: keyId,
                parent_id: parentId
            },
            success: function(response) {
                const variants = response.data || response;
                console.log('✅ Child variants loaded:', variants.length);

                if (variants.length > 0) {
                    addVariantLevel($levelsContainer, variants, variantIndex, level + 1, selectedPath);
                } else {
                    // No more children - this is the final selection
                    finalizeVariantSelection(variantIndex, parentId, selectedPath);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading child variants:', error);
            }
        });
    }

    // Finalize variant selection (leaf node reached)
    function finalizeVariantSelection(variantIndex, variantId, selectedPath) {
        console.log('✅ Finalizing variant selection:', variantId, selectedPath);

        // Store the selected variant configuration ID
        $(`#variant-${variantIndex} .selected-variant-id`).val(variantId);

        // Show selected path
        const $pathContainer = $(`#variant-${variantIndex} .selected-variant-path`);
        $pathContainer.find('.path-text').text(selectedPath.join(' → '));
        $pathContainer.show();

        // Load pricing and stock form for this variant
        loadVariantPricingStock(variantIndex, variantId, selectedPath);
    }

    // Load pricing and stock form for variant
    function loadVariantPricingStock(variantIndex, variantId, selectedPath) {
        const $container = $(`#variant-${variantIndex}-pricing-stock`);

        // Get translations
        const translations = {
            pricing_and_stock: '{{ __("catalogmanagement::product.pricing_and_stock") }}',
            vendor_sku: '{{ __("catalogmanagement::product.vendor_sku") }}',
            price: '{{ __("catalogmanagement::product.price") }}',
            enable_discount: '{{ __("catalogmanagement::product.enable_discount") }}',
            price_before_discount: '{{ __("catalogmanagement::product.price_before_discount") }}',
            discount_end_date: '{{ __("catalogmanagement::product.discount_end_date") }}',
            regional_stock: '{{ __("catalogmanagement::product.regional_stock") }}',
            select_region: '{{ __("catalogmanagement::product.select_region") }}',
            quantity: '{{ __("catalogmanagement::product.quantity") }}'
        };

        const html = `
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="uil uil-dollar-sign"></i>
                        ${translations.pricing_and_stock}
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Pricing -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">${translations.vendor_sku} <span class="text-danger">*</span></label>
                            <input type="text" name="variants[${variantIndex}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="Enter variant SKU" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">${translations.price} <span class="text-danger">*</span></label>
                            <input type="number" name="variants[${variantIndex}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Discount -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label mb-2">${translations.enable_discount}</label>
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input variant-discount-toggle" type="checkbox" name="variants[${variantIndex}][has_discount]" id="variant_discount_${variantIndex}">
                            </div>
                        </div>
                    </div>

                    <!-- Discount Fields -->
                    <div class="discount-fields" id="variant_discount_fields_${variantIndex}" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">${translations.price_before_discount}</label>
                                <input type="number" name="variants[${variantIndex}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">${translations.discount_end_date}</label>
                                <input type="date" name="variants[${variantIndex}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                            </div>
                        </div>
                    </div>

                    <!-- Regional Stock -->
                    <div class="stock-section">
                        <h6 class="mb-3">${translations.regional_stock}</h6>
                        <div class="variant-stock-rows" id="variant_stock_rows_${variantIndex}">
                            <div class="row stock-row mb-2">
                                <div class="col-md-4">
                                    <select name="variants[${variantIndex}][stocks][0][region_id]" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" required>
                                        <option value="">${translations.select_region}</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="variants[${variantIndex}][stocks][0][stock]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="${translations.quantity}" min="0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-success btn-sm add-variant-stock-row" data-variant-index="${variantIndex}">
                                        <i class="uil uil-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="variants[${variantIndex}][variant_configuration_id]" value="${variantId}">
                </div>
            </div>
        `;

        $container.html(html).show();

        // Initialize Select2 for region dropdown and populate with regions
        setTimeout(function() {
            const $regionSelect = $container.find('select[name*="[region_id]"]');
            $regionSelect.select2({ theme: 'bootstrap-5', width: '100%' });
            updateRegionDropdowns();
        }, 100);

        console.log('✅ Pricing and stock form loaded for variant:', variantIndex);
    }

    // Inline Alert Function - shows alerts at the top of stock management section
    function showBootstrapAlert(type, message) {
        console.log(`🔔 Alert [${type}]:`, message);

        // Remove existing alerts
        $('#stock-management-alert').remove();

        // Create alert HTML
        let alertClass = 'alert-info';
        let iconClass = 'uil-info-circle';

        switch(type) {
            case 'success':
                alertClass = 'alert-success';
                iconClass = 'uil-check-circle';
                break;
            case 'danger':
            case 'error':
                alertClass = 'alert-danger';
                iconClass = 'uil-exclamation-triangle';
                break;
            case 'warning':
                alertClass = 'alert-warning';
                iconClass = 'uil-exclamation-triangle';
                break;
        }

        const alertHtml = `
            <div id="stock-management-alert" class="alert ${alertClass} alert-dismissible fade show mb-4" role="alert">
                <i class="uil ${iconClass} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Insert alert at the top of the stock management section
        const $stockManagement = $('#step-variant-stock-management');
        if ($stockManagement.length > 0) {
            $stockManagement.prepend(alertHtml);
        } else {
            // Fallback: insert at the top of variants container
            $('#variants-container').prepend(alertHtml);
        }

        // Auto-hide success alerts after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('#stock-management-alert').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Scroll to the alert
        $('html, body').animate({
            scrollTop: $('#stock-management-alert').offset().top - 100
        }, 500);
    }

    // Variant Product Stock Management Helper Functions
    function addVariantStockRow(variantIndex) {
        const stockRowHtml = `
            <div class="variant-stock-row mb-3 p-3 border rounded">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">${translations.region}</label>
                        <select class="form-select region-select" name="variants[${variantIndex}][stocks][${stockRowCounter}][region_id]" required>
                            <option value="">${translations.select_region}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">${translations.quantity}</label>
                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${variantIndex}][stocks][${stockRowCounter}][quantity]"
                               placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"></label>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-stock-btn" style="display: none;"
                                onclick="removeVariantStockRow(this, ${variantIndex})">
                            <i class="uil uil-trash-alt m-0"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $(`#variant-stock-rows-${variantIndex}`).append(stockRowHtml);

        // Populate region options for the new row
        populateRegionOptions();

        // Initialize Select2 for the new region select
        setTimeout(() => {
            $(`#variant-stock-rows-${variantIndex} .region-select:last`).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select Region'
            });
        }, 100);

        stockRowCounter++;

        // Update remove button visibility
        updateRemoveButtonsVisibility(variantIndex);
    }

    function removeVariantStockRow(button, variantIndex) {
        const container = $(`#variant-stock-rows-${variantIndex}`);
        const rowCount = container.find('.variant-stock-row').length;

        // Only remove if more than 1 row exists
        if (rowCount > 1) {
            $(button).closest('.variant-stock-row').remove();
            updateRemoveButtonsVisibility(variantIndex);
        }
    }

    function updateRemoveButtonsVisibility(variantIndex) {
        const container = $(`#variant-stock-rows-${variantIndex}`);
        const rows = container.find('.variant-stock-row');
        const rowCount = rows.length;

        // Show remove buttons only if there are 2 or more rows
        if (rowCount >= 2) {
            rows.find('.remove-stock-btn').show();
        } else {
            rows.find('.remove-stock-btn').hide();
        }
    }

    function populateAllTaxOptions() {
        console.log('🔄 Fetching taxes from API:', config.routes.taxesApi);

        // Fetch taxes from the backend
        $.ajax({
            url: config.routes.taxesApi,
            type: 'GET',
            success: function(response) {
                console.log('📥 Tax API response:', response);
                const taxes = response.data || response.taxes || response;

                console.log('📋 Processing taxes:', taxes);
                console.log('🎯 Found .tax-select elements:', $('.tax-select').length);

                $('.tax-select').each(function() {
                    const taxSelect = $(this);
                    taxSelect.html('<option value="">Select Tax</option>');

                    if (taxes && taxes.length > 0) {
                        taxes.forEach(tax => {
                            console.log('➕ Adding tax option:', tax);
                            taxSelect.append(`<option value="${tax.id}">${tax.name}</option>`);
                        });
                    } else {
                        console.warn('⚠️ No taxes found in response');
                    }
                });

                console.log('✅ Tax options populated successfully. Total taxes:', taxes.length);
            },
            error: function(xhr, status, error) {
                console.error('❌ Error fetching taxes:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                // Fallback to blade data if available
                populateTaxesFromBladeData();
            }
        });
    }

    function populateTaxesFromBladeData() {
        // Use taxes passed from the blade template
        const taxes = @json($taxes ?? []);

        $('.tax-select').each(function() {
            const taxSelect = $(this);
            taxSelect.html('<option value="">Select Tax</option>');

            if (taxes && taxes.length > 0) {
                taxes.forEach(tax => {
                    const taxName = tax.name || 'Tax';
                    const taxRate = tax.tax_rate || tax.rate || 0;
                    taxSelect.append(`<option value="${tax.id}">${taxName} (${taxRate}%)</option>`);
                });
            }
        });

        console.log('✅ Tax options populated from blade data:', taxes.length);
    }

    function saveAllVariantsStock() {
        console.log('💾 Saving all variants stock...');

        // Collect all variant forms data
        const allVariantsData = new FormData();
        let hasErrors = false;

        $('.variant-form').each(function(index) {
            const form = $(this);
            const variantId = form.data('variant-id');

            // Validate required fields
            const requiredFields = form.find('[required]');
            requiredFields.each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    hasErrors = true;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Collect form data
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
                allVariantsData.append(key, value);
            }
        });

        if (hasErrors) {
            showAlert('Validation Error', 'Please fill in all required fields', 'error');
            return;
        }

        // Add global data
        allVariantsData.append('product_id', selectedProduct);
        allVariantsData.append('vendor_id', selectedVendorId);
        allVariantsData.append('type', 'variants');

        $.ajax({
            url: config.routes.saveStock,
            type: 'POST',
            data: allVariantsData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    console.log('✅ All variants stock saved successfully');
                    showAlert('Success', 'All variants stock saved successfully!', 'success');

                    // Redirect to products page after 1.5 seconds
                    setTimeout(function() {
                        window.location.href = '{{ route("admin.products.index") }}';
                    }, 1500);
                } else {
                    console.error('❌ Error saving variants stock:', response.message);
                    showAlert('Error', response.message || 'Error saving variants stock', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX error saving variants stock:', error);
                showAlert('Error', 'Error saving variants stock: ' + error, 'error');
            }
        });
    }

    // Simple Product Stock Management Helper Functions
    function addSimpleStockRow() {
        const stockRowHtml = `
            <div class="stock-row mb-3 p-3 border rounded">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">${translations.region}</label>
                        <select class="form-select region-select" name="stocks[${stockRowCounter}][region_id]" required>
                            <option value="">${translations.select_region}</option>
                            <!-- Region options will be populated -->
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">${translations.quantity}</label>
                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="stocks[${stockRowCounter}][quantity]"
                               placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-simple-stock-btn"
                                onclick="removeSimpleStockRow(this)" style="display: none;">
                            <i class="uil uil-trash-alt m-0"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#simple-stock-rows').append(stockRowHtml);

        // Populate region options for the new row
        populateRegionOptions();

        // Initialize Select2 for the new region select
        setTimeout(() => {
            $('#simple-stock-rows .region-select:last').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: translations.select_region
            });
        }, 100);

        // Update remove button visibility
        updateSimpleStockRemoveButtons();

        stockRowCounter++;
    }

    function removeSimpleStockRow(button) {
        const rowCount = $('#simple-stock-rows .stock-row').length;

        // Only remove if more than 1 row exists
        if (rowCount > 1) {
            $(button).closest('.stock-row').remove();
            updateSimpleStockRemoveButtons();
        }
    }

    function updateSimpleStockRemoveButtons() {
        const container = $('#simple-stock-rows');
        const rows = container.find('.stock-row');
        const rowCount = rows.length;

        // Show remove buttons only if there are 2 or more rows
        if (rowCount >= 2) {
            rows.find('.remove-simple-stock-btn').show();
        } else {
            rows.find('.remove-simple-stock-btn').hide();
        }
    }

    function populateTaxOptions() {
        // Use the same tax population logic as variants
        populateAllTaxOptions();
    }

    function setupSimpleProductForm() {
        $('#simple-product-form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            console.log('💾 Saving simple product stock...');

            $.ajax({
                url: config.routes.saveStock,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        console.log('✅ Simple product stock saved successfully');
                        showAlert('Success', 'Product stock saved successfully!', 'success');

                        // Redirect to products page after 1.5 seconds
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.products.index") }}';
                        }, 1500);
                    } else {
                        console.error('❌ Error saving stock:', response.message);
                        showAlert('Error', response.message || 'Error saving stock', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ AJAX error saving stock:', error);
                    showAlert('Error', 'Error saving stock: ' + error, 'error');
                }
            });
        });
    }

    function cancelStockManagement() {
        $('#step-variant-stock-management').hide();
        selectedProduct = null;
        $('.product-card').removeClass('selected');
    }

    // Discount switcher functions
    function toggleDiscountFields(variantIndex) {
        const checkbox = $(`#hasDiscount${variantIndex}`);
        const discountFields = $(`#discountFields${variantIndex}`);

        if (checkbox.is(':checked')) {
            discountFields.slideDown();
        } else {
            discountFields.slideUp();
            // Clear discount fields when disabled
            discountFields.find('input').val('');
        }
    }

    function toggleSimpleDiscountFields() {
        const checkbox = $('#hasDiscountSimple');
        const discountFields = $('#simpleDiscountFields');

        if (checkbox.is(':checked')) {
            discountFields.slideDown();
        } else {
            discountFields.slideUp();
            // Clear discount fields when disabled
            discountFields.find('input').val('');
        }
    }

    // Test function to verify validation error display
    function testValidationErrors() {
        console.log('🧪 Testing validation error display...');

        const testErrors = {
            'variants.0.sku': ['The SKU field is required'],
            'variants.0.price': ['The price must be greater than 0'],
            'variants.0.stocks.0.region_id': ['Please select a region'],
            'variants.0.stocks.0.quantity': ['The quantity must be at least 1']
        };

        displayValidationErrors(testErrors);

        console.log('✅ Test validation errors displayed. Check the form for red borders and error messages under inputs.');
    }

    // Make functions globally accessible
    window.toggleDiscountFields = toggleDiscountFields;
    window.toggleSimpleDiscountFields = toggleSimpleDiscountFields;
    window.addVariantStockRow = addVariantStockRow;
    window.removeVariantStockRow = removeVariantStockRow;
    window.addSimpleStockRow = addSimpleStockRow;
    window.removeSimpleStockRow = removeSimpleStockRow;
    window.cancelStockManagement = cancelStockManagement;
    window.testValidationErrors = testValidationErrors;

})(jQuery);
</script>
