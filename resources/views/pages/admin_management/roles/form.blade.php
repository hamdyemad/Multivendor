@extends('layout.app')

@section('title', $title)
@section('content')
    <style>
        .permissions-container .card {
            transition: all 0.3s ease;
            border: 1px solid #f1f2f6 !important;
            border-radius: 12px !important;
        }

        .permissions-container .card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .card-holder .card-header {
            background: linear-gradient(90deg, #0052cc 0%, #cc0052 100%);
            border-bottom: 0;
            padding: 15px 20px !important;
        }

        /* Custom White Checkbox - Isolated Style */
        .custom-white-checkbox {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            margin-bottom: 0 !important;
        }

        .custom-white-checkbox input {
            display: none;
            /* Hide default checkbox */
        }

        .custom-white-checkbox .checkmark-box {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            margin-right: 8px;
            /* RTL aware in HTML structure */
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            background-color: transparent;
        }

        /* Checked & Indeterminate State */
        .custom-white-checkbox input:checked+.checkmark-box,
        .custom-white-checkbox input:indeterminate+.checkmark-box {
            background-color: #fff;
            border-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Checkmark Icon */
        .custom-white-checkbox .checkmark-box::after {
            content: '\2713';
            /* Check mark */
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #cc0052;
            /* Red Tick */
            display: none;
        }

        .custom-white-checkbox input:checked+.checkmark-box::after {
            display: block;
            content: '\2713';
        }

        .custom-white-checkbox input:indeterminate+.checkmark-box::after {
            display: block;
            content: '\2212';
            /* Dash */
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
        }

        /* Label Text */
        .custom-white-checkbox .label-text {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        /* RTL Support */
        [dir="rtl"] .custom-white-checkbox .checkmark-box {
            margin-right: 0;
            margin-left: 8px;
        }

        .submodule-section:last-child {
            border-bottom: 0 !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        .last-child-no-border:last-child {
            border-bottom: 0 !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .badge {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
        }

        .permission-item {
            padding: 4px 0;
        }
    </style>
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
                        'title' => trans('menu.admin managment.roles managment'),
                        'url' => route('admin.admin-management.roles.index'),
                    ],
                    [
                        'title' => isset($role) ? __('roles.edit_role') : __('roles.create_role'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 bg-white radius-xl w-100 mb-30">
                    <div class="card-header py-20 px-25 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ isset($role) ? __('roles.edit_role') : __('roles.create_role') }}</h6>
                    </div>
                    <div class="card-body p-25">
                        <form id="roleForm"
                            action="{{ isset($role) ? route('admin.admin-management.roles.update', $role->id) : route('admin.admin-management.roles.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($role))
                                @method('PUT')
                            @endif
                            <input type="hidden" name="type"
                                value="{{ $type ?? (isset($role) ? $role->type : request('type')) }}">

                            <!-- Alert Container -->
                            <div id="alertContainer"></div>

                            <div class="row">
                                @foreach ($languages as $language)
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="name_{{ $language->id }}"
                                                class="il-gray fs-14 fw-500 align-center mb-10"
                                                @if ($language->code == 'ar') dir="rtl" @endif>
                                                @if ($language->code == 'ar')
                                                    اسم الدور ({{ $language->name }}) <span class="text-danger">*</span>
                                                @else
                                                    {{ __('roles.name') }} ({{ $language->name }}) <span
                                                        class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('name_' . $language->code) is-invalid @enderror"
                                                id="name_{{ $language->id }}" name="name_{{ $language->code }}"
                                                value="{{ isset($role) ? $role->getTranslation('name', $language->code) ?? '' : old('name_' . $language->code) }}"
                                                placeholder="@if ($language->code == 'ar') أدخل اسم الدور بالعربية@else{{ __('roles.enter_role_name_in') }} {{ $language->name }} @endif"
                                                @if ($language->code == 'ar') dir="rtl" @endif>
                                            @error('translations.' . $language->id . '.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Permissions Section -->
                            <div class="row">
                                <div class="col-12 mb-25">
                                    <div class="dm-tag-wrap">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-20 border-bottom pb-10">
                                            <h6 class="fw-500 color-dark">{{ __('roles.select_permissions') }}</h6>
                                            <div class="button-group d-flex" style="gap: 10px;">
                                                <button type="button"
                                                    class="btn btn-primary btn-sm d-flex align-items-center"
                                                    id="select_all_btn">
                                                    <i class="uil uil-check-square me-1"></i>
                                                    <span>{{ __('roles.select_all') }}</span>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm d-flex align-items-center"
                                                    id="deselect_all_btn">
                                                    <i class="uil uil-minus-square me-1"></i>
                                                    <span>{{ __('roles.deselect_all') }}</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Grouped Permissions -->
                                        <div class="row permissions-container">
                                            @foreach ($groupedPermissions as $moduleKey => $moduleData)
                                                <div class="col-lg-4 col-md-6 mb-25">
                                                    <div class="card border-0 shadow-sm overflow-hidden h-100 card-holder"
                                                        style="border-radius: 10px;">
                                                        <div
                                                            class="card-header py-12 px-20 border-bottom d-flex justify-content-between align-items-center">
                                                            <h6 class="fs-15 fw-600 text-white mb-0">
                                                                <i
                                                                    class="uil {{ $moduleData['icon'] ?? 'uil-setting' }} me-2"></i>
                                                                {{ $moduleData['name'] }}
                                                            </h6>

                                                            <label class="custom-white-checkbox">
                                                                <input type="checkbox" class="module-checkbox"
                                                                    id="module_{{ Str::slug($moduleKey) }}"
                                                                    data-module="{{ Str::slug($moduleKey) }}">
                                                                <span class="checkmark-box"></span>
                                                                <span class="label-text">{{ __('roles.all') }}</span>
                                                            </label>
                                                        </div>
                                                        <div class="card-body p-20 bg-white">
                                                            @foreach ($moduleData['sub_modules'] as $subModuleName => $actions)
                                                                <div
                                                                    class="submodule-section mb-20 border-bottom pb-10 last-child-no-border">
                                                                    <div class="d-flex flex-column" style="gap: 8px;">
                                                                        @foreach ($actions as $action => $permissionData)
                                                                            <div class="permission-item">
                                                                                <div
                                                                                    class="checkbox-theme-default custom-checkbox d-flex align-items-center">
                                                                                    <input
                                                                                        class="checkbox permission-checkbox"
                                                                                        type="checkbox" name="permissions[]"
                                                                                        value="{{ $permissionData['permission']->id }}"
                                                                                        id="permission_{{ $permissionData['permission']->id }}"
                                                                                        data-module="{{ Str::slug($moduleKey) }}"
                                                                                        {{ isset($role) && $role->permessions->contains($permissionData['permission']->id) ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="permission_{{ $permissionData['permission']->id }}"
                                                                                        class="fs-13 d-flex align-items-center mb-0 cursor-pointer">
                                                                                        @php
                                                                                            $badgeClass =
                                                                                                $permissionData[
                                                                                                    'color'
                                                                                                ] ?? 'bg-secondary';
                                                                                            $permLabel =
                                                                                                $permissionData['name'];
                                                                                        @endphp
                                                                                        <span
                                                                                            class="badge {{ $badgeClass }} text-white me-2 px-2 py-1"
                                                                                            style="min-width: 50px; border-radius: 4px; font-size: 10px;">{{ $permLabel }}</span>
                                                                                        <span
                                                                                            class="checkbox-text text-dark fw-500">
                                                                                            {{ $permLabel }}
                                                                                            @if (!str_contains(strtolower($permLabel), strtolower($subModuleName)))
                                                                                                {{ $subModuleName }}
                                                                                            @endif
                                                                                        </span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="button-group d-flex pt-25 justify-content-end" style="gap: 10px;">
                                        <a href="{{ route('admin.admin-management.roles.index') }}"
                                            class="btn btn-light btn-default btn-squared fw-400 text-capitalize"
                                            style="white-space: nowrap;">
                                            <i class="uil uil-angle-left"></i> {{ __('common.cancel') }}
                                        </a>
                                        <button type="submit" id="submitBtn"
                                            class="btn btn-primary btn-default btn-squared text-capitalize"
                                            style="white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="uil uil-check"></i>
                                            <span>{{ isset($role) ? __('roles.update_role') : __('roles.create_role') }}</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                                aria-hidden="true"></span>
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // AJAX Form Submission
                const roleForm = document.getElementById('roleForm');
                const submitBtn = document.getElementById('submitBtn');
                const alertContainer = document.getElementById('alertContainer');

                // Function to clear validation errors on input interaction
                function clearError(e) {
                    if (e.target.classList.contains('is-invalid')) {
                        e.target.classList.remove('is-invalid');
                        const feedback = e.target.parentNode.querySelector('.invalid-feedback');
                        if (feedback) feedback.remove();
                    }
                }

                roleForm.addEventListener('input', clearError);
                roleForm.addEventListener('change', clearError);
                roleForm.addEventListener('focusin', clearError);

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

                roleForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Disable submit button and show loading
                    submitBtn.disabled = true;
                    const btnIcon = submitBtn.querySelector('i');
                    const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                    if (btnIcon) btnIcon.classList.add('d-none');
                    if (btnText) btnText.classList.add('d-none');
                    submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                    // Update loading text and show overlay
                    const loadingText = @json(isset($role) ? trans('loading.updating') : trans('loading.creating'));
                    const overlay = document.getElementById('loadingOverlay');
                    if (overlay) {
                        const loadingTextEl = overlay.querySelector('.loading-text');
                        const loadingSubtextEl = overlay.querySelector('.loading-subtext');
                        if (loadingTextEl) loadingTextEl.textContent = loadingText;
                        if (loadingSubtextEl) loadingSubtextEl.textContent =
                            '{{ trans('loading.please_wait') }}';
                    }

                    // Show loading overlay
                    if (window.LoadingOverlay) {
                        LoadingOverlay.show();
                    }

                    // Clear previous alerts
                    alertContainer.innerHTML = '';

                    // Remove previous validation errors
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                    // Start progress bar animation
                    LoadingOverlay.animateProgressBar(30, 300).then(() => {
                            // Prepare form data
                            const formData = new FormData(roleForm);

                            // Send AJAX request
                            return fetch(roleForm.action, {
                                method: roleForm.method,
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
                                // Show success animation
                                const successMessage = @json(isset($role) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                                LoadingOverlay.showSuccess(
                                    successMessage,
                                    '{{ trans('loading.redirecting') }}'
                                );

                                // Redirect after 1.5 seconds
                                setTimeout(() => {
                                    window.location.href = data.redirect ||
                                        '{{ route('admin.admin-management.roles.index') }}';
                                }, 1500);
                            });
                        })
                        .catch(error => {
                            // Hide loading overlay
                            LoadingOverlay.hide();

                            // Handle validation errors
                            // Handle validation errors
                            if (error.errors) {
                                Object.keys(error.errors).forEach(key => {
                                    // Handle array notation conversion (e.g., translations.1.name -> translations[1][name])
                                    let inputName = key;
                                    if (key.includes('.')) {
                                        const parts = key.split('.');
                                        inputName = parts.shift() + parts.map(p => `[${p}]`).join(
                                            '');
                                    }

                                    // Try to find input with converted name, or original name
                                    let input = document.querySelector(`[name="${inputName}"]`);
                                    if (!input) {
                                        input = document.querySelector(`[name="${key}"]`);
                                    }

                                    if (input) {
                                        input.classList.add('is-invalid');
                                        const feedback = document.createElement('div');
                                        feedback.className =
                                            'invalid-feedback d-block'; // Force display
                                        feedback.textContent = error.errors[key][0];
                                        input.parentNode.appendChild(feedback);
                                    }

                                    // Special handling for permissions array
                                    if (key === 'permissions') {
                                        showAlert('warning', error.errors[key][0]);
                                    }
                                });

                                if (!document.querySelector('.is-invalid') && !error.errors.permissions) {
                                    showAlert('danger', error.message ||
                                        '{{ __('Please check the form for errors') }}');
                                } else {
                                    showAlert('danger', '{{ __('Please check the form for errors') }}');
                                }
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

                // Select All / Deselect All Buttons
                const selectAllBtn = document.getElementById('select_all_btn');
                const deselectAllBtn = document.getElementById('deselect_all_btn');
                const allPermissionCheckboxes = document.querySelectorAll('.permission-checkbox');
                const allModuleCheckboxes = document.querySelectorAll('.module-checkbox');

                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function() {
                        allPermissionCheckboxes.forEach(checkbox => checkbox.checked = true);
                        allModuleCheckboxes.forEach(checkbox => {
                            checkbox.checked = true;
                            checkbox.indeterminate = false;
                        });
                    });
                }

                if (deselectAllBtn) {
                    deselectAllBtn.addEventListener('click', function() {
                        allPermissionCheckboxes.forEach(checkbox => checkbox.checked = false);
                        allModuleCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                            checkbox.indeterminate = false;
                        });
                    });
                }

                // Module Checkboxes
                allModuleCheckboxes.forEach(moduleCheckbox => {
                    moduleCheckbox.addEventListener('change', function() {
                        const moduleName = this.dataset.module;
                        const isChecked = this.checked;
                        const modulePermissions = document.querySelectorAll(
                            `.permission-checkbox[data-module="${moduleName}"]`);

                        modulePermissions.forEach(checkbox => {
                            checkbox.checked = isChecked;
                        });
                    });
                });

                // Individual Permission Checkboxes
                allPermissionCheckboxes.forEach(permissionCheckbox => {
                    permissionCheckbox.addEventListener('change', function() {
                        const moduleName = this.dataset.module;
                        updateModuleCheckboxState(moduleName);
                    });
                });

                // Update module checkbox state based on its permissions
                function updateModuleCheckboxState(moduleName) {
                    const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${moduleName}"]`);
                    if (!moduleCheckbox) return;

                    const modulePermissions = document.querySelectorAll(
                        `.permission-checkbox[data-module="${moduleName}"]`);
                    const checkedPermissions = document.querySelectorAll(
                        `.permission-checkbox[data-module="${moduleName}"]:checked`);

                    if (checkedPermissions.length === 0) {
                        moduleCheckbox.checked = false;
                        moduleCheckbox.indeterminate = false;
                    } else if (checkedPermissions.length === modulePermissions.length) {
                        moduleCheckbox.checked = true;
                        moduleCheckbox.indeterminate = false;
                    } else {
                        moduleCheckbox.checked = false;
                        moduleCheckbox.indeterminate = true;
                    }
                }

                // Initialize states on page load
                allModuleCheckboxes.forEach(moduleCheckbox => {
                    const moduleName = moduleCheckbox.dataset.module;
                    updateModuleCheckboxState(moduleName);
                });
            });
        </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay :loadingText="trans('loading.processing')" :loadingSubtext="trans('loading.please_wait')" />
@endpush
