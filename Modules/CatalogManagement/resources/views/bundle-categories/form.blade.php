@extends('layout.app')
@section('title', (isset($bundleCategory)) ? trans('catalogmanagement::bundle_category.edit_bundle_category') : trans('catalogmanagement::bundle_category.create_bundle_category'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::bundle_category.bundle_categories_management'), 'url' => route('admin.bundle-categories.index')],
                    ['title' => isset($bundleCategory) ? trans('catalogmanagement::bundle_category.edit_bundle_category') : trans('catalogmanagement::bundle_category.create_bundle_category')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($bundleCategory) ? trans('catalogmanagement::bundle_category.edit_bundle_category') : trans('catalogmanagement::bundle_category.create_bundle_category') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <form id="bundleCategoryForm"
                              action="{{ isset($bundleCategory) ? route('admin.bundle-categories.update', $bundleCategory->id) : route('admin.bundle-categories.store') }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($bundleCategory))
                                @method('PUT')
                            @endif

                            {{-- Bundle Category Name Fields --}}
                            <x-multilingual-input
                                name="name"
                                label="Name"
                                labelAr="الاسم"
                                placeholder="Enter bundle category name"
                                placeholderAr="أدخل اسم فئة الحزمة"
                                :required="true"
                                :languages="$languages"
                                :model="$bundleCategory ?? null"
                            />
                            <div class="row">
                                {{-- Bundle Category Image --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <x-image-upload
                                            id="bundle_category_image"
                                            name="image"
                                            :placeholder="trans('catalogmanagement::bundle_category.click_to_upload_image')"
                                            :recommendedSize="trans('catalogmanagement::bundle_category.recommended_size')"
                                            :existingImage="isset($bundleCategory) && $bundleCategory->image ? $bundleCategory->image : null"
                                            aspectRatio="square"
                                            required=true
                                        />
                                        @error('image')
                                            <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Activation Switcher --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('catalogmanagement::bundle_category.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="active"
                                                       name="active"
                                                       value="1"
                                                       {{ old('active', $bundleCategory->active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Information Section --}}
                            <div class="row mt-30">
                                <div class="col-12">
                                    <h6 class="mb-20 fw-500">{{ trans('catalogmanagement::bundle_category.seo_information') }}</h6>
                                </div>
                            </div>
                            {{-- SEO Title Fields --}}
                            <x-multilingual-input
                                name="seo_title"
                                label="SEO Title"
                                labelAr="عنوان SEO"
                                placeholder="Enter SEO title"
                                placeholderAr="أدخل عنوان SEO"
                                :languages="$languages"
                                :model="$bundleCategory ?? null"
                            />

                            {{-- SEO Description Fields --}}
                            <x-multilingual-input
                                name="seo_description"
                                label="SEO Description"
                                labelAr="وصف SEO"
                                placeholder="Enter SEO description"
                                placeholderAr="أدخل وصف SEO"
                                type="textarea"
                                rows="3"
                                :languages="$languages"
                                :model="$bundleCategory ?? null"
                            />

                            {{-- SEO Keywords Fields --}}
                            <x-multilingual-input
                                name="seo_keywords"
                                label="SEO Keywords"
                                labelAr="كلمات مفتاحية SEO"
                                placeholder="Type a keyword and press Enter..."
                                placeholderAr="اكتب كلمة مفتاحية واضغط انتر"
                                :tags="true"
                                :languages="$languages"
                                :model="$bundleCategory ?? null"
                            />

                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.bundle-categories.index') }}"
                                   class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ trans('catalogmanagement::bundle_category.cancel') }}
                                </a>
                                <button id="submitBtn"
                                        class="btn btn-primary btn-default btn-squared text-capitalize"
                                        style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span>{{ isset($bundleCategory) ? trans('catalogmanagement::bundle_category.update_bundle_category') : trans('catalogmanagement::bundle_category.add_bundle_category') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
        document.addEventListener('DOMContentLoaded', function() {
            // AJAX Form Submission
            const bundleCategoryForm = document.getElementById('bundleCategoryForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');

            // Clear validation errors on input
            bundleCategoryForm.querySelectorAll('input, textarea').forEach(input => {
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

            bundleCategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Disable submit button and show loading
                submitBtn.disabled = true;
                const btnIcon = submitBtn.querySelector('i');
                const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                if (btnIcon) btnIcon.classList.add('d-none');
                if (btnText) btnText.classList.add('d-none');
                submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                // Update loading text dynamically
                const loadingText = @json(isset($bundleCategory) ? trans('loading.updating') : trans('loading.creating'));
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
                document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                // Start progress bar animation
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                    // Prepare form data
                    const formData = new FormData(bundleCategoryForm);

                    // Send AJAX request
                    return fetch(bundleCategoryForm.action, {
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
                        const successMessage = @json(isset($bundleCategory) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                        LoadingOverlay.showSuccess(
                            successMessage,
                            '{{ trans("loading.redirecting") }}'
                        );

                        // Show success alert
                        if (data.success) {
                            // Success - redirect after delay
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            }, 2000);
                        }
                    });
                })
                .catch(error => {
                    // Hide loading overlay and reset progress bar
                    LoadingOverlay.hide();

                    // Handle validation errors
                    if (error.errors) {
                        Object.keys(error.errors).forEach(field => {
                            // Convert Laravel dot notation to HTML bracket notation
                            // "translations.1.name" -> "translations[1][name]"
                            let fieldName = field.replace(/\.(\d+)\./g, '[$1][').replace(/\.(\w+)$/, '[$1]');
                            if (fieldName.includes('[') && !fieldName.endsWith(']')) {
                                fieldName += ']';
                            }

                            // Try multiple selectors to find the input
                            let input = document.querySelector(`[name="${fieldName}"]`) ||
                                       document.querySelector(`[name="${field}"]`) ||
                                       document.querySelector(`input[name*="${field.split('.').pop()}"]`) ||
                                       document.querySelector(`textarea[name*="${field.split('.').pop()}"]`);

                            if (input) {
                                input.classList.add('is-invalid');

                                // Add invalid border to image upload container if it's an image field
                                if (field === 'image') {
                                    const imageContainer = input.closest('.dm-uploader');
                                    if (imageContainer) {
                                        imageContainer.style.border = '1px solid #dc3545';
                                        imageContainer.style.borderRadius = '4px';
                                    }
                                }

                                // Remove any existing error message for this field
                                const existingError = input.parentNode.querySelector('.invalid-feedback');
                                if (existingError) {
                                    existingError.remove();
                                }

                                // Get language information from the input's data-lang attribute or label
                                let languageName = '';
                                const langCode = input.getAttribute('data-lang');
                                if (langCode) {
                                    // Get language name from the label
                                    const label = input.parentNode.querySelector('label');
                                    if (label) {
                                        const labelText = label.textContent;
                                        const match = labelText.match(/\(([^)]+)\)/);
                                        if (match) {
                                            languageName = ` (${match[1]})`;
                                        }
                                    }
                                }

                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.style.display = 'block !important';
                                feedback.textContent = error.errors[field][0] + languageName;
                                input.parentNode.appendChild(feedback);
                            } else {
                                console.log('Input not found for field:', field, 'Tried:', fieldName);
                            }
                        });
                    }

                    // Show error message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                        ${error.message || 'An error occurred'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    alertContainer.appendChild(alert);

                    // Scroll to top to show errors
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    // Re-enable submit button
                    submitBtn.disabled = false;
                    if (btnIcon) btnIcon.classList.remove('d-none');
                    if (btnText) btnText.classList.remove('d-none');
                    submitBtn.querySelector('.spinner-border').classList.add('d-none');
                });
            });
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
