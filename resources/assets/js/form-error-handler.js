/**
 * Global Form Error Handler
 * Provides consistent error handling and loading states for all CRUD forms
 */

class FormErrorHandler {
    constructor(options = {}) {
        this.options = {
            formSelector: '#form',
            loadingText: 'Processing...',
            successText: 'Success!',
            redirectDelay: 1500,
            scrollOffset: 100,
            showProgressBar: true,
            showErrorAlert: true,
            showFieldErrors: true,
            ...options
        };
        
        this.form = null;
        this.init();
    }

    /**
     * Initialize the form error handler
     */
    init() {
        console.log('🔍 Initializing FormErrorHandler with selector:', this.options.formSelector);
        this.form = $(this.options.formSelector);
        
        if (!this.form.length) {
            console.error('❌ FormErrorHandler: Form not found with selector:', this.options.formSelector);
            console.log('Available forms on page:', $('form').map(function() { return this.id || 'no-id'; }).get());
            return;
        }

        console.log('📋 Found form:', this.form[0]);
        this.bindEvents();
        console.log('✅ FormErrorHandler initialized successfully for:', this.options.formSelector);
    }

    /**
     * Bind form submission event
     */
    bindEvents() {
        // Mark form as initialized to prevent global interceptor
        this.form.data('form-error-handler-initialized', true);
        
        this.form.on('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
        
        // Add keyup event listeners to clear errors on input
        this.bindInputEvents();
    }
    
    /**
     * Bind input events for real-time error clearing
     */
    bindInputEvents() {
        console.log('🎧 Binding input events for real-time error clearing...');
        
        // Handle regular input fields
        this.form.on('keyup input change', 'input, textarea', (e) => {
            const input = $(e.target);
            const fieldName = input.attr('name');
            
            if (fieldName && input.hasClass('is-invalid')) {
                console.log('🧹 Clearing error for field on input:', fieldName);
                this.clearFieldError(fieldName);
            }
        });
        
        // Handle select fields (including Select2)
        this.form.on('change', 'select', (e) => {
            const select = $(e.target);
            const fieldName = select.attr('name');
            
            if (fieldName && (select.hasClass('is-invalid') || select.next('.select2-container').find('.select2-selection').hasClass('is-invalid'))) {
                console.log('🧹 Clearing error for select field:', fieldName);
                this.clearFieldError(fieldName);
            }
        });
        
        // Handle Select2 specific events
        this.form.on('select2:select select2:unselect', 'select', (e) => {
            const select = $(e.target);
            const fieldName = select.attr('name');
            
            if (fieldName) {
                console.log('🧹 Clearing error for Select2 field:', fieldName);
                this.clearFieldError(fieldName);
            }
        });
        
        console.log('✅ Input events bound successfully');
    }

    /**
     * Handle form submission
     */
    handleSubmit() {
        console.log('🚀 FormErrorHandler: Handling form submission for', this.options.formSelector);
        
        // Clear previous errors
        this.clearErrors();
        
        // Show loading overlay
        this.showLoading();
        
        // Get form data
        const formData = new FormData(this.form[0]);
        const url = this.form.attr('action');
        const method = this.form.attr('method') || 'POST';
        
        // Submit form via AJAX
        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => this.handleSuccess(response),
            error: (xhr) => this.handleError(xhr)
        });
    }

    /**
     * Handle successful form submission
     */
    handleSuccess(response) {
        // Complete progress bar
        if (typeof LoadingOverlay !== 'undefined' && this.options.showProgressBar) {
            LoadingOverlay.animateProgressBar(100);
        }
        
        const message = response.message || response.success || this.options.successText;
        
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.showSuccess(message, 'Redirecting...');
        }
        
        // Handle redirect
        setTimeout(() => {
            if (response.redirect) {
                window.location.href = response.redirect;
            } else if (this.options.redirectUrl) {
                window.location.href = this.options.redirectUrl;
            } else {
                // Try to find index route from current URL
                const currentPath = window.location.pathname;
                const indexPath = currentPath.replace(/\/(create|edit|[0-9]+).*$/, '');
                window.location.href = indexPath;
            }
        }, this.options.redirectDelay);
    }

    /**
     * Handle form submission error
     */
    handleError(xhr) {
        this.hideLoading();
        
        if (xhr.status === 422) {
            // Validation errors
            const errors = xhr.responseJSON?.errors || {};
            const message = xhr.responseJSON?.message || 'Please fix the validation errors below.';
            
            if (this.options.showErrorAlert) {
                this.displayErrorAlert(message, errors);
            }
            
            if (this.options.showFieldErrors) {
                this.displayFieldErrors(errors);
            }
            
            // Scroll to top to show error alert
            this.scrollToTop();
        } else {
            // Other errors
            const message = xhr.responseJSON?.message || 'An error occurred while processing your request.';
            
            if (this.options.showErrorAlert) {
                this.displayErrorAlert(message);
            } else {
                this.showNotification('error', message);
            }
        }
    }

    /**
     * Show loading overlay with progress
     */
    showLoading() {
        console.log('⏳ FormErrorHandler: Showing loading overlay...');
        
        if (typeof LoadingOverlay !== 'undefined') {
            console.log('✅ Using LoadingOverlay.js');
            const isEdit = this.form.find('input[name="_method"][value="PUT"]').length > 0;
            const loadingText = isEdit ? 
                this.options.loadingText.replace('Processing', 'Updating') : 
                this.options.loadingText.replace('Processing', 'Creating');
            
            console.log('📝 Loading text:', loadingText);
            
            LoadingOverlay.show({
                text: loadingText,
                progress: this.options.showProgressBar
            });
            
            if (this.options.showProgressBar) {
                LoadingOverlay.progressSequence([30, 60, 90]);
            }
        } else if ($('.loading-overlay').length) {
            console.log('✅ Using existing .loading-overlay element');
            $('.loading-overlay').show();
        } else {
            console.log('⚠️ No LoadingOverlay found, using global fallback');
            showGlobalLoader(this.options.loadingText);
        }
    }

    /**
     * Hide loading overlay
     */
    hideLoading() {
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.hide();
        } else {
            $('.loading-overlay').hide();
        }
    }

    /**
     * Clear all form errors
     */
    clearErrors() {
        console.log('🧹 FormErrorHandler: Clearing all errors...');
        
        // Clear error alert
        $('#form-error-alert').remove();
        
        // Clear field errors
        $('.error-message').hide().text('').removeClass('text-danger');
        $('.form-control, .select2-selection').removeClass('is-invalid border-danger text-danger');
        
        // Clear Select2 errors
        $('.select2-container .select2-selection').removeClass('is-invalid border-danger');
        
        // Clear any existing validation classes
        $('.form-group').removeClass('has-error');
        
        console.log('✅ FormErrorHandler: All errors cleared');
    }
    
    /**
     * Clear error for a specific field
     */
    clearFieldError(fieldName) {
        console.log('🧹 Clearing error for specific field:', fieldName);
        
        // Clear error message containers
        const errorContainerSelectors = [
            `#error-${fieldName.replace(/\./g, '\\.')}`,
            `#error-${fieldName.replace(/\./g, '-')}`,
            `[data-error-for="${fieldName}"]`,
            `.error-${fieldName.replace(/\./g, '-')}`
        ];
        
        errorContainerSelectors.forEach(selector => {
            const container = $(selector);
            if (container.length) {
                container.hide().text('').removeClass('text-danger');
                console.log('✅ Cleared error container:', selector);
            }
        });
        
        // Clear input field styling
        const inputField = $(`[name="${fieldName}"]`);
        if (inputField.length) {
            inputField.removeClass('is-invalid border-danger text-danger');
            inputField.closest('.form-group').removeClass('has-error');
            
            // Clear Select2 styling
            if (inputField.hasClass('select2') || inputField.data('select2')) {
                inputField.next('.select2-container').find('.select2-selection').removeClass('is-invalid border-danger');
            }
            
            // Stop class protection for this field
            const element = inputField[0];
            if (element._classProtector) {
                element._classProtector.disconnect();
                delete element._classProtector;
                console.log('🛡️ Disabled class protection for:', fieldName);
            }
            
            console.log('✅ Cleared field styling for:', fieldName);
        }
        
        // Check if all errors are cleared and hide main error alert
        const remainingErrors = this.form.find('.error-message:visible, .is-invalid');
        if (remainingErrors.length === 0) {
            $('#form-error-alert').fadeOut();
            console.log('✅ All field errors cleared, hiding main error alert');
        }
    }

    /**
     * Display error alert at top of form
     */
    displayErrorAlert(message, errors = null) {
        // Remove existing error alert
        $('#form-error-alert').remove();
        
        let errorHtml = `
            <div id="form-error-alert" class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="uil uil-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> ${message}
                </div>
        `;
        
        if (errors && Object.keys(errors).length > 0) {
            errorHtml += '<div class="form-error-list mt-3"><ul class="mb-0">';
            
            for (let field in errors) {
                const errorMessages = errors[field];
                errorMessages.forEach(msg => {
                    errorHtml += `<li class="mb-1"><span>${msg}</span></li>`;
                });
            }
            
            errorHtml += '</ul></div>';
        }
        
        errorHtml += `
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Insert error alert at top of form container
        const formContainer = this.form.closest('.card-body, .form-container, .container');
        if (formContainer.length) {
            formContainer.prepend(errorHtml);
        } else {
            this.form.before(errorHtml);
        }
    }

    /**
     * Display field-specific errors
     */
    displayFieldErrors(errors) {
        console.log('🚨 FormErrorHandler: Displaying field errors:', errors);
        
        $.each(errors, (fieldName, messages) => {
            console.log(`🔍 Processing error for field: ${fieldName} - ${messages[0]}`);
            
            // Try multiple error container patterns
            const errorContainerSelectors = [
                `#error-${fieldName.replace(/\./g, '\\.')}`,
                `#error-${fieldName.replace(/\./g, '-')}`,
                `[data-error-for="${fieldName}"]`,
                `.error-${fieldName.replace(/\./g, '-')}`
            ];
            
            let errorContainer = null;
            let usedSelector = '';
            
            for (let selector of errorContainerSelectors) {
                errorContainer = $(selector);
                if (errorContainer.length) {
                    usedSelector = selector;
                    break;
                }
            }
            
            if (errorContainer && errorContainer.length) {
                console.log(`✅ Found error container with selector: ${usedSelector}`);
                
                // Show error message with icon
                const errorMsg = `<i class="uil uil-exclamation-triangle"></i> ${messages[0]}`;
                errorContainer.html(errorMsg).show().addClass('text-danger');
                
                // Add error styling to input with a small delay to ensure it sticks
                setTimeout(() => {
                    this.addFieldErrorStyling(fieldName);
                    
                    // Force reapply classes after another short delay
                    setTimeout(() => {
                        this.forceErrorStyling(fieldName);
                        
                        // Final check and force is-invalid class specifically
                        setTimeout(() => {
                            this.ensureIsInvalidClass(fieldName);
                        }, 100);
                    }, 50);
                }, 10);
            } else {
                console.log(`⚠️ No error container found for: ${fieldName}, creating one...`);
                // Try to find and create error container
                this.createFieldErrorContainer(fieldName, messages[0]);
            }
        });
        
        console.log('✅ FormErrorHandler: All field errors processed');
    }

    /**
     * Add error styling to field
     */
    addFieldErrorStyling(fieldName) {
        console.log('🎨 Adding error styling to field:', fieldName);
        
        const inputField = $(`[name="${fieldName}"]`);
        if (inputField.length) {
            console.log('📝 Found input field:', inputField[0]);
            console.log('📝 Current classes before:', inputField.attr('class'));
            
            // Remove any existing validation classes first
            inputField.removeClass('is-valid border-success');
            
            // Add error classes
            inputField.addClass('is-invalid border-danger');
            
            // Verify classes were added
            console.log('📝 Current classes after:', inputField.attr('class'));
            console.log('📝 Has is-invalid:', inputField.hasClass('is-invalid'));
            console.log('📝 Has border-danger:', inputField.hasClass('border-danger'));
            
            // Also add to parent form-group
            inputField.closest('.form-group').addClass('has-error');
            
            // For Select2 elements
            if (inputField.hasClass('select2') || inputField.data('select2')) {
                console.log('🔽 Styling Select2 element');
                const select2Selection = inputField.next('.select2-container').find('.select2-selection');
                select2Selection.removeClass('is-valid border-success').addClass('is-invalid border-danger');
                console.log('🔽 Select2 classes:', select2Selection.attr('class'));
            }
            
            console.log('✅ Error styling applied to:', fieldName);
        } else {
            console.warn('⚠️ Input field not found for:', fieldName);
            // Try alternative selectors
            const alternativeSelectors = [
                `input[name="${fieldName}"]`,
                `select[name="${fieldName}"]`,
                `textarea[name="${fieldName}"]`,
                `#${fieldName.replace(/[\[\]\.]/g, '_')}`
            ];
            
            for (let selector of alternativeSelectors) {
                const altField = $(selector);
                if (altField.length) {
                    console.log('✅ Found field with alternative selector:', selector);
                    altField.removeClass('is-valid border-success').addClass('is-invalid border-danger');
                    altField.closest('.form-group').addClass('has-error');
                    break;
                }
            }
        }
    }

    /**
     * Force error styling to stick (aggressive approach)
     */
    forceErrorStyling(fieldName) {
        console.log('💪 Force applying error styling to field:', fieldName);
        
        const inputField = $(`[name="${fieldName}"]`);
        if (inputField.length) {
            console.log('💪 Element found:', inputField[0]);
            console.log('💪 Current class attribute:', inputField[0].className);
            
            // Try different approaches to add is-invalid class
            
            // Method 1: Direct DOM manipulation
            const element = inputField[0];
            element.classList.remove('is-valid', 'border-success');
            element.classList.add('is-invalid', 'border-danger');
            
            console.log('💪 After classList.add:', element.className);
            console.log('💪 classList contains is-invalid:', element.classList.contains('is-invalid'));
            
            // Method 2: jQuery approach
            inputField.removeClass('is-valid border-success');
            inputField.addClass('is-invalid border-danger');
            
            console.log('💪 After jQuery addClass:', inputField.attr('class'));
            
            // Method 3: Force with setAttribute
            const currentClasses = element.className;
            if (!currentClasses.includes('is-invalid')) {
                element.setAttribute('class', currentClasses + ' is-invalid');
            }
            if (!currentClasses.includes('border-danger')) {
                element.setAttribute('class', element.className + ' border-danger');
            }
            
            console.log('💪 After setAttribute:', element.className);
            
            // Method 4: Use a MutationObserver to prevent class removal
            this.protectClasses(element, ['is-invalid', 'border-danger']);
            
            // Verify final state
            console.log('💪 Final verification:');
            console.log('💪 - className:', element.className);
            console.log('💪 - hasClass is-invalid:', inputField.hasClass('is-invalid'));
            console.log('💪 - hasClass border-danger:', inputField.hasClass('border-danger'));
            console.log('💪 - classList contains is-invalid:', element.classList.contains('is-invalid'));
            console.log('💪 - classList contains border-danger:', element.classList.contains('border-danger'));
            
            // For Select2 elements
            if (inputField.hasClass('select2') || inputField.data('select2')) {
                const select2Selection = inputField.next('.select2-container').find('.select2-selection');
                if (select2Selection.length) {
                    const select2Element = select2Selection[0];
                    select2Element.classList.remove('is-valid', 'border-success');
                    select2Element.classList.add('is-invalid', 'border-danger');
                    console.log('💪 Select2 classes:', select2Element.className);
                }
            }
        } else {
            console.error('💪 No element found for field:', fieldName);
        }
    }
    
    /**
     * Protect classes from being removed by other scripts
     */
    protectClasses(element, classesToProtect) {
        if (!element._classProtector) {
            element._classProtector = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        classesToProtect.forEach(className => {
                            if (!element.classList.contains(className)) {
                                console.log('🛡️ Reapplying protected class:', className);
                                element.classList.add(className);
                            }
                        });
                    }
                });
            });
            
            element._classProtector.observe(element, {
                attributes: true,
                attributeFilter: ['class']
            });
            
            console.log('🛡️ Class protection enabled for:', classesToProtect);
        }
    }
    
    /**
     * Ensure is-invalid class is applied (last resort method)
     */
    ensureIsInvalidClass(fieldName) {
        console.log('🎯 Final check for is-invalid class on field:', fieldName);
        
        const inputField = $(`[name="${fieldName}"]`);
        if (inputField.length) {
            const element = inputField[0];
            
            // Check current state
            const hasIsInvalid = element.classList.contains('is-invalid');
            const hasBorderDanger = element.classList.contains('border-danger');
            
            console.log('🎯 Current state - is-invalid:', hasIsInvalid, 'border-danger:', hasBorderDanger);
            
            if (!hasIsInvalid) {
                console.log('🎯 is-invalid missing, forcing it...');
                
                // Try every possible method to add is-invalid
                element.classList.add('is-invalid');
                inputField.addClass('is-invalid');
                
                // Direct string manipulation
                if (!element.className.includes('is-invalid')) {
                    element.className = element.className + ' is-invalid';
                }
                
                // Use setAttribute as last resort
                const classes = element.getAttribute('class') || '';
                if (!classes.includes('is-invalid')) {
                    element.setAttribute('class', classes + ' is-invalid');
                }
                
                console.log('🎯 After forcing is-invalid:', element.classList.contains('is-invalid'));
                console.log('🎯 Final className:', element.className);
            }
            
            if (!hasBorderDanger) {
                console.log('🎯 border-danger missing, forcing it...');
                element.classList.add('border-danger');
                inputField.addClass('border-danger');
                
                if (!element.className.includes('border-danger')) {
                    element.className = element.className + ' border-danger';
                }
            }
            
            // Final verification
            console.log('🎯 FINAL VERIFICATION:');
            console.log('🎯 - Element:', element);
            console.log('🎯 - Full className:', element.className);
            console.log('🎯 - classList.contains("is-invalid"):', element.classList.contains('is-invalid'));
            console.log('🎯 - classList.contains("border-danger"):', element.classList.contains('border-danger'));
            console.log('🎯 - jQuery hasClass("is-invalid"):', inputField.hasClass('is-invalid'));
            console.log('🎯 - jQuery hasClass("border-danger"):', inputField.hasClass('border-danger'));
        }
    }

    /**
     * Create error container if it doesn't exist
     */
    createFieldErrorContainer(fieldName, message) {
        const inputField = $(`[name="${fieldName}"]`);
        if (inputField.length) {
            // Add error styling
            this.addFieldErrorStyling(fieldName);
            
            // Check if error container already exists
            let errorContainer = inputField.siblings('.error-message').first();
            if (!errorContainer.length) {
                // Create new error container
                const errorMsg = `<div class="error-message text-danger"><i class="uil uil-exclamation-triangle"></i> ${message}</div>`;
                
                // Insert after Select2 container if it exists, otherwise after input
                const select2Container = inputField.next('.select2-container');
                if (select2Container.length) {
                    select2Container.after(errorMsg);
                } else {
                    inputField.after(errorMsg);
                }
            } else {
                // Update existing container
                const errorMsg = `<i class="uil uil-exclamation-triangle"></i> ${message}`;
                errorContainer.html(errorMsg).show();
            }
        }
    }

    /**
     * Scroll to top of form
     */
    scrollToTop() {
        const target = this.form.closest('.card-body, .form-container, .container');
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - this.options.scrollOffset
            }, 300);
        }
    }

    /**
     * Show notification (fallback)
     */
    showNotification(type, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'success' ? 'success' : 'error',
                title: type === 'success' ? 'Success!' : 'Error!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    /**
     * Update options
     */
    updateOptions(newOptions) {
        this.options = { ...this.options, ...newOptions };
    }

    /**
     * Destroy the handler
     */
    destroy() {
        if (this.form && this.form.length) {
            this.form.off('submit');
        }
    }
}

