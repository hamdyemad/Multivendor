/**
 * Vendor Form JavaScript
 * Contains all JavaScript logic for vendor creation/editing wizard
 */

console.log('🚀 Vendor form script loaded!');

// Global variables for wizard state
let currentStep = 1;
const totalSteps = 3;
let documentIndex = 0;
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
        // Check if we're in RTL mode
        const isRtl = document.documentElement.dir === 'rtl' ||
                      document.documentElement.lang === 'ar' ||
                      $('html').attr('lang') === 'ar';

        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: window.vendorFormConfig?.selectPlaceholder || 'Select...',
            dir: isRtl ? 'rtl' : 'ltr',
            language: {
                noResults: function() {
                    return isRtl ? 'لم يتم العثور على نتائج' : 'No results found';
                },
                searching: function() {
                    return isRtl ? 'جاري البحث...' : 'Searching...';
                },
                loadingMore: function() {
                    return isRtl ? 'جاري تحميل المزيد...' : 'Loading more results...';
                }
            }
        });
        console.log('Select2 initialized with ' + (isRtl ? 'RTL' : 'LTR') + ' support');
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

    // Ensure buttons are in correct initial state
    $('#prevBtn').hide(); // Should be hidden on step 1
    $('#nextBtn').show(); // Should be visible on step 1
    $('#submitBtn').hide(); // Should be hidden on step 1

    // Protect required field asterisks from being removed
    protectRequiredAsterisks();

    // Add at least one document box on page load (only for new vendors)
    const isEditMode = $('input[name="_method"][value="PUT"]').length > 0 ||
                      $('.image-preview-container img').length > 0 ||
                      $('input[name="translations"]').filter(function() { return $(this).val() !== ''; }).length > 0;

    if (!isEditMode) {
        addDocumentRow();
    }

    // Add Document Row using template
    $('#addDocument').on('click', function() {
        addDocumentRow();
    });

    // Remove Document Row
    $(document).on('click', '.remove-document-row', function() {
        $(this).closest('.document-row').remove();
        updateRemoveButtons();
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

    // Next button - Validate current step before proceeding
    $('#nextBtn').on('click', function() {
        console.log('📍 Next button clicked. Current step:', currentStep);

        // Clear previous errors
        clearAllErrors();

        // Validate current step
        const validation = validateCurrentStep(currentStep);

        if (!validation.valid) {
            console.log('❌ Validation failed:', validation.errors);

            // Show validation errors
            displayStepErrors(validation.errors, currentStep);

            // Show error alert
            const errorMessage = window.vendorFormConfig?.validationError || 'Please fill in all required fields before proceeding to the next step.';
            showErrorAlert(errorMessage);

            // Scroll to first error
            scrollToFirstError();

            return false;
        }

        console.log('✅ Validation passed. Moving to next step.');

        // Proceed to next step
        currentStep++;
        if (currentStep > totalSteps) currentStep = totalSteps;
        showStep(currentStep);

        // No review step anymore - removed step 4
    });

    // Previous button
    $('#prevBtn').on('click', function() {
        currentStep--;
        if (currentStep < 1) currentStep = 1;
        showStep(currentStep);
    });

    // Click on wizard step navigation - Validate before moving forward
    $('.wizard-step-nav').on('click', function() {
        console.log('🖱️ Wizard step clicked!');
        const targetStep = parseInt($(this).data('step'));
        console.log('Clicked step:', targetStep);

        // Allow going backward without validation
        if (targetStep < currentStep) {
            currentStep = targetStep;
            showStep(currentStep);
            return;
        }

        // Allow going to the same step
        if (targetStep === currentStep) {
            return;
        }

        // Validate all steps between current and target
        let allValid = true;
        let failedStep = null;

        for (let step = currentStep; step < targetStep; step++) {
            clearAllErrors();
            const validation = validateCurrentStep(step);

            if (!validation.valid) {
                console.log('❌ Validation failed for step:', step);
                failedStep = step;

                // Switch to the failed step
                currentStep = step;
                showStep(currentStep);

                // Show validation errors on that step
                displayStepErrors(validation.errors, step);

                // Show error alert
                const errorMessage = window.vendorFormConfig?.validationError || 'Please fill in all required fields before proceeding to the next step.';
                showErrorAlert(errorMessage);

                // Scroll to first error
                scrollToFirstError();

                allValid = false;
                break;
            }
        }

        if (allValid) {
            currentStep = targetStep;
            showStep(currentStep);

            // No review step anymore - removed step 4
        }
    });

    console.log('✅ Click handlers attached to', $('.wizard-step-nav').length, 'wizard steps');

    // Form submission handler
    $('#vendorForm').on('submit', handleFormSubmission);

    // Clear validation errors when user starts typing/selecting
    initializeErrorClearingHandlers();

    // Prevent form submission on Enter key press
    preventEnterSubmission();

    // Re-protect asterisks on any form interaction
    protectAsterisksOnInteraction();
});

