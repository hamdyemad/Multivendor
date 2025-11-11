/**
 * Product Form JavaScript
 * Contains all JavaScript logic for product creation/editing wizard
 */


// Global variables for wizard state
let currentStep = 1;
const totalSteps = 4;
let validationErrors = {};

// Immediate initialization to hide steps
document.addEventListener('DOMContentLoaded', function() {
    const allSteps = document.querySelectorAll('.wizard-step-content');
    allSteps.forEach(function(step, index) {
        if (index === 0) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
});

// Use jQuery document ready to ensure DOM and jQuery are loaded
jQuery(document).ready(function($) {
    console.log('✅ Product form jQuery ready');
    
    // Function to attach event handlers
    function attachEventHandlers() {
        console.log('🔧 Attaching event handlers to Select2 dropdowns...');
        
        // Check department select status
        const deptElement = $('#department_id');
        console.log('📍 Department element found:', deptElement.length > 0);
        console.log('📍 Department has Select2:', deptElement.hasClass('select2-hidden-accessible'));
        console.log('📍 Department value:', deptElement.val());
        
        // Remove any existing handlers to prevent duplicates
        $('#department_id').off('change.productForm select2:select.productForm');
        
        // Department change handler - Use namespaced events and listen for select2:select
        // Use event delegation on the body to ensure it survives re-initialization
        $(document).off('change.productForm', '#department_id').on('change.productForm', '#department_id', function(e) {
            console.log('🎯 Department event triggered:', e.type);
            const departmentId = $(this).val();
            console.log('🔄 Department changed:', departmentId);

        const categorySelect = $('#category_id');
        const subCategorySelect = $('#sub_category_id');

        // Reset category and subcategory
        categorySelect.empty().append('<option value="">Loading categories...</option>').prop('disabled', true).trigger('change');
        subCategorySelect.empty().append('<option value="">Select Sub Category</option>').val('').trigger('change');

        if (departmentId) {
            // Load categories for selected department
            const url = `${window.productFormConfig.categoriesRoute}?department_id=${departmentId}`;
            console.log('🌐 Fetching categories from:', url);

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('📥 Categories response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    console.log('✅ Categories API response:', response);

                    // Reset with empty option
                    categorySelect.empty().append('<option value="">Select Category</option>').prop('disabled', false);

                    // Handle API response format: {status, message, data, errors, code}
                    if (response.status && response.data && response.data.length > 0) {
                        response.data.forEach(category => {
                            categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                        });
                        console.log(`✅ Loaded ${response.data.length} categories`);
                    } else {
                        console.log('⚠️ No categories found for department:', departmentId);
                        categorySelect.append('<option value="">No categories available</option>');
                    }
                    // Refresh Select2 dropdown
                    categorySelect.trigger('change');
                })
                .catch(error => {
                    console.error('❌ Error loading categories:', error);
                    categorySelect.empty().append('<option value="">Error loading categories</option>').prop('disabled', false).trigger('change');
                });
        } else {
            categorySelect.empty().append('<option value="">Select Category</option>').prop('disabled', false).trigger('change');
        }
        });

        console.log('✅ Department handler attached');

        // Remove any existing handlers for category to prevent duplicates
        $('#category_id').off('change.productForm select2:select.productForm');
        
        // Category change handler - Use event delegation to survive re-initialization
        $(document).off('change.productForm', '#category_id').on('change.productForm', '#category_id', function(e) {
            console.log('🎯 Category event triggered:', e.type);
            const categoryId = $(this).val();
            console.log('🔄 Category changed:', categoryId);

        const subCategorySelect = $('#sub_category_id');

        // Reset subcategory
        subCategorySelect.empty().append('<option value="">Loading subcategories...</option>').prop('disabled', true).trigger('change');

        if (categoryId) {
            // Load subcategories for selected category
            const url = `${window.productFormConfig.subCategoriesRoute}?category_id=${categoryId}`;
            console.log('🌐 Fetching subcategories from:', url);

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('📥 SubCategories response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    console.log('✅ SubCategories API response:', response);

                    // Reset with empty option
                    subCategorySelect.empty().append('<option value="">Select Sub Category</option>').prop('disabled', false);

                    // Handle API response format: {status, message, data, errors, code}
                    if (response.status && response.data && response.data.length > 0) {
                        response.data.forEach(subcategory => {
                            subCategorySelect.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                        });
                        console.log(`✅ Loaded ${response.data.length} subcategories`);
                    } else {
                        console.log('⚠️ No subcategories found for category:', categoryId);
                        subCategorySelect.append('<option value="">No subcategories available</option>');
                    }
                    // Refresh Select2 dropdown
                    subCategorySelect.trigger('change');
                })
                .catch(error => {
                    console.error('❌ Error loading subcategories:', error);
                    subCategorySelect.empty().append('<option value="">Error loading subcategories</option>').prop('disabled', false).trigger('change');
                });
        } else {
            subCategorySelect.empty().append('<option value="">Select Sub Category</option>').prop('disabled', false).trigger('change');
        }
        });

        console.log('✅ Category handler attached');
        console.log('✅ All handlers ready!');
    }
    
    // Wait for Select2 to be fully initialized by the global layout
    // Check if Select2 is already initialized, if not wait
    function waitForSelect2AndAttach() {
        const deptElement = $('#department_id');
        if (deptElement.length && deptElement.hasClass('select2-hidden-accessible')) {
            // Select2 is initialized, attach handlers
            attachEventHandlers();
        } else {
            // Wait and try again
            setTimeout(waitForSelect2AndAttach, 100);
        }
    }
    
    // Start checking for Select2 initialization after a short delay
    setTimeout(waitForSelect2AndAttach, 200);

    // Initialize wizard on page load
    showStep(currentStep);

    // Next button
    $('#nextBtn').on('click', function() {
        console.log('📍 Next button clicked. Current step:', currentStep);

        // Clear previous errors
        clearAllErrors();

        // Proceed to next step
        currentStep++;
        if (currentStep > totalSteps) currentStep = totalSteps;
        showStep(currentStep);

        // Update review when going to step 4
        if (currentStep === 4) {
            updateReview();
        }
    });

    // Previous button
    $('#prevBtn').on('click', function() {
        currentStep--;
        if (currentStep < 1) currentStep = 1;
        showStep(currentStep);
    });

    // Click on wizard step navigation
    $('.wizard-step-nav').on('click', function() {
        console.log('🖱️ Wizard step clicked!');
        const targetStep = parseInt($(this).data('step'));
        console.log('Clicked step:', targetStep);

        // Clear errors when navigating
        clearAllErrors();

        currentStep = targetStep;
        showStep(currentStep);

        // Update review when going to step 4
        if (currentStep === 4) {
            updateReview();
        }
    });

    // Edit button in review page
    $(document).on('click', '.edit-step', function() {
        const targetStep = parseInt($(this).data('step'));

        // Clear any existing errors when editing
        clearAllErrors();

        currentStep = targetStep;
        showStep(currentStep);

        // Scroll to top of form
        $('html, body').animate({
            scrollTop: $('.card').offset().top - 100
        }, 300);
    });

    // Form submission handler
    $('#productForm').on('submit', handleFormSubmission);

    // Configuration Type Toggle
    $('#configuration_type').on('change', function() {
        const selectedType = $(this).val();
        
        if (selectedType === 'simple') {
            $('#simple-product-section').show();
            $('#variants-section').hide();
        } else if (selectedType === 'variants') {
            $('#simple-product-section').hide();
            $('#variants-section').show();
        } else {
            // No selection - hide both sections
            $('#simple-product-section').hide();
            $('#variants-section').hide();
        }
    });

    // Discount Checkbox Toggle
    $('#has_discount').on('change', function() {
        if ($(this).is(':checked')) {
            $('#discount-fields').slideDown();
        } else {
            $('#discount-fields').slideUp();
            $('#price_before_discount').val('');
            $('#offer_end_date').val('');
        }
    });

    // Stock Row Index
    let stockRowIndex = 0;

    // Add Stock Row
    $('#add-stock-row').on('click', function() {
        addStockRow();
    });

    // Remove Stock Row (Event Delegation)
    $(document).on('click', '.remove-stock-row', function() {
        $(this).closest('tr').remove();
        calculateTotalStock();
    });

    // Calculate Total Stock on Input Change
    $(document).on('input', '.stock-quantity', function() {
        calculateTotalStock();
    });

    console.log('✅ Product form navigation initialized');
});
/**
 * Show/Hide wizard steps
 */
