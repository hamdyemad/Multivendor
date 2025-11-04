@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate'
                    ],
                    [
                        'title' => trans('menu.admin managment.roles managment'),
                        'url' => route('admin.admin-management.roles.index')
                    ],
                    [
                        'title' => isset($role) ? __('roles.edit_role') : __('roles.create_role')
                    ]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 bg-white radius-xl w-100 mb-30">
                    <div class="card-header py-20 px-25 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ isset($role) ? __('roles.edit_role') : __('roles.create_role') }}</h6>
                        <span class="badge badge-primary badge-lg" style="border-radius: 6px; padding: 6px 12px;">
                            <i class="uil uil-shield-check me-1"></i>
                            {{ $groupedPermissions->flatten()->count() }} {{ trans('roles.permissions') }} {{ __('common.available') }}
                        </span>
                    </div>
                    <div class="card-body p-25">
                        <form id="roleForm" 
                              action="{{ isset($role) ? route('admin.admin-management.roles.update', $role->id) : route('admin.admin-management.roles.store') }}" 
                              method="POST">
                            @csrf
                            @if(isset($role))
                                @method('PUT')
                            @endif
                            
                            <!-- Alert Container -->
                            <div id="alertContainer"></div>

                            <div class="row">
                                @foreach($languages as $language)
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="name_{{ $language->id }}" class="il-gray fs-14 fw-500 align-center mb-10" @if($language->code == 'ar') dir="rtl" @endif>
                                                @if($language->code == 'ar')
                                                    اسم الدور ({{ $language->name }}) <span class="text-danger">*</span>
                                                @else
                                                    {{ __('roles.name') }} ({{ $language->name }}) <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.name') is-invalid @enderror" 
                                                   id="name_{{ $language->id }}" 
                                                   name="translations[{{ $language->id }}][name]"  
                                                   value="{{ isset($role) ? ($role->getTranslation('name', $language->code) ?? '') : old('translations.' . $language->id . '.name') }}"
                                                   placeholder="@if($language->code == 'ar')أدخل اسم الدور بالعربية@else{{ __('roles.enter_role_name_in') }} {{ $language->name }}@endif"
                                                   @if($language->code == 'ar') dir="rtl" @endif
                                                   >
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
                                        <h6 class="mb-20 fw-500 color-dark">{{ __('roles.assign_permissions') }}</h6>
                                        
                                        <!-- Select All Checkbox -->
                                        <div class="card border-0 mb-25 shadow-sm">
                                            <div class="card-body p-20 bg-primary-transparent">
                                                <div class="checkbox-theme-default custom-checkbox checkbox-primary">
                                                    <input class="checkbox" 
                                                           type="checkbox" 
                                                           id="select_all_permissions">
                                                    <label for="select_all_permissions" class="fs-15 fw-500 color-primary">
                                                        <span class="checkbox-text">
                                                            <i class="uil uil-check-circle me-2"></i>{{ __('roles.select_all_permissions') }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grouped Permissions -->
                                        <div class="permissions-container">
                                            @foreach($groupedPermissions as $groupName => $permissions)
                                                <div class="card border-0 mb-20 shadow-sm">
                                                    <div class="card-header bg-normal py-15 px-20 border-bottom">
                                                        <div class="checkbox-theme-default custom-checkbox">
                                                            <input class="checkbox group-checkbox" 
                                                                   type="checkbox" 
                                                                   id="group_{{ Str::slug($groupName) }}"
                                                                   data-group="{{ Str::slug($groupName) }}">
                                                            <label for="group_{{ Str::slug($groupName) }}" class="fs-15 fw-500">
                                                                <span class="checkbox-text">{{ $groupName }} <span class="badge badge-primary badge-sm ms-2">{{ $permissions->count() }}</span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-20">
                                                        <div class="row">
                                                            @foreach($permissions as $permission)
                                                                <div class="col-md-4 col-lg-3 mb-15">
                                                                    <div class="checkbox-theme-default custom-checkbox">
                                                                        <input class="checkbox permission-checkbox" 
                                                                               type="checkbox" 
                                                                               name="permissions[]" 
                                                                               value="{{ $permission->id }}" 
                                                                               id="permission_{{ $permission->id }}"
                                                                               data-group="{{ Str::slug($groupName) }}"
                                                                               {{ isset($role) && $role->permessions->contains($permission->id) ? 'checked' : '' }}>
                                                                        <label for="permission_{{ $permission->id }}" class="fs-13">
                                                                            <span class="checkbox-text">{{ $permission->getTranslation('name', app()->getLocale()) ?? $permission->key }}</span>
                                                                        </label>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // AJAX Form Submission
            const roleForm = document.getElementById('roleForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');

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
                window.scrollTo({ top: 0, behavior: 'smooth' });
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
                    if (loadingSubtextEl) loadingSubtextEl.textContent = '{{ trans("loading.please_wait") }}';
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
                            '{{ trans("loading.redirecting") }}'
                        );
                        
                        // Redirect after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = data.redirect || '{{ route("admin.admin-management.roles.index") }}';
                        }, 1500);
                    });
                })
                .catch(error => {
                    // Hide loading overlay
                    LoadingOverlay.hide();
                    
                    // Handle validation errors
                    if (error.errors) {
                        Object.keys(error.errors).forEach(key => {
                            const input = document.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = error.errors[key][0];
                                input.parentNode.appendChild(feedback);
                            }
                        });
                        showAlert('danger', error.message || '{{ __("Please check the form for errors") }}');
                    } else {
                        showAlert('danger', error.message || '{{ __("An error occurred") }}');
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

            // Select All Permissions
            const selectAllCheckbox = document.getElementById('select_all_permissions');
            const allPermissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            const allGroupCheckboxes = document.querySelectorAll('.group-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                allPermissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                allGroupCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            });

            // Group Checkboxes
            allGroupCheckboxes.forEach(groupCheckbox => {
                groupCheckbox.addEventListener('change', function() {
                    const groupName = this.dataset.group;
                    const isChecked = this.checked;
                    const groupPermissions = document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`);
                    
                    groupPermissions.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });

                    updateSelectAllState();
                });
            });

            // Individual Permission Checkboxes
            allPermissionCheckboxes.forEach(permissionCheckbox => {
                permissionCheckbox.addEventListener('change', function() {
                    const groupName = this.dataset.group;
                    updateGroupCheckboxState(groupName);
                    updateSelectAllState();
                });
            });

            // Update group checkbox state based on its permissions
            function updateGroupCheckboxState(groupName) {
                const groupCheckbox = document.querySelector(`.group-checkbox[data-group="${groupName}"]`);
                const groupPermissions = document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`);
                const checkedPermissions = document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]:checked`);

                if (checkedPermissions.length === 0) {
                    groupCheckbox.checked = false;
                    groupCheckbox.indeterminate = false;
                } else if (checkedPermissions.length === groupPermissions.length) {
                    groupCheckbox.checked = true;
                    groupCheckbox.indeterminate = false;
                } else {
                    groupCheckbox.checked = false;
                    groupCheckbox.indeterminate = true;
                }
            }

            // Update select all state
            function updateSelectAllState() {
                const checkedPermissions = document.querySelectorAll('.permission-checkbox:checked');
                
                if (checkedPermissions.length === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedPermissions.length === allPermissionCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }

            // Initialize states on page load
            allGroupCheckboxes.forEach(groupCheckbox => {
                const groupName = groupCheckbox.dataset.group;
                updateGroupCheckboxState(groupName);
            });
            updateSelectAllState();
        });
    </script>
    @endpush
@endsection

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay 
        :loadingText="trans('loading.processing')" 
        :loadingSubtext="trans('loading.please_wait')" 
    />
@endpush
