/**
 * Vendor Form JavaScript
 * Contains all JavaScript logic for vendor creation/editing wizard
 */

console.log('🚀 Vendor form script loaded!');

// Global variables for wizard state
let currentStep = 1;
const totalSteps = 4;
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
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: window.vendorFormConfig?.selectPlaceholder || 'Select...'
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

    // Add Document Row using template
    $('#addDocument').on('click', function() {
        const uniqueId = 'document_' + documentIndex + '_' + Date.now();
        const template = document.getElementById('document-row-template');
        const templateContent = template.content.cloneNode(true);
        const div = document.createElement('div');
        div.appendChild(templateContent);
        
        // Replace placeholders
        let html = div.innerHTML;
        html = html.replace(/__INDEX__/g, documentIndex);
        html = html.replace(/__UNIQUEID__/g, uniqueId);
        
        $('#documentsContainer').append(html);
        documentIndex++;
        updateRemoveButtons();
        
        // Re-initialize upload handlers for new document
        initializeDocumentUpload(uniqueId);
    });

    // Remove Document Row
    $(document).on('click', '.remove-document-row', function() {
        $(this).closest('.document-row').remove();
        updateRemoveButtons();
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

    // Next button - Allow free navigation without validation
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

    // Click on wizard step navigation - Allow free navigation
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
    $('#vendorForm').on('submit', handleFormSubmission);
});

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
    const config = window.vendorFormConfig;
    if (!config) {
        console.error('vendorFormConfig not found!');
        return;
    }
    
    // Update names and descriptions for each language
    config.languages.forEach(lang => {
        $(`.review-name-${lang.code}`).text($(`input[name="translations[${lang.id}][name]"]`).val() || config.notProvided);
        $(`.review-description-${lang.code}`).text($(`textarea[name="translations[${lang.id}][description]"]`).val() || config.notProvided);
    });

    // Update country
    $('.review-country').text($('#country_id option:selected').text() || config.notProvided);

    // Update commission
    const commission = $('#commission').val();
    $('.review-commission').text(commission ? commission + '%' : config.notProvided);

    // Update active status
    const isActive = $('#active').is(':checked');
    const activeLabel = config.activeLabel || 'Active';
    const inactiveLabel = config.inactiveLabel || 'Inactive';
    $('.review-active').html(isActive 
        ? `<span class="badge badge-success">${activeLabel}</span>`
        : `<span class="badge badge-secondary">${inactiveLabel}</span>`
    );

    // Update activities
    const selectedActivities = $('#activities option:selected').map(function() { return $(this).text(); }).get();
    $('.review-activities').html(selectedActivities.length > 0 
        ? selectedActivities.map(a => `<span class="badge badge-primary badge-round me-1">${a}</span>`).join('') 
        : config.notProvided);

    // Update Logo
    const logoInput = document.getElementById('logo');
    if (logoInput?.files?.[0]) {
        const reader = new FileReader();
        reader.onload = e => $('.review-logo').html(`<img src="${e.target.result}" alt="Logo" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">`);
        reader.readAsDataURL(logoInput.files[0]);
    } else {
        $('.review-logo').html(`<span class="text-muted">${config.noLogoUploaded}</span>`);
    }

    // Update Banner
    const bannerInput = document.getElementById('banner');
    if (bannerInput?.files?.[0]) {
        const reader = new FileReader();
        reader.onload = e => $('.review-banner').html(`<img src="${e.target.result}" alt="Banner" class="img-thumbnail" style="max-width: 300px; max-height: 100px;">`);
        reader.readAsDataURL(bannerInput.files[0]);
    } else {
        $('.review-banner').html(`<span class="text-muted">${config.noBannerUploaded}</span>`);
    }

    // Update SEO - for each language
    let seoHtml = '';
    config.languages.forEach((lang, index) => {
        const metaTitle = $(`input[name="translations[${lang.id}][meta_title]"]`).val() || config.notProvided;
        const metaDescription = $(`textarea[name="translations[${lang.id}][meta_description]"]`).val() || config.notProvided;
        const metaKeywords = $(`input[name="translations[${lang.id}][meta_keywords]"]`).val() || config.notProvided;
        
        seoHtml += `<div class="${index > 0 ? 'mt-3 pt-3 border-top' : ''}">`;
        seoHtml += `<h6 class="text-muted mb-2">${lang.name}</h6>`;
        seoHtml += `<div class="mb-2"><strong>${config.metaTitle}:</strong> ${metaTitle}</div>`;
        seoHtml += `<div class="mb-2"><strong>${config.metaDescription}:</strong> ${metaDescription}</div>`;
        seoHtml += `<div><strong>${config.metaKeywords}:</strong> ${metaKeywords}</div>`;
        seoHtml += '</div>';
    });
    $('.review-seo').html(seoHtml);

    // Update documents
    const documents = [];
    $('.document-row').each(function() {
        const idx = $(this).data('document-index');
        const name = $(this).find(`input[name^="documents[${idx}][translations]"][name$="[name]"]`).first().val();
        const file = $(this).find(`input[name="documents[${idx}][file]"]`)[0].files[0];
        if (name || file) documents.push({ name: name || config.notProvided, file: file ? file.name : config.notProvided });
    });
    
    $('.review-documents').html(documents.length > 0
        ? '<ul class="list-unstyled mb-0">' + documents.map(d => `<li class="mb-2"><i class="uil uil-file-alt text-primary"></i> <strong>${d.name}</strong> - ${d.file}</li>`).join('') + '</ul>'
        : `<p class="text-muted">${config.noDocumentsUploaded}</p>`
    );

    // Update email
    $('.review-email').text($('#email').val() || config.notProvided);
}

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
                alert(config.errorOccurred);
            }
        }
    });
}
