/**
 * Product Form Variants Module
 * Handles variant management, simple products, and stock management
 */

class ProductFormVariants {
    constructor() {
        this.config = window.productFormConfig || {};
        this.variantIndex = 0;
    }

    /**
     * Initialize variant management
     */
    init() {
        this.setupEventListeners();
    }

    /**
     * Setup variant-related event listeners
     */
    setupEventListeners() {
        // Add variant button
        $(document).on('click', '#add-variant-btn', () => {
            this.addVariantBox();
        });

        // Remove variant button
        $(document).on('click', '.remove-variant-btn', (e) => {
            this.removeVariantBox($(e.target).closest('.variant-box'));
        });

        // Discount toggle for simple products
        $(document).on('change', '.simple-discount-toggle', (e) => {
            this.toggleDiscountFields($(e.target), '.simple-discount-fields');
        });

        // Discount toggle for variants
        $(document).on('change', '.variant-discount-toggle', (e) => {
            this.toggleDiscountFields($(e.target), '.variant-discount-fields');
        });

        // Variant key selection change - load tree
        $(document).on('change', '.variant-key-select', (e) => {
            const variantBox = $(e.target).closest('.variant-box');
            const variantIndex = variantBox.data('variant-index');
            const keyId = $(e.target).val();

            console.log(`🔄 Variant key changed for variant ${variantIndex}, key: ${keyId}`);

            if (keyId) {
                this.loadVariantTree(variantBox, variantIndex, keyId);
            } else {
                // Clear tree if no key selected
                variantBox.find('.variant-tree-container').hide();
                variantBox.find('.nested-variant-levels').empty();
            }
        });

        // Variant level selection change - load next level or show stock
        $(document).on('change', '.variant-level-select', (e) => {
            const variantBox = $(e.target).closest('.variant-box');
            const variantIndex = variantBox.data('variant-index');
            const selectedValueId = $(e.target).val();
            const currentLevel = $(e.target).data('level');

            console.log(`🔄 Variant level ${currentLevel} changed, value: ${selectedValueId}`);

            if (selectedValueId) {
                this.handleVariantLevelChange(variantBox, variantIndex, selectedValueId, currentLevel);
            }
        });
    }

    /**
     * Generate simple product boxes
     */
    generateSimpleProductBoxes() {
        const container = $("#simple-product-details-container");
        container.empty();

        // Generate product details box
        const productDetailsHtml = this.generateProductDetailsBox('simple');
        container.append(productDetailsHtml);

        // Generate stock management box
        const stockManagementHtml = this.generateStockManagementBox('simple');
        container.append(stockManagementHtml);

        console.log("✅ Simple product boxes generated");
    }