// Global helper function to initialize form error handler
window.initFormErrorHandler = function(options = {}) {
    return new FormErrorHandler(options);
};

// Global form submission interceptor for progress loader
$(document).ready(function() {
    // Auto-initialize for forms with data-auto-error-handler attribute
    $('form[data-auto-error-handler]').each(function() {
        const form = $(this);
        const formId = form.attr('id');
        
        if (!formId) {
            console.warn('FormErrorHandler: Form with data-auto-error-handler must have an ID attribute', form);
            return;
        }
        
        console.log('🚀 Auto-initializing FormErrorHandler for form:', formId);
        
        const options = {
            formSelector: '#' + formId,
            loadingText: form.data('loading-text') || 'Processing...',
            redirectUrl: form.data('redirect-url'),
            showProgressBar: form.data('show-progress-bar') !== false,
            showErrorAlert: form.data('show-error-alert') !== false,
            showFieldErrors: form.data('show-field-errors') !== false,
            redirectDelay: form.data('redirect-delay') || 1500,
            scrollOffset: form.data('scroll-offset') || 100
        };
        
        new FormErrorHandler(options);
    });
    
    // Global interceptor for ALL form submissions (shows progress loader)
    $(document).on('submit', 'form:not([data-no-loader]):not([data-auto-error-handler])', function(e) {
        const form = $(this);
        
        // Skip if form is already handled by FormErrorHandler
        if (form.data('form-error-handler-initialized')) {
            return;
        }
        
        // Skip if form has data-no-loader attribute
        if (form.data('no-loader')) {
            return;
        }
        
        // Skip if it's a GET form (search forms, etc.)
        if (form.attr('method')?.toLowerCase() === 'get') {
            return;
        }
        
        // Skip if form has file uploads and is not using AJAX
        const hasFileUploads = form.find('input[type="file"]').length > 0;
        if (hasFileUploads && !form.data('ajax-submit')) {
            // For file uploads without AJAX, show simple loader
            showGlobalLoader('Uploading files...');
            return;
        }
        
        // Show progress loader for regular form submissions
        const loadingText = form.data('loading-text') || 'Processing...';
        showGlobalLoader(loadingText);
        
        // For AJAX forms, we'll handle the response
        if (form.data('ajax-submit')) {
            e.preventDefault();
            handleGlobalAjaxSubmission(form);
        }
    });
    
    // Global interceptor for button clicks that might submit forms
    $(document).on('click', 'button[type="submit"], input[type="submit"]', function() {
        const button = $(this);
        const form = button.closest('form');
        
        if (form.length && !form.data('no-loader') && !form.data('auto-error-handler')) {
            const loadingText = button.data('loading-text') || form.data('loading-text') || 'Processing...';
            
            // Small delay to ensure form submission starts
            setTimeout(() => {
                showGlobalLoader(loadingText);
            }, 50);
        }
    });
});

