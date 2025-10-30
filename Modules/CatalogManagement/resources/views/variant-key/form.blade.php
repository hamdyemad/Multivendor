@extends('layout.app')

@push('styles')
<style>
    /* Validation styles */
    .invalid-feedback {
        display: block !important;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        color: #dc3545;
        font-weight: 500;
    }
    
    .invalid-feedback.d-block {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    input[data-lang="ar"] + .invalid-feedback,
    textarea[data-lang="ar"] + .invalid-feedback {
        direction: rtl;
        text-align: right;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff5f5;
    }
    
    .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .form-control {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    /* Error icon for invalid inputs */
    .form-group.has-error .form-control {
        padding-right: 2.5rem;
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => 'Variant Configuration Keys', 'url' => route('admin.variant-keys.index')],
                    ['title' => isset($variantKey) ? 'Edit Variant Key' : 'Add Variant Key']
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($variantKey) ? 'Edit Variant Key' : 'Add Variant Key' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="variantKeyForm" 
                              action="{{ isset($variantKey) ? route('admin.variant-keys.update', $variantKey->id) : route('admin.variant-keys.store') }}" 
                              method="POST">
                            @csrf
                            @if(isset($variantKey))
                                @method('PUT')
                            @endif

                            <div class="row">
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="translation_{{ $language->id }}_name" 
                                                   class="il-gray fs-14 fw-500 mb-10" 
                                                   @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                @if($language->code == 'ar')
                                                    اسم المفتاح ({{ $language->name }}) <span class="text-danger">*</span>
                                                @else
                                                    Variant Key Name ({{ $language->name }}) <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.name') is-invalid @enderror" 
                                                   id="translation_{{ $language->id }}_name" 
                                                   name="translations[{{ $language->id }}][name]"  
                                                   value="{{ isset($variantKey) ? ($variantKey->getTranslation('name', $language->code) ?? '') : old('translations.' . $language->id . '.name') }}"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   data-lang="{{ $language->code }}"
                                                   >
                                            @error('translations.' . $language->id . '.name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <!-- Parent Variant Key -->
                                <div class="col-md-12">
                                    <label for="parent_key_id" class="il-gray fs-14 fw-500 align-center">
                                        Parent Variant Key 
                                        <span class="text-muted">(Optional)</span>
                                    </label>
                                    <select name="parent_key_id" id="parent_key_id" 
                                            class="form-control select2 ih-medium ip-gray radius-xs b-light px-15">
                                        <option value="">-- No Parent --</option>
                                        @foreach($variantKeys as $key)
                                            <option value="{{ $key['id'] }}" 
                                                {{ (isset($variantKey) && $variantKey->parent_key_id == $key['id']) ? 'selected' : '' }}>
                                                {{ $key['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_key_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <div class="button-group d-flex pt-25 gap-3">
                                        <a href="{{ route('admin.variant-keys.index') }}" 
                                           class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                            <i class="uil uil-angle-left"></i> {{ trans('common.cancel') }}
                                        </a>
                                        <button type="submit" id="submitBtn" 
                                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                            <i class="uil uil-check"></i> 
                                            <span>{{ isset($variantKey) ? 'Update Variant Key' : 'Create Variant Key' }}</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-body')
    <x-loading-overlay 
        :loadingText="trans('loading.processing')" 
        :loadingSubtext="trans('loading.please_wait')" 
    />
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#variantKeyForm').on('submit', function(e) {
            e.preventDefault();
            
            const variantKeyForm = this;
            const $submitBtn = $('#submitBtn');
            const $spinner = $submitBtn.find('.spinner-border');
            const alertContainer = document.getElementById('alertContainer');
            
            // Disable button and show spinner
            $submitBtn.prop('disabled', true);
            $spinner.removeClass('d-none');

            // Update loading text and show overlay
            const loadingText = @json(isset($variantKey) ? trans('loading.updating') : trans('loading.creating'));
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                const loadingTextEl = overlay.querySelector('.loading-text');
                const loadingSubtextEl = overlay.querySelector('.loading-subtext');
                if (loadingTextEl) loadingTextEl.textContent = loadingText;
                if (loadingSubtextEl) loadingSubtextEl.textContent = '{{ trans("loading.please_wait") }}';
            }
            
            // Show loading overlay
            if (window.LoadingOverlay) {
                LoadingOverlay.show();
            }

            // Clear previous alerts
            alertContainer.innerHTML = '';
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Start progress bar animation
            LoadingOverlay.animateProgressBar(30, 300).then(() => {
                // Prepare form data
                const formData = new FormData(variantKeyForm);

                // Send AJAX request
                return fetch(variantKeyForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
            })
            .then(response => {
                // Progress to 60%
                LoadingOverlay.animateProgressBar(60, 200);
                
                if (!response.ok) {
                    return response.json().then(data => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then(data => {
                // Progress to 90%
                return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
            })
            .then(data => {
                // Complete progress bar
                return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                    // Show success animation
                    const successMessage = @json(isset($variantKey) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                    LoadingOverlay.showSuccess(
                        successMessage,
                        '{{ trans("loading.redirecting") }}'
                    );
                    
                    // Redirect after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route("admin.variant-keys.index") }}';
                    }, 1500);
                });
            })
            .catch(error => {
                // Hide loading overlay
                LoadingOverlay.hide();
                
                // Re-enable button
                $submitBtn.prop('disabled', false);
                $spinner.addClass('d-none');
                
                // Clear previous errors first
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                
                // Handle validation errors
                if (error.errors) {
                    console.log('Validation errors received:', error.errors);
                    
                    Object.keys(error.errors).forEach(field => {
                        console.log('Processing field:', field);
                        
                        // Convert dot notation to bracket notation
                        // e.g., "translations.1.name" -> "translations[1][name]"
                        let parts = field.split('.');
                        let fieldName = parts[0]; // Start with first part
                        
                        for (let i = 1; i < parts.length; i++) {
                            fieldName += '[' + parts[i] + ']';
                        }
                        
                        console.log('Converted to:', fieldName);
                        
                        // Try to find the field with bracket notation first
                        let $field = $('input[name="' + fieldName + '"], textarea[name="' + fieldName + '"], select[name="' + fieldName + '"]');
                        
                        console.log('Found field:', $field.length);
                        
                        // If not found, try with original dot notation
                        if ($field.length === 0) {
                            $field = $('input[name="' + field + '"], textarea[name="' + field + '"], select[name="' + field + '"]');
                            console.log('Tried original notation, found:', $field.length);
                        }
                        
                        if ($field.length > 0) {
                            $field.addClass('is-invalid');
                            
                            // Insert error message after the input
                            const errorHtml = '<div class="invalid-feedback d-block" style="display: block !important; color: #dc3545; font-weight: 500; margin-top: 0.5rem;">' + error.errors[field][0] + '</div>';
                            $field.after(errorHtml);
                            
                            console.log('Added error for:', field);
                        } else {
                            console.error('Could not find field for:', field);
                        }
                    });
                    
                    // Show error alert
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-1"></i> {{ trans('common.please_fix_errors') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                } else {
                    // General error
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-1"></i> ${error.message || '{{ trans('common.error_occurred') }}'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }
                
                // Scroll to top
                $('html, body').animate({ scrollTop: 0 }, 500);
            });
        });
    });
</script>
@endpush
