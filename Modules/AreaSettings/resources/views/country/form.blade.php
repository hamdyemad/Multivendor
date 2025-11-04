@extends('layout.app')

@section('title')
{{ $title }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::country.countries_management'), 'url' => route('admin.area-settings.countries.index')],
                    ['title' => isset($country) ? __('areasettings::country.edit_country') : __('areasettings::country.create_country')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($country) ? __('areasettings::country.edit_country') : __('areasettings::country.create_country') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ __('areasettings::country.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="countryForm" method="POST" action="{{ isset($country) ? route('admin.area-settings.countries.update', $country->id) : route('admin.area-settings.countries.store') }}">
                            @csrf
                            @if(isset($country))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <!-- Translation Fields -->
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name_{{ $language->id }}" class="form-label w-100" @if($language->code == 'ar') dir="rtl" @endif>{{ __('areasettings::country.name_' . ($language->code == 'ar' ? 'ar' : 'en')) }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name_{{ $language->id }}" @if($language->code == 'ar') dir="rtl" @endif name="translations[{{ $language->id }}][name]" value="{{ old('translations.' . $language->id . '.name', isset($country) ? $country->translations->where('lang_id', $language->id)->first()->lang_value ?? '' : '') }}">
                                            @error('translations.' . $language->id . '.name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="code" class="form-label">{{ __('areasettings::country.country_code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code', isset($country) ? $country->code : '') }}" maxlength="3">
                                        @error('code')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="phone_code" class="form-label">{{ __('areasettings::country.phone_code') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phone_code" name="phone_code" value="{{ old('phone_code', isset($country) ? $country->phone_code : '') }}" maxlength="10">
                                        @error('phone_code')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="currency_id" class="form-label">{{ __('areasettings::country.currency') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="currency_id" name="currency_id">
                                            <option value="">{{ __('areasettings::country.select_currency') }}</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}" 
                                                    {{ old('currency_id', isset($country) ? $country->currency_id : '') == $currency->id ? 'selected' : '' }}>
                                                    {{ $currency->getTranslation('name', app()->getLocale()) }} ({{ $currency->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('currency_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                {{-- Active Status Switcher --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('areasettings::country.active') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="active" 
                                                       name="active" 
                                                       value="1"
                                                       {{ old('active', isset($country) ? $country->active : 1) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active"></label>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex align-items-center justify-content-end">
                                        <a href="{{ route('admin.area-settings.countries.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('areasettings::country.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i> {{ isset($country) ? __('areasettings::country.update_country') : __('areasettings::country.create_country') }}
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX Form Submission
    const countryForm = document.getElementById('countryForm');
    const submitBtn = countryForm.querySelector('button[type="submit"]');
    const alertContainer = document.getElementById('alertContainer');
    let originalBtnHtml = ''; // Store outside to access in catch block

    countryForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML; // Store original HTML
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        // Update loading text dynamically
        const loadingText = @json(isset($country) ? trans('loading.updating') : trans('loading.creating'));
        const loadingSubtext = '{{ trans("loading.please_wait") }}';
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-text').textContent = loadingText;
            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
        }
        
        // Show loading overlay
        LoadingOverlay.show();

        // Clear previous alerts
        alertContainer.innerHTML = '';

        // Remove previous validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Start progress bar animation
        LoadingOverlay.animateProgressBar(30, 300).then(() => {
            // Prepare form data
            const formData = new FormData(countryForm);

            // Send AJAX request
            return fetch(countryForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
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
                // Show success animation with dynamic message
                const successMessage = @json(isset($country) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                LoadingOverlay.showSuccess(
                    successMessage,
                    '{{ trans("loading.redirecting") }}'
                );
                
                // Show success alert
                showAlert('success', data.message || successMessage);
                
                // Redirect after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("admin.area-settings.countries.index") }}';
                }, 1500);
            });
        })
        .catch(error => {
            // Hide loading overlay and reset progress bar
            LoadingOverlay.hide();
            
            // Handle validation errors
            if (error.errors) {
                console.log('Validation errors received:', error.errors);
                Object.keys(error.errors).forEach(key => {
                    console.log('Processing error key:', key);
                    
                    let input = null;
                    const possibleSelectors = [];
                    
                    // Add original key
                    possibleSelectors.push(`[name="${key}"]`);
                    
                    // If key contains dots (Laravel format: translations.0.name)
                    if (key.includes('.')) {
                        // Convert to bracket notation: translations[0][name]
                        const bracketKey = key.replace(/^([^.]+)\.(\d+)\.([^.]+)$/, '$1[$2][$3]');
                        possibleSelectors.push(`[name="${bracketKey}"]`);
                        
                        // Also try with escaped brackets
                        const escapedBracketKey = bracketKey.replace(/\[/g, '\\\\[').replace(/\]/g, '\\\\]');
                        possibleSelectors.push(`[name="${escapedBracketKey}"]`);
                    }
                    
                    // If key contains brackets, try escaping them
                    if (key.includes('[')) {
                        const escapedKey = key.replace(/\[/g, '\\\\[').replace(/\]/g, '\\\\]');
                        possibleSelectors.push(`[name="${escapedKey}"]`);
                    }
                    
                    // Try each selector until we find the input
                    for (const selector of possibleSelectors) {
                        console.log('Trying selector:', selector);
                        try {
                            input = document.querySelector(selector);
                            if (input) {
                                console.log('Found input with selector:', selector);
                                break;
                            }
                        } catch (e) {
                            console.log('Invalid selector:', selector, e.message);
                        }
                    }
                    
                    // If still not found, try to find by ID pattern (for translation fields)
                    if (!input && key.match(/^translations\.(\d+)\.name$/)) {
                        const languageId = key.match(/^translations\.(\d+)\.name$/)[1];
                        const idSelector = `#name_${languageId}`;
                        console.log('Trying ID selector:', idSelector);
                        input = document.querySelector(idSelector);
                        if (input) {
                            console.log('Found input with ID selector:', idSelector);
                        }
                    }
                    
                    if (input) {
                        console.log('Found input for key:', key);
                        input.classList.add('is-invalid');
                        
                        // Remove any existing feedback
                        const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }
                        
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = error.errors[key][0];
                        input.parentNode.appendChild(feedback);
                    } else {
                        console.log('Could not find input for error key:', key);
                        console.log('Available inputs:', Array.from(document.querySelectorAll('input, select, textarea')).map(el => el.name));
                    }
                });
                showAlert('danger', error.message || '{{ __("Please check the form for errors") }}');
            } else {
                showAlert('danger', error.message || '{{ __("An error occurred") }}');
            }
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });
    
    // Show alert function
    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show mb-20`;
        alert.innerHTML = `
            <i class="uil uil-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.getElementById('alertContainer').appendChild(alert);

        // Scroll to top to show alert
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Auto-format phone code to start with +
    $('#phone_code').on('input', function() {
        let value = $(this).val();
        if (value && !value.startsWith('+')) {
            $(this).val('+' + value);
        }
    });
    
    // Auto-format country code to uppercase
    $('#code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
});
</script>
@endpush

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay 
        :loadingText="trans('loading.processing')" 
        :loadingSubtext="trans('loading.please_wait')" 
    />
@endpush