/**
 * Show global loading overlay
 */
function showGlobalLoader(text = 'Processing...', showProgress = true) {
    if (typeof LoadingOverlay !== 'undefined') {
        LoadingOverlay.show({
            text: text,
            progress: showProgress
        });
        
        if (showProgress) {
            LoadingOverlay.progressSequence([30, 60, 90]);
        }
    } else if ($('.loading-overlay').length) {
        $('.loading-overlay').show();
    } else {
        // Create a simple fallback loader
        if (!$('#global-fallback-loader').length) {
            $('body').append(`
                <div id="global-fallback-loader" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    color: white;
                    font-size: 16px;
                ">
                    <div style="text-align: center;">
                        <div style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin: 0 auto 10px;"></div>
                        <div>${text}</div>
                    </div>
                </div>
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            `);
        } else {
            $('#global-fallback-loader').show();
        }
    }
}

/**
 * Hide global loading overlay
 */
function hideGlobalLoader() {
    if (typeof LoadingOverlay !== 'undefined') {
        LoadingOverlay.hide();
    } else if ($('.loading-overlay').length) {
        $('.loading-overlay').hide();
    } else {
        $('#global-fallback-loader').hide();
    }
}

/**
 * Handle global AJAX form submission
 */
function handleGlobalAjaxSubmission(form) {
    const formData = new FormData(form[0]);
    const url = form.attr('action');
    const method = form.attr('method') || 'POST';
    
    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.animateProgressBar(100);
            }
            
            const message = response.message || response.success || 'Success!';
            
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.showSuccess(message, 'Redirecting...');
            }
            
            // Handle redirect
            setTimeout(() => {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    // Try to determine redirect from form data
                    const redirectUrl = form.data('redirect-url');
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
                        // Reload the page
                        window.location.reload();
                    }
                }
            }, 1500);
        },
        error: function(xhr) {
            hideGlobalLoader();
            
            // Basic error handling
            let errorMessage = 'An error occurred while processing your request.';
            
            if (xhr.status === 422 && xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            // Show error using available notification system
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            } else {
                alert(errorMessage);
            }
        }
    });
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormErrorHandler;
}
