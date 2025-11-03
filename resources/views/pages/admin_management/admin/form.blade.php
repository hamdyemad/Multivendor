@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('admin.admins_management'), 'url' => route('admin.admin-management.admins.index')],
                    ['title' => isset($admin) ? __('admin.edit_admin') : __('admin.create_admin')]
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
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ __('admin.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="adminForm" method="POST" action="{{ isset($admin) ? route('admin.admin-management.admins.update', $admin->id) : route('admin.admin-management.admins.store') }}">
                            @csrf
                            @if(isset($admin))
                                @method('PUT')
                            @endif
                            
                            <div class="row">
                                <!-- Translation Fields - Names -->
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name_{{ $language->id }}" class="form-label">
                                                {{ __('admin.name') }} ({{ $language->name }}) 
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="name_{{ $language->id }}" 
                                                   name="translations[{{ $language->id }}][name]" 
                                                   value="{{ old('translations.' . $language->id . '.name', isset($admin) ? $admin->translations->where('lang_id', $language->id)->where('lang_key', 'name')->first()->lang_value ?? '' : '') }}"
                                                   @if($language->rtl) dir="rtl" @endif>
                                            @error('translations.' . $language->id . '.name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">
                                            {{ __('admin.email') }} 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control text-lowercase" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', isset($admin) ? $admin->email : '') }}">
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="role_id" class="form-label">
                                            {{ __('admin.role') }} 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="role_id" name="role_id">
                                            <option value="">{{ __('admin.select_role') }}</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" 
                                                    {{ old('role_id', isset($admin) && $admin->roles->contains($role->id) ? $role->id : '') == $role->id ? 'selected' : '' }}>
                                                    {{ $role->getTranslation('name', app()->getLocale()) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">
                                            {{ __('admin.password') }} 
                                            @if(!isset($admin))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="{{ isset($admin) ? __('admin.leave_empty_to_keep_password') : __('admin.enter_password') }}">
                                        <small class="text-muted">
                                            @if(isset($admin))
                                                {{ __('admin.leave_empty_to_keep_password') }}
                                            @else
                                                {{ __('admin.password_min_8') }}
                                            @endif
                                        </small>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            {{ __('admin.confirm_password') }}
                                            @if(!isset($admin))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               placeholder="{{ __('admin.confirm_password') }}">
                                        @error('password_confirmation')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
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
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="active" 
                                                       name="active" 
                                                       value="1"
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
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="block" 
                                                       name="block" 
                                                       value="1"
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
                                        <a href="{{ route('admin.admin-management.admins.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('admin.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i> {{ isset($admin) ? __('admin.update_admin') : __('admin.create_admin') }}
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

    adminForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        // Update loading text
        const loadingText = @json(isset($admin) ? trans('loading.updating') : trans('loading.creating'));
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
        document.querySelectorAll('.text-danger.small').forEach(el => el.remove());

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
                    '{{ trans("loading.redirecting") }}'
                );
                
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("admin.admin-management.admins.index") }}';
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
                    input = document.querySelector(`[name="${key}"]`);
                    
                    // Convert dot notation to bracket notation
                    if (!input && key.includes('.')) {
                        // Split by dots and rebuild with brackets
                        // translations.1.name -> translations[1][name]
                        const parts = key.split('.');
                        const bracketKey = parts[0] + parts.slice(1).map(part => `[${part}]`).join('');
                        
                        console.log(`Trying to find: ${bracketKey} for error key: ${key}`);
                        input = document.querySelector(`[name="${bracketKey}"]`);
                    }
                    
                    if (input) {
                        console.log(`Found input for ${key}:`, input);
                        input.classList.add('is-invalid');
                        
                        // Remove existing error messages for this field
                        const existingErrors = input.parentNode.querySelectorAll('.text-danger.small');
                        existingErrors.forEach(err => err.remove());
                        
                        const feedback = document.createElement('div');
                        feedback.className = 'text-danger small mt-1';
                        feedback.textContent = error.errors[key][0];
                        input.parentNode.appendChild(feedback);
                    } else {
                        console.warn(`Could not find input for error key: ${key}`);
                    }
                });
                
                // Scroll to first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            
            // Show error message
            const errorMessage = error.message || '{{ __("admin.error_occurred") }}';
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
