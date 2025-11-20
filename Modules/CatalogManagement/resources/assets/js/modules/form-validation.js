/**
 * Product Form Validation Module
 * Handles form validation, error display, and submission
 */

class ProductFormValidation {
    constructor() {
        this.config = window.productFormConfig || {};
        this.errors = {};
    }

    /**
     * Initialize validation
     */
    init() {
        this.setupEventListeners();
        this.ensureErrorContainers();
    }

    /**
     * Setup validation event listeners
     */
    setupEventListeners() {
        // Form submission
        $('#productForm').on('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmission();
        });

        // Real-time validation
        $(document).on('input change', 'input, select, textarea', (e) => {
            this.clearFieldError($(e.target));
        });
    }

    /**
     * Handle form submission
     */
    handleFormSubmission() {
        console.log('📝 Validating and submitting form...');

        if (this.validateForm()) {
            this.submitForm();
        } else {
            console.log('❌ Form validation failed');
            this.displayValidationErrors();
        }
    }

    /**
     * Validate entire form
     */
    validateForm() {
        this.errors = {};
        let isValid = true;

        // Validate each step
        for (let step = 1; step <= 4; step++) {
            if (!this.validateStep(step)) {
                isValid = false;
            }
        }

        return isValid;
    }

    /**
     * Validate specific step
     */
    validateStep(stepNumber) {
        switch (stepNumber) {
            case 1:
                return this.validateStep1();
            case 2:
                return this.validateStep2();
            case 3:
                return this.validateStep3();
            case 4:
                return this.validateStep4();
            default:
                return true;
        }
    }

    /**
     * Validate Step 1: Basic Information
     */
    validateStep1() {
        let isValid = true;

        // Validate titles
        $('.wizard-step-content[data-step="1"] input[name*="[title]"]').each((index, element) => {
            const $element = $(element);
            if (!$element.val().trim()) {
                // Extract language info from field ID (e.g., title_en, title_ar)
                const fieldId = $element.attr('id');
                let languageLabel = '';

                if (fieldId) {
                    if (fieldId.includes('_en')) {
                        languageLabel = ' (EN)';
                    } else if (fieldId.includes('_ar')) {
                        languageLabel = ' (AR)';
                    }
                }

                const errorMessage = (this.config.titleRequired || 'Title is required') + languageLabel;
                this.addError($element.attr('name'), errorMessage);
                isValid = false;
            }
        });

        // Validate SKU
        const sku = $('#sku').val();
        if (!sku || !sku.trim()) {
            this.addError('sku', this.config.skuRequired || 'SKU is required');
            isValid = false;
        }

        // Validate points
        const points = $('#points').val();
        if (!points || points < 0) {
            this.addError('points', this.config.pointsPositive || 'Points must be a positive number');
            isValid = false;
        }

        // Validate brand
        const brandId = $('#brand_id').val();
        if (!brandId) {
            this.addError('brand_id', this.config.brandRequired || 'Brand is required');
            isValid = false;
        }

        // Validate vendor (for admin users)
        const vendorId = $('#vendor_id').val();
        if (!vendorId && $('#vendor_id').attr('type') !== 'hidden') {
            this.addError('vendor_id', this.config.vendorRequired || 'Vendor is required');
            isValid = false;
        }

        // Validate department
        const departmentId = $('#department_id').val();
        if (!departmentId) {
            this.addError('department_id', this.config.departmentRequired || 'Department is required');
            isValid = false;
        }

        // Validate category
        const categoryId = $('#category_id').val();
        if (!categoryId) {
            this.addError('category_id', this.config.categoryRequired || 'Category is required');
            isValid = false;
        }

        // Validate tax
        const taxId = $('#tax_id').val();
        if (!taxId) {
            this.addError('tax_id', this.config.taxRequired || 'Tax is required');
            isValid = false;
        }

        // Validate max per order
        const maxPerOrder = $('#max_per_order').val();
        if (!maxPerOrder || maxPerOrder < 1) {
            this.addError('max_per_order', this.config.maxPerOrderMin || 'Max per order must be at least 1');
            isValid = false;
        }

        return isValid;
    }

    /**
     * Validate Step 2: Product Details
     */
    validateStep2() {
        let isValid = true;

        // Validate main image (only required in create mode)
        const mainImageInput = $('#main_image');
        const existingImage = mainImageInput.data('existing-image');
        const isEditMode = this.config.isEditMode || window.productFormConfig?.isEditMode || false;

        // Main image is only required when creating a new product
        // In edit mode, it's optional (can keep existing image)
        if (!isEditMode && !mainImageInput.val() && !existingImage) {
            this.addError('main_image', this.config.mainImageRequired || 'Main product image is required');
            isValid = false;
        }
        return isValid;
    }

    /**
     * Validate Step 3: Variant Configurations
     */
    validateStep3() {
        let isValid = true;

        // Validate configuration type
        const configType = $('#configuration_type').val();
        if (!configType) {
            this.addError('configuration_type', this.config.productTypeRequired || 'Product type is required');
            isValid = false;
            return false;
        }

        if (configType === 'simple') {
            isValid = this.validateSimpleProduct() && isValid;
        } else if (configType === 'variants') {
            isValid = this.validateVariants() && isValid;
        }

        return isValid;
    }

    /**
     * Validate simple product
     */
    validateSimpleProduct() {
        let isValid = true;

        // Validate SKU
        const sku = $('input[name="sku"]').val();
        if (!sku || !sku.trim()) {
            this.addError('sku', this.config.skuRequired || 'SKU is required');
            isValid = false;
        }

        // Validate price
        const price = $('input[name="price"]').val();
        if (!price || parseFloat(price) <= 0) {
            this.addError('price', this.config.priceGreaterThanZero || 'Price must be greater than 0');
            isValid = false;
        }

        // Validate stock
        const stockRows = $('.stock-row');
        if (stockRows.length === 0) {
            this.addError('stocks', this.config.stockEntryRequired || 'At least one stock entry is required');
            isValid = false;
        } else {
            stockRows.each((index, row) => {
                const $row = $(row);
                const regionId = $row.find('select[name*="[region_id]"]').val();
                const quantity = $row.find('input[name*="[quantity]"]').val();

                if (!regionId) {
                    this.addError(`stocks.${index}.region_id`, this.config.regionRequired || 'Region is required');
                    isValid = false;
                }

                if (!quantity || parseInt(quantity) < 0) {
                    this.addError(`stocks.${index}.quantity`, this.config.quantityPositive || 'Quantity must be 0 or greater');
                    isValid = false;
                }
            });
        }

        return isValid;
    }

    /**
     * Validate variants
     */
    validateVariants() {
        let isValid = true;

        const variantBoxes = $('.variant-box');
        if (variantBoxes.length === 0) {
            this.addError('variants', this.config.variantRequired || 'At least one variant is required');
            isValid = false;
            return false;
        }

        variantBoxes.each((index, box) => {
            const $box = $(box);
            const variantIndex = $box.data('variant-index');

            // Validate variant key
            const keyId = $box.find('select[name*="[key_id]"]').val();
            if (!keyId) {
                this.addError(`variants.${variantIndex}.key_id`, this.config.variantKeyRequired || 'Variant key is required');
                isValid = false;
            }

            // Validate variant value
            const valueId = $box.find('input[name*="[value_id]"]').val();
            if (!valueId) {
                this.addError(`variants.${variantIndex}.value_id`, this.config.variantValueRequired || 'Variant value is required');
                isValid = false;
            }

            // Validate SKU
            const sku = $box.find('input[name*="[sku]"]').val();
            if (!sku || !sku.trim()) {
                this.addError(`variants.${variantIndex}.sku`, this.config.variantSkuRequired || 'Variant SKU is required');
                isValid = false;
            }

            // Validate price
            const price = $box.find('input[name*="[price]"]').val();
            if (!price || parseFloat(price) <= 0) {
                this.addError(`variants.${variantIndex}.price`, this.config.variantPriceGreaterThanZero || 'Variant price must be greater than 0');
                isValid = false;
            }

            // Validate variant stock
            const variantStockRows = $box.find('.variant-stock-row');
            if (variantStockRows.length === 0) {
                this.addError(`variants.${variantIndex}.stocks`, this.config.variantStockRequired || 'At least one stock entry is required for this variant');
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Validate Step 4: SEO
     */
    validateStep4() {
        let isValid = true;

        // SEO validation is optional, but we can add basic checks
        $('.wizard-step-content[data-step="4"] input[name*="[meta_title]"]').each((index, element) => {
            const $element = $(element);
            const value = $element.val();

            if (value && value.length > 60) {
                // Extract language info from field ID
                const fieldId = $element.attr('id');
                let languageLabel = '';

                if (fieldId) {
                    if (fieldId.includes('_en')) {
                        languageLabel = ' (EN)';
                    } else if (fieldId.includes('_ar')) {
                        languageLabel = ' (AR)';
                    }
                }

                const errorMessage = (this.config.metaTitleMax || 'Meta title should be 60 characters or less') + languageLabel;
                this.addError($element.attr('name'), errorMessage);
                isValid = false;
            }
        });

        $('.wizard-step-content[data-step="4"] textarea[name*="[meta_description]"]').each((index, element) => {
            const $element = $(element);
            const value = $element.val();

            if (value && value.length > 160) {
                // Extract language info from field ID
                const fieldId = $element.attr('id');
                let languageLabel = '';

                if (fieldId) {
                    if (fieldId.includes('_en')) {
                        languageLabel = ' (EN)';
                    } else if (fieldId.includes('_ar')) {
                        languageLabel = ' (AR)';
                    }
                }

                const errorMessage = (this.config.metaDescriptionMax || 'Meta description should be 160 characters or less') + languageLabel;
                this.addError($element.attr('name'), errorMessage);
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Add validation error
     */
    addError(field, message) {
        if (!this.errors[field]) {
            this.errors[field] = [];
        }
        this.errors[field].push(message);
    }

    /**
     * Clear field error
     */
    clearFieldError(element) {
        const fieldName = element.attr('name');
        if (fieldName && this.errors[fieldName]) {
            delete this.errors[fieldName];
            this.hideFieldError(element);
        }
    }

    /**
     * Display validation errors
     */
    displayValidationErrors() {
        // Clear previous errors
        $('.error-message').hide().text('');
        $('#validation-alerts-container').empty();

        if (Object.keys(this.errors).length === 0) {
            return;
        }

        // Show field-specific errors
        Object.keys(this.errors).forEach(field => {
            const messages = this.errors[field];
            this.showFieldError(field, messages[0]); // Show first error
        });

        // Show general error alert with error list
        const errorCount = Object.keys(this.errors).length;

        // Build error list HTML
        let errorListHtml = '<ul class="mb-0 mt-2">';
        Object.keys(this.errors).forEach(field => {
            const messages = this.errors[field];
            messages.forEach(message => {
                errorListHtml += `<li>${message}</li>`;
            });
        });
        errorListHtml += '</ul>';

        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                <div class="d-flex align-items-center">
                    <i class="uil uil-exclamation-triangle me-2"></i>
                    <strong>${this.config.validationErrorTitle || 'Validation Error'}! </strong>
                </div>
                <p>${(this.config.fixErrorsBeforeSubmit || ' Please fix {count} error(s) before submitting.').replace('{count}', errorCount)}</p>
                ${errorListHtml}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('#validation-alerts-container').html(alertHtml);

        // Scroll to first error
        this.scrollToFirstError();
    }

    /**
     * Show field error
     */
    showFieldError(fieldName, message) {
        const errorId = `error-${fieldName.replace(/[\[\]\.]/g, '-')}`;
        const errorElement = $(`#${errorId}`);

        if (errorElement.length) {
            errorElement.text(message).show();
        }

        // Also add error class to field
        const fieldElement = $(`[name="${fieldName}"]`);
        if (fieldElement.length) {
            fieldElement.addClass('is-invalid');
        }

        // Handle variant-related errors (e.g., variants.1.stock, variants.1.sku, etc.)
        if (fieldName.includes('variants.')) {
            const match = fieldName.match(/variants\.(\d+)\./);
            if (match) {
                const variantIndex = match[1];
                const variantBox = $(`.variant-box[data-variant-index="${variantIndex}"]`);

                if (variantBox.length) {
                    // Check if there's already an error container for this specific field
                    const hasSpecificErrorContainer = errorElement.length > 0;

                    if (!hasSpecificErrorContainer) {
                        // Show error in the variant box as an alert
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show mt-2 variant-validation-error" role="alert">
                                <i class="uil uil-exclamation-triangle me-2"></i>
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;

                        // Remove existing variant validation alerts first
                        variantBox.find('.variant-validation-error').remove();

                        // Add the alert after the variant box header
                        const cardBody = variantBox.find('.card-body').first();
                        if (cardBody.length) {
                            cardBody.prepend(alertHtml);
                        }

                        // Scroll to the variant box
                        $('html, body').animate({
                            scrollTop: variantBox.offset().top - 100
                        }, 500);
                    }
                }
            }
        }
    }

    /**
     * Hide field error
     */
    hideFieldError(element) {
        const fieldName = element.attr('name');
        const errorId = `error-${fieldName.replace(/[\[\]\.]/g, '-')}`;

        $(`#${errorId}`).hide();
        element.removeClass('is-invalid');
    }

    /**
     * Scroll to first error
     */
    scrollToFirstError() {
        const firstError = $('.is-invalid').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }

    /**
     * Ensure error containers exist
     */
    ensureErrorContainers() {
        setTimeout(() => {
            $('input, select, textarea').each((index, element) => {
                const $element = $(element);
                const fieldName = $element.attr('name');

                if (fieldName) {
                    const errorId = `error-${fieldName.replace(/[\[\]\.]/g, '-')}`;

                    if ($(`#${errorId}`).length === 0) {
                        const errorDiv = `<div class="error-message text-danger" id="${errorId}" style="display: none;"></div>`;
                        $element.closest('.form-group').append(errorDiv);
                    }
                }
            });
        }, 500);
    }

    /**
     * Clean empty stock rows before submission
     */
    cleanEmptyStockRows() {
        console.log('🧹 Cleaning empty stock rows...');

        // Find all stock rows (both simple product and variant)
        $('.stock-row, .variant-stock-row').each((index, row) => {
            const $row = $(row);
            const regionSelect = $row.find('select[name*="region_id"]');
            const quantityInput = $row.find('input[name*="quantity"]');

            const regionValue = regionSelect.val();
            const quantityValue = quantityInput.val();

            // If both fields are empty, disable the inputs so they won't be submitted
            if (!regionValue && !quantityValue) {
                console.log(`🗑️ Disabling empty stock row ${index}`);
                regionSelect.prop('disabled', true);
                quantityInput.prop('disabled', true);
            }
            // If one field is filled but the other is not, keep it enabled (will be caught by validation)
        });

        console.log('✅ Empty stock rows cleaned');
    }

    /**
     * Re-enable stock rows after submission
     */
    reEnableStockRows() {
        console.log('🔓 Re-enabling stock rows...');

        // Re-enable all disabled stock inputs
        $('.stock-row, .variant-stock-row').each((index, row) => {
            const $row = $(row);
            $row.find('select[name*="region_id"], input[name*="quantity"]').prop('disabled', false);
        });

        console.log('✅ Stock rows re-enabled');
    }

    /**
     * Submit form
     */
    submitForm() {
        // Clean empty stock rows before creating FormData
        this.cleanEmptyStockRows();

        // Trigger CKEditor to save content to textareas
        if (typeof CKEDITOR !== 'undefined') {
            for (var instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            console.log('✅ CKEditor content saved to textareas');
        }

        const form = $('#productForm')[0];
        const formData = new FormData(form);
        const url = form.action;
        const method = form.method;

        // Show loading overlay
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.show({
                text: this.config.isEditMode ? (this.config.updatingProduct || 'Updating Product...') : (this.config.creatingProduct || 'Creating Product...'),
                subtext: 'Please wait while we process your request...'
            });
        }

        // Show loading state on button
        this.showLoadingState();

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            this.handleSubmissionResponse(data);
        })
        .catch(error => {
            console.error('❌ Form submission error:', error);
            this.handleSubmissionError(error);
        })
        .finally(() => {
            this.hideLoadingState();
            // Re-enable stock rows
            this.reEnableStockRows();
            // Hide loading overlay
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }
        });
    }

    /**
     * Handle submission response
     */
    handleSubmissionResponse(data) {
        if (data.success) {
            console.log('✅ Form submitted successfully');

            // Show success message with loading overlay
            const successMessage = data.message || (this.config.isEditMode ? this.config.productUpdated : this.config.productCreated);

            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.showSuccess(successMessage, 'Redirecting...');
            } else {
                this.showSuccessMessage(successMessage);
            }

            // Redirect if specified
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        } else {
            console.log('❌ Form submission failed:', data);
            this.handleValidationErrors(data.errors || {});
        }
    }

    /**
     * Handle submission error
     */
    handleSubmissionError(error) {
        const errorMessage = this.config.errorOccurred || 'An error occurred. Please try again.';
        this.showErrorMessage(errorMessage);
    }

    /**
     * Show loading state
     */
    showLoadingState() {
        const submitBtn = $('#submitBtn');
        const loadingText = this.config.isEditMode ? this.config.updatingProduct : this.config.creatingProduct;

        submitBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            ${loadingText}
        `);
    }

    /**
     * Hide loading state
     */
    hideLoadingState() {
        const submitBtn = $('#submitBtn');
        const buttonText = this.config.isEditMode ? (this.config.updateProduct || 'Update Product') : (this.config.createProduct || 'Create Product');

        submitBtn.prop('disabled', false).html(`
            <i class="uil uil-check"></i> ${buttonText}
        `);
    }

    /**
     * Show success message
     */
    showSuccessMessage(message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="uil uil-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('#validation-alerts-container').html(alertHtml);
        $('html, body').animate({ scrollTop: 0 }, 500);
    }

    /**
     * Show error message
     */
    showErrorMessage(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="uil uil-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $('#validation-alerts-container').html(alertHtml);
        $('html, body').animate({ scrollTop: 0 }, 500);
    }

    /**
     * Handle validation errors from server
     */
    handleValidationErrors(errors) {
        this.errors = errors;
        this.displayValidationErrors();
    }
}

// Export for global use
window.ProductFormValidation = ProductFormValidation;
