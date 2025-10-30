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
    
    /* Select2 error state */
    .select2-container.is-invalid .select2-selection {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
    }
    
    .select2-container.is-invalid .select2-selection:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid mb-30">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::variantsconfig.variants_configurations'), 'url' => route('admin.variants-configurations.index')],
                    ['title' => isset($variantsConfig) ? trans('catalogmanagement::variantsconfig.edit_variants_config') : trans('catalogmanagement::variantsconfig.add_variants_config')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($variantsConfig) ? trans('catalogmanagement::variantsconfig.edit_variants_config') : trans('catalogmanagement::variantsconfig.add_variants_config') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="variantsConfigForm" 
                              action="{{ isset($variantsConfig) ? route('admin.variants-configurations.update', $variantsConfig->id) : route('admin.variants-configurations.store') }}" 
                              method="POST">
                            @csrf
                            @if(isset($variantsConfig))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Name Fields for Languages -->
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="translation_{{ $language->id }}_name" 
                                                   class="il-gray fs-14 fw-500 mb-10" 
                                                   @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                @if($language->code == 'ar')
                                                    الاسم ({{ $language->name }}) <span class="text-danger">*</span>
                                                @else
                                                    {{ trans('catalogmanagement::variantsconfig.name') }} ({{ $language->name }}) <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.name') is-invalid @enderror" 
                                                   id="translation_{{ $language->id }}_name" 
                                                   name="translations[{{ $language->id }}][name]"  
                                                   value="{{ isset($variantsConfig) ? ($variantsConfig->getTranslation('name', $language->code) ?? '') : old('translations.' . $language->id . '.name') }}"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   data-lang="{{ $language->code }}"
                                                   >
                                            @error('translations.' . $language->id . '.name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Type Selection -->
                                <div class="col-md-12">
                                    <div class="form-group mb-25">
                                        <label for="type" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::variantsconfig.type') }}
                                        </label>
                                        <select name="type" id="type" 
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('type') is-invalid @enderror">
                                            <option value="">-- {{ trans('common.select') }} --</option>
                                            <option value="text" {{ (isset($variantsConfig) && $variantsConfig->type == 'text') ? 'selected' : (old('type') == 'text' ? 'selected' : '') }}>
                                                {{ trans('catalogmanagement::variantsconfig.text') }}
                                            </option>
                                            <option value="color" {{ (isset($variantsConfig) && $variantsConfig->type == 'color') ? 'selected' : (old('type') == 'color' ? 'selected' : '') }}>
                                                {{ trans('catalogmanagement::variantsconfig.color') }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Value - Text Input (shown when type is 'text') -->
                                    <div class="form-group mb-25" id="textValueContainer" style="display: none;">
                                        <label for="value_text" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::variantsconfig.value') }}
                                        </label>
                                        <input type="text" 
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                               id="value_text" 
                                               name="value_text"  
                                               value="{{ isset($variantsConfig) && $variantsConfig->type == 'text' ? $variantsConfig->value : old('value') }}"
                                               placeholder="{{ trans('catalogmanagement::variantsconfig.enter_text_value') }}">
                                    </div>
                                </div>


                                <!-- Value - Color Picker (shown when type is 'color') -->
                                <div class="col-md-12" id="colorValueContainer" style="display: none;">
                                    <div class="form-group mb-25">
                                        <label for="value_color" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::variantsconfig.value') }}
                                        </label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="color" 
                                                   class="form-control form-control-color @error('value') is-invalid @enderror" 
                                                   id="value_color" 
                                                   name="value_color"  
                                                   value="{{ isset($variantsConfig) && $variantsConfig->type == 'color' ? $variantsConfig->value : (old('value') ?: '#000000') }}"
                                                   title="{{ trans('catalogmanagement::variantsconfig.choose_color') }}"
                                                   style="width: 80px; height: 45px;">
                                            <input type="text" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                   id="color_hex" 
                                                   readonly
                                                   value="{{ isset($variantsConfig) && $variantsConfig->type == 'color' ? $variantsConfig->value : (old('value') ?: '#000000') }}"
                                                   placeholder="#000000"
                                                   style="flex: 1;">
                                        </div>
                                        @error('value')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Hidden input to store the actual value -->
                                <input type="hidden" name="value" id="value" value="{{ isset($variantsConfig) ? $variantsConfig->value : old('value') }}">

                                <!-- Variant Configuration Key -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="key_id" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::variantsconfig.key') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="key_id" id="key_id" 
                                                class="form-control select2 ih-medium ip-gray radius-xs b-light px-15 @error('key_id') is-invalid @enderror">
                                            <option value="">-- {{ trans('common.select') }} --</option>
                                            @if(isset($variantKeys))
                                                @foreach($variantKeys as $key)
                                                    <option value="{{ $key['id'] }}" 
                                                        {{ (isset($variantsConfig) && $variantsConfig['id'] == $key['id']) ? 'selected' : (old('key_id') == $key['id'] ? 'selected' : '') }}>
                                                        {{ $key['name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('key_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Parent Configuration -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="parent_id" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::variantsconfig.parent') }} 
                                            <span class="text-muted">({{ trans('common.optional') }})</span>
                                        </label>
                                        <select name="parent_id" id="parent_id" 
                                                class="form-control select2 ih-medium ip-gray radius-xs b-light px-15 @error('parent_id') is-invalid @enderror">
                                            <option value="">-- {{ trans('catalogmanagement::variantsconfig.no_parent') }} --</option>
                                            @if(isset($allVariantsConfigs))
                                                @foreach($allVariantsConfigs as $config)
                                                    @if(!isset($variantsConfig) || $config->id != $variantsConfig->id)
                                                        <option value="{{ $config->id }}" 
                                                            {{ (isset($variantsConfig) && $variantsConfig->parent_id == $config->id) ? 'selected' : (old('parent_id') == $config->id ? 'selected' : '') }}>
                                                            {{ $config->value }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('parent_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="button-group d-flex pt-25 gap-3">
                                        <a href="{{ route('admin.variants-configurations.index') }}" 
                                           class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                            <i class="uil uil-angle-left"></i> {{ trans('common.cancel') }}
                                        </a>
                                        <button type="submit" id="submitBtn" 
                                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                            <i class="uil uil-check"></i> 
                                            <span>{{ isset($variantsConfig) ? trans('common.update') : trans('common.create') }}</span>
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
        // Initialize Select2 for better UX
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%',
                placeholder: '{{ trans("common.select") }}'
            });
        }

        // Function to toggle value input based on type
        function toggleValueInput() {
            const type = $('#type').val();
            
            if (type === 'text') {
                $('#textValueContainer').show();
                $('#colorValueContainer').hide();
            } else if (type === 'color') {
                $('#textValueContainer').hide();
                $('#colorValueContainer').show();
            } else {
                $('#textValueContainer').hide();
                $('#colorValueContainer').hide();
            }
        }

        // Trigger on page load
        toggleValueInput();

        // Trigger on type change
        $('#type').on('change', function() {
            toggleValueInput();
        });

        // Update hidden value field when text input changes
        $('#value_text').on('input', function() {
            $('#value').val($(this).val());
        });

        // Update hidden value and hex display when color changes
        $('#value_color').on('input change', function() {
            const colorValue = $(this).val();
            $('#value').val(colorValue);
            $('#color_hex').val(colorValue);
        });

        // Allow manual hex input
        $('#color_hex').on('input', function() {
            let hexValue = $(this).val();
            // Ensure it starts with #
            if (!hexValue.startsWith('#')) {
                hexValue = '#' + hexValue;
            }
            // Validate hex color
            if (/^#[0-9A-F]{6}$/i.test(hexValue)) {
                $('#value_color').val(hexValue);
                $('#value').val(hexValue);
            }
        });

        $('#variantsConfigForm').on('submit', function(e) {
            e.preventDefault();
            
            const variantsConfigForm = this;
            const $submitBtn = $('#submitBtn');
            const $spinner = $submitBtn.find('.spinner-border');
            const alertContainer = document.getElementById('alertContainer');
            
            // Disable button and show spinner
            $submitBtn.prop('disabled', true);
            $spinner.removeClass('d-none');

            // Update loading text and show overlay
            const loadingText = @json(isset($variantsConfig) ? trans('loading.updating') : trans('loading.creating'));
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
                const formData = new FormData(variantsConfigForm);

                // Send AJAX request
                return fetch(variantsConfigForm.action, {
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
                    const successMessage = @json(isset($variantsConfig) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                    LoadingOverlay.showSuccess(
                        successMessage,
                        '{{ trans("loading.redirecting") }}'
                    );
                    
                    // Redirect after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route("admin.variants-configurations.index") }}';
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
                        
                        // Special handling for 'value' field - show in visible container
                        if (field === 'value') {
                            const type = $('#type').val();
                            let $field;
                            
                            if (type === 'text') {
                                $field = $('#value_text');
                            } else if (type === 'color') {
                                $field = $('#value_color');
                            }
                            
                            if ($field && $field.length > 0) {
                                $field.addClass('is-invalid');
                                
                                // Insert error message inside the visible container
                                const errorHtml = '<div class="invalid-feedback d-block" style="display: block !important; color: #dc3545; font-weight: 500; margin-top: 0.5rem;">' + error.errors[field][0] + '</div>';
                                $field.closest('.form-group').append(errorHtml);
                                
                                console.log('Added value error for type:', type);
                            }
                            return;
                        }
                        
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
                            
                            // Insert error message
                            const errorHtml = '<div class="invalid-feedback d-block" style="display: block !important; color: #dc3545; font-weight: 500; margin-top: 0.5rem;">' + error.errors[field][0] + '</div>';
                            
                            // Check if it's a Select2 element
                            if ($field.hasClass('select2') || $field.data('select2')) {
                                // For Select2, insert after the Select2 container
                                const $select2Container = $field.next('.select2-container');
                                if ($select2Container.length > 0) {
                                    $select2Container.after(errorHtml);
                                    $select2Container.addClass('is-invalid');
                                } else {
                                    $field.after(errorHtml);
                                }
                            } else {
                                // For regular inputs, insert after the input
                                $field.after(errorHtml);
                            }
                            
                            console.log('Added error for:', field);
                        } else {
                            console.error('Could not find field for:', field);
                        }
                    });
                    
                    // Show error alert
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-1"></i> {{ trans('common.please_fix_errors') ?? 'Please fix the errors below' }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                } else {
                    // General error
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-1"></i> ${error.message || '{{ trans('common.error_occurred') ?? 'An error occurred' }}'}
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
