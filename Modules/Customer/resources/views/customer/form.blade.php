@extends('layout.app')

@section('title', isset($customer) ? __('customer::customer.edit_customer') : __('customer::customer.create_customer'))

@push('styles')
<!-- Select2 CSS loaded via Vite -->
@endpush

@section('content')
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('customer::customer.customers_management'), 'url' => route('admin.customers.index')],
                    ['title' => isset($customer) ? __('customer::customer.edit_customer') : __('customer::customer.add_customer')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0 fw-bold">{{ isset($customer) ? __('customer::customer.edit_customer') : __('customer::customer.add_customer') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ __('customer::customer.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="customerForm" method="POST" action="{{ isset($customer) ? route('admin.customers.update', $customer->id) : route('admin.customers.store') }}">
                            @csrf
                            @if(isset($customer))
                                @method('PUT')
                            @endif

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i class="uil uil-user me-1"></i>{{ __('customer::customer.basic_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- First Name --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="first_name" class="form-label">
                                                    {{ __('customer::customer.first_name') }} <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="first_name"
                                                    id="first_name"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="{{ isset($customer) ? $customer->first_name : old('first_name') }}"
                                                    placeholder="{{ __('customer::customer.first_name') }}"

                                                >
                                                @error('first_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Last Name --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="last_name" class="form-label">
                                                    {{ __('customer::customer.last_name') }} <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="last_name"
                                                    id="last_name"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="{{ isset($customer) ? $customer->last_name : old('last_name') }}"
                                                    placeholder="{{ __('customer::customer.last_name') }}"

                                                >
                                                @error('last_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Phone --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="phone" class="form-label">
                                                    {{ __('customer::customer.phone') }}
                                                </label>
                                                <input
                                                    type="text"
                                                    name="phone"
                                                    id="phone"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="{{ isset($customer) ? $customer->phone : old('phone') }}"
                                                    placeholder="{{ __('customer::customer.phone') }}"
                                                >
                                                @error('phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="status" class="form-label">
                                                    {{ __('customer::customer.status') }}
                                                </label>
                                                <select name="status" id="status" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                                    <option value="1" {{ (isset($customer) && $customer->status) || old('status') == '1' ? 'selected' : '' }}>
                                                        {{ __('customer::customer.active') }}
                                                    </option>
                                                    <option value="0" {{ (isset($customer) && !$customer->status) || old('status') == '0' ? 'selected' : '' }}>
                                                        {{ __('customer::customer.inactive') }}
                                                    </option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Account Information --}}
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i class="uil uil-lock me-1"></i>{{ __('customer::customer.account_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Email --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="email" class="form-label">
                                                    {{ __('customer::customer.email') }} <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="email"
                                                    name="email"
                                                    id="email"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="{{ isset($customer) ? $customer->email : old('email') }}"
                                                    placeholder="{{ __('customer::customer.email') }}"

                                                >
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Password --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="password" class="form-label">
                                                    {{ __('customer::customer.password') }}
                                                    @if(!isset($customer)) <span class="text-danger">*</span> @endif
                                                </label>
                                                <input
                                                    type="password"
                                                    name="password"
                                                    id="password"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ __('customer::customer.password') }}"
                                                >
                                                @if(isset($customer))
                                                    <small class="text-muted">{{ __('Leave empty to keep current password') }}</small>
                                                @endif
                                                @error('password')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password Confirmation --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="password_confirmation" class="form-label">
                                                    {{ __('customer::customer.password_confirmation') }}
                                                    @if(!isset($customer)) <span class="text-danger">*</span> @endif
                                                </label>
                                                <input
                                                    type="password"
                                                    name="password_confirmation"
                                                    id="password_confirmation"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ __('customer::customer.password_confirmation') }}"
                                                >
                                                @error('password_confirmation')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                {{-- Submit Buttons --}}
                                <div class="col-md-12">
                                    <div class="form-group d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.customers.index') }}" class="btn btn-light btn-default btn-squared">
                                            {{ __('customer::customer.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared">
                                            <i class="uil uil-check me-1"></i>
                                            {{ isset($customer) ? __('customer::customer.update') : __('customer::customer.save') }}
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
    // Initialize Select2
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // AJAX Form Submission
    const customerForm = document.getElementById('customerForm');
    const submitBtn = customerForm.querySelector('button[type="submit"]');
    const alertContainer = document.getElementById('alertContainer');
    let originalBtnHtml = '';

    customerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        // Update loading text dynamically
        const loadingText = @json(isset($customer) ? trans('loading.updating') : trans('loading.creating'));
        const loadingSubtext = '{{ trans("loading.please_wait") }}';
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-text').textContent = loadingText;
            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
        }

        // Show loading overlay
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.show();
        }

        // Clear previous alerts
        alertContainer.innerHTML = '';

        // Remove previous validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Start progress bar animation
        const progressPromise = typeof LoadingOverlay !== 'undefined' ?
            LoadingOverlay.animateProgressBar(30, 300) :
            Promise.resolve();

        progressPromise.then(() => {
            // Prepare form data
            const formData = new FormData(customerForm);

            // Send AJAX request
            return fetch(customerForm.action, {
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
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.animateProgressBar(60, 200);
            }

            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            // Progress to 90%
            const progressPromise = typeof LoadingOverlay !== 'undefined' ?
                LoadingOverlay.animateProgressBar(90, 200) :
                Promise.resolve();
            return progressPromise.then(() => data);
        })
        .then(data => {
            // Complete progress bar
            const completePromise = typeof LoadingOverlay !== 'undefined' ?
                LoadingOverlay.animateProgressBar(100, 200) :
                Promise.resolve();

            return completePromise.then(() => {
                // Show success animation with dynamic message
                const successMessage = @json(isset($customer) ? trans('customer::customer.customer_updated') : trans('customer::customer.customer_saved'));

                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.showSuccess(
                        successMessage,
                        '{{ trans("loading.redirecting") }}'
                    );
                }

                // Show success alert
                showAlert('success', data.message || successMessage);

                // Redirect after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("admin.customers.index") }}';
                }, 1500);
            });
        })
        .catch(error => {
            // Hide loading overlay and reset progress bar
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }

            // Handle validation errors
            if (error.errors) {
                console.log('Validation errors received:', error.errors);
                Object.keys(error.errors).forEach(key => {
                    console.log('Processing error key:', key);

                    const input = document.querySelector(`[name="${key}"]`);

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

    // Remove validation classes on input
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.remove();
            }
        });
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