function showStep(step) {

    // Hide all steps
    $('.wizard-step-content').each(function() {
        $(this).removeClass('active').css('display', 'none');
    });

    // Show target step
    const targetStep = $(`.wizard-step-content[data-step="${step}"]`);

    if (targetStep.length) {
        targetStep.addClass('active').css('display', 'block');
    } else {
    }

    // Reapply validation errors if they exist for this step
    if (Object.keys(validationErrors).length > 0 && step !== 4) {
        for (let field in validationErrors) {
            const bracketField = convertDotToBracket(field);
            const fieldElement = targetStep.find(`[name="${bracketField}"], [name="${bracketField}[]"], [name="${field}"], [name="${field}[]"]`).first();

            if (fieldElement.length) {
                fieldElement.addClass('is-invalid');
                fieldElement.closest('.form-group').find('.error-message').remove();

                const errorMsg = `<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${validationErrors[field][0]}</div>`;

                if (fieldElement.hasClass('select2') || fieldElement.data('select2')) {
                    const select2Container = fieldElement.next('.select2-container');
                    if (select2Container.length) {
                        select2Container.after(errorMsg);
                    } else {
                        fieldElement.after(errorMsg);
                    }
                } else {
                    fieldElement.after(errorMsg);
                }
            }
        }
    }

    // Update wizard navigation
    $('.wizard-step-nav').removeClass('current');
    $(`.wizard-step-nav[data-step="${step}"]`).addClass('current');

    // Mark completed steps
    $('.wizard-step-nav').each(function() {
        const stepNum = parseInt($(this).data('step'));
        if (stepNum < step) {
            $(this).addClass('completed');
        } else {
            $(this).removeClass('completed');
        }
    });

    // Update review page when navigating to step 4
    if (step === 4 && typeof updateReview === 'function') {
        updateReview();
    }

    // Update buttons
    if (step === 1) {
        $('#prevBtn').hide();
    } else {
        $('#prevBtn').show();
    }

    if (step === totalSteps) {
        $('#nextBtn').hide();
        $('#submitBtn').show();
    } else {
        $('#nextBtn').show();
        $('#submitBtn').hide();
    }

    // Scroll to top
    $('html, body').animate({
        scrollTop: $('.card-body').offset().top - 100
    }, 300);
}

