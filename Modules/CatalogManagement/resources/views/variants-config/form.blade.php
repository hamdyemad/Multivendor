@extends('layout.app')
@section('title')
    {{ isset($variantsConfig) ? trans('catalogmanagement::variantsconfig.edit_variants_config') : trans('catalogmanagement::variantsconfig.add_variants_config') }} | Bnaia
@endsection

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

    /* RTL Support for Select2 */
    [dir="rtl"] .select2-container--bootstrap-5 .select2-selection {
        text-align: right;
    }
    
    [dir="rtl"] .select2-container--bootstrap-5 .select2-selection__arrow {
        left: 1px;
        right: auto;
    }
    
    [dir="rtl"] .select2-container--bootstrap-5 .select2-selection__clear {
        float: left;
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
                                                   placeholder="@if($language->code == 'ar')أدخل اسم المتغير@else{{ trans('catalogmanagement::variantsconfig.enter_variant_name') }}@endif"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   data-lang="{{ $language->code }}"
                                                   >
                                            @error('translations.' . $language->id . '.name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Variant Configuration Key - Hierarchical Selection -->
                                <div class="col-md-12">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block"
                                               @if(app()->getLocale() == 'ar') dir="rtl" style="text-align: right;" @endif>
                                            {{ trans('catalogmanagement::variantsconfig.key') }} <span class="text-danger">*</span>
                                        </label>
                                        <div id="keySelectorsContainer">
                                            <!-- Root Key Selector -->
                                            <div class="mb-3">
                                                <x-custom-select
                                                    id="root_key_id"
                                                    name="root_key_id"
                                                    :label="null"
                                                    :options="collect($variantKeys ?? [])->filter(fn($key) => !isset($key['parent_key_id']) || $key['parent_key_id'] === null)->values()->toArray()"
                                                    :selected="isset($variantsConfig) ? $variantsConfig['key_id'] : old('key_id')"
                                                    :placeholder="trans('catalogmanagement::variantsconfig.select_root_key')"
                                                />
                                            </div>
                                            <!-- Child key selectors will be dynamically added here -->
                                        </div>
                                        <!-- Hidden input to store the final selected key_id -->
                                        <input type="hidden" name="key_id" id="key_id" value="{{ isset($variantsConfig) ? $variantsConfig['key_id'] : old('key_id') }}">
                                        @error('key_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Parent Variant Configuration - Hierarchical Selection -->
                                <div class="col-md-12" id="parentVariantContainer" style="display: none;">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10"
                                               @if(app()->getLocale() == 'ar') dir="rtl" style="text-align: right; display: block;" @endif>
                                            {{ trans('catalogmanagement::variantsconfig.parent') }}
                                        </label>
                                        <div id="variantSelectorsContainer">
                                            <!-- Variant selectors will be dynamically added here -->
                                        </div>
                                        <!-- Hidden input to store the final selected parent_id -->
                                        <input type="hidden" name="parent_id" id="parent_id" value="{{ isset($variantsConfig) ? $variantsConfig['parent_id'] : old('parent_id') }}">
                                        <small class="text-muted">{{ trans('catalogmanagement::variantsconfig.parent_help') }}</small>
                                        @error('parent_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Type Selection -->
                                <div class="col-md-6">
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
                                </div>

                                <!-- Value - Text Input (shown when type is 'text') -->
                                <div class="col-md-6" id="textValueContainer" style="display: none;">
                                    <div class="form-group mb-25">
                                        <label for="value_text" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::variantsconfig.value') }}
                                        </label>
                                        <input type="text"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="value_text"
                                               value="{{ isset($variantsConfig) && $variantsConfig->type == 'text' ? $variantsConfig->value : old('value') }}"
                                               placeholder="{{ trans('catalogmanagement::variantsconfig.enter_text_value') }}">
                                    </div>
                                </div>

                                <!-- Value - Color Picker (shown when type is 'color') -->
                                <div class="col-md-6" id="colorValueContainer" style="display: none;">
                                    <div class="form-group mb-25">
                                        <label for="value_color" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::variantsconfig.value') }}
                                        </label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="color"
                                                   class="form-control form-control-color @error('value') is-invalid @enderror"
                                                   id="value_color"
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
                                <div class="col-12">
                                    <div class="button-group d-flex gap-3">
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
        // Initialize Select2 for better UX with RTL support
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%',
                placeholder: '{{ trans("common.select") }}',
                @if(app()->getLocale() == 'ar')
                dir: 'rtl'
                @endif
            });
        }

        // Function to toggle value input based on type
        function toggleValueInput(syncToHidden = false) {
            const type = $('#type').val();
            const currentValue = $('#value').val();

            if (type === 'text') {
                $('#textValueContainer').show();
                $('#colorValueContainer').hide();
                // Set text input value from hidden field if not already set
                if (currentValue && !$('#value_text').val()) {
                    $('#value_text').val(currentValue);
                }
                // Sync text value to hidden field
                if (syncToHidden || !currentValue) {
                    const textVal = $('#value_text').val();
                    if (textVal) {
                        $('#value').val(textVal);
                    }
                }
            } else if (type === 'color') {
                $('#textValueContainer').hide();
                $('#colorValueContainer').show();
                // Set color input value from hidden field if not already set
                if (currentValue) {
                    $('#value_color').val(currentValue);
                    $('#color_hex').val(currentValue);
                } else {
                    // If no current value, sync from color picker
                    const colorVal = $('#value_color').val();
                    if (colorVal) {
                        $('#value').val(colorVal);
                        $('#color_hex').val(colorVal);
                    }
                }
            } else {
                $('#textValueContainer').hide();
                $('#colorValueContainer').hide();
            }
        }

        // Trigger on page load
        toggleValueInput();
        
        // Ensure hidden value is synced on page load for existing records
        const initialType = $('#type').val();
        if (initialType === 'color') {
            const colorVal = $('#value_color').val();
            if (colorVal && colorVal !== '#000000') {
                $('#value').val(colorVal);
            }
        } else if (initialType === 'text') {
            const textVal = $('#value_text').val();
            if (textVal) {
                $('#value').val(textVal);
            }
        }

        // Trigger on type change
        $('#type').on('change', function() {
            // Clear all values when type changes
            $('#value').val('');
            $('#value_text').val('');
            $('#value_color').val('#000000');
            $('#color_hex').val('#000000');
            // Then toggle visibility
            toggleValueInput(true);
        });

        // Update hidden value field when text input changes
        $('#value_text').on('input change', function() {
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

            // Sync value to hidden field before submission
            const type = $('#type').val();
            if (type === 'color') {
                const colorVal = $('#value_color').val();
                $('#value').val(colorVal);
                console.log('Syncing color value before submit:', colorVal);
            } else if (type === 'text') {
                const textVal = $('#value_text').val();
                $('#value').val(textVal);
                console.log('Syncing text value before submit:', textVal);
            }

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
                
                // Debug: Log form data entries
                console.log('Form data being submitted:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}: ${value}`);
                }

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

        // Store translations
        const translations = {
            selectChildKey: '{{ trans('catalogmanagement::variantsconfig.select_child_key') }}',
            selectParentVariant: '{{ trans('catalogmanagement::variantsconfig.select_parent_variant') }}',
            level: '{{ trans('common.level') }}',
            select: '{{ trans('common.select') }}',
            search: '{{ trans('common.search') }}',
            noResults: '{{ trans('common.no_results') }}',
            noParent: '{{ trans('catalogmanagement::variantsconfig.no_parent') }}'
        };

        // Store all variant keys for hierarchical selection
        const allVariantKeys = @json($variantKeys ?? []);
        let keySelectionLevel = 0;
        let variantSelectionLevel = 0;
        
        // Store edit mode parent ID for pre-selection
        const editModeParentId = {{ isset($variantsConfig) && $variantsConfig->parent_id ? $variantsConfig->parent_id : 'null' }};

        // Debug: Log all variant keys to see the data structure
        console.log('All variant keys loaded:', allVariantKeys);
        console.log('Total keys:', allVariantKeys.length);
        console.log('Edit mode parent ID:', editModeParentId);
        
        // Debug: Show keys with their parent_key_id
        const rootKeys = [];
        const childKeys = [];
        allVariantKeys.forEach(key => {
            console.log(`Key ID: ${key.id}, Name: ${key.name}, Parent Key ID: ${key.parent_key_id}`);
            if (!key.parent_key_id || key.parent_key_id === null) {
                rootKeys.push(key);
            } else {
                childKeys.push(key);
            }
        });
        
        console.log('Root keys (no parent):', rootKeys);
        console.log('Child keys (has parent):', childKeys);

        // Function to get child keys of a parent key
        function getChildKeys(parentKeyId) {
            console.log('getChildKeys called with parentKeyId:', parentKeyId, 'type:', typeof parentKeyId);
            const children = allVariantKeys.filter(key => {
                const match = key.parent_key_id == parentKeyId;
                if (match) {
                    console.log(`  Found child: ${key.name} (ID: ${key.id})`);
                }
                return match;
            });
            console.log(`Total children found: ${children.length}`);
            return children;
        }

        // Function to add a child key selector
        function addChildKeySelector(parentKeyId, level, selectedValue = null) {
            const childKeys = getChildKeys(parentKeyId);
            
            if (childKeys.length === 0) {
                // No more child keys, this is the final key
                // Set the final key_id and load variants
                $('#key_id').val(parentKeyId);
                loadVariantsForKey(parentKeyId);
                return;
            }

            // Check if selector already exists at this level
            const existingSelector = document.querySelector(`.key-selector-level-${level}`);
            if (existingSelector) {
                console.log(`Key selector already exists at level ${level}, skipping`);
                return;
            }

            // Remove any selectors after this level
            const selectorsToRemove = document.querySelectorAll(`.key-selector-level-${level}, .key-selector-level-${level} ~ .key-selector-level-${level + 1}, .key-selector-level-${level} ~ .key-selector-level-${level + 2}`);
            selectorsToRemove.forEach(el => el.remove());

            // Create new selector
            const selectorId = `child_key_level_${level}`;
            
            const selectorHtml = `
                <div class="mb-3 key-selector-level-${level}">
                    <label class="il-gray fs-14 fw-500 mb-10 d-block">
                        ${translations.selectChildKey} (${translations.level} ${level + 1})
                    </label>
                    <div class="custom-select-container" id="${selectorId}" data-name="child_key_${level}">
                        <div class="custom-select-display">
                            <div class="custom-select-value">
                                <span class="custom-select-placeholder">${translations.select}</span>
                            </div>
                            <span class="custom-select-arrow">
                                <i class="uil uil-angle-down"></i>
                            </span>
                        </div>
                        <div class="custom-select-dropdown">
                            <div class="custom-select-search-wrapper">
                                <input type="text" class="custom-select-search-input" placeholder="${translations.search}" autocomplete="off">
                            </div>
                            <div class="custom-select-options"></div>
                            <div class="custom-select-no-results" style="display: none;">${translations.noResults}</div>
                        </div>
                        <input type="hidden" name="child_key_${level}" value="">
                    </div>
                </div>
            `;

            $('#keySelectorsContainer').append(selectorHtml);

            // Initialize the custom select
            CustomSelect.init(selectorId);

            // Set options
            const options = childKeys.map(key => ({ id: key.id, name: key.name }));
            CustomSelect.setOptions(selectorId, options, translations.select);

            // Set selected value if provided
            if (selectedValue) {
                CustomSelect.setValue(selectorId, selectedValue);
            }

            // Handle change event (only attach once)
            const selectorElement = document.getElementById(selectorId);
            if (selectorElement && !selectorElement.dataset.listenerAttached) {
                selectorElement.dataset.listenerAttached = 'true';
                selectorElement.addEventListener('change', function(e) {
                    const selectedKeyId = e.detail ? e.detail.value : CustomSelect.getValue(selectorId);
                    const currentLevel = level;
                    
                    if (selectedKeyId) {
                        // Check if this key has children
                        addChildKeySelector(selectedKeyId, currentLevel + 1);
                    } else {
                        // Clear subsequent selectors
                        const selectorsToRemove = document.querySelectorAll(`.key-selector-level-${currentLevel + 1}, .key-selector-level-${currentLevel + 2}, .key-selector-level-${currentLevel + 3}`);
                        selectorsToRemove.forEach(el => el.remove());
                        $('#key_id').val('');
                        $('#parentVariantContainer').hide();
                        $('#variantSelectorsContainer').empty();
                    }
                });
            }

            // If there's a selected value, trigger change to load next level
            if (selectedValue) {
                const event = new CustomEvent('change', { 
                    detail: { value: selectedValue },
                    bubbles: true
                });
                document.getElementById(selectorId).dispatchEvent(event);
            }
        }

        // Function to load variants for the final selected key
        function loadVariantsForKey(keyId) {
            console.log('Loading variants for key:', keyId);
            
            // Show parent variant container
            $('#parentVariantContainer').show();
            $('#variantSelectorsContainer').empty();

            // Load root variants (variants without parent)
            loadVariantLevel(keyId, null, 0);
        }

        // Function to load variants at a specific level
        function loadVariantLevel(keyId, parentVariantId, level) {
            console.log(`Loading variant level ${level} for key ${keyId}, parent ${parentVariantId}`);

            $.ajax({
                url: '{{ route('admin.variants-configurations.get-parents-by-key') }}',
                method: 'GET',
                data: {
                    key_id: keyId,
                    parent_id: parentVariantId,
                    current_id: '{{ isset($variantsConfig) ? $variantsConfig->id : '' }}'
                },
                success: function(response) {
                    console.log(`Variant level ${level} response:`, response);

                    if (response.success && response.data && response.data.length > 0) {
                        addVariantSelector(keyId, parentVariantId, level, response.data);
                    } else {
                        console.log(`No variants found at level ${level}`);
                        // If this is level 0 and no variants, just show message
                        if (level === 0) {
                            $('#variantSelectorsContainer').html(`
                                <div class="alert alert-info">
                                    <i class="uil uil-info-circle me-1"></i>
                                    {{ trans('catalogmanagement::variantsconfig.no_variants_for_key') }}
                                </div>
                            `);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading variants:', error);
                }
            });
        }

        // Function to add a variant selector
        function addVariantSelector(keyId, parentVariantId, level, variants) {
            // Check if selector already exists at this level
            const existingSelector = document.querySelector(`.variant-selector-level-${level}`);
            if (existingSelector) {
                console.log(`Selector already exists at level ${level}, skipping`);
                return;
            }

            // Remove any selectors after this level
            const selectorsToRemove = document.querySelectorAll(`.variant-selector-level-${level}, .variant-selector-level-${level} ~ .variant-selector-level-${level + 1}, .variant-selector-level-${level} ~ .variant-selector-level-${level + 2}`);
            selectorsToRemove.forEach(el => el.remove());

            const selectorId = `variant_level_${level}`;
            
            const selectorHtml = `
                <div class="mb-3 variant-selector-level-${level}">
                    <label class="il-gray fs-14 fw-500 mb-10 d-block">
                        ${translations.selectParentVariant} (${translations.level} ${level + 1})
                    </label>
                    <div class="custom-select-container" id="${selectorId}" data-name="variant_${level}" data-key-id="${keyId}">
                        <div class="custom-select-display">
                            <div class="custom-select-value">
                                <span class="custom-select-placeholder">${translations.noParent}</span>
                            </div>
                            <span class="custom-select-arrow">
                                <i class="uil uil-angle-down"></i>
                            </span>
                        </div>
                        <div class="custom-select-dropdown">
                            <div class="custom-select-search-wrapper">
                                <input type="text" class="custom-select-search-input" placeholder="${translations.search}" autocomplete="off">
                            </div>
                            <div class="custom-select-options"></div>
                            <div class="custom-select-no-results" style="display: none;">${translations.noResults}</div>
                        </div>
                        <input type="hidden" name="variant_${level}" value="">
                    </div>
                </div>
            `;

            $('#variantSelectorsContainer').append(selectorHtml);

            // Initialize the custom select
            CustomSelect.init(selectorId);

            // Set options
            const options = variants.map(v => ({ id: v.id, name: v.display_name }));
            CustomSelect.setOptions(selectorId, options, translations.noParent);

            // Check if we need to pre-select a variant in edit mode
            if (editModeParentId && level === 0) {
                console.log('Edit mode: Checking if parent variant', editModeParentId, 'is in this level');
                const parentVariantInList = variants.find(v => v.id == editModeParentId);
                if (parentVariantInList) {
                    console.log('Edit mode: Pre-selecting parent variant', editModeParentId);
                    CustomSelect.setValue(selectorId, editModeParentId);
                    $('#parent_id').val(editModeParentId);
                }
            }

            // Handle change event (only attach once)
            const selectorElement = document.getElementById(selectorId);
            if (selectorElement && !selectorElement.dataset.listenerAttached) {
                selectorElement.dataset.listenerAttached = 'true';
                selectorElement.addEventListener('change', function(e) {
                    const selectedVariantId = e.detail ? e.detail.value : CustomSelect.getValue(selectorId);
                    const currentLevel = level;
                    const currentKeyId = this.dataset.keyId;
                    
                    // Update hidden parent_id field
                    $('#parent_id').val(selectedVariantId || '');
                    
                    if (selectedVariantId) {
                        // Load child variants
                        loadVariantLevel(currentKeyId, selectedVariantId, currentLevel + 1);
                    } else {
                        // Clear subsequent selectors
                        const selectorsToRemove = document.querySelectorAll(`.variant-selector-level-${currentLevel + 1}, .variant-selector-level-${currentLevel + 2}, .variant-selector-level-${currentLevel + 3}`);
                        selectorsToRemove.forEach(el => el.remove());
                    }
                });
            }
        }

        // Handle root key selection using custom select event
        const rootKeyElement = document.getElementById('root_key_id');
        if (rootKeyElement && !rootKeyElement.dataset.listenerAttached) {
            rootKeyElement.dataset.listenerAttached = 'true';
            rootKeyElement.addEventListener('change', function(e) {
                const selectedKeyId = e.detail ? e.detail.value : CustomSelect.getValue('root_key_id');
                
                console.log('=== Root key changed ===');
                console.log('Selected key ID:', selectedKeyId);
                
                // Clear all child selectors
                const childSelectors = document.querySelectorAll('#keySelectorsContainer .key-selector-level-0, #keySelectorsContainer .key-selector-level-0 ~ *');
                childSelectors.forEach(el => {
                    if (!el.classList.contains('mb-3') || el.querySelector('#root_key_id')) return;
                    el.remove();
                });
                
                $('#key_id').val('');
                $('#parent_id').val('');
                $('#parentVariantContainer').hide();
                $('#variantSelectorsContainer').empty();
                
                if (selectedKeyId) {
                    // Check if this key has children
                    const childKeys = getChildKeys(selectedKeyId);
                    
                    console.log('Decision: Has children?', childKeys.length > 0);
                    
                    if (childKeys.length > 0) {
                        // Has child keys, show next level
                        console.log('Showing child key selector');
                        addChildKeySelector(selectedKeyId, 0);
                    } else {
                        // No child keys, this is the final key
                        console.log('No children, loading variants');
                        $('#key_id').val(selectedKeyId);
                        loadVariantsForKey(selectedKeyId);
                    }
                }
            });
        }

        // Handle Key Selection Change - Filter Parent Variants (OLD CODE - KEEP FOR BACKWARD COMPATIBILITY)
        $('#key_id').on('change', function() {
            const selectedKeyId = $(this).val();
            const parentSelect = $('#parent_id');

            console.log('Key changed to:', selectedKeyId);

            if (!selectedKeyId) {
                // If no key selected, show no parent message
                parentSelect.html('<option value="">-- {{ trans('catalogmanagement::variantsconfig.select_key_first') }} --</option>');
                parentSelect.prop('disabled', true);
                // Destroy and reinitialize Select2
                if (parentSelect.data('select2')) {
                    parentSelect.select2('destroy');
                }
                parentSelect.select2({
                    width: '100%',
                    @if(app()->getLocale() == 'ar')
                    dir: 'rtl'
                    @endif
                });
                return;
            }

            // Show loading state
            parentSelect.html('<option value="">{{ trans('common.loading') }}...</option>');
            parentSelect.prop('disabled', true);

            console.log('Making AJAX request to get parents for key:', selectedKeyId);

            // Make AJAX request to get parent variants for selected key
            $.ajax({
                url: '{{ route('admin.variants-configurations.get-parents-by-key') }}',
                method: 'GET',
                data: {
                    key_id: selectedKeyId,
                    current_id: '{{ isset($variantsConfig) ? $variantsConfig->id : '' }}'
                },
                success: function(response) {
                    console.log('AJAX response:', response);

                    let options = '<option value="">-- {{ trans('catalogmanagement::variantsconfig.no_parent') }} --</option>';

                    if (response.success && response.data && response.data.length > 0) {
                        console.log('Found', response.data.length, 'parent variants');
                        response.data.forEach(function(parent) {
                            const selected = '{{ isset($variantsConfig) ? $variantsConfig->parent_id : old('parent_id') }}' == parent.id ? 'selected' : '';
                            options += `<option value="${parent.id}" ${selected}>${parent.display_name}</option>`;
                        });
                    } else {
                        console.log('No parent variants found for this key');
                    }

                    parentSelect.html(options);
                    parentSelect.prop('disabled', false);

                    // Destroy and reinitialize Select2 with RTL support
                    if (parentSelect.data('select2')) {
                        parentSelect.select2('destroy');
                    }
                    parentSelect.select2({
                        width: '100%',
                        @if(app()->getLocale() == 'ar')
                        dir: 'rtl'
                        @endif
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching parent variants:', error);
                    console.error('XHR:', xhr);
                    console.error('Status:', status);
                    parentSelect.html('<option value="">-- {{ trans('catalogmanagement::variantsconfig.error_loading_parents') }} --</option>');
                    parentSelect.prop('disabled', false);
                }
            });
        });

        // Trigger change event on page load if key is already selected
        if ($('#key_id').val()) {
            console.log('Triggering change on page load for key:', $('#key_id').val());
            $('#key_id').trigger('change');
        }

        // Handle edit mode - pre-populate hierarchical selectors
        @if(isset($variantsConfig))
        (function initializeEditMode() {
            const editKeyId = {{ $variantsConfig->key_id ?? 'null' }};
            const editParentId = {{ $variantsConfig->parent_id ?? 'null' }};
            
            console.log('=== EDIT MODE INITIALIZATION ===');
            console.log('Edit Key ID:', editKeyId);
            console.log('Edit Parent ID:', editParentId);
            
            if (!editKeyId) {
                console.log('No key ID in edit mode');
                return;
            }
            
            // Find the key in allVariantKeys
            const selectedKey = allVariantKeys.find(k => k.id == editKeyId);
            if (!selectedKey) {
                console.log('Selected key not found in allVariantKeys');
                return;
            }
            
            console.log('Selected key:', selectedKey);
            
            // Build the key hierarchy path from root to selected key
            function getKeyPath(keyId) {
                const path = [];
                let currentKey = allVariantKeys.find(k => k.id == keyId);
                
                while (currentKey) {
                    path.unshift(currentKey);
                    if (currentKey.parent_key_id) {
                        currentKey = allVariantKeys.find(k => k.id == currentKey.parent_key_id);
                    } else {
                        currentKey = null;
                    }
                }
                
                return path;
            }
            
            const keyPath = getKeyPath(editKeyId);
            console.log('Key path:', keyPath.map(k => `${k.name} (${k.id})`));
            
            if (keyPath.length === 0) {
                console.log('Empty key path');
                return;
            }
            
            // Set root key (without triggering change)
            const rootKey = keyPath[0];
            console.log('Setting root key:', rootKey.name, rootKey.id);
            CustomSelect.setValue('root_key_id', rootKey.id);
            
            // If there's only one key in path (root key is the final key)
            if (keyPath.length === 1) {
                console.log('Root key is the final key');
                $('#key_id').val(editKeyId);
                loadVariantsForKey(editKeyId);
                return;
            }
            
            // Create child key selectors for each level (without triggering change events)
            for (let i = 0; i < keyPath.length - 1; i++) {
                const parentKey = keyPath[i];
                const childKey = keyPath[i + 1];
                
                console.log(`Creating selector for level ${i}: parent=${parentKey.name}, child=${childKey.name}`);
                
                // Get child keys for this parent
                const childKeys = getChildKeys(parentKey.id);
                
                if (childKeys.length === 0) {
                    console.log('No child keys found, stopping');
                    break;
                }
                
                // Create the selector HTML
                const selectorId = `child_key_level_${i}`;
                const selectorHtml = `
                    <div class="mb-3 key-selector-level-${i}">
                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                            ${translations.selectChildKey} (${translations.level} ${i + 1})
                        </label>
                        <div class="custom-select-container" id="${selectorId}" data-name="child_key_${i}">
                            <div class="custom-select-display">
                                <div class="custom-select-value">
                                    <span class="custom-select-placeholder">${translations.select}</span>
                                </div>
                                <span class="custom-select-arrow">
                                    <i class="uil uil-angle-down"></i>
                                </span>
                            </div>
                            <div class="custom-select-dropdown">
                                <div class="custom-select-search-wrapper">
                                    <input type="text" class="custom-select-search-input" placeholder="${translations.search}" autocomplete="off">
                                </div>
                                <div class="custom-select-options"></div>
                                <div class="custom-select-no-results" style="display: none;">${translations.noResults}</div>
                            </div>
                            <input type="hidden" name="child_key_${i}" value="">
                        </div>
                    </div>
                `;
                
                $('#keySelectorsContainer').append(selectorHtml);
                
                // Initialize the custom select
                CustomSelect.init(selectorId);
                
                // Set options
                const options = childKeys.map(key => ({ id: key.id, name: key.name }));
                CustomSelect.setOptions(selectorId, options, translations.select);
                
                // Set selected value
                CustomSelect.setValue(selectorId, childKey.id);
                
                // Attach change event listener
                const selectorElement = document.getElementById(selectorId);
                if (selectorElement && !selectorElement.dataset.listenerAttached) {
                    selectorElement.dataset.listenerAttached = 'true';
                    selectorElement.addEventListener('change', function(e) {
                        const selectedKeyId = e.detail ? e.detail.value : CustomSelect.getValue(selectorId);
                        const currentLevel = i;
                        
                        if (selectedKeyId) {
                            addChildKeySelector(selectedKeyId, currentLevel + 1);
                        } else {
                            const selectorsToRemove = document.querySelectorAll(`.key-selector-level-${currentLevel + 1}, .key-selector-level-${currentLevel + 2}, .key-selector-level-${currentLevel + 3}`);
                            selectorsToRemove.forEach(el => el.remove());
                            $('#key_id').val('');
                            $('#parentVariantContainer').hide();
                            $('#variantSelectorsContainer').empty();
                        }
                    });
                }
            }
            
            // Set the final key_id
            $('#key_id').val(editKeyId);
            console.log('Set final key_id to:', editKeyId);
            
            // Load variants for the final key
            console.log('Loading variants for final key');
            loadVariantsForKey(editKeyId);
            
            // If there's a parent variant, we need to build its hierarchy and pre-select
            if (editParentId) {
                console.log('Edit mode has parent variant:', editParentId);
                
                // Wait for variants to load, then pre-select the parent
                setTimeout(function() {
                    console.log('Attempting to pre-select parent variant:', editParentId);
                    
                    // Find the variant selector at level 0
                    const level0Selector = document.getElementById('variant_level_0');
                    if (level0Selector) {
                        console.log('Found level 0 variant selector, setting value to:', editParentId);
                        CustomSelect.setValue('variant_level_0', editParentId);
                        $('#parent_id').val(editParentId);
                    } else {
                        console.log('Level 0 variant selector not found yet');
                    }
                }, 1500);
            }
        })();
        @endif
    });
</script>
@endpush