/**
 * Add a new document row to the form
 */
function addDocumentRow() {
    const uniqueId = 'document_' + documentIndex + '_' + Date.now();
    const template = document.getElementById('document-row-template');
    const templateContent = template.content.cloneNode(true);
    const div = document.createElement('div');
    div.appendChild(templateContent);

    // Replace placeholders
    let html = div.innerHTML;
    html = html.replace(/__INDEX__/g, documentIndex);
    html = html.replace(/__UNIQUEID__/g, uniqueId);

    $('#documentsContainer').prepend(html);
    documentIndex++;
    updateRemoveButtons();

    // Re-initialize upload handlers for new document
    initializeDocumentUpload(uniqueId);
}

/**
 * Initialize handlers to clear validation errors when user interacts with form fields
 */
function initializeErrorClearingHandlers() {
    // Handle text inputs, textareas, email inputs, number inputs, and other common input types
    $(document).on('input keyup focus', 'input[type="text"], input[type="email"], input[type="number"], input[type="tel"], input[type="url"], input[type="password"], textarea', function() {
        clearFieldError($(this));
        clearValidationAlert();
        // Ensure asterisks remain protected after clearing errors
        protectRequiredAsterisks();
    });

    // Handle select dropdowns (including Select2)
    $(document).on('change focus', 'select', function() {
        clearFieldError($(this));
        clearValidationAlert();
        protectRequiredAsterisks();
    });

    // Handle Select2 specifically
    $(document).on('select2:select select2:unselect select2:open select2:close', 'select', function() {
        clearFieldError($(this));
        clearValidationAlert();
        protectRequiredAsterisks();
    });

    // Handle specific vendor form dropdowns - Aggressive clearing
    $(document).on('change select2:select select2:open select2:opening focus click', '#country_id, #type, #departments', function() {
        const fieldId = $(this).attr('id');
        console.log('🔧 Clearing error for:', fieldId);

        // Clear field error
        clearFieldError($(this));

        // Additional aggressive clearing for these specific fields
        clearDropdownErrors(fieldId);

        clearValidationAlert();
        protectRequiredAsterisks();
    });

    // GENERIC CLEAR: Click on any red select2 box clears it immediately
    $(document).on('click', '.select2-selection', function() {
        const selection = $(this);
        const container = selection.closest('.select2-container');
        const multipleContainer = $('.select2');
        multipleContainer.removeClass('is-invalid border-danger');
        if (selection.hasClass('is-invalid') || selection.hasClass('border-danger') || container.hasClass('is-invalid')) {
             console.log('🧹 Generic click clear');
             selection.removeClass('is-invalid border-danger');
             container.removeClass('is-invalid border-danger');
             container.closest('.form-group').find('.error-message').remove();
        }
    });

    // Handle file inputs
    $(document).on('change focus', 'input[type="file"]', function() {
        clearFieldError($(this));
        clearValidationAlert();
        // Also clear error from associated image preview container
        const previewContainer = $(this).siblings('.image-preview-container, .logo-preview-container');
        if (previewContainer.length) {
            previewContainer.removeClass('is-invalid border-danger');
        }
        // Clear error from image upload wrapper
        $(this).closest('.image-upload-wrapper').find('.error-message').remove();
        protectRequiredAsterisks();
    });

    // Handle checkboxes and radio buttons
    $(document).on('change focus', 'input[type="checkbox"], input[type="radio"]', function() {
        clearFieldError($(this));
        clearValidationAlert();
        protectRequiredAsterisks();
    });

    // Handle image preview container clicks (for image uploads)
    $(document).on('click', '.image-preview-container, .logo-preview-container', function() {
        const wrapper = $(this).closest('.image-upload-wrapper');
        const fileInput = wrapper.find('input[type="file"]');
        if (fileInput.length) {
            clearFieldError(fileInput);
            clearValidationAlert();
            $(this).removeClass('is-invalid border-danger');
            wrapper.find('.error-message').remove();
        }
    });

    console.log('✅ Error clearing handlers initialized');
}

