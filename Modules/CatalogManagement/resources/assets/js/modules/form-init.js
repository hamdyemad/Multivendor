/**
 * Product Form Initialization Module
 * Handles form setup, configuration, and basic initialization
 */

class ProductFormInit {
    constructor() {
        this.config = window.productFormConfig || {};
        this.isInitializing = false;
    }

    /**
     * Initialize the product form
     */
    init() {
        console.log('🚀 Initializing Product Form...');

        this.ensureConfig();
        this.initializeSelect2();
        this.setupEventListeners();

        if (this.config.isEditMode) {
            this.initializeEditMode();
        }

        console.log('✅ Product Form initialized');
    }

    /**
     * Ensure product form configuration exists
     */
    ensureConfig() {
        if (!window.productFormConfig) {
            console.warn('⚠️ productFormConfig not found, creating default');
            window.productFormConfig = {
                isEditMode: false,
                selectedValues: {},
                existingVariants: []
            };
        }
        this.config = window.productFormConfig;
    }

    /**
     * Initialize Select2 dropdowns
     */
    initializeSelect2() {
        const selectElements = '#brand_id, #vendor_id, #department_id, #category_id, #sub_category_id, #tax_id, #configuration_type';

        // Wait for Select2 to be available
        const waitForSelect2 = () => {
            if (typeof $.fn.select2 !== 'undefined') {
                console.log('✅ Select2 is available, initializing...');

                $(selectElements).each(function() {
                    // Skip hidden inputs
                    if ($(this).attr('type') === 'hidden') {
                        console.log('⏭️ Skipping Select2 init for hidden input:', $(this).attr('id'));
                        return;
                    }

                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            allowClear: false
                        });
                        console.log('✅ Select2 initialized for:', $(this).attr('id'));
                    }
                });
            } else {
                console.log('⏳ Waiting for Select2 to load...');
                setTimeout(waitForSelect2, 200);
            }
        };

        setTimeout(waitForSelect2, 100);
    }

    /**
     * Setup basic event listeners
     */
    setupEventListeners() {
        // Configuration type change handler
        $('#configuration_type').on('change', (e) => {
            this.handleConfigurationTypeChange($(e.target).val());
        });
    }

    /**
     * Handle configuration type change
     */
    handleConfigurationTypeChange(selectedType) {
        console.log('🔄 Configuration type changed to:', selectedType);

        if (selectedType === 'simple') {
            $('#simple-product-section').show();
            $('#variants-section').hide();

            // Use the global productForm instance to access variants module
            if (window.productForm && window.productForm.getModule) {
                const variantsModule = window.productForm.getModule('variants');
                if (variantsModule && variantsModule.generateSimpleProductBoxes) {
                    variantsModule.generateSimpleProductBoxes();
                } else {
                    console.warn('⚠️ Variants module not available, generating simple boxes manually');
                    this.generateSimpleProductBoxesManually();
                }
            } else {
                console.warn('⚠️ ProductForm not initialized, generating simple boxes manually');
                this.generateSimpleProductBoxesManually();
            }
        } else if (selectedType === 'variants') {
            $('#simple-product-section').hide();
            $('#variants-section').show();
            $('#simple-product-details-container').empty();
        } else {
            $('#simple-product-section').hide();
            $('#variants-section').hide();
            $('#simple-product-details-container').empty();
        }
    }

    /**
     * Manually generate simple product boxes as fallback
     */
    generateSimpleProductBoxesManually() {
        const container = $("#simple-product-details-container");
        container.empty();

        // Basic product details box
        const productDetailsHtml = `
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="uil uil-receipt"></i>
                        Product Details
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" placeholder="Enter price" required>
                                <div class="error-message text-danger" id="error-price" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label d-block">Enable Discount Offer</label>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input simple-discount-toggle" type="checkbox" role="switch" name="has_discount" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="simple-discount-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Price Before Discount</label>
                                        <input type="number" name="price_before_discount" class="form-control" min="0" step="0.01" placeholder="Enter original price">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Offer End Date</label>
                                        <input type="date" name="offer_end_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Basic stock management box
        const stockManagementHtml = `
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="uil uil-package"></i>
                            Stock per Region
                        </div>
                        <button type="button" class="btn btn-primary btn-sm add-stock-row">
                            <i class="uil uil-plus"></i> Add New Region
                        </button>
                    </h5>
                    <div class="stock-empty-state text-center py-4">
                        <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mb-0">No regions added yet. Click "Add New Region" to start.</p>
                    </div>
                    <div class="stock-rows-container">
                        <!-- Stock rows will be added here -->
                    </div>
                </div>
            </div>
        `;

        container.append(productDetailsHtml);
        container.append(stockManagementHtml);

        console.log("✅ Simple product boxes generated manually");
    }

    /**
     * Initialize edit mode
     */
    initializeEditMode() {
        console.log('🔧 Initializing edit mode...');

        const selectedValues = this.config.selectedValues;
        if (!selectedValues) {
            console.warn('⚠️ No selectedValues found for edit mode');
            return;
        }

        // Initialize cascading selects
        if (selectedValues.vendor_id) {
            setTimeout(() => {
                const editModule = window.productForm?.getModule('edit');
                if (editModule && editModule.loadDepartmentsForEdit) {
                    editModule.loadDepartmentsForEdit(selectedValues.vendor_id, selectedValues.department_id);
                }
            }, 100);
        }

        if (selectedValues.department_id && selectedValues.category_id) {
            setTimeout(() => {
                const editModule = window.productForm?.getModule('edit');
                if (editModule && editModule.loadCategoriesForEdit) {
                    editModule.loadCategoriesForEdit(selectedValues.department_id, selectedValues.category_id);
                }
            }, 300);
        }

        if (selectedValues.category_id && selectedValues.sub_category_id) {
            setTimeout(() => {
                const editModule = window.productForm?.getModule('edit');
                if (editModule && editModule.loadSubCategoriesForEdit) {
                    editModule.loadSubCategoriesForEdit(selectedValues.category_id, selectedValues.sub_category_id);
                }
            }, 600);
        }

        // Set configuration type and trigger appropriate sections
        if (selectedValues.configuration_type) {
            setTimeout(() => {
                const configSelect = $('#configuration_type');
                configSelect.val(selectedValues.configuration_type);
                configSelect.trigger('change');

                console.log('🔧 Configuration type set to:', selectedValues.configuration_type);

                // Populate product details after sections are shown
                setTimeout(() => {
                    const editModule = window.productForm?.getModule('edit');
                    if (editModule && editModule.populateProductDetailsForEdit) {
                        editModule.populateProductDetailsForEdit();
                    }
                }, 200);
            }, 800);
        }
    }
}

// Export for global use
window.ProductFormInit = ProductFormInit;
