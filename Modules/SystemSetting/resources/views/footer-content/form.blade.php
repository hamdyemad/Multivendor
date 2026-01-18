@extends('layout.app')

@section('title', __('systemsetting::footer_content.footer_content_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                [
                    'title' => __('common.dashboard'),
                    'url' => route('admin.dashboard'),
                    'icon' => 'uil uil-estate',
                ],
                ['title' => __('systemsetting::footer_content.footer_content_management')],
            ]" />
        </div>
    </div>

    <form id="footerContentForm" action="{{ route('admin.system-settings.footer-content.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::footer_content.footer_content_management') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="card card-holder">
                            <div class="card-header">
                                <h6 class="m-0 text-white">
                                    <i class="uil uil-info-circle me-2"></i>{{ __('systemsetting::footer_content.basic_information') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Title (Multilingual) --}}
                                    <div class="col-md-12 mb-3">
                                        <x-multilingual-input
                                            name="title"
                                            oldPrefix="translations"
                                            label="Title"
                                            :labelAr="'العنوان'"
                                            :placeholder="'Title'"
                                            :placeholderAr="'العنوان'"
                                            type="text"
                                            :languages="$languages"
                                            :model="$footerContent ?? null"
                                        />
                                    </div>

                                    {{-- Description (Multilingual) --}}
                                    <div class="col-md-12 mb-3">
                                        <x-multilingual-input
                                            name="description"
                                            oldPrefix="translations"
                                            label="Description"
                                            :labelAr="'الوصف'"
                                            :placeholder="'Description'"
                                            :placeholderAr="'الوصف'"
                                            type="textarea"
                                            rows="4"
                                            :languages="$languages"
                                            :model="$footerContent ?? null"
                                        />
                                    </div>

                                    {{-- Google Play Link --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="google_play_link" class="form-label">
                                                {{ __('systemsetting::footer_content.google_play_link') }}
                                            </label>
                                            <input type="url"
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                   id="google_play_link"
                                                   name="google_play_link"
                                                   value="{{ old('google_play_link', $footerContent->google_play_link ?? '') }}"
                                                   placeholder="{{ __('systemsetting::footer_content.google_play_link_placeholder') }}">
                                        </div>
                                    </div>

                                    {{-- Apple Store Link --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="apple_store_link" class="form-label">
                                                {{ __('systemsetting::footer_content.apple_store_link') }}
                                            </label>
                                            <input type="url"
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                   id="apple_store_link"
                                                   name="apple_store_link"
                                                   value="{{ old('apple_store_link', $footerContent->apple_store_link ?? '') }}"
                                                   placeholder="{{ __('systemsetting::footer_content.apple_store_link_placeholder') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-default btn-squared">
                                        <i class="uil uil-save me-2"></i>{{ __('systemsetting::footer_content.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const footerContentForm = document.getElementById('footerContentForm');

    if (footerContentForm) {
        footerContentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Sync all CKEditor instances to their textareas
            for (let instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);

            // Disable submit button and show loading
            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> {{ __("common.processing") ?? "Processing..." }}';

            // Show loading overlay
            if (window.LoadingOverlay) {
                window.LoadingOverlay.show();
            }

            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.showSuccess(
                            data.message || '{{ __("systemsetting::footer_content.saved_successfully") }}',
                            '{{ __("common.redirecting") ?? "Redirecting..." }}'
                        );
                    }

                    // Redirect immediately without delay
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    // Hide loading overlay
                    if (window.LoadingOverlay) {
                        window.LoadingOverlay.hide();
                    }

                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;

                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || '{{ __("common.error_occurred") }}');
                    } else {
                        alert(data.message || '{{ __("common.error_occurred") }}');
                    }

                    // Display validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            // Try to find input by exact name match
                            let input = document.querySelector(`[name="${key}"]`);

                            // If not found, convert dot notation to bracket notation
                            if (!input) {
                                const parts = key.split('.');
                                let bracketKey = parts[0];
                                for (let i = 1; i < parts.length; i++) {
                                    bracketKey += '[' + parts[i] + ']';
                                }
                                input = document.querySelector(`[name="${bracketKey}"]`);
                            }

                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = data.errors[key][0];
                                input.parentNode.appendChild(errorDiv);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Hide loading overlay
                if (window.LoadingOverlay) {
                    window.LoadingOverlay.hide();
                }

                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                // Show error message
                if (typeof toastr !== 'undefined') {
                    toastr.error('{{ __("common.error_occurred") }}');
                } else {
                    alert('{{ __("common.error_occurred") }}');
                }
            });
        });

        // Clear errors on input
        document.querySelectorAll('input, select, textarea').forEach(input => {
            const clearError = () => {
                input.classList.remove('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            };

            input.addEventListener('input', clearError);
            input.addEventListener('change', clearError);
        });
    }
});
</script>
@endpush