/**
 * Prevent form submission when Enter key is pressed
 */
function preventEnterSubmission() {
    // Prevent Enter key from submitting the form
    $('#vendorForm').on('keypress', function(e) {
        // Check if Enter key was pressed (keyCode 13)
        if (e.which === 13 || e.keyCode === 13) {
            // Allow Enter in textareas (for line breaks)
            if (e.target.tagName.toLowerCase() === 'textarea') {
                return true;
            }

            // Prevent form submission for all other inputs
            e.preventDefault();
            console.log('🚫 Enter key form submission prevented');
            return false;
        }
    });

    // Also prevent keydown Enter events
    $('#vendorForm').on('keydown', function(e) {
        if (e.which === 13 || e.keyCode === 13) {
            // Allow Enter in textareas
            if (e.target.tagName.toLowerCase() === 'textarea') {
                return true;
            }

            // Prevent form submission
            e.preventDefault();
            return false;
        }
    });

    console.log('✅ Enter key form submission prevention initialized');
}

/**
 * Clear validation error for a specific field
 */
function clearFieldError($field) {
    // Remove error classes from the field itself
    $field.removeClass('is-invalid border-danger');

    // Remove error message associated with this field (but NOT asterisks in labels)
    $field.siblings('.error-message, .invalid-feedback').remove();
    $field.siblings('.text-danger:not(label .text-danger):not(.form-label .text-danger)').remove();
    $field.parent().find('.error-message, .invalid-feedback').remove();
    $field.parent().find('.text-danger:not(label .text-danger):not(.form-label .text-danger)').remove();

    // For Select2, also clear error from the Select2 container
    if ($field.hasClass('select2') || $field.data('select2')) {
        const select2Container = $field.siblings('.select2-container');
        select2Container.find('.select2-selection').removeClass('is-invalid border-danger');
        select2Container.removeClass('is-invalid border-danger');
        // Also remove error messages after Select2 container
        select2Container.siblings('.error-message, .invalid-feedback').remove();
        select2Container.next('.error-message, .invalid-feedback').remove();
    }

    // Enhanced Select2 error clearing by field ID
    const fieldId = $field.attr('id');
    if (fieldId) {
        $(`#${fieldId}_error, .${fieldId}-error`).remove();
        $(`.select2-container--bootstrap-5[data-select2-id="${fieldId}"]`).removeClass('is-invalid border-danger');
    }

    // Remove error from image preview containers
    $field.siblings('.image-preview-container, .logo-preview-container').removeClass('is-invalid border-danger');

    // Clear from form group (but preserve asterisks in labels)
    $field.closest('.form-group').find('.error-message, .invalid-feedback').remove();
    $field.closest('.form-group').find('.text-danger:not(label .text-danger):not(.form-label .text-danger)').remove();
    $field.closest('.form-group').removeClass('has-error');

    // Clear from image upload wrapper (for image components)
    $field.closest('.image-upload-wrapper').find('.error-message, .invalid-feedback').remove();
    $field.closest('.image-upload-wrapper').find('.image-preview-container, .logo-preview-container').removeClass('is-invalid border-danger');

    // Clear from document row (for document uploads)
    $field.closest('.document-row').find('.error-message, .invalid-feedback').remove();
}

/**
 * Clear validation alert at the top of the form
 */
function clearValidationAlert() {
    // Remove validation error alerts
    $('.validation-errors-alert').remove();
    $('#step-validation-alert').remove();
    $('#validation-errors-alert').remove();
}

/**
 * Aggressively clear dropdown errors for specific fields
 */