/**
 * Clear all validation errors
 */
function clearAllErrors() {
    $('.error-message').remove();
    $('.is-invalid').removeClass('is-invalid');
    $('#review-validation-errors').hide();
    $('#review-errors-list').html('');
    validationErrors = {};
}

/**
 * Helper function to convert dot notation to bracket notation
 * e.g., "translations.1.title" -> "translations[1][title]"
 */
function convertDotToBracket(field) {
    const parts = field.split('.');
    if (parts.length === 1) return field;

    let result = parts[0];
    for (let i = 1; i < parts.length; i++) {
        result += `[${parts[i]}]`;
    }
    return result;
}

/**
 * Display validation errors in alert box at top of Step 4
 */
function displayValidationErrors(errors) {
    validationErrors = errors;

    let errorListHtml = '<ul class="mb-0">';

    for (let field in errors) {
        const errorMessages = errors[field];

        errorMessages.forEach(msg => {
            errorListHtml += `<li class="mb-2">${msg}</li>`;
        });

        const bracketField = convertDotToBracket(field);
        const fieldElement = $(`[name="${bracketField}"], [name="${bracketField}[]"], [name="${field}"], [name="${field}[]"]`).first();

        if (fieldElement.length) {
            fieldElement.addClass('is-invalid');

            const errorMsg = `<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${errorMessages[0]}</div>`;
            fieldElement.closest('.form-group').find('.error-message').remove();

            if (fieldElement.hasClass('select2') || fieldElement.data('select2')) {
                const select2Container = fieldElement.next('.select2-container');
                if (select2Container.length) {
                    select2Container.after(errorMsg);
                } else {
                    fieldElement.after(errorMsg);
                }
            } else {
                fieldElement.after(errorMsg);
            }
        }
    }

    errorListHtml += '</ul>';

    $('#review-errors-list').html(errorListHtml);
    $('#review-validation-errors').show();
}

