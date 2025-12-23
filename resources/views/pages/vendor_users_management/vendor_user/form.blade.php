@extends('layout.app')
@section('title', isset($user) ? trans('admin.edit_vendor_user') : trans('admin.create_vendor_user'))

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
                        'title' => __('admin.vendor_users_management'),
                        'url' => route('admin.vendor-users-management.vendor-users.index'),
                    ],
                    ['title' => isset($user) ? __('admin.edit_vendor_user') : __('admin.create_vendor_user')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($user) ? __('admin.edit_vendor_user') : __('admin.create_vendor_user') }}
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

                        <form id="vendorUserForm" method="POST" enctype="multipart/form-data"
                            action="{{ isset($user) ? route('admin.vendor-users-management.vendor-users.update', $user->id) : route('admin.vendor-users-management.vendor-users.store') }}">
                            @csrf
                            @if (isset($user))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Translation Fields - Names -->
                                @php
                                    // Prepare the model data for multilingual-input component
                                    $userModel = null;
                                    if (isset($user)) {
                                        $userModel = new class ($user) {
                                            private $user;

                                            public function __construct($user)
                                            {
                                                $this->user = $user;
                                            }

                                            public function getTranslation($key, $langCode)
                                            {
                                                $language = \App\Models\Language::where('code', $langCode)->first();
                                                if (!$language) {
                                                    return '';
                                                }

                                                return $this->user->translations
                                                    ->where('lang_id', $language->id)
                                                    ->where('lang_key', $key)
                                                    ->first()->lang_value ?? '';
                                            }
                                        };
                                    }
                                @endphp

                                <x-multilingual-input name="name" :label="'Name'" :labelAr="'الأسم'" type="text"
                                    :placeholder="'Name'" :placeholderAr="'الأسم'" :required="true" :languages="$languages"
                                    :model="$userModel" oldPrefix="translations" :cols="6" />

                                <div class="col-md-12 mb-20">
                                    <x-image-upload id="image" name="image" :label="__('admin.vendor_user_image') ?? 'Vendor User Image'" :existingImage="isset($user) ? $user->image : null"
                                        :placeholder="__('admin.click_to_upload_image') ?? 'Click to upload image'" aspectRatio="square" />
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">
                                            {{ __('admin.email') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input autocomplete="new-email" placeholder="{{ __('admin.email') }}"  type="email" class="form-control text-lowercase" id="email"
                                            name="email" value="{{ old('email', isset($user) ? $user->email : '') }}">
                                        <div id="email-error-container">
                                            @error('email')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Vendor -->
                                @if (isAdmin())
                                    <div class="col-md-6">
                                        <x-searchable-tags 
                                            name="vendor_id" 
                                            :label="__('admin.vendor')" 
                                            :options="$vendors->map(fn($vendor) => [
                                                'id' => $vendor->id,
                                                'name' => $vendor->getTranslation('name', app()->getLocale()),
                                            ])->toArray()"
                                            :selected="old('vendor_id', isset($user) ? [$user->vendor_id] : [])" 
                                            :placeholder="__('admin.select_vendor')" 
                                            :required="true" 
                                            :multiple="false" 
                                            id="vendor_id"
                                        />
                                        <div id="vendor_id-error-container">
                                            @error('vendor_id')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $vendorId = auth()->user()->vendor_id;
                                        if (!$vendorId && auth()->user()->vendorByUser) {
                                            $vendorId = auth()->user()->vendorByUser->id;
                                        }
                                    @endphp
                                    <input type="hidden" name="vendor_id" value="{{ $vendorId }}">
                                @endif

                                <!-- Roles -->
                                <div class="col-md-6">
                                    @if(isAdmin() && !isset($user))
                                        <div class="form-group mb-3">
                                            <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                                {{ __('admin.roles') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div id="roles-placeholder" class="alert alert-info mb-0 py-2" style="{{ old('vendor_id') ? 'display:none;' : '' }}">
                                                <i class="uil uil-info-circle me-1"></i>
                                                {{ __('admin.select_vendor_first_to_load_roles') }}
                                            </div>
                                            <div id="roles-container" style="{{ old('vendor_id') ? '' : 'display:none;' }}">
                                                <x-searchable-tags name="role_ids[]" :label="null"
                                                    :selected="old('role_ids', [])" :placeholder="__('admin.select_roles')" :required="true" :multiple="true" />
                                            </div>
                                            <div id="role_ids-error-container">
                                                @error('role_ids')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @else
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
                                                isset($user) ? $user->roles->pluck('id')->toArray() : [],
                                            )" :placeholder="__('admin.select_roles')" :required="true" :multiple="true" />
                                        <div id="role_ids-error-container">
                                            @error('role_ids')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">
                                            {{ __('admin.password') }}
                                            @if (!isset($user))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="{{ isset($user) ? __('admin.leave_empty_to_keep_password') : __('admin.enter_password') }}"
                                                autocomplete="new-password">
                                            <span toggle="#password"
                                                class="uil uil-eye-slash text-lighten fs-15 field-icon toggle-password2"></span>
                                        </div>
                                        <div id="password-error-container">
                                            @error('password')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if (!isset($user))
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
                                            @if (!isset($user))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation"
                                                placeholder="{{ __('admin.confirm_password') }}"
                                                autocomplete="new-password">
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
                                                    {{ old('active', isset($user) ? $user->active : 1) == 1 ? 'checked' : '' }}>
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
                                                    {{ old('block', isset($user) ? $user->block : 0) == 1 ? 'checked' : '' }}>
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
                                        <a href="{{ route('admin.vendor-users-management.vendor-users.index') }}"
                                            class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('admin.back_to_list') }}
                                        </a>
                                        <button type="button" id="submitBtn"
                                            class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i>
                                            {{ isset($user) ? __('admin.update_user') : __('admin.create_vendor_user') }}
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
        // Handle vendor selection change - load roles for selected vendor
        @if(isAdmin())
        // Watch for vendor selection changes via MutationObserver on the hidden input
        var vendorWrapper = document.querySelector('[data-name="vendor_id"]');
        if (vendorWrapper) {
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        // Check if a hidden input was added or removed
                        var vendorInput = vendorWrapper.querySelector('input[type="hidden"][name="vendor_id"]');
                        var vendorId = vendorInput ? vendorInput.value : '';
                        loadRolesForVendor(vendorId);
                    }
                });
            });
            
            observer.observe(vendorWrapper, { childList: true, subtree: true });
        }
        
        function loadRolesForVendor(vendorId) {
            var rolesUrl = '{{ route("admin.vendor-users-management.roles.by-vendor") }}';
            
            // Show/hide placeholder and roles container
            if (!vendorId) {
                $('#roles-placeholder').show();
                $('#roles-container').hide();
                return;
            }
            
            $('#roles-placeholder').hide();
            $('#roles-container').show();
            
            // Get the searchable tags wrapper for roles
            var $rolesWrapper = $('[data-name="role_ids[]"]');
            var componentId = $rolesWrapper.find('.tag-input-container').data('id');
            var $tagsDisplay = $rolesWrapper.find('#' + componentId + '-tags-display');
            var $dropdown = $rolesWrapper.find('#' + componentId + '-dropdown');
            var $input = $rolesWrapper.find('#' + componentId + '-input');
            
            // Clear current selections
            $tagsDisplay.empty();
            
            // Show loading state
            $dropdown.html('<div class="tag-option text-muted p-2">{{ __("common.loading") }}...</div>');
            
            // Fetch roles for selected vendor
            $.ajax({
                url: rolesUrl,
                type: 'GET',
                data: { vendor_id: vendorId },
                success: function(response) {
                    if (response.success && response.roles) {
                        // Rebuild dropdown options
                        $dropdown.empty();
                        var inputName = 'role_ids[]';
                        
                        if (response.roles.length === 0) {
                            $dropdown.html('<div class="tag-option text-muted p-2">{{ __("common.no_roles_found") }}</div>');
                            return;
                        }
                        
                        response.roles.forEach(function(role) {
                            var $option = $('<div>')
                                .addClass('tag-option p-2 cursor-pointer')
                                .attr('data-id', role.id)
                                .attr('data-name', role.name)
                                .text(role.name)
                                .on('click', function(e) {
                                    e.stopPropagation();
                                    window.searchableTags.addTag(componentId, String(role.id), role.name, inputName, true);
                                });
                            $dropdown.append($option);
                        });
                        
                        // Reset input placeholder
                        $input.attr('placeholder', '{{ __("admin.select_roles") }}');
                    }
                },
                error: function() {
                    $dropdown.html('<div class="tag-option text-danger p-2">{{ __("common.error_loading_roles") }}</div>');
                }
            });
        }
        @endif

        $(document).ready(function() {

            var vendorUserForm = document.getElementById('vendorUserForm');
            var submitBtn = document.getElementById('submitBtn');
            var alertContainer = document.getElementById('alertContainer');
            var originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';


            // Function to clear validation errors
            function clearError(target) {
                var $target = $(target);
                $target.removeClass('is-invalid');
                
                var $formGroup = $target.closest('.form-group');
                var $searchableWrapper = $target.closest('.searchable-tags-wrapper');
                
                if ($formGroup.length) {
                    $formGroup.find('.is-invalid').removeClass('is-invalid');
                    $formGroup.find('.text-danger.small').remove();
                    $formGroup.find('.invalid-feedback').remove();
                    $formGroup.find('.helper-text').removeClass('d-none');
                    $formGroup.find('[id$="-error-container"]').empty();
                }
                
                if ($searchableWrapper.length) {
                    $searchableWrapper.find('.is-invalid').removeClass('is-invalid');
                    $searchableWrapper.find('.tag-input-container').removeClass('is-invalid');
                    $searchableWrapper.find('.text-danger.small').remove();
                    $searchableWrapper.find('[id$="-error-container"]').empty();
                }
                
                if ($target.hasClass('select2-hidden-accessible')) {
                    $target.next('.select2-container').removeClass('is-invalid');
                }
            }

            // Event listeners for clearing errors
            $(vendorUserForm).on('input change focusin', 'input, select, textarea', function(e) {
                clearError(e.target);
            });

            $('.select2').on('select2:open select2:select', function() {
                clearError(this);
            });

            $(document).on('click focus', '.tag-input-container, .tag-input', function() {
                clearError(this);
            });

            // Show alert function
            function showAlert(type, message) {
                if (alertContainer) {
                    alertContainer.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show mb-20"><i class="uil uil-' + (type === 'success' ? 'check-circle' : 'exclamation-triangle') + '"></i> ' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
            }

            // Submit button click handler
            $(document).on('click', '#submitBtn', function(e) {
                e.preventDefault();
                
                var isUpdate = {{ isset($user) ? 'true' : 'false' }};
                var loadingText = isUpdate ? '{{ trans("loading.updating") }}' : '{{ trans("loading.creating") }}';
                var successMessage = isUpdate ? '{{ trans("loading.updated_successfully") }}' : '{{ trans("loading.created_successfully") }}';
                var redirectUrl = '{{ route("admin.vendor-users-management.vendor-users.index") }}';

                // Disable button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ trans("common.processing") }}';

                // Show loading overlay
                if (window.LoadingOverlay) {
                    var overlay = document.getElementById('loadingOverlay');
                    if (overlay) {
                        var loadingTextEl = overlay.querySelector('.loading-text');
                        var loadingSubtextEl = overlay.querySelector('.loading-subtext');
                        if (loadingTextEl) loadingTextEl.textContent = loadingText;
                        if (loadingSubtextEl) loadingSubtextEl.textContent = '{{ trans("loading.please_wait") }}';
                    }
                    LoadingOverlay.show();
                }

                // Clear previous errors
                if (alertContainer) alertContainer.innerHTML = '';
                $(vendorUserForm).find('.is-invalid').removeClass('is-invalid');
                $(vendorUserForm).find('.text-danger.small').remove();
                $(vendorUserForm).find('.invalid-feedback').remove();
                $(vendorUserForm).find('[id$="-error-container"]').empty();

                // Submit form via AJAX
                var formData = new FormData(vendorUserForm);

                LoadingOverlay.animateProgressBar(30, 300).then(function() {
                    return $.ajax({
                        url: vendorUserForm.action,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                }).then(function(data) {
                    return LoadingOverlay.animateProgressBar(100, 300).then(function() {
                        LoadingOverlay.showSuccess(successMessage, '{{ trans("loading.redirecting") }}');
                        setTimeout(function() {
                            window.location.href = data.redirect || redirectUrl;
                        }, 1500);
                    });
                }).catch(function(xhr) {
                    LoadingOverlay.hide();
                    
                    var error = xhr.responseJSON || {};
                    
                    if (error.errors) {
                        var processedFields = {};
                        
                        $.each(error.errors, function(key, messages) {
                            var fieldKey = key.indexOf('.') !== -1 ? key.split('.')[0] : key;
                            
                            // For translation fields, use the full key to avoid duplicates
                            var uniqueKey = key;
                            if (processedFields[uniqueKey]) return;
                            
                            var input = null;
                            
                            // Handle translation fields like translations.1.name -> translations[1][name]
                            if (key.indexOf('translations.') === 0) {
                                var parts = key.split('.');
                                if (parts.length === 3) {
                                    var bracketKey = parts[0] + '[' + parts[1] + '][' + parts[2] + ']';
                                    input = document.querySelector('[name="' + bracketKey + '"]');
                                }
                            }
                            
                            // Try other selectors if not found
                            if (!input) {
                                input = document.querySelector('[name="' + key + '"]') ||
                                       document.querySelector('[name="' + key + '[]"]') ||
                                       document.querySelector('[name="' + fieldKey + '[]"]') ||
                                       document.querySelector('.searchable-tags-wrapper[data-name="' + fieldKey + '[]"]');
                            }
                            
                            // Convert dot notation to bracket notation for other fields
                            if (!input && key.indexOf('.') !== -1) {
                                var keyParts = key.split('.');
                                var bracketKey = keyParts[0];
                                for (var i = 1; i < keyParts.length; i++) {
                                    bracketKey += '[' + keyParts[i] + ']';
                                }
                                input = document.querySelector('[name="' + bracketKey + '"]');
                            }
                            
                            if (!input && (key === 'vendor_id' || fieldKey === 'vendor_id')) {
                                input = document.getElementById('vendor_id');
                            }
                            
                            if (input) {
                                processedFields[uniqueKey] = true;
                                
                                if (input.classList.contains('searchable-tags-wrapper')) {
                                    var container = input.querySelector('.tag-input-container');
                                    if (container) container.classList.add('is-invalid');
                                } else if (input.classList.contains('select2-hidden-accessible')) {
                                    var select2Container = input.nextElementSibling;
                                    if (select2Container) select2Container.classList.add('is-invalid');
                                } else {
                                    input.classList.add('is-invalid');
                                }
                                
                                var formGroup = input.closest('.form-group') || input.closest('.searchable-tags-wrapper') || input.parentNode;
                                var safeKey = key.replace(/\./g, '_').replace(/[\[\]]/g, '_');
                                var errorContainer = document.getElementById(safeKey + '-error-container') || formGroup;
                                
                                if (errorContainer) {
                                    // Check if error message already exists
                                    var existingError = errorContainer.querySelector('.text-danger.small');
                                    if (!existingError) {
                                        var feedback = document.createElement('div');
                                        feedback.className = 'text-danger small mt-1';
                                        feedback.textContent = messages[0];
                                        errorContainer.appendChild(feedback);
                                    }
                                }
                            }
                        });
                        
                        var firstError = document.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        
                        showAlert('danger', '{{ __("admin.please_check_form_errors") }}');
                    } else {
                        showAlert('danger', error.message || '{{ __("admin.error_occurred") }}');
                    }
                    
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                });
            });
        });
    </script>
@endpush