function clearDropdownErrors(fieldId) {
    console.log('🧹 Aggressive clearing for:', fieldId);

    // Remove all possible error elements
    $(`#${fieldId}`).removeClass('is-invalid border-danger');

    // Clear Select2 containers
    // Clear Select2 containers - Robust finding
    const element = $(`#${fieldId}`);
    let select2Container = element.next('.select2-container');
    if (!select2Container.length) select2Container = element.siblings('.select2-container').first();
    if (!select2Container.length) select2Container = element.closest('.form-group').find('.select2-container');

    if (select2Container.length) {
        select2Container.removeClass('is-invalid border-danger');
        select2Container.find('.select2-selection').removeClass('is-invalid border-danger');
    }

    // Clear error messages by various selectors
    $(`.error-message:contains("${fieldId}")`).remove();
    $(`[data-field="${fieldId}"] .error-message`).remove();

    // Clear from form group containing this field
    $(`#${fieldId}`).closest('.form-group').find('.error-message, .invalid-feedback').remove();
    $(`#${fieldId}`).closest('.form-group').removeClass('has-error');

    // Clear any error messages that might be after the Select2 container
    $(`#${fieldId}`).siblings('.select2-container').nextAll('.error-message, .invalid-feedback').remove();

    console.log('✅ Cleared all errors for:', fieldId);
}

/**
 * Update remove buttons visibility
 */
function updateRemoveButtons() {
    const documentRows = $('.document-row');
    // Always show remove button, even for single document
    documentRows.find('.remove-document-row').show();
}

/**
 * Initialize document upload handlers
 */
function initializeDocumentUpload(previewId) {
    const input = document.getElementById(previewId);
    if (!input) return;

    const container = document.getElementById(previewId + '-preview-container');
    const placeholder = document.getElementById(previewId + '-placeholder');
    const changeBtn = container.querySelector('.btn-change-image');
    const removeBtn = container.querySelector('.btn-remove-image');

    // Click on container to select file
    container.addEventListener('click', (e) => {
        if (!e.target.classList.contains('btn-remove-image')) {
            input.click();
        }
    });

    if (changeBtn) {
        changeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            input.click();
        });
    }

    // Handle file selection
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileName = file.name;
            placeholder.querySelector('p').textContent = fileName;

            if (removeBtn) removeBtn.style.display = 'inline-flex';
        }
    });

    // Remove file
    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            input.value = '';
            placeholder.querySelector('p').textContent = window.vendorFormConfig?.uploadText || 'Click to upload document';
            removeBtn.style.display = 'none';
        });
    }
}

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
    if (Object.keys(validationErrors).length > 0) {
        for (let field in validationErrors) {
            const bracketField = convertDotToBracket(field);
            const fieldElement = targetStep.find(`[name="${bracketField}"], [name="${bracketField}[]"], [name="${field}"], [name="${field}[]"]`).first();

            if (fieldElement.length) {
                fieldElement.addClass('is-invalid');
                fieldElement.closest('.form-group').find('.error-message').remove();

                const errorMsg = `<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${validationErrors[field][0]}</div>`;

                if (fieldElement.hasClass('select2') || fieldElement.data('select2') || fieldElement.attr('id') === 'departments') {
                    let select2Container = fieldElement.next('.select2-container');
                    if (!select2Container.length) {
                        select2Container = fieldElement.siblings('.select2-container').first();
                    }
                    if (!select2Container.length) {
                        select2Container = fieldElement.closest('.form-group').find('.select2-container');
                    }

                    if (select2Container.length) {
                        select2Container.addClass('is-invalid border-danger');
                        select2Container.find('.select2-selection').addClass('is-invalid border-danger');
                    }

                    // Append to form-group to ensure visibility
                    fieldElement.closest('.form-group').append(errorMsg);
                } else {
                    fieldElement.after(errorMsg);
                }
            }
        }
    }

    // Verify visibility
    $('.wizard-step-content').each(function(index) {
        const display = $(this).css('display');
        const hasActive = $(this).hasClass('active');
        console.log(`Step ${index + 1}: display=${display}, active=${hasActive}`);
    });

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

    // No review step anymore - removed step 4

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
    $('.border-danger').removeClass('border-danger');
    $('.validation-errors-alert').remove(); // Clear validation error alerts
    // Removed review error elements since step 4 was removed
    $('#step-validation-alert').remove();
    validationErrors = {};
}

