@extends('layout.app')

@push('styles')
<style>
    /* Ensure validation messages are always visible */
    .invalid-feedback {
        display: block !important;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* RTL support for Arabic validation messages */
    input[data-lang="ar"] + .invalid-feedback,
    textarea[data-lang="ar"] + .invalid-feedback {
        direction: rtl;
        text-align: right;
    }
    
    /* Highlight invalid fields with red border */
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    /* Smooth transition for error states */
    .form-control {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('activity.activities_management'), 'url' => route('admin.category-management.activities.index')],
                    ['title' => isset($activity) ? __('activity.edit_activity') : __('activity.create_activity')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($activity) ? __('activity.edit_activity') : __('activity.create_activity') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="activityForm" 
                              action="{{ isset($activity) ? route('admin.category-management.activities.update', $activity->id) : route('admin.category-management.activities.store') }}" 
                              method="POST">
                            @csrf
                            @if(isset($activity))
                                @method('PUT')
                            @endif

                            <div class="row">
                                {{-- Dynamic Language Fields for Name --}}
                                @foreach($languages as $language)
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="translation_{{ $language->id }}_name" class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                @if($language->code == 'ar')
                                                    الاسم ({{ $language->name }}) <span class="text-danger">*</span>
                                                @else
                                                    {{ __('activity.name_english') }} ({{ $language->name }}) <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.name') is-invalid @enderror" 
                                                   id="translation_{{ $language->id }}_name" 
                                                   name="translations[{{ $language->id }}][name]"  
                                                   value="{{ isset($activity) ? ($activity->getTranslation('name', $language->code) ?? '') : old('translations.' . $language->id . '.name') }}"
                                                   placeholder="@if($language->code == 'ar')أدخل اسم النشاط بالعربية@else{{ __('activity.enter_activity_name_english') }}@endif"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   data-lang="{{ $language->code }}">
                                            @error('translations.' . $language->id . '.name')
                                                <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
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
                                                    {{ __('activity.description_english') }} ({{ $language->name }})
                                                @endif
                                            </label>
                                            <textarea 
                                                   class="form-control ip-gray radius-xs b-light px-15 @error('translations.' . $language->id . '.description') is-invalid @enderror" 
                                                   id="translation_{{ $language->id }}_description" 
                                                   name="translations[{{ $language->id }}][description]"  
                                                   rows="4"
                                                   placeholder="@if($language->code == 'ar')أدخل وصف النشاط بالعربية@else{{ __('activity.enter_activity_description_english') }}@endif"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   data-lang="{{ $language->code }}">{{ isset($activity) ? ($activity->getTranslation('description', $language->code) ?? '') : old('translations.' . $language->id . '.description') }}</textarea>
                                            @error('translations.' . $language->id . '.description')
                                                <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Activation Switcher --}}
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('activity.activation') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="active" 
                                                       name="active" 
                                                       value="1"
                                                       {{ old('active', $activity->active ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.category-management.activities.index') }}" 
                                   class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ __('activity.cancel') }}
                                </a>
                                <button type="submit" id="submitBtn" 
                                        class="btn btn-primary btn-default btn-squared text-capitalize"
                                        style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i> 
                                    <span>{{ isset($activity) ? __('activity.update_activity') : __('activity.add_activity') }}</span>
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
            const activityForm = document.getElementById('activityForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');

            // Clear validation errors on input
            activityForm.querySelectorAll('input, textarea').forEach(input => {
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

            activityForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Disable submit button and show loading
                submitBtn.disabled = true;
                const btnIcon = submitBtn.querySelector('i');
                const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                if (btnIcon) btnIcon.classList.add('d-none');
                if (btnText) btnText.classList.add('d-none');
                submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                // Update loading text dynamically
                const loadingText = @json(isset($activity) ? trans('loading.updating') : trans('loading.creating'));
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
                    const formData = new FormData(activityForm);

                    // Send AJAX request
                    return fetch(activityForm.action, {
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
                        const successMessage = @json(isset($activity) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                        LoadingOverlay.showSuccess(
                            successMessage,
                            '{{ trans("loading.redirecting") }}'
                        );
                        
                        // Show success alert
                        showAlert('success', data.message || successMessage);
                        
                        // Redirect after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = data.redirect || '{{ route("admin.category-management.activities.index") }}';
                        }, 1500);
                    });
                })
                .catch(error => {
                    // Hide loading overlay and reset progress bar
                    LoadingOverlay.hide();
                    
                    // Handle validation errors
                    if (error.errors) {
                        let errorCount = 0;
                        Object.keys(error.errors).forEach(key => {
                            // Handle both dot notation and bracket notation for nested fields
                            const inputName = key.replace(/\./g, '][').replace(/^/, '').replace(/\]$/, '');
                            let input = document.querySelector(`[name="${key}"]`);
                            
                            // Try alternative selectors for nested fields
                            if (!input) {
                                const bracketKey = key.replace(/\./g, '][');
                                input = document.querySelector(`[name="${bracketKey}"]`);
                            }
                            if (!input) {
                                const parts = key.split('.');
                                if (parts.length === 3) {
                                    // translations.1.name -> translations[1][name]
                                    input = document.querySelector(`[name="${parts[0]}[${parts[1]}][${parts[2]}]"]`);
                                }
                            }
                            
                            if (input) {
                                errorCount++;
                                input.classList.add('is-invalid');
                                
                                // Remove existing feedback to avoid duplicates
                                const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                                if (existingFeedback) {
                                    existingFeedback.remove();
                                }
                                
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.style.display = 'block';
                                feedback.textContent = error.errors[key][0];
                                input.parentNode.appendChild(feedback);
                                
                                // Scroll to first error
                                if (errorCount === 1) {
                                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }
                        });
                        
                        const errorMessage = error.message || '{{ __("Please check the form for errors") }}';
                        showAlert('danger', errorMessage + ` (${errorCount} ${errorCount === 1 ? 'error' : 'errors'})`);
                    } else {
                        showAlert('danger', error.message || '{{ __("An error occurred") }}');
                    }
                    
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    const btnIcon = submitBtn.querySelector('i');
                    const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                    if (btnIcon) btnIcon.classList.remove('d-none');
                    if (btnText) btnText.classList.remove('d-none');
                    submitBtn.querySelector('.spinner-border').classList.add('d-none');
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
                alertContainer.appendChild(alert);

                // Scroll to top to show alert
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
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
