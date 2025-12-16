@extends('layout.app')

@section('title', isset($blog) ? __('systemsetting::blogs.edit_blog') : __('systemsetting::blogs.create_blog'))

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
                        'title' => __('systemsetting::blogs.blogs_management'),
                        'url' => route('admin.system-settings.blogs.index'),
                    ],
                    [
                        'title' => isset($blog)
                            ? __('systemsetting::blogs.edit_blog')
                            : __('systemsetting::blogs.create_blog'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4 ">
                    <div class="card-header">
                        <h6>{{ isset($blog) ? __('systemsetting::blogs.edit_blog') : __('systemsetting::blogs.create_blog') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                                <strong>{{ __('systemsetting::blogs.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="blogForm" method="POST"
                            action="{{ isset($blog) ? route('admin.system-settings.blogs.update', $blog->id) : route('admin.system-settings.blogs.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($blog))
                                @method('PUT')
                            @endif

                            <div class="card card-holder">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i
                                            class="uil uil-info-circle me-1"></i>{{ __('systemsetting::blogs.basic_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Title --}}
                                        <x-multilingual-input name="title" :label="'Title'" :labelAr="'العنوان'"
                                            :placeholder="'Blog title'" :placeholderAr="'عنوان المقال'" :languages="$languages" :model="isset($blog) ? $blog : null"
                                            :required=true />

                                        {{-- Content --}}
                                        <x-multilingual-input name="content" :label="'Content'" :labelAr="'المحتوى'"
                                            :placeholder="'Blog content'" :placeholderAr="'محتوى المقال'" type="textarea" :rows="5"
                                            :languages="$languages" :model="isset($blog) ? $blog : null" :required=true />

                                        {{-- Category --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label class="il-gray fs-14 fw-500 mb-10">
                                                    {{ __('systemsetting::blogs.category') }} <span
                                                        class="text-danger">*</span>
                                                </label>
                                                <select class="form-control select2" name="blog_category_id"
                                                    id="blog_category_id">
                                                    <option value="">{{ __('systemsetting::blogs.select_category') }}
                                                    </option>
                                                    @foreach ($blogCategories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('blog_category_id') == $category->id || (isset($blog) && $blog->blog_category_id == $category->id) ? 'selected' : '' }}>
                                                            {{ $category->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('blog_category_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Blog Image --}}
                                        <div class="col-md-6">
                                            <x-image-upload id="image" name="image" :label="__('systemsetting::blogs.image')"
                                                :existingImage="isset($blog) && $blog->mainImage && $blog->mainImage->path
                                                    ? $blog->mainImage->path
                                                    : null" :placeholder="__('systemsetting::blogs.upload_image')" :recommendedSize="__('systemsetting::blogs.recommended_size')"
                                                aspectRatio="wide" />
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label class="form-label">
                                                    {{ __('systemsetting::blogs.status') }}
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" name="active"
                                                        id="active" value="1"
                                                        {{ old('active', isset($blog) ? $blog->active : 1) ? 'checked' : '' }}
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
                                        <i class="uil uil-search me-1"></i>{{ __('systemsetting::blogs.seo_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    {{-- SEO Title --}}
                                    <x-multilingual-input name="meta_title" :label="'SEO Title'" :labelAr="'عنوان الميتا'"
                                        :placeholder="'Enter SEO Title'" :placeholderAr="'عنوان الميتا للمحتوى'" :languages="$languages" :model="isset($blog) ? $blog : null" />

                                    {{-- SEO Description --}}
                                    <x-multilingual-input name="meta_description" :label="'SEO Description'" :labelAr="'وصف الميتا'"
                                        :placeholder="'Enter SEO Description'" :placeholderAr="'وصف الميتا للمحتوى'" type="textarea" :rows="3"
                                        :languages="$languages" :model="isset($blog) ? $blog : null" />

                                    {{-- SEO Keywords --}}
                                    <x-multilingual-input name="meta_keywords" :label="'SEO Keywords'" :labelAr="'كلمات المفتاحية'"
                                        :placeholder="'Enter SEO Keywords'" :placeholderAr="'كلمات المفتاحية للمحتوى (مفصولة بفواصل)'" :tags="true" :languages="$languages"
                                        :model="isset($blog) ? $blog : null" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                {{-- Submit Buttons --}}
                                <div class="col-md-12">
                                    <div class="form-group d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.system-settings.blogs.index') }}"
                                            class="btn btn-light btn-default btn-squared">
                                            {{ __('systemsetting::blogs.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared">
                                            <i class="uil uil-check me-1"></i>
                                            {{ isset($blog) ? __('systemsetting::blogs.update_blog') : __('systemsetting::blogs.create_blog') }}
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
            const blogForm = document.getElementById('blogForm');
            if (!blogForm) {
                console.error('Form not found');
                return;
            }

            const submitBtn = blogForm.querySelector('button[type="submit"]');
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

            blogForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Convert select2 values to hidden inputs or ensure they are submitting correctly
                // For select2
                $('#blog_category_id').trigger('change');

                submitBtn.disabled = true;
                originalBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span> {{ __('common.processing') ?? 'Processing...' }}';

                const loadingText = @json(isset($blog) ? trans('loading.updating') : trans('loading.creating'));
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
                        const formData = new FormData(blogForm);
                        return fetch(blogForm.action, {
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

                        const successMessage = @json(isset($blog)
                                ? trans('systemsetting::blogs.updated_successfully')
                                : trans('systemsetting::blogs.created_successfully'));

                        showAlert('success', data.message || successMessage);

                        setTimeout(() => {
                            window.location.href =
                                data.redirect ||
                                '{{ route('admin.system-settings.blogs.index') }}';
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

            // Select2 change event needing special handling for error clearing
            $('#blog_category_id').on('change', function() {
                clearInputError(this);
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