/**
 * Helper function to convert dot notation to bracket notation
 * e.g., "translations.1.name" -> "translations[1][name]"
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
        let fieldElement = $(`[name="${bracketField}"], [name="${bracketField}[]"], [name="${field}"], [name="${field}[]"]`).first();

        // Special handling for document fields that might not be found due to dynamic indexing
        if (!fieldElement.length && field.includes('documents.')) {
            // Try to find document fields with partial matching
            const fieldParts = field.split('.');
            if (fieldParts.length >= 4 && fieldParts[0] === 'documents') {
                const docIndex = fieldParts[1];
                const langId = fieldParts[3];
                const fieldName = fieldParts[4];

                // Try to find the field with a more flexible selector
                fieldElement = $(`input[name*="documents[${docIndex}][translations][${langId}][${fieldName}]"]`).first();

                // If still not found, try any document field with the same language and field name
                if (!fieldElement.length) {
                    fieldElement = $(`input[name*="[translations][${langId}][${fieldName}]"]`).first();
                }
            }
        }

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

    // Display validation errors in an alert at the top of the form
    if (Object.keys(errors).length > 0) {
        const isRtl = document.documentElement.dir === 'rtl' ||
                      document.documentElement.lang === 'ar' ||
                      $('html').attr('lang') === 'ar';

        const config = window.vendorFormConfig;
        const pleaseCorrectErrors = config?.errorMessages?.pleaseCorrectErrors || (isRtl ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:');

        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show validation-errors-alert" role="alert">
                <div class="d-flex align-items-start">
                    <i class="uil uil-exclamation-triangle me-2" style="font-size: 18px; margin-top: 2px;"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2">${pleaseCorrectErrors}</h6>
                        ${errorListHtml}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Remove any existing validation error alerts
        $('.validation-errors-alert').remove();

        // Add the alert to the dedicated validation alerts container
        const alertsContainer = $('#validation-alerts-container');
        if (alertsContainer.length) {
            alertsContainer.html(alertHtml);
        } else {
            // Fallback: add at the position where the static alert is in the HTML
            const staticAlert = $('#validation-errors-alert');
            if (staticAlert.length) {
                staticAlert.replaceWith(alertHtml);
            } else {
                $('.card-body').prepend(alertHtml);
            }
        }

        // Scroll to the alert
        $('html, body').animate({
            scrollTop: $('.validation-errors-alert').offset().top - 100
        }, 300);
    }
}

// Removed updateReview function - no longer needed since step 4 was removed

/**
 * Handle form submission
 */
function handleFormSubmission(e) {
    e.preventDefault();

    const config = window.vendorFormConfig;
    if (!config) {
        console.error('vendorFormConfig not found!');
        return;
    }

    // Clear previous errors
    clearAllErrors();

    // Show loading overlay with appropriate message
    if (typeof LoadingOverlay !== 'undefined') {
        // Check if this is an edit operation
        const isEdit = $('input[name="_method"][value="PUT"]').length > 0;
        const loadingMessage = isEdit ?
            (config.updatingVendor || 'Updating vendor...') :
            (config.creatingVendor || 'Creating vendor...');

        // Show loading overlay with custom message
        LoadingOverlay.show({
            text: loadingMessage,
            progress: true
        });
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
                        response.message || config.vendorCreated,
                        config.redirecting
                    );
                }

                setTimeout(function() {
                    window.location.href = config.indexRoute;
                }, 1500);
            }
        },
        error: function(xhr) {
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }

            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                // Display errors on current step
                displayValidationErrors(errors);

                // Scroll to top of form on validation error
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: $('.card-body').offset().top - 100
                    }, 300);
                }, 100);
            } else {
                alert(config.errorOccurred);
            }
        }
    });
}

/**
 * Validate current step before moving to next
 */
