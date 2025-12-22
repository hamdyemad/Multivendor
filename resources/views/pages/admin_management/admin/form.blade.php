@extends('layout.app')
@section('title', isset($admin) ? trans('admin.edit_admin') : trans('admin.create_admin'))

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
                    ['title' => __('admin.admins_management'), 'url' => route('admin.admin-management.admins.index')],
                    ['title' => isset($admin) ? __('admin.edit_admin') : __('admin.create_admin')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($admin) ? __('admin.edit_admin') : __('admin.create_admin') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                                <strong>{{ __('admin.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="adminForm" method="POST" enctype="multipart/form-data"
                            action="{{ isset($admin) ? route('admin.admin-management.admins.update', $admin->id) : route('admin.admin-management.admins.store') }}">
                            @csrf
                            @if (isset($admin))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Translation Fields - Names -->
                                @php
                                    // Prepare the model data for multilingual-input component
                                    $adminModel = null;
                                    if (isset($admin)) {
                                        $adminModel = new class ($admin) {
                                            private $admin;

                                            public function __construct($admin)
                                            {
                                                $this->admin = $admin;
                                            }

                                            public function getTranslation($key, $langCode)
                                            {
                                                $language = \App\Models\Language::where('code', $langCode)->first();
                                                if (!$language) {
                                                    return '';
                                                }

                                                return $this->admin->translations
                                                    ->where('lang_id', $language->id)
                                                    ->where('lang_key', $key)
                                                    ->first()->lang_value ?? '';
                                            }
                                        };
                                    }
                                @endphp

                                <x-multilingual-input name="name" :label="'Name'" :labelAr="'الأسم'" type="text"
                                    :placeholder="'Name'" :placeholderAr="'الأسم'" :required="true" :languages="$languages"
                                    :model="$adminModel" oldPrefix="translations" :cols="6" />

                                <div class="col-md-12 mb-20">
                                    <x-image-upload id="image" name="image" :label="__('admin.admin_image') ?? 'Admin Image'" :existingImage="isset($admin) ? $admin->image : null"
                                        :placeholder="__('admin.click_to_upload_image') ?? 'Click to upload image'" aspectRatio="square" />
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">
                                            {{ __('admin.email') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control text-lowercase" id="email"
                                            name="email" value="{{ old('email', isset($admin) ? $admin->email : '') }}">
                                        <div id="email-error-container">
                                            @error('email')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Roles -->
                                <div class="col-md-6">
                                    <x-searchable-tags name="role_ids[]" :label="__('admin.roles')" :options="$roles
                                        ->map(
                                            fn($role) => [
                                                'id' => $role->id,
                                                'name' => $role->getTranslation('name', app()->getLocale()),
                                            ],
                                        )
                                        ->toArray()"
                                        :selected="old(
                                            'role_ids',
                                            isset($admin) ? $admin->roles->pluck('id')->toArray() : [],
                                        )" :placeholder="__('admin.select_roles')" :required="true" :multiple="true" />
                                    <div id="role_ids-error-container">
                                        @error('role_ids')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">
                                            {{ __('admin.password') }}
                                            @if (!isset($admin))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="{{ isset($admin) ? __('admin.leave_empty_to_keep_password') : __('admin.enter_password') }}">
                                            <span toggle="#password"
                                                class="uil uil-eye-slash text-lighten fs-15 field-icon toggle-password2"></span>
                                        </div>
                                        <div id="password-error-container">
                                            @error('password')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if (!isset($admin))
                                            <small class="text-muted helper-text">
                                                {{ __('admin.password_min_8') }}
                                            </small>
                                        @else
                                            <small class="text-muted helper-text">
                                                {{ __('admin.leave_empty_to_keep_password') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            {{ __('admin.confirm_password') }}
                                            @if (!isset($admin))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation"
                                                placeholder="{{ __('admin.confirm_password') }}">
                                            <span toggle="#password_confirmation"
                                                class="uil uil-eye-slash text-lighten fs-15 field-icon toggle-password2"></span>
                                        </div>
                                        <div id="password_confirmation-error-container">
                                            @error('password_confirmation')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Active Status Switcher --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('admin.active') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox" class="form-check-input" id="active"
                                                    name="active" value="1"
                                                    {{ old('active', isset($admin) ? $admin->active : 1) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active"></label>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Block Status Switcher --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('admin.block') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-danger form-switch-md">
                                                <input type="hidden" name="block" value="0">
                                                <input type="checkbox" class="form-check-input" id="block"
                                                    name="block" value="1"
                                                    {{ old('block', isset($admin) ? $admin->block : 0) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="block"></label>
                                            </div>
                                        </div>
                                        @error('block')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex align-items-center justify-content-end">
                                        <a href="{{ route('admin.admin-management.admins.index') }}"
                                            class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('admin.back_to_list') }}
                                        </a>
                                        <button type="submit"
                                            class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i>
                                            {{ isset($admin) ? __('admin.update_admin') : __('admin.create_admin') }}
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
            const adminForm = document.getElementById('adminForm');
            const submitBtn = adminForm.querySelector('button[type="submit"]');
            const alertContainer = document.getElementById('alertContainer');
            let originalBtnHtml = '';

            // Handle clearing errors on focus/input
            adminForm.addEventListener('focusin', function(e) {
                clearFieldError(e.target);
            });

            adminForm.addEventListener('input', function(e) {
                clearFieldError(e.target);
            });

            function clearFieldError(target) {
                const group = target.closest('.form-group') ||
                    target.closest('.searchable-tags-wrapper') ||
                    target.closest('.row');

                if (group) {
                    // Remove is-invalid class from input or specialized container
                    const invalidElements = group.querySelectorAll('.is-invalid');
                    invalidElements.forEach(el => el.classList.remove('is-invalid'));

                    // Remove error messages
                    const errorMessages = group.querySelectorAll('.text-danger.small, .invalid-feedback');
                    errorMessages.forEach(msg => msg.remove());

                    // Show helper text again
                    const helperText = group.querySelector('.helper-text');
                    if (helperText) helperText.classList.remove('d-none');
                }
            }

            adminForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Disable submit button and show loading
                submitBtn.disabled = true;
                originalBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('common.processing') ?? 'Processing...' }}';

                // Update loading text
                const loadingText = @json(isset($admin) ? trans('loading.updating') : trans('loading.creating'));
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
                document.querySelectorAll('.text-danger.small, .invalid-feedback').forEach(el => el
                    .remove());
                document.querySelectorAll('.helper-text').forEach(el => el.classList.remove('d-none'));

                // Start progress bar
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                        const formData = new FormData(adminForm);

                        return fetch(adminForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                    })
                    .then(response => {
                        LoadingOverlay.animateProgressBar(60, 200);

                        if (!response.ok) {
                            return response.json().then(data => {
                                throw data;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
                    })
                    .then(data => {
                        return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                            const successMessage = @json(isset($admin) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                            LoadingOverlay.showSuccess(
                                successMessage,
                                '{{ trans('loading.redirecting') }}'
                            );

                            setTimeout(() => {
                                window.location.href = data.redirect ||
                                    '{{ route('admin.admin-management.admins.index') }}';
                            }, 1500);
                        });
                    })
                    .catch(error => {
                        LoadingOverlay.hide();

                        // Handle validation errors
                        if (error.errors) {
                            console.log('Validation errors:', error.errors);

                            Object.keys(error.errors).forEach(key => {
                                let input = null;

                                // Try direct match first
                                input = document.querySelector(`[name="${key}"]`) ||
                                    document.querySelector(`[name="${key}[]"]`) ||
                                    document.querySelector(
                                        `.searchable-tags-wrapper[data-name="${key}[]"]`);

                                // Convert dot notation to bracket notation
                                if (!input && key.includes('.')) {
                                    // Split by dots and rebuild with brackets
                                    const parts = key.split('.');
                                    const bracketKey = parts[0] + parts.slice(1).map(part =>
                                        `[${part}]`).join('');
                                    input = document.querySelector(`[name="${bracketKey}"]`);
                                }

                                // Special case for role_ids.*
                                if (!input && key.startsWith('role_ids.')) {
                                    input = document.querySelector(`[name="role_ids[]"]`) ||
                                        document.querySelector(
                                            `.searchable-tags-wrapper[data-name="role_ids[]"]`);
                                }

                                if (input) {
                                    console.log(`Found input for ${key}:`, input);

                                    // Handle searchable tags specifically
                                    if (input.classList.contains('searchable-tags-wrapper')) {
                                        const container = input.querySelector(
                                            '.tag-input-container');
                                        if (container) container.classList.add('is-invalid');
                                    } else {
                                        input.classList.add('is-invalid');
                                    }

                                    // Hide helper text if error exists
                                    const formGroup = input.closest('.form-group');
                                    if (formGroup) {
                                        const helperText = formGroup.querySelector(
                                            '.helper-text');
                                        if (helperText) helperText.classList.add('d-none');
                                    }

                                    // Append error message to a dedicated container if it exists, otherwise to parent
                                    const safeKey = key.replace(/\./g, '_').replace(/\[|\]/g,
                                        '_');
                                    const errorContainer = document.getElementById(
                                            `${safeKey}-error-container`) ||
                                        (formGroup ? formGroup : null) ||
                                        input.closest('.searchable-tags-wrapper') ||
                                        input.parentNode;

                                    if (errorContainer) {
                                        const feedback = document.createElement('div');
                                        feedback.className = 'text-danger small mt-1 py-1';
                                        feedback.textContent = error.errors[key][0];
                                        errorContainer.appendChild(feedback);
                                    }
                                } else {
                                    console.warn(`Could not find input for error key: ${key}`);
                                }
                            });

                            // Scroll to first error
                            const firstError = document.querySelector('.is-invalid');
                            if (firstError) {
                                firstError.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }
                        }

                        // Show error message
                        const errorMessage = error.message || '{{ __('admin.error_occurred') }}';
                        alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>${errorMessage}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    });
            });
        });
    </script>
@endpush
