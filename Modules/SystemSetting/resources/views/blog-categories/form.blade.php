@extends('layout.app')

@section('title', isset($blogCategory) ? __('systemsetting::blog_categories.edit_blog_category') :
    __('systemsetting::blog_categories.create_blog_category'))

@section('content')
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => __('systemsetting::blog_categories.blog_categories_management'),
                        'url' => route('admin.system-settings.blog-categories.index'),
                    ],
                    [
                        'title' => isset($blogCategory)
                            ? __('systemsetting::blog_categories.edit_blog_category')
                            : __('systemsetting::blog_categories.create_blog_category'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4 ">
                    <div class="card-header">
                        <h6>{{ isset($blogCategory) ? __('systemsetting::blog_categories.edit_blog_category') : __('systemsetting::blog_categories.create_blog_category') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                                <strong>{{ __('systemsetting::blog_categories.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="blogCategoryForm" method="POST"
                            action="{{ isset($blogCategory) ? route('admin.system-settings.blog-categories.update', $blogCategory->id) : route('admin.system-settings.blog-categories.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($blogCategory))
                                @method('PUT')
                            @endif

                            <div class="card card-holder">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i
                                            class="uil uil-info-circle me-1"></i>{{ __('systemsetting::blog_categories.basic_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Title --}}
                                        <x-multilingual-input name="title" :label="'Title'" :labelAr="'العنوان'"
                                            :placeholder="'Category title'" :placeholderAr="'عنوان الفئة'" :languages="$languages" :model="isset($blogCategory) ? $blogCategory : null"
                                            :required=true />
                                        {{-- Blog Category Image --}}
                                        <div class="col-md-6">
                                            <x-image-upload id="image" name="image" :label="__('systemsetting::blog_categories.image')"
                                                :existingImage="isset($blogCategory) && $blogCategory->mainImage
                                                    ? $blogCategory->mainImage->path
                                                    : null" :placeholder="__('systemsetting::blog_categories.upload_image')" :recommendedSize="__('systemsetting::blog_categories.recommended_size')" aspectRatio="wide" />
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label class="form-label">
                                                    {{ __('systemsetting::blog_categories.status') }}
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" name="active"
                                                        id="active" value="1"
                                                        {{ old('active', isset($blogCategory) ? $blogCategory->active : 1) ? 'checked' : '' }}
                                                        style="width: 40px; height: 20px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Information --}}
                            <div class="card mt-3 card-holder">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i
                                            class="uil uil-search me-1"></i>{{ __('systemsetting::blog_categories.seo_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    {{-- SEO Title --}}
                                    <x-multilingual-input name="meta_title" :label="'Seo Title'" :labelAr="'عنوان الميتا'"
                                        :placeholder="'Enter Seo Title'" :placeholderAr="'عنوان الميتا للمحتوى'" :languages="$languages" :model="isset($blogCategory) ? $blogCategory : null" />

                                    {{-- SEO Description --}}
                                    <x-multilingual-input name="meta_description" :label="'Seo Description'" :labelAr="'وصف الميتا'"
                                        :placeholder="'Enter Seo Description'" :placeholderAr="'وصف الميتا للمحتوى'" type="textarea" :rows="3"
                                        :languages="$languages" :model="isset($blogCategory) ? $blogCategory : null" />

                                    {{-- SEO Keywords --}}
                                    <x-multilingual-input name="meta_keywords" :label="'Seo Keywords'" :labelAr="'كلمات المفتاحية'"
                                        :placeholder="'Enter Seo Keywords'" :placeholderAr="'كلمات المفتاحية للمحتوى (مفصولة بفواصل)'" :tags="true" :languages="$languages"
                                        :model="isset($blogCategory) ? $blogCategory : null" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                {{-- Submit Buttons --}}
                                <div class="col-md-12">
                                    <div class="form-group d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.system-settings.blog-categories.index') }}"
                                            class="btn btn-light btn-default btn-squared">
                                            {{ __('systemsetting::blog_categories.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared">
                                            <i class="uil uil-check me-1"></i>
                                            {{ isset($blogCategory) ? __('systemsetting::blog_categories.update_blog_category') : __('systemsetting::blog_categories.create_blog_category') }}
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
    <x-loading-overlay :loadingText="trans('loading.processing')" :loadingSubtext="trans('loading.please_wait')" />
@endpush

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
            const blogCategoryForm = document.getElementById('blogCategoryForm');
            if (!blogCategoryForm) {
                console.error('Form not found');
                return;
            }

            const submitBtn = blogCategoryForm.querySelector('button[type="submit"]');
            const alertContainer = document.getElementById('alertContainer');
            let originalBtnHtml = '';

            // Show alert function (moved outside submit handler)
            function showAlert(type, message) {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible fade show mb-20`;
                alert.innerHTML = `
            <i class="uil uil-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                alertContainer.appendChild(alert);
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Clear validation errors from an input
            function clearInputError(input) {
                if (!input) return;

                input.classList.remove('is-invalid');

                // Find and remove feedback in various possible locations
                const parentNode = input.parentNode;
                const formGroup = input.closest('.form-group');

                // Remove from immediate parent
                const feedbackInParent = parentNode?.querySelector('.invalid-feedback');
                if (feedbackInParent) {
                    feedbackInParent.remove();
                }

                // Remove from form group
                const feedbackInGroup = formGroup?.querySelector('.invalid-feedback');
                if (feedbackInGroup) {
                    feedbackInGroup.remove();
                }

                // Remove any sibling feedback
                let sibling = input.nextElementSibling;
                while (sibling) {
                    if (sibling.classList.contains('invalid-feedback')) {
                        sibling.remove();
                        break;
                    }
                    sibling = sibling.nextElementSibling;
                }
            }

            blogCategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                submitBtn.disabled = true;
                originalBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span> {{ __('common.processing') ?? 'Processing...' }}';

                const loadingText = @json(isset($blogCategory) ? trans('loading.updating') : trans('loading.creating'));
                const loadingSubtext = '{{ trans('loading.please_wait') }}';

                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.querySelector('.loading-text').textContent = loadingText;
                    overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
                }

                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show();
                }

                alertContainer.innerHTML = '';
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                const progressPromise = typeof LoadingOverlay !== 'undefined' ?
                    LoadingOverlay.animateProgressBar(30, 300) :
                    Promise.resolve();

                progressPromise
                    .then(() => {
                        const formData = new FormData(blogCategoryForm);
                        return fetch(blogCategoryForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                    })
                    .then(response => {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.animateProgressBar(60, 200);
                        }

                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.animateProgressBar(90, 200);
                        }
                        return data;
                    })
                    .then(data => {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.animateProgressBar(100, 200);
                        }

                        const successMessage = @json(isset($blogCategory)
                                ? trans('systemsetting::blog_categories.updated_successfully')
                                : trans('systemsetting::blog_categories.created_successfully'));

                        showAlert('success', data.message || successMessage);

                        setTimeout(() => {
                            window.location.href =
                                data.redirect ||
                                '{{ route('admin.system-settings.blog-categories.index') }}';
                        }, 1500);
                    })
                    .catch(error => {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        if (error.errors) {
                            Object.keys(error.errors).forEach(key => {
                                // Try to find input by exact name match
                                let input = document.querySelector(`[name="${key}"]`);

                                // If not found, convert dot notation to bracket notation
                                // e.g., "translations.1.title" -> "translations[1][title]"
                                if (!input) {
                                    const parts = key.split('.');
                                    let bracketKey = parts[0];
                                    for (let i = 1; i < parts.length; i++) {
                                        bracketKey += '[' + parts[i] + ']';
                                    }
                                    input = document.querySelector(`[name="${bracketKey}"]`);
                                }

                                if (!input) {
                                    console.warn('Input not found for error key:', key);
                                    return;
                                }

                                input.classList.add('is-invalid');

                                // Remove existing feedback if any
                                const existingFeedback = input.parentNode.querySelector(
                                    '.invalid-feedback');
                                if (existingFeedback) {
                                    existingFeedback.remove();
                                }

                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.textContent = error.errors[key][0];
                                input.parentNode.appendChild(feedback);
                            });

                            showAlert('danger', error.message ||
                                '{{ __('Please check the form for errors') }}');
                        } else {
                            showAlert('danger', error.message || '{{ __('An error occurred') }}');
                        }

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    });
            });

            // Remove validation on input - using clearInputError helper
            document.querySelectorAll('input, select, textarea').forEach(input => {
                ['input', 'change', 'keyup', 'blur'].forEach(evt => {
                    input.addEventListener(evt, function() {
                        clearInputError(this);
                    });
                });
            });

            // Clear errors when user starts typing in multilingual inputs
            document.querySelectorAll('input, textarea').forEach(input => {
                ['input', 'keyup', 'change'].forEach(evt => {
                    input.addEventListener(evt, function() {
                        // Clear error from this input
                        clearInputError(this);

                        // Also clear errors from the corresponding input in other languages
                        const inputName = this.getAttribute('name');
                        if (inputName) {
                            // Match pattern like translations[1][title] and clear errors for translations[2][title], etc.
                            const match = inputName.match(/^(.+)\[(\d+)\](.+)$/);
                            if (match) {
                                const prefix = match[1];
                                const suffix = match[3];
                                // Find all related language inputs
                                document.querySelectorAll('input, textarea').forEach(
                                    otherInput => {
                                        const otherName = otherInput.getAttribute(
                                            'name');
                                        if (otherName && otherName !== inputName) {
                                            const otherMatch = otherName.match(
                                                /^(.+)\[(\d+)\](.+)$/);
                                            if (otherMatch && otherMatch[1] ===
                                                prefix && otherMatch[3] === suffix) {
                                                clearInputError(otherInput);
                                            }
                                        }
                                    });
                            }
                        }
                    });
                });
            });

            // Clear errors for image upload when file is selected
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    clearInputError(this);
                });
            });

        });
    </script>
@endpush