function validateCurrentStep(step) {
    const errors = [];
    const stepElement = $(`.wizard-step-content[data-step="${step}"]`);

    if (step === 1) {
        // Step 1: Vendor Information
        // Validate name fields for all languages
        stepElement.find('input[name^="translations"][name$="[name]"]').each(function() {
            if (!$(this).val().trim()) {
                const fieldName = $(this).attr('name');
                const config = window.vendorFormConfig;
                let message;

                // Check if this is Arabic field (language ID 2) or English field (language ID 1)
                if (fieldName.includes('[2][name]')) {
                    // Arabic field
                    message = config?.errorMessages?.vendorNameArRequired || 'اسم التاجر باللغة العربية مطلوب';
                } else if (fieldName.includes('[1][name]')) {
                    // English field
                    message = config?.errorMessages?.vendorNameEnRequired || 'Vendor name in English is required';
                } else {
                    // Fallback for other languages
                    message = config?.errorMessages?.vendorNameRequired || 'Vendor name is required';
                }

                errors.push({
                    field: $(this).attr('name'),
                    message: message,
                    element: $(this)
                });
            }
        });

        // Validate departments
        const departments = $('#departments').val();
        if (!departments || departments.length === 0) {
            const config = window.vendorFormConfig;
            errors.push({
                field: 'departments',
                message: config?.errorMessages?.departmentsRequired || 'Please select at least one department',
                element: $('#departments')
            });
        }

        // Check if this is edit mode (has existing vendor data)
        const isEditMode = $('input[name="_method"][value="PUT"]').length > 0 ||
                          $('.image-preview-container img').length > 0 ||
                          $('input[name="translations"]').filter(function() { return $(this).val() !== ''; }).length > 0;

        // Validate Logo (required only for new vendor, not for edit)
        if (!isEditMode) {
            const logoInput = stepElement.find('input[name="logo"]');
            const logoPreviewContainer = stepElement.find('#logo-preview-container');
            const hasExistingLogo = logoPreviewContainer.find('img').length > 0 || logoInput.data('has-image') === true;
            const logoFile = logoInput[0]?.files?.length > 0;

            if (!hasExistingLogo && !logoFile) {
                const config = window.vendorFormConfig;
                errors.push({
                    field: 'logo',
                    message: config?.errorMessages?.logoRequired || 'Logo is required',
                    element: logoPreviewContainer.length ? logoPreviewContainer : logoInput
                });
            }
        }

        // Validate Banner (required only for new vendor, not for edit)
        if (!isEditMode) {
            const bannerInput = stepElement.find('input[name="banner"]');
            const bannerPreviewContainer = stepElement.find('#banner-preview-container');
            const hasExistingBanner = bannerPreviewContainer.find('img').length > 0 || bannerInput.data('has-image') === true;
            const bannerFile = bannerInput[0]?.files?.length > 0;

            if (!hasExistingBanner && !bannerFile) {
                const config = window.vendorFormConfig;
                errors.push({
                    field: 'banner',
                    message: config?.errorMessages?.bannerRequired || 'Banner is required',
                    element: bannerInput.length ? bannerInput : stepElement.find('#banner-preview-container')
                });
            }
        }
    } else if (step === 2) {
        // Step 2: Documents - Required validation (only for new vendors)
        const config = window.vendorFormConfig;

        // Check if this is edit mode
        const isEditMode = $('input[name="_method"][value="PUT"]').length > 0 ||
                          $('.image-preview-container img').length > 0 ||
                          $('input[name="translations"]').filter(function() { return $(this).val() !== ''; }).length > 0;

        // Check if at least one document is added (only required for new vendors)
        const documentRows = stepElement.find('.document-row');
        if (!isEditMode && documentRows.length === 0) {
            errors.push({
                field: 'documents',
                message: config?.errorMessages?.documentsRequired || 'At least one document is required',
                element: stepElement.find('#documentsContainer')
            });
        } else if (documentRows.length > 0) {
            // Validate each document row
            documentRows.each(function(index) {
                const documentRow = $(this);
                const documentIndex = documentRow.data('document-index');

                // Check if document names are provided for both English and Arabic
                let hasEnglishName = false;
                let hasArabicName = false;

                documentRow.find('input[name*="[name]"]').each(function() {
                    const inputName = $(this).attr('name');
                    const inputValue = $(this).val().trim();

                    if (inputName.includes('[1][name]') && inputValue !== '') { // English (ID: 1)
                        hasEnglishName = true;
                    }
                    if (inputName.includes('[2][name]') && inputValue !== '') { // Arabic (ID: 2)
                        hasArabicName = true;
                    }
                });

                if (!hasEnglishName) {
                    errors.push({
                        field: `documents[${documentIndex}][translations][1][name]`,
                        message: config?.errorMessages?.documentNameEnRequired || 'Document name in English is required',
                        element: documentRow.find('input[name*="[1][name]"]')
                    });
                }

                if (!hasArabicName) {
                    errors.push({
                        field: `documents[${documentIndex}][translations][2][name]`,
                        message: config?.errorMessages?.documentNameArRequired || 'Document name in Arabic is required',
                        element: documentRow.find('input[name*="[2][name]"]')
                    });
                }

                // Check if document file is uploaded
                const fileInput = documentRow.find('input[type="file"]');
                const hasFile = fileInput[0]?.files?.length > 0;
                const hasExistingFile = fileInput.data('has-file') === true;

                if (!hasFile && !hasExistingFile) {
                    errors.push({
                        field: `documents[${documentIndex}][file]`,
                        message: config?.errorMessages?.documentFileRequired || 'Document file is required',
                        element: fileInput.closest('.image-upload-wrapper').find('.image-preview-container')
                    });
                }
            });
        }
    } else if (step === 3) {
        // Step 3: Account Details
        const config = window.vendorFormConfig;

        // Validate email
        const email = stepElement.find('#email').val();
        if (!email || email.trim() === '') {
            errors.push({
                field: 'email',
                message: config?.errorMessages?.emailRequired || 'Email is required',
                element: stepElement.find('#email')
            });
        } else if (!isValidEmail(email)) {
            errors.push({
                field: 'email',
                message: config?.errorMessages?.emailInvalid || 'Please enter a valid email address',
                element: stepElement.find('#email')
            });
        }

        // Validate password (only for new vendor)
        const isEditMode = $('input[name="_method"][value="PUT"]').length > 0 ||
                          $('.image-preview-container img').length > 0 ||
                          $('input[name="translations"]').filter(function() { return $(this).val() !== ''; }).length > 0;
        const password = stepElement.find('#password').val();

        if (!isEditMode && (!password || password.trim() === '')) {
            errors.push({
                field: 'password',
                message: config?.errorMessages?.passwordRequired || 'Password is required',
                element: stepElement.find('#password')
            });
        }
    }

    return {
        valid: errors.length === 0,
        errors: errors
    };
}