/**
 * Update Review Page with form data
 */
function updateReview() {
    const config = window.productFormConfig;
    if (!config) {
        return;
    }

    // Update titles for each language
    config.languages.forEach(lang => {
        $(`.review-title-${lang.code}`).text($(`input[name="translations[${lang.id}][title]"]`).val() || '-');
    });

    // Update SKU
    $('.review-sku').text($('#sku').val() || '-');

    // Update Brand
    $('.review-brand').text($('#brand_id option:selected').text() || '-');

    // Update Price
    const price = $('#price').val();
    $('.review-price').text(price ? '$' + price : '-');

    // Update Stock
    $('.review-stock').text($('#stock_quantity').val() || '-');
}

/**
 * Handle form submission
 */
function handleFormSubmission(e) {
    e.preventDefault();

    const config = window.productFormConfig;
    if (!config) {
        return;
    }

    // Clear previous errors
    clearAllErrors();

    // Show loading overlay
    if (typeof LoadingOverlay !== 'undefined') {
        LoadingOverlay.show();
        LoadingOverlay.progressSequence([30, 60, 90]);
    }

    const formData = new FormData(this);
    const url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.animateProgressBar(100);
            }

            if (response.success) {
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.showSuccess(
                        response.message || 'Product created successfully!',
                        'Redirecting...'
                    );
                }

                setTimeout(function() {
                    window.location.href = config.indexRoute || '/admin/products';
                }, 1500);
            }
        },
        error: function(xhr) {
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }

            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                // Display errors in step 4 review page
                displayValidationErrors(errors);

                // Navigate to step 4 to show review with errors
                currentStep = 4;
                showStep(4);

                // Scroll to error alert box at top of Step 4
                setTimeout(function() {
                    const errorAlert = $('#review-validation-errors');
                    if (errorAlert.is(':visible')) {
                        $('html, body').animate({
                            scrollTop: errorAlert.offset().top - 100
                        }, 300);
                    }
                }, 100);
            } else {
                alert('An error occurred. Please try again.');
            }
        }
    });
}

/**
 * Add Stock Row to Table
 */
function addStockRow() {
    const config = window.productFormConfig;
    if (!config || !config.regions) {
        console.error('Regions data not available');
        return;
    }

    const rowIndex = $('.stock-row').length;
    
    let regionOptions = '<option value="">Select Region</option>';
    config.regions.forEach(region => {
        regionOptions += `<option value="${region.id}">${region.name}</option>`;
    });

    const rowHtml = `
        <tr class="stock-row">
            <td>
                <select name="stocks[${rowIndex}][region_id]" class="form-control select2-stock" required>
                    ${regionOptions}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${rowIndex}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt"></i>
                </button>
            </td>
        </tr>
    `;

    $('#stock-rows').append(rowHtml);
    
    // Initialize Select2 for the new row
    $('.select2-stock').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    calculateTotalStock();
}

/**
 * Calculate Total Stock
 */
function calculateTotalStock() {
    let total = 0;
    
    $('.stock-quantity').each(function() {
        const value = parseInt($(this).val()) || 0;
        total += value;
    });
    
    $('#total-stock').text(total);
}
