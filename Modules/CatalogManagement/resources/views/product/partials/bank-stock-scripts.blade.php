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

            console.log('Bank stock management initialized successfully');
        }, 100); // Small delay to let other scripts finish
    });

    // Step 1: Vendor Selection
    function initVendorSelect() {
        const $vendorSelect = $('#vendor_select');
        $vendorSelect.select2({ theme: 'bootstrap-5', width: '100%' });

        $vendorSelect.on('change', function() {
            selectedVendorId = $(this).val();
            console.log('🏪 Vendor selected:', selectedVendorId);

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

            // Check if product is simple or has variants
            if (product.configuration_type === 'simple') {
                console.log('📦 Simple product - showing direct stock management');
                showSimpleProductStockManagement(product);
            } else {
                console.log('🎛️ Variant product - loading variants');
                loadProductVariants(productId);
            }
        } else {
            console.error('❌ Product data not found for ID:', productId);
            loadProductVariants(productId); // Fallback to variant loading
        }
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
        console.log('📦 Setting up simple product stock management for:', product.name);

        $('#step-variant-stock-management').show();
        $('#variants-loading').hide();

        // Create simple product stock form
        const simpleProductHtml = `
            <div class="simple-product-stock">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="uil uil-package me-2"></i>
                            ${translations.stock_management}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Product Info -->
                            <div class="col-md-4">
                                <div class="product-info-section">
                                    <img src="${product.image || '/images/default-product.png'}"
                                         alt="${product.name}"
                                         class="img-fluid rounded mb-3"
                                         style="max-height: 200px; object-fit: cover;">
                                    <h6>${product.name}</h6>
                                    <p class="text-muted mb-1">
                                        <i class="uil uil-tag-alt me-1"></i>
                                        Brand: ${product.brand || 'N/A'}
                                    </p>
                                    <p class="text-muted mb-1">
                                        <i class="uil uil-layer-group me-1"></i>
                                        Category: ${product.category || 'N/A'}
                                    </p>
                                </div>
                            </div>

                            <!-- Stock Management Form -->
                            <div class="col-md-8">
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
                                                <div class="discount-fields" id="simpleDiscountFields" style="display: none;">
                                                    <div class="mb-3">
                                                        <label class="form-label">${translations.price_before_discount}</label>
                                                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="price_before_discount"
                                                               placeholder="0.00" step="0.01" min="0">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">${translations.discount_end_date}</label>
                                                        <input type="datetime-local" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="discount_end_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tax</label>
                                            <select class="form-select" name="tax_id">
                                                <option value="">Select Tax</option>
                                                <!-- Tax options will be populated -->
                                            </select>
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
        $('#product-image').attr('src', product.image || '/images/default-product.png');
        $('#product-title').text(product.name);
        $('#product-category').text(product.category || 'No Category');
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
        return `
            <div class="variant-management-box mb-4" data-variant-index="${index}">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="uil uil-cube me-2"></i>
                                ${variant.name || `Variant ${variant.id}`}
                            </h6>
                            <div class="variant-info">
                                <span class="badge bg-primary">ID: ${variant.id}</span>
                                ${variant.key ? `<span class="badge bg-info ms-1">${variant.key.name}</span>` : ''}
                            </div>
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
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tax</label>
                                        <select class="form-select tax-select" name="variants[${index}][tax_id]">
                                            <option value="">Select Tax</option>
                                        </select>
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
                                                    <input type="datetime-local" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${index}][discount_end_date]">
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
        $('#save-variant-stocks').on('click', function() {
            saveVariantStocks();
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

    function saveVariantStocks() {
        const formData = $('#vendor-product-form').serialize();

        $.ajax({
            url: config.routes.saveStock,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Stocks saved successfully');
                } else {
                    toastr.error(response.message || 'Error saving stocks');
                }
            },
            error: function() {
                toastr.error('Error saving stocks');
            }
        });
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
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>${translations.price_before_discount}</label>
                                    <input type="number" name="price_before_discount" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                        <i class="uil uil-trash-alt"></i>
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
                        <div class="col-md-6">
                            <label class="form-label">{{ __('catalogmanagement::product.price_before_discount') }}</label>
                            <input type="number" name="products[${productIndex}][variants][${variantIndex}][price_before_discount]"
                                   class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
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
            showBootstrapAlert('{{ __("common.error") }}', errorMessage, 'danger');

            // Also show toastr if available
            if (typeof toastr !== 'undefined') {
                toastr.error('{{ __("catalogmanagement::product.please_fill_required_fields") }}');
            }

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
            if (typeof toastr !== 'undefined') {
                toastr.error('{{ __("catalogmanagement::product.please_select_products_first") }}');
            } else {
                showBootstrapAlert('{{ __("common.error") }}', '{{ __("catalogmanagement::product.please_select_products_first") }}', 'warning');
            }
            return;
        }

        // Check if vendor is selected
        if (!selectedVendorId) {
            if (typeof toastr !== 'undefined') {
                toastr.error('{{ __("catalogmanagement::product.please_select_vendor_first") }}');
            } else {
                showBootstrapAlert('{{ __("common.error") }}', '{{ __("catalogmanagement::product.please_select_vendor_first") }}', 'warning');
            }
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

                    // Redirect back to bank products page
                    setTimeout(function() {
                        window.location.href = '{{ route("admin.products.bank") }}';
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

                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    showBootstrapAlert('{{ __("common.error") }}', errorMessage, 'danger');
                }
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
                            <div class="col-md-6">
                                <label class="form-label">${translations.price_before_discount}</label>
                                <input type="number" name="variants[${variantIndex}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
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

    // Bootstrap Alert Modal Function
    function showBootstrapAlert(title, message, type = 'info') {
        // Remove existing alert modal if any
        $('#bootstrap-alert-modal').remove();

        // Determine icon and color based on type
        let icon = 'uil-info-circle';
        let headerClass = 'bg-primary';
        let buttonClass = 'btn-primary';

        switch(type) {
            case 'danger':
            case 'error':
                icon = 'uil-exclamation-triangle';
                headerClass = 'bg-danger';
                buttonClass = 'btn-danger';
                break;
            case 'warning':
                icon = 'uil-exclamation-triangle';
                headerClass = 'bg-warning';
                buttonClass = 'btn-warning';
                break;
            case 'success':
                icon = 'uil-check-circle';
                headerClass = 'bg-success';
                buttonClass = 'btn-success';
                break;
        }

        // Format message for HTML (convert \n to <br>)
        const formattedMessage = message.replace(/\n/g, '<br>');

        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="bootstrap-alert-modal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header ${headerClass} text-white">
                            <h5 class="modal-title" id="alertModalLabel">
                                <i class="uil ${icon} me-2"></i>${title}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-start">
                                ${formattedMessage}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn ${buttonClass}" data-bs-dismiss="modal">
                                {{ __('common.ok') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to body and show
        $('body').append(modalHtml);
        $('#bootstrap-alert-modal').modal('show');

        // Remove modal from DOM after it's hidden
        $('#bootstrap-alert-modal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    // Variant Product Stock Management Helper Functions
    function addVariantStockRow(variantIndex) {
        const stockRowHtml = `
            <div class="variant-stock-row mb-3 p-3 border rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">${translations.region}</label>
                        <select class="form-select region-select" name="variants[${variantIndex}][stocks][${stockRowCounter}][region_id]" required>
                            <option value="">${translations.select_region}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">${translations.quantity}</label>
                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${variantIndex}][stocks][${stockRowCounter}][quantity]"
                               placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">${translations.alert_quantity}</label>
                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="variants[${variantIndex}][stocks][${stockRowCounter}][alert_quantity]"
                               placeholder="0" min="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="removeVariantStockRow(this)">
                            <i class="uil uil-trash-alt"></i>
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
    }

    function removeVariantStockRow(button) {
        $(button).closest('.variant-stock-row').remove();
    }

    function populateAllTaxOptions() {
        // Fetch taxes from the backend
        $.ajax({
            url: config.routes.taxesApi,
            type: 'GET',
            success: function(response) {
                const taxes = response.data || response.taxes || response;

                $('.tax-select').each(function() {
                    const taxSelect = $(this);
                    taxSelect.html('<option value="">Select Tax</option>');

                    if (taxes && taxes.length > 0) {
                        taxes.forEach(tax => {
                            taxSelect.append(`<option value="${tax.id}">${tax.name} (${tax.rate}%)</option>`);
                        });
                    }
                });

                console.log('✅ Tax options populated:', taxes.length);
            },
            error: function(xhr, status, error) {
                console.error('❌ Error fetching taxes:', error);
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
                    taxSelect.append(`<option value="${tax.id}">${tax.name} ${tax.rate ? '(' + tax.rate + '%)' : ''}</option>`);
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
                    // Optionally reset or redirect
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
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">${translations.region}</label>
                        <select class="form-select region-select" name="stocks[${stockRowCounter}][region_id]" required>
                            <option value="">${translations.select_region}</option>
                            <!-- Region options will be populated -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">${translations.quantity}</label>
                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="stocks[${stockRowCounter}][quantity]"
                               placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">${translations.alert_quantity}</label>
                        <input type="number" class="form-control ih-medium ip-gray radius-xs b-light px-15" name="stocks[${stockRowCounter}][alert_quantity]"
                               placeholder="0" min="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="removeSimpleStockRow(this)">
                            <i class="uil uil-trash-alt"></i>
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

        stockRowCounter++;
    }

    function removeSimpleStockRow(button) {
        $(button).closest('.stock-row').remove();
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
                        // Optionally reset form or redirect
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

    // Make functions globally accessible
    window.toggleDiscountFields = toggleDiscountFields;
    window.toggleSimpleDiscountFields = toggleSimpleDiscountFields;
    window.addVariantStockRow = addVariantStockRow;
    window.removeVariantStockRow = removeVariantStockRow;
    window.addSimpleStockRow = addSimpleStockRow;
    window.removeSimpleStockRow = removeSimpleStockRow;
    window.cancelStockManagement = cancelStockManagement;

})(jQuery);
</script>
