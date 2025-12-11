@extends('layout.app')

@section('title', isset($city) ? __('areasettings::city.edit_city') : __('areasettings::city.create_city'))

@push('styles')
<!-- Select2 CSS loaded via Vite -->
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::city.cities_management'), 'url' => route('admin.area-settings.cities.index')],
                    ['title' => isset($city) ? __('areasettings::city.edit_city') : __('areasettings::city.add_city')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{ isset($city) ? __('areasettings::city.edit_city') : __('areasettings::city.add_city') }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($city) ? route('admin.area-settings.cities.update', $city->id) : route('admin.area-settings.cities.store') }}" id="cityForm">
                            @csrf
                            @if(isset($city))
                                @method('PUT')
                            @endif

                            <div class="row">
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="translations_{{ $language->id }}_name" class="form-label w-100 text-start">
                                                @if($language->code == 'en')
                                                {{ __('areasettings::city.name') }} ({{ $language->name }})
                                                @elseif($language->code == 'ar')
                                                    الاسم بالعربية
                                                @endif

                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="translations[{{ $language->id }}][name]"
                                                id="translations_{{ $language->id }}_name"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                value="{{ isset($city) ? $city->getTranslation('name', $language->code) : old('translations.'.$language->id.'.name') }}"
                                                {{ $language->rtl ? 'dir=rtl' : '' }}
                                                placeholder="{{ $language->code == 'ar' ? 'أدخل اسم المدينة' : 'Enter city name' }}"
                                            >
                                            @error('translations.'.$language->id.'.name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <!-- Country Selection -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="country_id" class="form-label">{{ __('areasettings::city.country') }} <span class="text-danger">*</span></label>
                                        <select name="country_id" id="country_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                            <option value="">{{ __('areasettings::city.select_country') }}</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country['id'] }}" {{ (isset($city) && $city->country_id == $country['id']) ? 'selected' : '' }}>
                                                    {{ $country['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Active Status Switcher --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('areasettings::city.active') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="active"
                                                       name="active"
                                                       value="1"
                                                       {{ old('active', isset($city) ? $city->active : 1) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active"></label>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Default City Switcher --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('areasettings::city.default') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-success form-switch-md">
                                                <input type="hidden" name="default" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="default"
                                                       name="default"
                                                       value="1"
                                                       {{ old('default', isset($city) ? $city->default : 0) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="default"></label>
                                            </div>
                                            <span class="text-muted fs-12 ms-2">{{ __('areasettings::city.default_city_info') }}</span>
                                        </div>
                                        @error('default')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group d-flex justify-content-end mt-4">
                                        <a href="{{ route('admin.area-settings.cities.index') }}" class="btn btn-light btn-default btn-squared text-capitalize me-2">
                                            <i class="uil uil-arrow-left"></i> {{ __('areasettings::city.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                            <i class="uil uil-check"></i> {{ isset($city) ? __('areasettings::city.update_city') : __('areasettings::city.create_city') }}
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
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            placeholder: "{{ __('areasettings::city.select_country') }}",
            allowClear: true
        });
    }

    // AJAX Form Submission
    const cityForm = document.getElementById('cityForm');
    const submitBtn = cityForm.querySelector('button[type="submit"]');
    let originalBtnHtml = '';

    cityForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        // Update loading text dynamically
        const loadingText = @json(isset($city) ? trans('loading.updating') : trans('loading.creating'));
        const loadingSubtext = '{{ trans("loading.please_wait") }}';
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-text').textContent = loadingText;
            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
        }

        // Show loading overlay
        LoadingOverlay.show();

        // Start progress bar animation
        LoadingOverlay.animateProgressBar(30, 300).then(() => {
            // Prepare form data
            const formData = new FormData(cityForm);

            // Send AJAX request
            return fetch(cityForm.action, {
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
                const successMessage = @json(isset($city) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                LoadingOverlay.showSuccess(
                    successMessage,
                    '{{ trans("loading.redirecting") }}'
                );

                // Redirect after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("admin.area-settings.cities.index") }}';
                }, 1500);
            });
        })
        .catch(error => {
            // Hide loading overlay
            LoadingOverlay.hide();

            // Remove previous validation errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Handle validation errors
            if (error.errors) {
                Object.keys(error.errors).forEach(key => {
                    const errorMessages = error.errors[key];

                    // Find the input field
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
                        try {
                            input = document.querySelector(selector);
                            if (input) break;
                        } catch (e) {
                            // Invalid selector, continue
                        }
                    }

                    // If still not found, try to find by ID pattern (for translation fields)
                    if (!input && key.match(/^translations\.(\d+)\.name$/)) {
                        const languageId = key.match(/^translations\.(\d+)\.name$/)[1];
                        input = document.querySelector(`#name_${languageId}`);
                    }

                    if (input) {
                        // Add is-invalid class
                        input.classList.add('is-invalid');

                        // Remove any existing feedback
                        const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }

                        // Create and append error message
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback d-block';
                        feedback.textContent = errorMessages[0];
                        input.parentNode.appendChild(feedback);
                    }

                    // Also show toastr notification
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessages[0]);
                    }
                });
            } else if (error.message) {
                if (typeof toastr !== 'undefined') {
                    toastr.error(error.message);
                }
            }

            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });
});
</script>
@endpush
