/**
 * Product Form JavaScript
 * Contains all JavaScript logic for product creation/editing wizard
 */

console.log('🚀 Product form script loaded!');

// Global variables for wizard state
let currentStep = 1;
const totalSteps = 4;
let validationErrors = {};

// Immediate initialization to hide steps
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - hiding all steps except first');
    const allSteps = document.querySelectorAll('.wizard-step-content');
    console.log('Found steps:', allSteps.length);
    allSteps.forEach(function(step, index) {
        if (index === 0) {
            step.classList.add('active');
            console.log('Step 1 activated');
        } else {
            step.classList.remove('active');
            console.log('Step ' + (index + 1) + ' hidden');
        }
    });
});

// Initialize on jQuery ready
$(document).ready(function() {
    console.log('✅ jQuery ready!');

    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: window.productFormConfig?.selectPlaceholder || 'Select...'
        });
        console.log('Select2 initialized');
    } else {
        console.error('Select2 not found!');
    }

    console.log('Initializing wizard...');
    console.log('Current step:', currentStep);
    console.log('Total wizard steps found:', $('.wizard-step-content').length);
    
    // Ensure only first step is visible initially
    $('.wizard-step-content').removeClass('active');
    $('.wizard-step-content[data-step="1"]').addClass('active');
    console.log('Active class set to step 1');
    
    // Initialize wizard on page load
    showStep(currentStep);

    // Department change handler - Load categories
    $('#department_id').on('change', function() {
        const departmentId = $(this).val();
        const categorySelect = $('#category_id');
        const subCategorySelect = $('#sub_category_id');
        
        console.log('Department changed:', departmentId);
        
        // Reset category and subcategory
        categorySelect.html('<option value="">Select Category</option>').trigger('change');
        subCategorySelect.html('<option value="">Select Sub Category</option>').trigger('change');
        
        if (departmentId) {
            // Load categories for selected department
            const url = `${window.productFormConfig.categoriesRoute}?department_id=${departmentId}`;
            console.log('Fetching categories from:', url);
            
            fetch(url)
                .then(response => response.json())
                .then(response => {
                    console.log('Categories API response:', response);
                    
                    // Handle API response format: {status, message, data, errors, code}
                    if (response.status && response.data && response.data.length > 0) {
                        response.data.forEach(category => {
                            categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                        });
                        console.log(`Loaded ${response.data.length} categories`);
                    } else {
                        console.log('No categories found');
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });
        }
    });

    // Category change handler - Load subcategories
    $('#category_id').on('change', function() {
        const categoryId = $(this).val();
        const subCategorySelect = $('#sub_category_id');
        
        console.log('Category changed:', categoryId);
        
        // Reset subcategory
        subCategorySelect.html('<option value="">Select Sub Category</option>').trigger('change');
        
        if (categoryId) {
            // Load subcategories for selected category
            const url = `${window.productFormConfig.subCategoriesRoute}?category_id=${categoryId}`;
            console.log('Fetching subcategories from:', url);
            
            fetch(url)
                .then(response => response.json())
                .then(response => {
                    console.log('SubCategories API response:', response);
                    
                    // Handle API response format: {status, message, data, errors, code}
                    if (response.status && response.data && response.data.length > 0) {
                        response.data.forEach(subcategory => {
                            subCategorySelect.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                        });
                        console.log(`Loaded ${response.data.length} subcategories`);
                    } else {
                        console.log('No subcategories found');
                    }
                })
                .catch(error => {
                    console.error('Error loading subcategories:', error);
                });
        }
    });

    // Edit button in review page
    $(document).on('click', '.edit-step', function() {
        const targetStep = parseInt($(this).data('step'));
        currentStep = targetStep;
        showStep(currentStep);
        
        // Scroll to top of form
        $('html, body').animate({
            scrollTop: $('.card').offset().top - 100
        }, 300);
    });

    // Next button
    $('#nextBtn').on('click', function() {
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
        const step = parseInt($(this).data('step'));
        console.log('Clicked step:', step);
        currentStep = step;
        showStep(currentStep);
        
        // Update review when going to step 4
        if (currentStep === 4) {
            updateReview();
        }
    });
    
    console.log('✅ Click handlers attached to', $('.wizard-step-nav').length, 'wizard steps');

    // Form submission handler
    $('#productForm').on('submit', handleFormSubmission);
});

/**
 * Show/Hide wizard steps
 */
function showStep(step) {
    console.log('📍 showStep called with step:', step);
    
    // Hide all steps
    $('.wizard-step-content').each(function() {
        $(this).removeClass('active').css('display', 'none');
    });
    console.log('Hidden all steps');
    
    // Show target step
    const targetStep = $(`.wizard-step-content[data-step="${step}"]`);
    console.log('Target step element:', targetStep.length ? 'Found' : 'NOT FOUND');
    
    if (targetStep.length) {
        targetStep.addClass('active').css('display', 'block');
        console.log('✅ Step', step, 'is now visible');
    } else {
        console.error('❌ Could not find step', step);
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
        console.error('productFormConfig not found!');
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
        console.error('productFormConfig not found!');
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
