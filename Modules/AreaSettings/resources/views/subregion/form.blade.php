@extends('layout.app')

@section('title', isset($subregion) ? __('areasettings::subregion.edit_subregion') : __('areasettings::subregion.create_subregion'))

@push('styles')
<!-- Select2 CSS loaded via Vite -->
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::subregion.subregions_management'), 'url' => route('admin.area-settings.subregions.index')],
                    ['title' => isset($subregion) ? __('areasettings::subregion.edit_subregion') : __('areasettings::subregion.add_subregion')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ isset($subregion) ? __('areasettings::subregion.edit_subregion') : __('areasettings::subregion.add_subregion') }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($subregion) ? route('admin.area-settings.subregions.update', $subregion->id) : route('admin.area-settings.subregions.store') }}" id="subregionForm">
                            @csrf
                            @if(isset($subregion))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Translations -->
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-25">
                                            <label for="translations_{{ $language->id }}_name" class="form-label w-100 @if($language->code == 'ar') text-end @endif">
                                                @if($language->rtl)
                                                    الاسم باللغة
                                                @else
                                                    {{ __('areasettings::subregion.name') }} ({{ $language->name }})
                                                @endif
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="translations[{{ $language->id }}][name]" 
                                                id="translations_{{ $language->id }}_name"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                value="{{ isset($subregion) ? $subregion->getTranslation('name', $language->code) : old('translations.'.$language->id.'.name') }}"
                                                {{ $language->rtl ? 'dir=rtl' : '' }}
                                                placeholder="{{ $language->code == 'ar' ? 'أدخل اسم المنطقة الفرعية' : 'Enter subregion name' }}"
                                            >
                                            @error('translations.'.$language->id.'.name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <!-- Region Selection -->
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label for="region_id" class="form-label">{{ __('areasettings::subregion.region') }} <span class="text-danger">*</span></label>
                                        <select name="region_id" id="region_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                                            <option value="">{{ __('areasettings::subregion.select_region') }}</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region['id'] }}" {{ isset($subregion) && $subregion->region_id == $region['id'] ? 'selected' : '' }}>
                                                    {{ $region['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('region_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Active Status Switcher --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('areasettings::subregion.active') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="active" 
                                                       name="active" 
                                                       value="1"
                                                       {{ old('active', isset($subregion) ? $subregion->active : 1) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active"></label>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex justify-content-end">
                                        <a href="{{ route('admin.area-settings.subregions.index') }}" class="btn btn-light btn-default btn-squared text-capitalize me-2">
                                            <i class="uil uil-arrow-left"></i> {{ __('areasettings::subregion.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                            <i class="uil uil-check"></i> {{ __('areasettings::subregion.save') }}
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
    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            allowClear: true
        });
    }

    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');
    const regionSelect = document.getElementById('region_id');

    // Load cities when country changes
    if (countrySelect) {
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            
            citySelect.innerHTML = '<option value="">{{ __("areasettings::subregion.select_city") }}</option>';
            regionSelect.innerHTML = '<option value="">{{ __("areasettings::subregion.select_region") }}</option>';
            
            if (countryId) {
                fetch('{{ route("admin.area-settings.cities.by-country", ":id") }}'.replace(':id', countryId))
                    .then(response => response.json())
                    .then(cities => {
                        cities.forEach(city => {
                            const translation = city.translations.find(t => t.lang_key === 'name' && t.lang_id == {{ $languages->first()->id }});
                            const cityName = translation ? translation.lang_value : city.id;
                            const option = new Option(cityName, city.id);
                            citySelect.add(option);
                        });
                        if (typeof jQuery !== 'undefined' && $.fn.select2) {
                            $(citySelect).trigger('change.select2');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading cities:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error loading cities. Please try again.');
                        }
                    });
            }
        });
    }

    // Load regions when city changes
    if (citySelect) {
        citySelect.addEventListener('change', function() {
            const cityId = this.value;
            
            regionSelect.innerHTML = '<option value="">{{ __("areasettings::subregion.select_region") }}</option>';
            
            if (cityId) {
                fetch('{{ route("admin.area-settings.regions.by-city", ":id") }}'.replace(':id', cityId))
                    .then(response => response.json())
                    .then(regions => {
                        regions.forEach(region => {
                            const translation = region.translations.find(t => t.lang_key === 'name' && t.lang_id == {{ $languages->first()->id }});
                            const regionName = translation ? translation.lang_value : region.id;
                            const option = new Option(regionName, region.id);
                            regionSelect.add(option);
                        });
                        if (typeof jQuery !== 'undefined' && $.fn.select2) {
                            $(regionSelect).trigger('change.select2');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading regions:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error loading regions. Please try again.');
                        }
                    });
            }
        });
    }

    // AJAX Form Submission
    const subregionForm = document.getElementById('subregionForm');
    const submitBtn = subregionForm.querySelector('button[type="submit"]');
    let originalBtnHtml = '';

    subregionForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        // Update loading text dynamically
        const loadingText = @json(isset($subregion) ? trans('loading.updating') : trans('loading.creating'));
        const loadingSubtext = '{{ trans("loading.please_wait") }}';
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-text').textContent = loadingText;
            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
        }
        
        // Show loading overlay
        LoadingOverlay.show();

        // Start progress bar animation
        LoadingOverlay.animateProgressBar(30, 300).then(() => {
            // Prepare form data
            const formData = new FormData(subregionForm);

            // Send AJAX request
            return fetch(subregionForm.action, {
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
                const successMessage = @json(isset($subregion) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                LoadingOverlay.showSuccess(
                    successMessage,
                    '{{ trans("loading.redirecting") }}'
                );
                
                // Redirect after 1.5 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("admin.area-settings.subregions.index") }}';
                }, 1500);
            });
        })
        .catch(error => {
            // Hide loading overlay
            LoadingOverlay.hide();
            
            // Remove previous validation errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            
            // Handle validation errors
            if (error.errors) {
                Object.keys(error.errors).forEach(key => {
                    const errorMessages = error.errors[key];
                    
                    // Find the input field
                    let input = null;
                    const possibleSelectors = [];
                    
                    // Add original key
                    possibleSelectors.push(`[name="${key}"]`);
                    
                    // If key contains dots (Laravel format: translations.0.name)
                    if (key.includes('.')) {
                        // Convert to bracket notation: translations[0][name]
                        const bracketKey = key.replace(/^([^.]+)\.(\d+)\.([^.]+)$/, '$1[$2][$3]');
                        possibleSelectors.push(`[name="${bracketKey}"]`);
                        
                        // Also try with escaped brackets
                        const escapedBracketKey = bracketKey.replace(/\[/g, '\\\\[').replace(/\]/g, '\\\\]');
                        possibleSelectors.push(`[name="${escapedBracketKey}"]`);
                    }
                    
                    // If key contains brackets, try escaping them
                    if (key.includes('[')) {
                        const escapedKey = key.replace(/\[/g, '\\\\[').replace(/\]/g, '\\\\]');
                        possibleSelectors.push(`[name="${escapedKey}"]`);
                    }
                    
                    // Try each selector until we find the input
                    for (const selector of possibleSelectors) {
                        try {
                            input = document.querySelector(selector);
                            if (input) break;
                        } catch (e) {
                            // Invalid selector, continue
                        }
                    }
                    
                    // If still not found, try to find by ID pattern (for translation fields)
                    if (!input && key.match(/^translations\.(\d+)\.name$/)) {
                        const languageId = key.match(/^translations\.(\d+)\.name$/)[1];
                        input = document.querySelector(`#name_${languageId}`);
                    }
                    
                    if (input) {
                        // Add is-invalid class
                        input.classList.add('is-invalid');
                        
                        // Remove any existing feedback
                        const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }
                        
                        // Create and append error message
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback d-block';
                        feedback.textContent = errorMessages[0];
                        input.parentNode.appendChild(feedback);
                    }
                    
                    // Also show toastr notification
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessages[0]);
                    }
                });
            } else if (error.message) {
                if (typeof toastr !== 'undefined') {
                    toastr.error(error.message);
                }
            }
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });
});
</script>
@endpush
