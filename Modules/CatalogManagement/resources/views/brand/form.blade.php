@extends('layout.app')
@section('title', (isset($brand)) ? trans('catalogmanagement::brand.edit_brand') : trans('catalogmanagement::brand.create_brand'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::brand.brands_management'), 'url' => route('admin.brands.index')],
                    ['title' => isset($brand) ? trans('catalogmanagement::brand.edit_brand') : trans('catalogmanagement::brand.add_brand')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($brand) ? trans('catalogmanagement::brand.edit_brand') : trans('catalogmanagement::brand.add_brand') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="brandForm"
                              action="{{ isset($brand) ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @if(isset($brand))
                                @method('PUT')
                            @endif

                            <div class="row">
                                {{-- Dynamic Language Fields for Name --}}
                                @foreach($languages as $language)
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="translation_{{ $language->id }}_name" class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                @if($language->code == 'ar')
                                                    اسم العلامة التجارية ({{ $language->name }}) <span class="text-danger">*</span>
                                                @else
                                                    {{ trans('catalogmanagement::brand.name') }} ({{ $language->name }}) <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text"
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.name') is-invalid @enderror"
                                                   id="translation_{{ $language->id }}_name"
                                                   name="translations[{{ $language->id }}][name]"
                                                   value="{{ isset($brand) ? ($brand->getTranslation('name', $language->code) ?? '') : old('translations.' . $language->id . '.name') }}"
                                                   placeholder="@if($language->code == 'ar')أدخل اسم العلامة التجارية@else{{ trans('catalogmanagement::brand.enter_brand_name') }}@endif"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   data-lang="{{ $language->code }}">
                                            @error('translations.' . $language->id . '.name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Dynamic Language Fields for Description --}}
                                @foreach($languages as $language)
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="translation_{{ $language->id }}_description" class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                @if($language->code == 'ar')
                                                    الوصف ({{ $language->name }})
                                                @else
                                                    {{ trans('catalogmanagement::brand.description') }} ({{ $language->name }})
                                                @endif
                                            </label>
                                            <textarea
                                                   class="form-control ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.description') is-invalid @enderror"
                                                   id="translation_{{ $language->id }}_description"
                                                   name="translations[{{ $language->id }}][description]"
                                                   rows="4"
                                                   placeholder="@if($language->code == 'ar')أدخل وصف العلامة التجارية@else{{ trans('catalogmanagement::brand.enter_brand_description') }}@endif"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   data-lang="{{ $language->code }}">{{ isset($brand) ? ($brand->getTranslation('description', $language->code) ?? '') : old('translations.' . $language->id . '.description') }}</textarea>
                                            @error('translations.' . $language->id . '.description')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Logo Upload --}}
                                <div class="col-md-6 mb-25">
                                    <x-image-upload
                                        id="logo"
                                        name="logo"
                                        :label="trans('catalogmanagement::brand.logo')"
                                        :placeholder="trans('common.click_to_upload')"
                                        :recommendedSize="trans('common.recommended_logo_size')"
                                        aspectRatio="logo"
                                        :existingImage="isset($brand) && $brand->logo ? $brand->logo->path : null"
                                    />
                                </div>

                                {{-- Cover Upload --}}
                                <div class="col-md-6 mb-25">
                                    <x-image-upload
                                        id="cover"
                                        name="cover"
                                        :label="trans('catalogmanagement::brand.cover')"
                                        :placeholder="trans('common.click_to_upload')"
                                        :recommendedSize="trans('common.recommended_cover_size')"
                                        aspectRatio="wide"
                                        :existingImage="isset($brand) && $brand->cover ? $brand->cover->path : null"
                                    />
                                </div>

                                {{-- Social Media Section --}}
                                <div class="col-12 mb-2">
                                    <h6 class="fw-500">{{ trans('catalogmanagement::brand.social_media') }}</h6>
                                </div>

                                {{-- Facebook URL --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="facebook_url" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::brand.facebook_url') }}
                                        </label>
                                        <input type="url"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('facebook_url') is-invalid @enderror"
                                               id="facebook_url"
                                               name="facebook_url"
                                               value="{{ old('facebook_url', $brand->facebook_url ?? '') }}"
                                               placeholder="https://facebook.com/your-brand">
                                        @error('facebook_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Twitter URL --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="twitter_url" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::brand.twitter_url') }}
                                        </label>
                                        <input type="url"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('twitter_url') is-invalid @enderror"
                                               id="twitter_url"
                                               name="twitter_url"
                                               value="{{ old('twitter_url', $brand->twitter_url ?? '') }}"
                                               placeholder="https://twitter.com/your-brand">
                                        @error('twitter_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Instagram URL --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="instagram_url" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::brand.instagram_url') }}
                                        </label>
                                        <input type="url"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('instagram_url') is-invalid @enderror"
                                               id="instagram_url"
                                               name="instagram_url"
                                               value="{{ old('instagram_url', $brand->instagram_url ?? '') }}"
                                               placeholder="https://instagram.com/your-brand">
                                        @error('instagram_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- LinkedIn URL --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="linkedin_url" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::brand.linkedin_url') }}
                                        </label>
                                        <input type="url"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('linkedin_url') is-invalid @enderror"
                                               id="linkedin_url"
                                               name="linkedin_url"
                                               value="{{ old('linkedin_url', $brand->linkedin_url ?? '') }}"
                                               placeholder="https://linkedin.com/company/your-brand">
                                        @error('linkedin_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Pinterest URL --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="pinterest_url" class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('catalogmanagement::brand.pinterest_url') }}
                                        </label>
                                        <input type="url"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('pinterest_url') is-invalid @enderror"
                                               id="pinterest_url"
                                               name="pinterest_url"
                                               value="{{ old('pinterest_url', $brand->pinterest_url ?? '') }}"
                                               placeholder="https://pinterest.com/your-brand">
                                        @error('pinterest_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Activation Switcher --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ trans('catalogmanagement::brand.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="active"
                                                       name="active"
                                                       value="1"
                                                       {{ old('active', $brand->active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.brands.index') }}"
                                   class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ trans('common.cancel') }}
                                </a>
                                <button type="submit" id="submitBtn"
                                        class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-check"></i>
                                    <span>{{ isset($brand) ? trans('catalogmanagement::brand.update_brand') : trans('catalogmanagement::brand.add_brand') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-body')
    <x-loading-overlay
        :loadingText="trans('loading.processing')"
        :loadingSubtext="trans('loading.please_wait')"
    />
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Form submission with AJAX
        $('#brandForm').on('submit', function(e) {
            e.preventDefault();

            const brandForm = this;
            const $submitBtn = $('#submitBtn');
            const $spinner = $submitBtn.find('.spinner-border');
            const alertContainer = document.getElementById('alertContainer');

            // Disable button and show spinner
            $submitBtn.prop('disabled', true);
            $spinner.removeClass('d-none');

            // Update loading text and show overlay
            const loadingText = @json(isset($brand) ? trans('loading.updating') : trans('loading.creating'));
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

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Start progress bar animation
            LoadingOverlay.animateProgressBar(30, 300).then(() => {
                // Prepare form data
                const formData = new FormData(brandForm);

                // Send AJAX request
                return fetch(brandForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
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
                    const successMessage = @json(isset($brand) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                    LoadingOverlay.showSuccess(
                        successMessage,
                        '{{ trans("loading.redirecting") }}'
                    );

                    // Redirect after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route("admin.brands.index") }}';
                    }, 1500);
                });
            })
            .catch(error => {
                // Hide loading overlay
                LoadingOverlay.hide();

                // Re-enable button
                $submitBtn.prop('disabled', false);
                $spinner.addClass('d-none');

                // Clear previous errors first
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                // Handle validation errors
                if (error.errors) {
                    console.log('Validation errors received:', error.errors);

                    Object.keys(error.errors).forEach(field => {
                        console.log('Processing field:', field);

                        // Convert dot notation to bracket notation
                        // e.g., "translations.1.name" -> "translations[1][name]"
                        let parts = field.split('.');
                        let fieldName = parts[0]; // Start with first part

                        for (let i = 1; i < parts.length; i++) {
                            fieldName += '[' + parts[i] + ']';
                        }

                        console.log('Converted to:', fieldName);

                        // Try to find the field with bracket notation first
                        let $field = $('input[name="' + fieldName + '"], textarea[name="' + fieldName + '"], select[name="' + fieldName + '"]');

                        console.log('Found field:', $field.length);

                        // If not found, try with original dot notation
                        if ($field.length === 0) {
                            $field = $('input[name="' + field + '"], textarea[name="' + field + '"], select[name="' + field + '"]');
                            console.log('Tried original notation, found:', $field.length);
                        }

                        if ($field.length > 0) {
                            $field.addClass('is-invalid');

                            // Insert error message after the input
                            const errorHtml = '<div class="invalid-feedback d-block" style="display: block !important; color: #dc3545; font-weight: 500; margin-top: 0.5rem;">' + error.errors[field][0] + '</div>';
                            $field.after(errorHtml);

                            console.log('Added error for:', field);
                        } else {
                            console.error('Could not find field for:', field);
                        }
                    });

                    // Show error alert
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-1"></i> {{ trans('common.please_fix_errors') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                } else {
                    // General error
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-1"></i> ${error.message || '{{ trans('common.error_occurred') }}'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }

                // Scroll to top
                $('html, body').animate({ scrollTop: 0 }, 500);
            });
        });
    });
</script>
@endpush