/**
 * Display validation errors for a specific step
 */
function displayStepErrors(errors, step) {
    errors.forEach(error => {
        const element = error.element;

        // Add is-invalid class
        element.addClass('is-invalid');

        // Remove any existing error message
        element.closest('.form-group, .image-upload-wrapper').find('.error-message').remove();

        // Detect if message contains Arabic characters
        const hasArabic = /[\u0600-\u06FF]/.test(error.message);

        // Create error message with RTL support for Arabic messages
        const dirAttr = hasArabic ? 'dir="rtl" style="text-align: right;"' : '';
        const errorHtml = `<div class="error-message text-danger mt-1" ${dirAttr}>
            <i class="uil uil-exclamation-triangle"></i> ${error.message}
        </div>`;

        // Handle Select2 elements
        if (element.hasClass('select2') || element.data('select2') || element.attr('id') === 'departments') {
            let select2Container = element.next('.select2-container');
            if (!select2Container.length) {
                select2Container = element.siblings('.select2-container').first();
            }
            if (!select2Container.length) {
                select2Container = element.closest('.form-group').find('.select2-container');
            }

            if (select2Container.length) {
                select2Container.addClass('is-invalid border-danger');
                select2Container.find('.select2-selection').addClass('is-invalid border-danger');
            }

            // Append to form-group to ensure visibility
            element.closest('.form-group').append(errorHtml);
        }
        // Handle preview containers directly (when element IS the preview container)
        else if (element.hasClass('image-preview-container') || (element.attr('id') && element.attr('id').includes('preview-container'))) {
            // element.addClass('border-danger is-invalid');

            // Find wrapper and append error message
            const wrapper = element.closest('.image-upload-wrapper');
            if (wrapper.length) {
                wrapper.append(errorHtml);
            } else {
                element.after(errorHtml);
            }
        }
        // Handle image upload components (logo, banner) when element is input
        else if (element.closest('.image-upload-wrapper').length || element.attr('name') === 'logo' || element.attr('name') === 'banner') {
            const wrapper = element.closest('.image-upload-wrapper');
            if (wrapper.length) {
                wrapper.append(errorHtml);

                // Add border to preview container to highlight error
                const previewContainer = wrapper.find('.image-preview-container');
                if (previewContainer.length) {
                    // previewContainer.addClass('border-danger is-invalid');
                }
            }

            // Also add to sibling preview container if element is input
            // element.siblings('.image-preview-container').addClass('border-danger is-invalid');
        }
        else {
            element.after(errorHtml);
        }
    });
}

/**
 * Show error alert at top of form
 */