    /**
     * Generate product details box
     */
    generateProductDetailsBox(type, index = null) {
        const isVariant = type === 'variant';
        const namePrefix = isVariant ? `variants[${index}]` : '';
        const idPrefix = isVariant ? `variant_${index}_` : '';
        const boxTitle = isVariant ? `${this.config.variantNumber || 'Variant'} ${index}` : (this.config.productDetails || 'Product Details');

        return `
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="uil uil-receipt"></i>
                        ${boxTitle}
                    </h5>
                    <div class="row">
                        ${isVariant ? `
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">${this.config.variantSku || 'Variant SKU'} <span class="text-danger">*</span></label>
                                <input type="text" name="${namePrefix}[sku]" id="${idPrefix}sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345" required>
                                <div class="error-message text-danger" id="error-${idPrefix}sku" style="display: none;"></div>
                            </div>
                        </div>
                        ` : ''}

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">${this.config.price || 'Price'} <span class="text-danger">*</span></label>
                                <input type="number" name="${namePrefix}${isVariant ? '[price]' : 'price'}" id="${idPrefix}price" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                <div class="error-message text-danger" id="error-${idPrefix}price" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label d-block">${this.config.enableDiscountOffer || 'Enable Discount Offer'}</label>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input ${isVariant ? 'variant' : 'simple'}-discount-toggle" type="checkbox" role="switch" name="${namePrefix}${isVariant ? '[has_discount]' : 'has_discount'}" value="1">
                                </div>
                            </div>
                        </div>

                        <!-- Discount Fields -->
                        <div class="${isVariant ? 'variant' : 'simple'}-discount-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${this.config.priceBeforeDiscount || 'Price Before Discount'}</label>
                                        <input type="number" name="${namePrefix}${isVariant ? '[price_before_discount]' : 'price_before_discount'}" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${this.config.offerEndDate || 'Offer End Date'}</label>
                                        <input type="date" name="${namePrefix}${isVariant ? '[offer_end_date]' : 'offer_end_date'}" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Generate stock management box
     */
    generateStockManagementBox(type, index = null) {
        const isVariant = type === 'variant';
        const dataAttr = isVariant ? `data-variant-index="${index}"` : '';
        const emptyStateClass = isVariant ? `variant-stock-empty-state` : 'stock-empty-state';
        const addButtonClass = isVariant ? `add-stock-row-variant` : 'add-stock-row';
        const sectionClass = isVariant ? 'variant-stock-section' : '';

        return `
            <div class="card mb-4 ${sectionClass}" ${dataAttr}>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">
                            <i class="uil uil-package"></i>
                            ${this.config.stockPerRegion || 'Stock per Region'}
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm ${addButtonClass}" ${dataAttr}>
                            <i class="uil uil-plus"></i> ${this.config.addNewRegion || 'Add New Region'}
                        </button>
                    </div>

                    <!-- Empty state message -->
                    <div class="${emptyStateClass} text-center py-4" ${dataAttr}>
                        <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mb-0">${this.config.noRegionsAddedYet || 'No regions added yet. Click "Add New Region" to start.'}</p>
                    </div>

                    <!-- Stock table -->
                    <div class="stock-table-container table-responsive" style="display: none;">
                        <table class="table mb-0 table-bordered table-hover dataTable" style="width: 100%;">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="dt-orderable-none" data-dt-column="0">
                                        <div class="dt-column-header">
                                            <span class="dt-column-title">
                                                <span class="userDatatable-title">${this.config.region || 'Region'}</span>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="dt-orderable-none" data-dt-column="1">
                                        <div class="dt-column-header">
                                            <span class="dt-column-title">
                                                <span class="userDatatable-title">${this.config.stockQuantity || 'Stock Quantity'}</span>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="text-center dt-orderable-none" data-dt-column="2">
                                        <div class="dt-column-header">
                                            <span class="dt-column-title">
                                                <span class="userDatatable-title">${this.config.actionsLabel || 'Actions'}</span>
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="stock-rows-container" ${dataAttr}>
                                <!-- Stock rows will be added here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Total stock display -->
                    <div class="total-stock-display mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <strong>${this.config.totalStock || 'Total Stock'}:</strong> <span class="total-stock-value">0</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Add variant box
     */
    addVariantBox() {
        this.variantIndex++;
        const variantHtml = this.generateVariantBox(this.variantIndex);

        $('#variants-container').append(variantHtml);
        $('#variants-empty-state').hide();

        // Initialize Select2 on the variant key select
        const variantBox = $(`.variant-box[data-variant-index="${this.variantIndex}"]`);
        const variantKeySelect = variantBox.find('.variant-key-select');

        if (variantKeySelect.length > 0 && typeof $.fn.select2 !== 'undefined') {
            variantKeySelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: variantBox,
                placeholder: this.config.selectVariantKey || 'Select Variant Key'
            });

            console.log(`✅ Select2 initialized for variant key selector ${this.variantIndex}`);
        }

        // Load variant keys
        this.loadVariantKeys(this.variantIndex);

        console.log(`✅ Added variant box ${this.variantIndex}`);

