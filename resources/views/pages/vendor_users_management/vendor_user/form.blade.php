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
                        'url' => route('admin.admin-management.vendor-users.index'),
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

                        <form id="vendorUserForm" method="POST"
                            action="{{ isset($user) ? route('admin.admin-management.vendor-users.update', $user->id) : route('admin.admin-management.vendor-users.store') }}">
                            @csrf
                            @if (isset($user))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Translation Fields - Names -->
                                @foreach ($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name_{{ $language->id }}" class="form-label">
                                                {{ __('admin.name') }} ({{ $language->name }})
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="name_{{ $language->id }}"
                                                name="translations[{{ $language->id }}][name]"
                                                value="{{ old('translations.' . $language->id . '.name', isset($user) ? $user->translations->where('lang_id', $language->id)->where('lang_key', 'name')->first()->lang_value ?? '' : '') }}"
                                                @if ($language->rtl) dir="rtl" @endif>
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
                                        <input type="email" class="form-control text-lowercase" id="email"
                                            name="email" value="{{ old('email', isset($user) ? $user->email : '') }}">
                                    </div>
                                </div>

                                <!-- Vendor -->
                                @if (isAdmin())
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="vendor_id" class="form-label">
                                                {{ __('admin.vendor') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control select2" id="vendor_id" name="vendor_id">
                                                <option value="">{{ __('admin.select_vendor') }}</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}"
                                                        {{ old('vendor_id', isset($user) ? $user->vendor_id : '') == $vendor->id ? 'selected' : '' }}>
                                                        {{ $vendor->getTranslation('name', app()->getLocale()) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="vendor_id"
                                        value="{{ auth()->user()->vendor_id ?? auth()->id() }}">
                                @endif

                                <!-- Roles -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="role_ids" class="form-label">
                                            {{ __('admin.roles') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control select2" id="role_ids" name="role_ids[]"
                                            multiple="multiple">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ (is_array(old('role_ids')) && in_array($role->id, old('role_ids'))) || (isset($user) && $user->roles->contains($role->id)) ? 'selected' : '' }}>
                                                    {{ $role->getTranslation('name', app()->getLocale()) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="{{ isset($user) ? __('admin.leave_empty_to_keep_password') : __('admin.enter_password') }}">
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
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="{{ __('admin.confirm_password') }}">
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
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex align-items-center justify-content-end">
                                        <a href="{{ route('admin.admin-management.vendor-users.index') }}"
                                            class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('admin.back_to_list') }}
                                        </a>
                                        <button type="submit"
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            const vendorUserForm = document.getElementById('vendorUserForm');
            const submitBtn = vendorUserForm.querySelector('button[type="submit"]');
            const alertContainer = document.getElementById('alertContainer');
            let originalBtnHtml = '';

            vendorUserForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Disable submit button and show loading
                submitBtn.disabled = true;
                originalBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('common.processing') ?? 'Processing...' }}';

                // Show loading overlay
                LoadingOverlay.show();

                // Clear previous alerts
                alertContainer.innerHTML = '';

                // Remove previous validation errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.text-danger.small').forEach(el => el.remove());

                // Start progress bar
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                        const formData = new FormData(vendorUserForm);

                        return fetch(vendorUserForm.action, {
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
                            const successMessage = @json(isset($user) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                            LoadingOverlay.showSuccess(
                                successMessage,
                                '{{ trans('loading.redirecting') }}'
                            );

                            setTimeout(() => {
                                window.location.href = data.redirect ||
                                    '{{ route('admin.admin-management.vendor-users.index') }}';
                            }, 1500);
                        });
                    })
                    .catch(error => {
                        LoadingOverlay.hide();

                        // Handle validation errors
                        if (error.errors) {
                            Object.keys(error.errors).forEach(key => {
                                let input = null;

                                // Try direct match first
                                input = document.querySelector(`[name="${key}"]`);

                                // Convert dot notation to bracket notation
                                if (!input && key.includes('.')) {
                                    const parts = key.split('.');
                                    const bracketKey = parts[0] + parts.slice(1).map(part =>
                                        `[${part}]`).join('');
                                    input = document.querySelector(`[name="${bracketKey}"]`);

                                    // Special case for role_ids.*
                                    if (!input && key.startsWith('role_ids.')) {
                                        input = document.querySelector('#role_ids');
                                    }
                                }

                                if (input) {
                                    input.classList.add('is-invalid');
                                    const feedback = document.createElement('div');
                                    feedback.className = 'text-danger small mt-1';
                                    feedback.textContent = error.errors[key][0];

                                    if (input.classList.contains('select2-hidden-accessible')) {
                                        input.nextElementSibling.after(feedback);
                                    } else {
                                        input.parentNode.appendChild(feedback);
                                    }
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
