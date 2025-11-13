@extends('layout.app')

@section('title')
{{ $title }}
@endsection
@push('styles')
<!-- Vendor Form Custom SCSS -->
@vite(['Modules/Vendor/resources/assets/scss/vendor-form.scss'])
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                ['title' => isset($vendor) ? trans('vendor::vendor.edit_vendor') : trans('vendor::vendor.create_vendor')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($vendor) ? trans('vendor::vendor.edit_vendor') : trans('vendor::vendor.create_vendor') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Wizard Navigation -->
                    <x-wizard :steps="[
                        trans('vendor::vendor.vendor_information'),
                        trans('vendor::vendor.vendor_documents'),
                        trans('vendor::vendor.vendor_account_details')
                    ]" :currentStep="1" />

                    <!-- Validation Alerts Container -->
                    <div id="validation-alerts-container" class="mb-3"></div>

                    <!-- Form -->
                    <form id="vendorForm" method="POST" action="{{ isset($vendor) ? route('admin.vendors.update', $vendor->id) : route('admin.vendors.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($vendor))
                            @method('PUT')
                        @endif

                        <!-- Step 1: Vendor Information -->
                        <div class="wizard-step-content active" data-step="1">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-4" style="">
                                        <i class="uil uil-store" style="font-size: 22px;"></i>
                                        {{ trans('vendor::vendor.vendor_information') }}
                                    </h5>
                                    <div class="row" style="margin-top: 20px;">
                                        <!-- Name Fields for each language -->
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="name_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                      {{ trans('vendor::vendor.name') }} ({{ $language->name }})
                                                    @else
                                                        الاسم باللغة العربية
                                                    @endif
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="translations[{{ $language->id }}][name]"
                                                    id="name_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="{{ isset($vendor) ? $vendor->getTranslation('name', $language->code) : old('translations.'.$language->id.'.name') }}"
                                                    placeholder="@if($language->code == 'ar')أدخل اسم التاجر@else{{ trans('vendor::vendor.enter_vendor_name') }}@endif"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}
                                                >
                                                @error('translations.'.$language->id.'.name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <!-- Description Fields for each language -->
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="description_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">

                                                    @if($language->code == 'en')
                                                    {{ trans('vendor::vendor.description') }} ({{ $language->name }})
                                                    @else
                                                    الوصف باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea
                                                    name="translations[{{ $language->id }}][description]"
                                                    id="description_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="4"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}
                                                >{{ isset($vendor) ? $vendor->getTranslation('description', $language->code) : old('translations.'.$language->id.'.description') }}</textarea>
                                                @error('translations.'.$language->id.'.description')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                            <!-- Logo and Banner Section -->
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h5 class="mb-4" style="">
                                        <i class="uil uil-palette" style="font-size: 22px;"></i>
                                        {{ trans('vendor::vendor.branding') }}
                                    </h5>

                                    <div class="row">
                                        <!-- Logo Upload -->
                                        <div class="col-md-6 mb-3">
                                            <x-image-upload
                                                id="logo"
                                                name="logo"
                                                label="{{ trans('vendor::vendor.logo') }}"
                                                :required="!isset($vendor)"
                                                :existingImage="isset($vendor) && $vendor->logo ? $vendor->logo->path : null"
                                                placeholder="{{ trans('vendor::vendor.click_to_upload_logo') }}"
                                                recommendedSize="{{ trans('vendor::vendor.logo_recommended_size') }}"
                                                accept="image/jpeg,image/png,image/jpg,image/gif"
                                                aspectRatio="logo"
                                            />
                                        </div>

                                        <!-- Banner Upload -->
                                        <div class="col-md-6 mb-3">
                                            <x-image-upload
                                                id="banner"
                                                name="banner"
                                                label="{{ trans('vendor::vendor.banner') }}"
                                                :required="!isset($vendor)"
                                                :existingImage="isset($vendor) && $vendor->banner ? $vendor->banner->path : null"
                                                placeholder="{{ trans('vendor::vendor.click_to_upload_banner') }}"
                                                recommendedSize="{{ trans('vendor::vendor.banner_recommended_size') }}"
                                                accept="image/jpeg,image/png,image/jpg,image/gif"
                                                aspectRatio="wide"
                                            />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Country Selection -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="country_id" class="form-label">
                                                    {{ trans('vendor::vendor.country') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select name="country_id" id="country_id" class="form-control select2">
                                                    <option value="">{{ trans('vendor::vendor.select_country') }}</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country['id'] }}"
                                                            {{ isset($vendor) && $vendor->country_id == $country['id'] ? 'selected' : '' }}>
                                                            {{ $country['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('country_id')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Commission -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="commission" class="form-label">
                                                    {{ trans('vendor::vendor.commission') }} (%)
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    name="commission"
                                                    id="commission"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    value="{{ (isset($vendor) && $vendor->commission) ? $vendor->commission->commission : old('commission') }}"
                                                    placeholder="{{ trans('vendor::vendor.enter_commission') }}"
                                                    @if(app()->getLocale() == 'ar') dir="rtl" @endif
                                                >
                                                @error('commission')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Vendor Type -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="type" class="form-label">
                                                    {{ trans('vendor::vendor.vendor_type') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select name="type" id="type" class="form-control select2">
                                                    <option value="">{{ trans('vendor::vendor.select_vendor_type') }}</option>
                                                    <option value="product" {{ (isset($vendor) && $vendor->type == 'product') || old('type') == 'product' ? 'selected' : '' }}>
                                                        {{ trans('vendor::vendor.product') }}
                                                    </option>
                                                    <option value="booking" {{ (isset($vendor) && $vendor->type == 'booking') || old('type') == 'booking' ? 'selected' : '' }}>
                                                        {{ trans('vendor::vendor.booking') }}
                                                    </option>
                                                    <option value="product_booking" {{ (isset($vendor) && $vendor->type == 'product_booking') || old('type') == 'product_booking' ? 'selected' : '' }}>
                                                        {{ trans('vendor::vendor.product_booking') }}
                                                    </option>
                                                </select>
                                                @error('type')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- Activities Selection -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="activities" class="form-label">
                                                    {{ trans('vendor::vendor.activities') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select name="activity_ids[]" id="activities" class="form-control select2" multiple>
                                                    @foreach($activities as $activity)
                                                        <option value="{{ $activity->id }}"
                                                            {{ isset($vendor) && $vendor->activities->contains($activity->id) ? 'selected' : '' }}>
                                                            {{ $activity->getTranslation('name', app()->getLocale()) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('activity_ids')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Active -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label d-block">
                                                    {{ trans('vendor::vendor.active') }}
                                                </label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="active"
                                                        name="active"
                                                        value="1"
                                                        {{ isset($vendor) && $vendor->active ? 'checked' : (!isset($vendor) ? 'checked' : '') }}
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Information -->
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h5 class="mb-4" style="">
                                        <i class="uil uil-search" style="font-size: 22px;"></i>
                                        {{ trans('vendor::vendor.seo_information') }}
                                    </h5>
                                    <div class="row">
                                        <!-- Meta Title Fields for each language -->
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="meta_title_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        {{ trans('vendor::vendor.meta_title') }} ({{ $language->name }})
                                                    @else
                                                        عنوان SEO باللغة العربية
                                                    @endif
                                                </label>
                                                <input
                                                    type="text"
                                                    name="translations[{{ $language->id }}][meta_title]"
                                                    id="meta_title_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    value="{{ isset($vendor) ? $vendor->getTranslation('meta_title', $language->code) : old('translations.'.$language->id.'.meta_title') }}"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل عنوان SEO' : trans('vendor::vendor.enter_meta_title') }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}
                                                >
                                                @error('translations.'.$language->id.'.meta_title')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <!-- Meta Description Fields for each language -->
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="meta_description_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        {{ trans('vendor::vendor.meta_description') }} ({{ $language->name }})
                                                    @else
                                                        وصف SEO باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea
                                                    name="translations[{{ $language->id }}][meta_description]"
                                                    id="meta_description_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="3"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}
                                                >{{ isset($vendor) ? $vendor->getTranslation('meta_description', $language->code) : old('translations.'.$language->id.'.meta_description') }}</textarea>
                                                @error('translations.'.$language->id.'.meta_description')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <!-- Meta Keywords Fields for each language -->
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="meta_keywords_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        {{ trans('vendor::vendor.meta_keywords') }} ({{ $language->name }})
                                                    @else
                                                        كلمات SEO المفتاحية باللغة العربية
                                                    @endif
                                                </label>
                                                <x-tags-input
                                                    name="translations[{{ $language->id }}][meta_keywords]"
                                                    :value="isset($vendor) ? $vendor->getMetaKeywordsString($language->code) : old('translations.'.$language->id.'.meta_keywords')"
                                                    placeholder="{{ $language->code == 'ar' ? 'اكتب كلمة مفتاحية واضغط انتر' : 'Type a keyword and press Enter...' }}"
                                                    rtl-placeholder="اكتب كلمة مفتاحية واضغط انتر"
                                                    language="{{ $language->code }}"
                                                    :allow-duplicates="true"
                                                    theme="primary"
                                                    size="md"
                                                    id="meta_keywords_{{ $language->code }}"
                                                />
                                                <small class="text-muted w-100 d-block" @if($language->code == 'ar') dir="rtl" style="text-align: right;" @endif>{{ $language->code == 'ar' ? 'اضغط انتر أو فاصلة لإنشاء كلمة مفتاحية' : 'Press Enter or comma to create a keyword' }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Step 2: Vendor Documents -->
                        <div class="wizard-step-content" data-step="2" style="display: none;">
                            <h5 class="mb-4" style="background: var(--color-primary); color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 0;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="uil uil-file-alt" style="font-size: 22px;"></i>
                                    {{ trans('vendor::vendor.vendor_documents') }}
                                </div>
                                <button type="button" id="addDocument" class="btn btn-squared" style="background: #ffffff; color: #0056B7; border: none; font-weight: 500;">
                                    <i class="uil uil-plus"></i> {{ trans('vendor::vendor.add_document') }}
                                </button>
                            </h5>
                            <div id="documentsContainer">
                                <!-- Documents will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Step 3: Vendor Account Details -->
                        <div class="wizard-step-content" data-step="3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-4" style="background: #0056B7; color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                                        <i class="uil uil-user-circle" style="font-size: 22px;"></i>
                                        {{ trans('vendor::vendor.vendor_account_details') }}
                                    </h5>
                                    <div class="row">
                                        <!-- Email -->
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="email" class="form-label">
                                                    {{ trans('vendor::vendor.email') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="email"
                                                    name="email"
                                                    id="email"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ trans('vendor::vendor.enter_email') }}"
                                                    value="{{ isset($vendor) ? $vendor->user->email ?? '' : old('email') }}"
                                                    @if(app()->getLocale() == 'ar') dir="rtl" @endif
                                                >
                                                @error('email')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- Password -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="password" class="form-label">
                                                    {{ trans('vendor::vendor.password') }}
                                                    @if(!isset($vendor))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input
                                                    type="password"
                                                    name="password"
                                                    id="password"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ trans('vendor::vendor.enter_password') }}"
                                                >
                                                <small class="text-muted">
                                                    @if(isset($vendor))
                                                        {{ trans('vendor::vendor.leave_empty_to_keep_current_password') }}
                                                    @else
                                                        {{ trans('vendor::vendor.password_min_8') }}
                                                    @endif
                                                </small>
                                                @error('password')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="password_confirmation" class="form-label">
                                                    {{ trans('vendor::vendor.confirm_password') }}
                                                    @if(!isset($vendor))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input
                                                    type="password"
                                                    name="password_confirmation"
                                                    id="password_confirmation"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ trans('vendor::vendor.confirm_password') }}"
                                                >
                                                @error('password_confirmation')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" id="prevBtn" class="btn btn-light btn-squared" style="display: none;">
                                <i class="uil uil-arrow-left"></i> {{ trans('vendor::vendor.previous') }}
                            </button>
                            <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-squared">
                                <i class="uil uil-times"></i> {{ trans('vendor::vendor.cancel') }}
                            </a>
                            <button type="button" id="nextBtn" class="btn btn-primary btn-squared">
                                {{ trans('vendor::vendor.next') }}
                                @if(app()->getLocale() == 'en')
                                    <i class="uil uil-arrow-right"></i>
                                @else
                                    <i class="uil uil-arrow-left"></i>
                                @endif
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-success btn-squared" style="display: none;">
                                <i class="uil uil-check"></i> {{ isset($vendor) ? trans('vendor::vendor.update_vendor') : trans('vendor::vendor.create_vendor_button') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loading Overlay Component --}}
<x-loading-overlay
    loadingText="{{ trans('vendor::vendor.creating_vendor') }}"
    loadingSubtext="{{ trans('vendor::vendor.please_wait') }}"
/>

{{-- Hidden Templates for JavaScript --}}
<template id="document-row-template">
    <div class="document-row mb-4 p-4 border rounded" data-document-index="__INDEX__">
        <div class="row">
            @foreach($languages as $language)
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label class="il-gray fs-14 fw-500 mb-10 w-100 {{ $language->rtl ? 'text-end' : '' }}">
                        @if($language->code == 'en')
                            {{ trans('vendor::vendor.document_name') }} ({{ $language->name }})
                        @else
                            الاسم باللغة العربية
                        @endif
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="documents[__INDEX__][translations][{{ $language->id }}][name]"
                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                        placeholder="{{ trans('vendor::vendor.eg_business_license') }}"
                        {{ $language->rtl ? 'dir=rtl' : '' }}
                    >
                </div>
            </div>
            @endforeach

            <div class="col-md-12 mb-3">
                <div class="form-group">
                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.document_file') }} <span class="text-danger">*</span></label>
                    <div class="image-upload-wrapper">
                        <div class="image-preview-container banner-preview" id="__UNIQUEID__-preview-container" data-target="__UNIQUEID__">
                            <div class="image-placeholder" id="__UNIQUEID__-placeholder">
                                <i class="uil uil-file-upload-alt"></i>
                                <p>{{ trans('vendor::vendor.click_to_upload_document') }}</p>
                                <small>{{ trans('vendor::vendor.accepted_document_types') }}</small>
                            </div>
                            <div class="image-overlay">
                                <button type="button" class="btn-change-image" data-target="__UNIQUEID__">
                                    <i class="uil uil-camera"></i> {{ trans('common.change') ?? 'Change' }}
                                </button>
                                <button type="button" class="btn-remove-image" data-target="__UNIQUEID__" style="display: none;">
                                    <i class="uil uil-trash-alt"></i> {{ trans('common.remove') ?? 'Remove' }}
                                </button>
                            </div>
                        </div>
                        <input type="file"
                               class="d-none image-file-input"
                               id="__UNIQUEID__"
                               name="documents[__INDEX__][file]"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               data-preview="__UNIQUEID__">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="button" class="btn btn-danger btn-sm btn-squared remove-document-row">
                <i class="uil uil-trash-alt"></i> {{ trans('vendor::vendor.remove') }}
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<!-- Vendor Form Configuration (Data Only) -->
<script>
window.vendorFormConfig = {
    selectPlaceholder: '{{ trans("vendor::vendor.select_option") }}',
    uploadText: '{{ trans("vendor::vendor.click_to_upload_document") }}',
    notProvided: '{{ trans("vendor::vendor.not_provided") }}',
    noLogoUploaded: '{{ trans("vendor::vendor.no_logo_uploaded") }}',
    noBannerUploaded: '{{ trans("vendor::vendor.no_banner_uploaded") }}',
    noDocumentsUploaded: '{{ trans("vendor::vendor.no_documents_uploaded") }}',
    vendorCreated: '{{ trans("vendor::vendor.vendor_created_successfully") }}',
    creatingVendor: '{{ trans("vendor::vendor.creating_vendor") }}',
    updatingVendor: '{{ trans("vendor::vendor.updating_vendor") }}',
    redirecting: '{{ trans("vendor::vendor.redirecting") }}',
    errorOccurred: '{{ trans("vendor::vendor.error_occurred") }}',
    validationError: '{{ trans("vendor::vendor.validation_error") }}',
    errorLabel: '{{ trans("vendor::vendor.error") }}',
    indexRoute: '{{ route("admin.vendors.index") }}',
    metaTitle: '{{ trans("vendor::vendor.meta_title") }}',
    metaDescription: '{{ trans("vendor::vendor.meta_description") }}',
    metaKeywords: '{{ trans("vendor::vendor.meta_keywords") }}',
    activeLabel: '{{ trans("vendor::vendor.active") }}',
    inactiveLabel: '{{ trans("vendor::vendor.inactive") }}',
    languages: [
        @foreach($languages as $language)
        {
            id: {{ $language->id }},
            code: '{{ $language->code }}',
            name: '{{ $language->name }}'
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
};
</script>

<!-- Vendor Form External JavaScript (All Logic) -->
@vite(['Modules/Vendor/resources/assets/js/vendor-form.js'])
@endpush