function showErrorAlert(message) {
    const isRtl = document.documentElement.dir === 'rtl' ||
                  document.documentElement.lang === 'ar' ||
                  $('html').attr('lang') === 'ar';

    const errorLabel = window.vendorFormConfig?.errorLabel || (isRtl ? 'خطأ' : 'Error');

    const alertHtml = `<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" id="step-validation-alert">
        <div class="d-flex align-items-center">
            <i class="uil uil-exclamation-triangle me-2" style="font-size: 20px;"></i>
            <div>
                <strong>${errorLabel}!</strong> ${message}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;

    // Remove existing alert if any
    $('#step-validation-alert').remove();

    // Add alert to the dedicated validation alerts container
    const alertsContainer = $('#validation-alerts-container');
    if (alertsContainer.length) {
        alertsContainer.html(alertHtml);
    } else {
        // Fallback: add at the position where the static alert is in the HTML
        const staticAlert = $('#validation-errors-alert');
        if (staticAlert.length) {
            staticAlert.replaceWith(alertHtml);
        } else {
            $('.card-body').prepend(alertHtml);
        }
    }
}

/**
 * Scroll to first error
 */
function scrollToFirstError() {
    const firstError = $('.is-invalid').first();
    if (firstError.length) {
        $('html, body').animate({
            scrollTop: firstError.offset().top - 150
        }, 300);

        // Focus on the field
        setTimeout(() => {
            firstError.focus();
        }, 350);
    }
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Protect required field asterisks from being removed or hidden
 */
function protectRequiredAsterisks() {
    console.log('🛡️ Protecting required field asterisks...');

    // Find all asterisks in labels and ensure they remain visible
    $('label .text-danger, .form-label .text-danger').each(function() {
        const $asterisk = $(this);

        // Ensure the asterisk is always visible
        $asterisk.css({
            'display': 'inline !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'color': '#dc3545 !important',
            'font-weight': 'bold !important'
        });

        // Add a data attribute to mark it as protected
        $asterisk.attr('data-protected', 'true');
    });

    // Set up a mutation observer to watch for changes to labels
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'attributes') {
                    // Re-protect asterisks if they've been modified
                    $(mutation.target).find('.text-danger[data-protected="true"]').each(function() {
                        const $asterisk = $(this);
                        $asterisk.css({
                            'display': 'inline !important',
                            'visibility': 'visible !important',
                            'opacity': '1 !important',
                            'color': '#dc3545 !important',
                            'font-weight': 'bold !important'
                        });
                    });
                }
            });
        });

        // Start observing the form for changes
        const formElement = document.getElementById('vendorForm');
        if (formElement) {
            observer.observe(formElement, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        }
    }

    console.log('✅ Required field asterisks protection enabled');
}

/**
 * Protect asterisks on any form interaction
 */
function protectAsterisksOnInteraction() {
    console.log('🔒 Setting up asterisk protection on form interactions...');

    // Protect asterisks on any input interaction
    $(document).on('input keyup keydown change focus blur', 'input, textarea, select', function() {
        setTimeout(function() {
            protectRequiredAsterisks();
        }, 10);
    });

    // Protect asterisks when Select2 changes
    $(document).on('select2:select select2:unselect select2:open select2:close', 'select', function() {
        setTimeout(function() {
            protectRequiredAsterisks();
        }, 10);
    });

    // Protect asterisks on file upload interactions
    $(document).on('change', 'input[type="file"]', function() {
        setTimeout(function() {
            protectRequiredAsterisks();
        }, 10);
    });

    // Protect asterisks on checkbox/radio interactions
    $(document).on('change', 'input[type="checkbox"], input[type="radio"]', function() {
        setTimeout(function() {
            protectRequiredAsterisks();
        }, 10);
    });

    // Protect asterisks when wizard steps change
    $(document).on('click', '.wizard-step-nav, #nextBtn, #prevBtn', function() {
        setTimeout(function() {
            protectRequiredAsterisks();
        }, 50);
    });

    // Protect asterisks on form validation
    $(document).on('DOMNodeInserted DOMNodeRemoved', function() {
        setTimeout(function() {
            protectRequiredAsterisks();
        }, 10);
    });

    console.log('✅ Asterisk protection on form interactions enabled');
}

/**
 * Clear field error for tags input
 */
function clearTagsInputError($container) {
    $container.removeClass('is-invalid border-danger');
    $container.siblings('.error-message, .text-danger, .invalid-feedback').remove();
    $container.parent().find('.error-message, .text-danger, .invalid-feedback').remove();
}