        return this.variantIndex;
    }

    /**
     * Load variant keys for a variant
     */
    loadVariantKeys(variantIndex) {
        const variantBox = $(`.variant-box[data-variant-index="${variantIndex}"]`);
        const variantKeySelect = variantBox.find('.variant-key-select');

        console.log(`🔄 Loading variant keys for variant ${variantIndex}...`);

        // Get variant keys from config
        const variantKeys = this.config.variantKeys || [];

        if (variantKeys.length === 0) {
            console.warn('⚠️ No variant keys found in config');
            variantKeySelect.html(`<option value="">${this.config.noVariantKeys || 'No variant keys available'}</option>`);
            return;
        }

        // Clear and populate options
        variantKeySelect.empty();
        variantKeySelect.append(`<option value="">${this.config.selectVariantKey || 'Select Variant Key'}</option>`);

        variantKeys.forEach(key => {
            variantKeySelect.append(`<option value="${key.id}">${key.name}</option>`);
        });

        console.log(`✅ Loaded ${variantKeys.length} variant keys`);
    }

    /**
     * Load variant tree (fetch children from API)
     */
    async loadVariantTree(variantBox, variantIndex, keyId) {
        const treeContainer = variantBox.find('.variant-tree-container');
        const nestedLevels = variantBox.find('.nested-variant-levels');

        console.log(`🌲 Loading variant tree for key ${keyId}...`);

        try {
            // Fetch variant configuration tree from API
            const response = await $.ajax({
                url: `/api/variant-configurations/key/${keyId}/tree`,
                method: 'GET',
                dataType: 'json'
            });

            console.log('🌲 Variant tree loaded:', response);

            // Clear previous tree
            nestedLevels.empty();

            // Build first level
            this.buildVariantLevel(nestedLevels, variantIndex, response, 0);

            // Show tree container
            treeContainer.show();

        } catch (error) {
            console.error('❌ Error loading variant tree:', error);
            nestedLevels.html(`<div class="alert alert-danger">${this.config.errorLoadingTree || 'Error loading variant tree'}</div>`);
        }
    }

    /**
     * Build a variant level (recursive tree building)
     */
    buildVariantLevel(container, variantIndex, data, level) {
        const levelHtml = `
            <div class="variant-level mb-3" data-level="${level}">
                <label class="form-label">
                    ${data.name || `Level ${level + 1}`}
                    <span class="text-danger">*</span>
                </label>
                <select class="form-control form-select ih-medium ip-gray radius-xs b-light px-15 variant-level-select"
                        data-level="${level}"
                        data-key-id="${data.id}"
                        required>
                    <option value="">${this.config.selectPlaceholder || 'Select'} ${data.name}</option>
                    ${(data.children || []).map(child => {
                        const hasChildren = child.children && child.children.length > 0;
                        const icon = hasChildren ? '🌳 ' : '';
                        return `<option value="${child.id}" data-has-children="${hasChildren ? 'true' : 'false'}">${icon}${child.name}</option>`;
                    }).join('')}
                </select>
            </div>
        `;

        container.append(levelHtml);

        // Initialize Select2 on the new select with custom template
        const newSelect = container.find(`.variant-level-select[data-level="${level}"]`);
        if (newSelect.length > 0 && typeof $.fn.select2 !== 'undefined') {
            newSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: container.closest('.variant-box'),
                placeholder: `${this.config.selectPlaceholder || 'Select'} ${data.name}`,
                templateResult: function(option) {
                    if (!option.id) {
                        return option.text;
                    }

                    const hasChildren = $(option.element).data('has-children') === 'true';

                    if (hasChildren) {
                        return $('<span><i class="uil uil-folder-open text-warning me-1"></i>' + option.text.replace('🌳 ', '') + '</span>');
                    }

                    return $('<span><i class="uil uil-file-alt text-muted me-1"></i>' + option.text + '</span>');
                },
                templateSelection: function(option) {
                    if (!option.id) {
                        return option.text;
                    }

                    const hasChildren = $(option.element).data('has-children') === 'true';

                    if (hasChildren) {
                        return $('<span><i class="uil uil-folder-open text-warning me-1"></i>' + option.text.replace('🌳 ', '') + '</span>');
                    }

                    return $('<span>' + option.text + '</span>');
                }
            });

            console.log(`✅ Select2 initialized for variant level ${level}`);
        }
    }

    /**
     * Handle variant level change
     */
    async handleVariantLevelChange(variantBox, variantIndex, valueId, currentLevel) {
        const nestedLevels = variantBox.find('.nested-variant-levels');
        const selectedOption = variantBox.find(`.variant-level-select[data-level="${currentLevel}"] option[value="${valueId}"]`);
        const hasChildren = selectedOption.data('has-children') === 'true' || selectedOption.data('has-children') === true;

        console.log(`🔄 Level ${currentLevel} selected, value: ${valueId}`);
        console.log(`📊 hasChildren data attribute:`, selectedOption.data('has-children'));
        console.log(`📊 hasChildren evaluated:`, hasChildren);

        // Remove all levels after current level
        variantBox.find(`.variant-level[data-level]`).each(function() {
            const level = parseInt($(this).data('level'));
            if (level > currentLevel) {
                $(this).remove();
            }
        });

        // Hide product details section when navigating tree
        variantBox.find('.variant-product-details').hide();
        variantBox.find('.variant-selection-info').hide();

        // Store the final variant value ID
        variantBox.find('.final-variant-id').val(valueId);

        if (hasChildren) {
            // Fetch and build next level
            console.log(`🌲 Fetching children for variant ${valueId}...`);
            try {
                const response = await $.ajax({
                    url: `/api/variant-configurations/${valueId}`,
                    method: 'GET',
                    dataType: 'json'
                });

                console.log('🌲 Next level API response:', response);
                console.log('🌲 Children count:', response.children ? response.children.length : 0);

                if (response.children && response.children.length > 0) {
                    console.log(`✅ Building next level (${currentLevel + 1}) with ${response.children.length} options`);
                    this.buildVariantLevel(nestedLevels, variantIndex, response, currentLevel + 1);
                } else {
                    console.log('⚠️ No children found in response - showing stock section');
                    // No more children - show stock section
                    this.showVariantStockSection(variantBox, variantIndex);
                }

            } catch (error) {
                console.error('❌ Error loading next level:', error);
                console.error('❌ Error details:', error.responseText);
            }
        } else {
            console.log('📄 Leaf node detected - showing stock section');
            // This is a leaf node - show stock section
            this.showVariantStockSection(variantBox, variantIndex);
        }
    }

    /**
     * Show product details and stock section for variant
     */
    showVariantStockSection(variantBox, variantIndex) {
        console.log(`✅ Showing product details and stock section for variant ${variantIndex}`);

        // Hide selection info
        variantBox.find('.variant-selection-info').hide();

        // Show the variant product details section (includes product details + stock)
        variantBox.find('.variant-product-details').show();

        console.log(`✅ Product details and stock section shown for variant ${variantIndex}`);
    }

    /**
     * Generate variant box HTML
     */
    generateVariantBox(index) {
        return `
            <div class="variant-box card mb-3" data-variant-index="${index}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0 text-primary variant-title">
                                <i class="uil uil-cube"></i>
                                ${this.config.variantNumber || 'Variant'} ${index}
                            </h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                            <i class="uil uil-trash-alt m-0"></i> ${this.config.remove || 'Remove'}
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">${this.config.selectVariantKey || 'Select Variant Key'} <span class="text-danger">*</span></label>
                            <select name="variants[${index}][key_id]" class="form-control form-select ih-medium ip-gray radius-xs b-light px-15 variant-key-select" required>
                                <option value="">${this.config.loadingVariantKeys || 'Loading variant keys...'}</option>
                            </select>
                            <small class="text-muted">${this.config.selectVariantKeyHelper || 'Choose a variant key to configure this variant'}</small>
                        </div>
                    </div>

                    <div class="variant-tree-container" style="display: none;">
                        <div class="nested-variant-levels">
                            <!-- Dynamic variant levels will be added here -->
                        </div>

                        <!-- Hidden input to store the final selected variant ID -->
                        <input type="hidden" name="variants[${index}][value_id]" class="final-variant-id">

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info variant-selection-info" style="display: none;">
                                    <i class="uil uil-info-circle"></i>
                                    <span class="selection-text">No variant selected</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Full Product Details Section (shown when final variant is selected) -->
                    <div class="variant-product-details mt-3" style="display: none;">
                        ${this.generateProductDetailsBox('variant', index)}
                        ${this.generateStockManagementBox('variant', index)}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Remove variant box
     */
    removeVariantBox(variantBox) {
        const variantIndex = variantBox.data('variant-index');
        console.log(`🗑️ Removing variant box ${variantIndex}`);

        variantBox.remove();

        // Show empty state if no variants left
        if ($('.variant-box').length === 0) {
            $('#variants-empty-state').show();
        }
    }

    /**
     * Toggle discount fields
     */
    toggleDiscountFields(toggleElement, fieldsSelector) {
        const discountFields = toggleElement.closest('.card').find(fieldsSelector);

        if (toggleElement.is(":checked")) {
            discountFields.show();
        } else {
            discountFields.hide();
            // Clear discount field values
            discountFields.find('input').val('');
        }
    }

    /**
     * Add stock row
     * Returns a promise that resolves when Select2 is initialized
     */
    addStockRow(container, isVariant = false, variantIndex = null) {
        return new Promise((resolve) => {
            const regions = this.config.regions || [];

            // Get the current stock row count to generate proper index
            const currentStockCount = container.find('.stock-rows-container tr').length;

            const stockRowHtml = this.generateStockRowHtml(regions, isVariant, variantIndex, currentStockCount);

            container.find('.stock-rows-container').append(stockRowHtml);
            container.find('.stock-empty-state, .variant-stock-empty-state').hide();
            container.find('.stock-table-container').show();
            container.find('.total-stock-display').show();

            // Initialize Select2 on the newly added select element
            const newRow = container.find('.stock-rows-container').children().last();
            const selectElement = newRow.find('select.select2');
            const quantityInput = newRow.find('input[name*="quantity"]');

            if (selectElement.length > 0 && typeof $.fn.select2 !== 'undefined') {
                selectElement.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: container.find('.stock-table-container'),
                    placeholder: this.config.selectPlaceholder || 'Select Region'
                });

                console.log('✅ Select2 initialized for stock region selector');

                // Resolve with the row elements after Select2 is ready
                resolve({
                    row: newRow,
                    regionSelect: selectElement,
                    quantityInput: quantityInput
                });
            } else {
                console.warn('⚠️ Select2 not available or element not found');
                resolve({
                    row: newRow,
                    regionSelect: selectElement,
                    quantityInput: quantityInput
                });
            }

            this.updateTotalStock(container);
        });
    }

    /**
     * Generate stock row HTML
     */
    generateStockRowHtml(regions, isVariant, variantIndex, stockIndex = 0) {
        const namePrefix = isVariant ? `variants[${variantIndex}]` : '';
        const rowClass = isVariant ? 'variant-stock-row' : 'stock-row';
        const dataAttr = isVariant ? `data-variant-index="${variantIndex}"` : '';

        // Generate proper indexed names instead of empty brackets
        const stocksArrayName = isVariant ? `[stocks][${stockIndex}]` : `stocks[${stockIndex}]`;

        return `
            <tr class="${rowClass}" ${dataAttr}>
                <td class="align-middle">
                    <select name="${namePrefix}${stocksArrayName}[region_id]" class="form-control form-select ih-medium ip-gray radius-xs b-light px-15 select2" required>
                        <option value="">${this.config.selectPlaceholder || 'Select Region'}</option>
                        ${regions.map(region => `<option value="${region.id}">${region.name}</option>`).join('')}
                    </select>
                </td>
                <td class="align-middle">
                    <input type="number" name="${namePrefix}${stocksArrayName}[quantity]" class="form-control ih-medium ip-gray radius-xs b-light px-15 stock-quantity" min="0" placeholder="${this.config.stockQuantity || 'Stock Quantity'}" required>
                </td>
                <td class="text-center align-middle">
                    <div class='actions'>
                        <button type="button" class="btn btn-danger btn-sm remove-stock-row" title="${this.config.remove || 'Remove'}">
                            <i class="uil uil-trash-alt m-0"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Update total stock calculation
     */
    updateTotalStock(container) {
        let totalStock = 0;

        container.find('.stock-quantity').each(function() {
            const quantity = parseInt($(this).val()) || 0;
            totalStock += quantity;
        });

        container.find('.total-stock-value').text(totalStock);
    }
}

// Export for global use
window.ProductFormVariants = ProductFormVariants;
