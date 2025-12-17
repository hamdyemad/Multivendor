@extends('layout.app')

@section('title')
    {{ isset($department) ? trans('categorymanagment::department.edit_department') : trans('categorymanagment::department.create_department') }}
@endsection


@push('styles')
    <style>
        /* Ensure validation messages are always visible */
        .invalid-feedback {
            display: block !important;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* RTL support for Arabic validation messages */
        input[data-lang="ar"]+.invalid-feedback,
        textarea[data-lang="ar"]+.invalid-feedback {
            direction: rtl;
            text-align: right;
        }

        /* Highlight invalid fields with red border */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        /* Select2 invalid state styling */
        .select2.is-invalid+.select2-container .select2-selection,
        .select2-container.is-invalid .select2-selection {
            border-color: #dc3545 !important;
        }

        /* Smooth transition for error states */
        .form-control {
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('categorymanagment::department.departments_management'),
                        'url' => route('admin.category-management.departments.index'),
                    ],
                    [
                        'title' => isset($department)
                            ? trans('categorymanagment::department.edit_department')
                            : trans('categorymanagment::department.create_department'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($department) ? trans('categorymanagment::department.edit_department') : trans('categorymanagment::department.create_department') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="departmentForm"
                            action="{{ isset($department) ? route('admin.category-management.departments.update', $department->id) : route('admin.category-management.departments.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($department))
                                @method('PUT')
                            @endif

                            <div class="row">
                                {{-- Dynamic Language Fields for Name --}}
                                @foreach ($languages as $language)
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="translation_{{ $language->id }}_name"
                                                class="il-gray fs-14 fw-500 mb-10"
                                                @if ($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                @if ($language->code == 'ar')
                                                    الاسم ({{ $language->name }}) <span class="text-danger">*</span>
                                                @else
                                                    {{ trans('categorymanagment::department.name_english') }}
                                                    ({{ $language->name }})
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.name') is-invalid @enderror"
                                                id="translation_{{ $language->id }}_name"
                                                name="translations[{{ $language->id }}][name]"
                                                value="{{ isset($department) ? $department->getTranslation('name', $language->code) ?? '' : old('translations.' . $language->id . '.name') }}"
                                                placeholder="@if ($language->code == 'ar') أدخل اسم القسم بالعربية@else{{ trans('categorymanagment::department.enter_department_name_english') }} @endif"
                                                @if ($language->rtl) dir="rtl" @endif
                                                data-lang="{{ $language->code }}">
                                            @error('translations.' . $language->id . '.name')
                                                <div class="invalid-feedback d-block" style="display: block !important;">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Dynamic Language Fields for Description --}}
                                @foreach ($languages as $language)
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="translation_{{ $language->id }}_description"
                                                class="il-gray fs-14 fw-500 mb-10"
                                                @if ($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                @if ($language->code == 'ar')
                                                    الوصف ({{ $language->name }})
                                                @else
                                                    {{ trans('categorymanagment::department.description') }}
                                                    ({{ $language->name }})
                                                @endif
                                            </label>
                                            <textarea
                                                class="form-control ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.description') is-invalid @enderror"
                                                id="translation_{{ $language->id }}_description" name="translations[{{ $language->id }}][description]"
                                                rows="4"
                                                placeholder="@if ($language->code == 'ar') أدخل وصف القسم بالعربية@else{{ trans('categorymanagment::department.enter_department_name_english') }} @endif"
                                                @if ($language->rtl) dir="rtl" @endif data-lang="{{ $language->code }}">{{ isset($department) ? $department->getTranslation('description', $language->code) ?? '' : old('translations.' . $language->id . '.description') }}</textarea>
                                            @error('translations.' . $language->id . '.description')
                                                <div class="invalid-feedback d-block" style="display: block !important;">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                {{-- Department Image --}}
                                <div class="col-md-6 mb-25">
                                    <x-image-upload id="department_image" name="image" :label="trans('categorymanagment::department.image')" :placeholder="trans('categorymanagment::department.click_to_upload_image')"
                                        :recommendedSize="trans('categorymanagment::department.recommended_size')" :existingImage="isset($department) && $department->image ? $department->image : null" aspectRatio="square" />
                                </div>

                                <div class="col-md-6 mb-25">
                                    <x-image-upload id="department_icon" name="icon" :label="trans('categorymanagment::department.icon')" :placeholder="trans('categorymanagment::department.click_to_upload_icon')"
                                        :recommendedSize="trans('categorymanagment::department.recommended_size_for_icon')" :existingImage="isset($department) && $department->icon ? $department->icon : null" aspectRatio="square" />
                                </div>


                                {{-- Activation Switcher --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('categorymanagment::department.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox" class="form-check-input" id="active"
                                                    name="active" value="1"
                                                    {{ old('active', $department->active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Commission Field --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="commission" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('categorymanagment::department.commission') }}<span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('commission') is-invalid @enderror"
                                            id="commission" name="commission"
                                            value="{{ old('commission', $department->commission ?? 0) }}" min="0"
                                            max="100" step="0.01">
                                        @error('commission')
                                            <div class="invalid-feedback d-block" style="display: block !important;">
                                                {{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.category-management.departments.index') }}"
                                    class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ trans('categorymanagment::department.cancel') }}
                                </a>
                                <button type="submit" id="submitBtn"
                                    class="btn btn-primary btn-default btn-squared text-capitalize"
                                    style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span>{{ isset($department) ? trans('categorymanagment::department.update_department') : trans('categorymanagment::department.add_department') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            if ($.fn.select2) {
                $('.select2-ajax').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '{{ trans('categorymanagment::department.select_activities') }}',
                    allowClear: true,
                    ajax: {
                        url: function() {
                            return $(this).data('ajax-url');
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0
                });
            }
            document.addEventListener('DOMContentLoaded', function() {
                // AJAX Form Submission
                const departmentForm = document.getElementById('departmentForm');
                const submitBtn = document.getElementById('submitBtn');
                const alertContainer = document.getElementById('alertContainer');

                // Clear validation errors on input
                departmentForm.querySelectorAll('input, textarea').forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.classList.contains('is-invalid')) {
                            this.classList.remove('is-invalid');
                            const feedback = this.parentNode.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.remove();
                            }
                        }
                    });
                });

                // Clear validation errors on select2 change
                $('.select2').on('change', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.remove();
                        }
                        // Also remove invalid class from Select2 container
                        const select2Container = this.parentNode.querySelector('.select2-container');
                        if (select2Container) {
                            select2Container.classList.remove('is-invalid');
                        }
                    }
                });

                departmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Disable submit button and show loading
                    submitBtn.disabled = true;
                    const btnIcon = submitBtn.querySelector('i');
                    const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                    if (btnIcon) btnIcon.classList.add('d-none');
                    if (btnText) btnText.classList.add('d-none');
                    submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                    // Update loading text dynamically
                    const loadingText = @json(isset($department) ? trans('loading.updating') : trans('loading.creating'));
                    const loadingSubtext = '{{ trans('loading.please_wait') }}';
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
                            const formData = new FormData(departmentForm);

                            // Send AJAX request
                            return fetch(departmentForm.action, {
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
                                const successMessage = @json(isset($department) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                                LoadingOverlay.showSuccess(
                                    successMessage,
                                    '{{ trans('loading.redirecting') }}'
                                );

                                // Show success alert
                                showAlert('success', data.message || successMessage);

                                // Redirect after 1.5 seconds
                                setTimeout(() => {
                                    window.location.href = data.redirect ||
                                        '{{ route('admin.category-management.departments.index') }}';
                                }, 1500);
                            });
                        })
                        .catch(error => {
                            // Hide loading overlay and reset progress bar
                            LoadingOverlay.hide();

                            // Handle validation errors
                            if (error.errors) {
                                let errorCount = 0;
                                Object.keys(error.errors).forEach(key => {
                                    // Handle both dot notation and bracket notation for nested fields
                                    const inputName = key.replace(/\./g, '][').replace(/^/, '')
                                        .replace(/\]$/, '');
                                    let input = document.querySelector(`[name="${key}"]`);

                                    // Try alternative selectors for nested fields
                                    if (!input) {
                                        const bracketKey = key.replace(/\./g, '][');
                                        input = document.querySelector(`[name="${bracketKey}"]`);
                                    }
                                    if (!input) {
                                        const parts = key.split('.');
                                        if (parts.length === 3) {
                                            // translations.1.name -> translations[1][name]
                                            input = document.querySelector(
                                                `[name="${parts[0]}[${parts[1]}][${parts[2]}]"]`
                                            );
                                        }
                                    }
                                    // Handle activities array errors (activities.0, activities.1, etc.)
                                    if (!input && key.startsWith('activities')) {
                                        input = document.querySelector('[name="activities[]"]');
                                    }

                                    if (input) {
                                        errorCount++;
                                        input.classList.add('is-invalid');

                                        // Remove existing feedback to avoid duplicates
                                        const existingFeedback = input.parentNode.querySelector(
                                            '.invalid-feedback');
                                        if (existingFeedback) {
                                            existingFeedback.remove();
                                        }

                                        const feedback = document.createElement('div');
                                        feedback.className = 'invalid-feedback d-block';
                                        feedback.style.display = 'block';
                                        feedback.textContent = error.errors[key][0];
                                        input.parentNode.appendChild(feedback);

                                        // For Select2, also add invalid class to the Select2 container
                                        if (input.classList.contains('select2')) {
                                            const select2Container = input.parentNode.querySelector(
                                                '.select2-container');
                                            if (select2Container) {
                                                select2Container.classList.add('is-invalid');
                                            }
                                        }

                                        // Scroll to first error
                                        if (errorCount === 1) {
                                            input.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                        }
                                    }
                                });

                                const errorMessage = error.message ||
                                    '{{ __('Please check the form for errors') }}';
                                showAlert('danger', errorMessage +
                                    ` (${errorCount} ${errorCount === 1 ? 'error' : 'errors'})`);
                            } else {
                                showAlert('danger', error.message || '{{ __('An error occurred') }}');
                            }

                            // Re-enable submit button
                            submitBtn.disabled = false;
                            const btnIcon = submitBtn.querySelector('i');
                            const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                            if (btnIcon) btnIcon.classList.remove('d-none');
                            if (btnText) btnText.classList.remove('d-none');
                            submitBtn.querySelector('.spinner-border').classList.add('d-none');
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
                    alertContainer.appendChild(alert);

                    // Scroll to top to show alert
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay :loadingText="trans('loading.processing')" :loadingSubtext="trans('loading.please_wait')" />
@endpush
