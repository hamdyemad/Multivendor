@extends('layout.app')

@section('title', isset($ad) ? __('systemsetting::ads.edit_ad') : __('systemsetting::ads.create_ad'))

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
                        'title' => __('systemsetting::ads.ads_management'),
                        'url' => route('admin.system-settings.ads.index'),
                    ],
                    ['title' => isset($ad) ? __('systemsetting::ads.edit_ad') : __('systemsetting::ads.create_ad')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4 ">
                    <div class="card-header">
                        <h6>{{ isset($ad) ? __('systemsetting::ads.edit_ad') : __('systemsetting::ads.create_ad') }}</h6>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                                <strong>{{ __('systemsetting::ads.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="adForm" method="POST"
                            action="{{ isset($ad) ? route('admin.system-settings.ads.update', $ad->id) : route('admin.system-settings.ads.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($ad))
                                @method('PUT')
                            @endif

                            <div class="card card-holder">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i
                                            class="uil uil-info-circle me-1"></i>{{ __('systemsetting::ads.basic_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Type --}}
                                        <div class="col-md-6 mb-25">
                                            <x-searchable-tags name="type[]" :label="__('systemsetting::ads.ad_type')" :options="[
                                                ['id' => 'mobile', 'name' => __('systemsetting::ads.mobile')],
                                                ['id' => 'website', 'name' => __('systemsetting::ads.website')],
                                            ]"
                                                :selected="isset($ad) ? $ad->type : []" :placeholder="__('systemsetting::ads.select_type') ?? 'Select platform...'" :required="true" />
                                        </div>

                                        {{-- Position --}}
                                        <div class="col-md-6 mb-25">
                                            <x-searchable-tags name="position" :label="__('systemsetting::ads.position')" :options="collect($positions)
                                                ->map(fn($val, $key) => ['id' => $key, 'name' => $val])
                                                ->toArray()"
                                                :selected="isset($ad) ? [$ad->position] : []" :placeholder="__('systemsetting::ads.position_placeholder')" :required="true" :multiple="false" />
                                        </div>

                                        {{-- Link --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label for="link" class="form-label">
                                                    {{ __('systemsetting::ads.link') }}
                                                </label>
                                                <input type="url" name="link" id="link"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('link') is-invalid @enderror"
                                                    placeholder="{{ __('systemsetting::ads.link_placeholder') }}"
                                                    value="{{ old('link', isset($ad) ? $ad->link : '') }}">
                                                @error('link')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Ad Image --}}
                                        <div class="col-md-6">
                                            <x-image-upload id="image" name="image" :label="__('systemsetting::ads.ad_image')"
                                                :existingImage="isset($ad) && $ad->image ? $ad->image : null" :placeholder="__('systemsetting::ads.ad_image')" :recommendedSize="'Recommended size: 1200x600px'"
                                                aspectRatio="wide" />
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-6">
                                            <div class="form-group mb-25">
                                                <label class="form-label">
                                                    {{ __('systemsetting::ads.status') }}
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" name="active"
                                                        id="active" value="1"
                                                        {{ old('active', isset($ad) ? $ad->active : 1) ? 'checked' : '' }}
                                                        style="width: 40px; height: 20px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Ad Content - Translations --}}
                            <div class="card mt-3 card-holder">
                                <div class="card-header">
                                    <h3 class="fw-bold m-0">
                                        <i class="uil uil-file-text me-1"></i>{{ __('systemsetting::ads.ad_content') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    {{-- Title --}}
                                    <x-multilingual-input name="title" :label="'Title'" :labelAr="'العنوان'"
                                        :placeholder="'Title'" :placeholderAr="'العنوان'" :languages="$languages" :model="isset($ad) ? $ad : null"
                                        :required=true />

                                    {{-- Subtitle --}}
                                    <x-multilingual-input name="subtitle" :label="'Subtitle'" :labelAr="'العنوان الفرعى'"
                                        :placeholder="'Subtitle'" :placeholderAr="'العنوان الفرعى'" :languages="$languages" :model="isset($ad) ? $ad : null" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                {{-- Submit Buttons --}}
                                <div class="col-md-12">
                                    <div class="form-group d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.system-settings.ads.index') }}"
                                            class="btn btn-light btn-default btn-squared">
                                            {{ __('systemsetting::ads.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared">
                                            <i class="uil uil-check me-1"></i>
                                            {{ isset($ad) ? __('systemsetting::ads.update_ad') : __('systemsetting::ads.create_ad') }}
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
            const adForm = document.getElementById('adForm');
            if (!adForm) {
                console.error('Form not found');
                return;
            }

            const submitBtn = adForm.querySelector('button[type="submit"]');
            const alertContainer = document.getElementById('alertContainer');
            let originalBtnHtml = '';

            adForm.addEventListener('submit', function(e) {
                e.preventDefault();

                submitBtn.disabled = true;
                originalBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span> {{ __('common.processing') ?? 'Processing...' }}';

                const loadingText = @json(isset($ad) ? trans('loading.updating') : trans('loading.creating'));
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
                        const formData = new FormData(adForm);
                        return fetch(adForm.action, {
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

                        const successMessage = @json(isset($ad) ? trans('systemsetting::ads.ad_updated') : trans('systemsetting::ads.ad_created'));

                        showAlert('success', data.message || successMessage);

                        setTimeout(() => {
                            window.location.href =
                                data.redirect ||
                                '{{ route('admin.system-settings.ads.index') }}';
                        }, 1500);
                    })
                    .catch(error => {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        if (error.errors) {
                            Object.keys(error.errors).forEach(key => {
                                // 1. Try exact name match
                                let input = document.querySelector(`[name="${key}"]`);

                                // 2. Try with brackets (array notation)
                                if (!input) {
                                    input = document.querySelector(`[name="${key}[]"]`);
                                }

                                // 3. Try converting dot notation to bracket notation
                                // e.g., "translations.1.title" -> "translations[1][title]"
                                if (!input) {
                                    const parts = key.split('.');
                                    let bracketKey = parts[0];
                                    for (let i = 1; i < parts.length; i++) {
                                        bracketKey += '[' + parts[i] + ']';
                                    }
                                    input = document.querySelector(`[name="${bracketKey}"]`);
                                }

                                // 4. Searchable tags might have NO hidden inputs if empty
                                // Look for the wrapper by data-name
                                let wrapper = null;
                                if (!input) {
                                    wrapper = document.querySelector(
                                            `.searchable-tags-wrapper[data-name="${key}"]`) ||
                                        document.querySelector(
                                            `.searchable-tags-wrapper[data-name="${key}[]"]`);
                                }

                                if (!input && !wrapper) {
                                    console.warn('Input not found for error key:', key);
                                    return;
                                }

                                // Find the closest container to append the error
                                let container = wrapper ||
                                    input.closest('.searchable-tags-wrapper') ||
                                    input.closest('.form-group') ||
                                    input.parentNode;

                                // For searchable-tags, we want to highlight the .tag-input-container
                                let highlightElement = (wrapper || input.closest(
                                        '.searchable-tags-wrapper')) ?
                                    (wrapper || input.closest('.searchable-tags-wrapper'))
                                    .querySelector('.tag-input-container') :
                                    (input.closest('.image-preview-container') || input.closest(
                                        '.banner-preview') || input);

                                if (highlightElement) highlightElement.classList.add(
                                    'is-invalid');

                                // Remove existing feedback if any
                                const existingFeedback = container.querySelector(
                                    '.invalid-feedback');
                                if (existingFeedback) {
                                    existingFeedback.remove();
                                }

                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.textContent = error.errors[key][0];
                                container.appendChild(feedback);
                            });

                            showAlert('danger', error.message ||
                                '{{ __('systemsetting::ads.validation_errors') }}');
                        } else {
                            showAlert('danger', error.message ||
                                '{{ __('common.error_occurred') ?? 'An error occurred' }}');
                        }

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    });
            });

            // Show alert
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

            // Remove validation on input
            document.querySelectorAll('input, select, textarea').forEach(input => {
                const clearError = () => {
                    const container = input.closest('.searchable-tags-wrapper') ||
                        input.closest('.form-group') ||
                        input.parentNode;

                    const highlightElement = input.closest('.searchable-tags-wrapper') ?
                        input.closest('.searchable-tags-wrapper').querySelector(
                        '.tag-input-container') :
                        (input.closest('.image-preview-container') || input.closest(
                            '.banner-preview') || input);

                    if (highlightElement) highlightElement.classList.remove('is-invalid');
                    input.classList.remove('is-invalid');

                    if (container) {
                        const feedback = container.querySelector('.invalid-feedback');
                        if (feedback) feedback.remove();
                    }
                };

                ['input', 'change', 'keyup', 'click'].forEach(evt =>
                    input.addEventListener(evt, clearError)
                );
            });

        });
    </script>
@endpush
