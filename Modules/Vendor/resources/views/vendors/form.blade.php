@extends('layout.app')

@php
    $vendorRequest = null;
    if (request('vendor_request_id')) {
        $vendorRequest = \Modules\Vendor\app\Models\VendorRequest::where('id', request('vendor_request_id'))
            ->where('status', 'pending')
            ->first();
    }
@endphp
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
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                    [
                        'title' => isset($vendor)
                            ? trans('vendor::vendor.edit_vendor')
                            : trans('vendor::vendor.create_vendor'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ isset($vendor) ? trans('vendor::vendor.edit_vendor') : trans('vendor::vendor.create_vendor') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Wizard Navigation -->
                        <x-wizard :steps="[
                            trans('vendor::vendor.vendor_information'),
                            trans('vendor::vendor.vendor_documents'),
                            trans('vendor::vendor.vendor_account_details'),
                        ]" :currentStep="1" />

                        <!-- Validation Alerts Container -->
                        <div id="validation-alerts-container" class="mb-3"></div>

                        <!-- Form -->
                        <form id="vendorForm" method="POST"
                            action="{{ isset($vendor) ? route('admin.vendors.update', $vendor->id) : route('admin.vendors.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($vendor))
                                @method('PUT')
                            @endif

                            <!-- Hidden field to store vendor request ID -->
                            @if ($vendorRequest)
                                <input type="hidden" name="vendor_request_id" value="{{ $vendorRequest->id }}">
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
                                            @foreach ($languages as $language)
                                                <div
                                                    class="col-md-6 mb-3 @if (app()->getLocale() == 'ar') {{ $language->code == 'ar' ? 'order-1' : 'order-2' }} @else {{ $language->code == 'en' ? 'order-1' : 'order-2' }} @endif">
                                                    <div class="form-group">
                                                        <label for="name_{{ $language->code }}" class="form-label w-100"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>
                                                            @if ($language->code == 'ar')
                                                                اسم التاجر
                                                            @else
                                                                Vendor Name
                                                            @endif
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" name="translations[{{ $language->id }}][name]"
                                                            id="name_{{ $language->code }}"
                                                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                            value="{{ isset($vendor) ? $vendor->getTranslation('name', $language->code) : old('translations.' . $language->id . '.name') ?? ($language->code == 'en' && $vendorRequest ? $vendorRequest->company_name : '') }}"
                                                            placeholder="{{ $language->code == 'ar' ? 'أدخل اسم التاجر' : 'Vendor Name' }}"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>
                                                        @error('translations.' . $language->id . '.name')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="row">
                                            <!-- Description Fields for each language -->
                                            @foreach ($languages as $language)
                                                <div
                                                    class="col-md-6 mb-3 @if (app()->getLocale() == 'ar') {{ $language->code == 'ar' ? 'order-1' : 'order-2' }} @else {{ $language->code == 'en' ? 'order-1' : 'order-2' }} @endif">
                                                    <div class="form-group">
                                                        <label for="description_{{ $language->code }}"
                                                            class="form-label w-100"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>
                                                            @if ($language->code == 'ar')
                                                                الوصف
                                                            @else
                                                                Description
                                                            @endif
                                                        </label>
                                                        <textarea name="translations[{{ $language->id }}][description]" id="description_{{ $language->code }}"
                                                            class="form-control ih-medium ip-gray radius-xs b-light px-15" rows="4"
                                                            placeholder="{{ $language->code == 'ar' ? 'أدخل وصف التاجر' : 'Enter vendor description' }}"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>{{ isset($vendor) ? $vendor->getTranslation('description', $language->code) : old('translations.' . $language->id . '.description') }}</textarea>
                                                        @error('translations.' . $language->id . '.description')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="row">
                                            <!-- Departments -->
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label for="departments" class="form-label">
                                                        {{ app()->getLocale() == 'ar' ? 'الأقسام' : 'Departments' }} <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <select name="departments[]" id="departments"
                                                        class="form-control select2" multiple required>
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department->id }}"
                                                                {{ collect(old('departments'))->contains($department->id) ? 'selected' : '' }}
                                                                {{ isset($vendor) && $vendor->departments->contains($department->id) ? 'selected' : '' }}>
                                                                {{ $department->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('departments')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Phone Number -->
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="phone" class="form-label">
                                                        {{ trans('common.phone') }}
                                                    </label>
                                                    <input type="text" name="phone" id="phone"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('phone') is-invalid @enderror"
                                                        value="{{ isset($vendor) ? $vendor->phone : old('phone') ?? ($vendorRequest ? $vendorRequest->phone : '') }}"
                                                        placeholder="{{ trans('common.phone') }}">
                                                    @error('phone')
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
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="active" name="active" value="1"
                                                            {{ isset($vendor) && $vendor->active ? 'checked' : (!isset($vendor) ? 'checked' : '') }}>
                                                    </div>
                                                </div>
                                            </div>
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
                                                @php
                                                    $existingLogo = null;
                                                    if (isset($vendor) && $vendor->logo) {
                                                        $existingLogo = $vendor->logo->path;
                                                    } elseif ($vendorRequest && $vendorRequest->company_logo) {
                                                        $existingLogo = $vendorRequest->company_logo;
                                                    }
                                                @endphp
                                                <x-image-upload id="logo" name="logo"
                                                    label="{{ trans('vendor::vendor.logo') }} ({{ trans('vendor::vendor.logo_recommended_size') }})"
                                                    :required="!isset($vendor) &&
                                                        (!$vendorRequest || !$vendorRequest->company_logo)" :existingImage="$existingLogo"
                                                    placeholder="{{ trans('vendor::vendor.click_to_upload_logo') }}"
                                                    recommendedSize="{{ trans('vendor::vendor.logo_recommended_size') }}"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif" aspectRatio="logo" />
                                            </div>

                                            <!-- Banner Upload -->
                                            <div class="col-md-6 mb-3">
                                                <x-image-upload id="banner" name="banner"
                                                    label="{{ trans('vendor::vendor.banner') }} ({{ trans('vendor::vendor.banner_recommended_size') }})"
                                                    :required="!isset($vendor)" :existingImage="isset($vendor) && $vendor->banner
                                                        ? $vendor->banner->path
                                                        : null"
                                                    placeholder="{{ trans('vendor::vendor.click_to_upload_banner') }}"
                                                    recommendedSize="{{ trans('vendor::vendor.banner_recommended_size') }}"
                                                    accept="image/jpeg,image/png,image/jpg,image/gif"
                                                    aspectRatio="wide" />
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <h5 class="fw-500">
                                                        {{ trans('catalogmanagement::brand.social_media') }}
                                                    </h5>

                                                    {{-- Facebook URL --}}
                                                    <div class="col-md-6 mb-25">
                                                        <div class="form-group">
                                                            <label for="facebook_url" class="il-gray fs-14 fw-500 mb-10">
                                                                {{ trans('catalogmanagement::brand.facebook_url') }}
                                                            </label>
                                                            <input type="url"
                                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 @error('facebook_url') is-invalid @enderror"
                                                                id="facebook_url" name="facebook_url"
                                                                value="{{ old('facebook_url', $vendor->facebook_url ?? '') }}"
                                                                placeholder="https://facebook.com/your-brand">
                                                            @error('facebook_url')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
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
                                                                id="twitter_url" name="twitter_url"
                                                                value="{{ old('twitter_url', $vendor->twitter_url ?? '') }}"
                                                                placeholder="https://twitter.com/your-brand">
                                                            @error('twitter_url')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
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
                                                                id="instagram_url" name="instagram_url"
                                                                value="{{ old('instagram_url', $vendor->instagram_url ?? '') }}"
                                                                placeholder="https://instagram.com/your-brand">
                                                            @error('instagram_url')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
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
                                                                id="linkedin_url" name="linkedin_url"
                                                                value="{{ old('linkedin_url', $vendor->linkedin_url ?? '') }}"
                                                                placeholder="https://linkedin.com/company/your-brand">
                                                            @error('linkedin_url')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
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
                                                                id="pinterest_url" name="pinterest_url"
                                                                value="{{ old('pinterest_url', $vendor->pinterest_url ?? '') }}"
                                                                placeholder="https://pinterest.com/your-brand">
                                                            @error('pinterest_url')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
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
                                            @foreach ($languages as $language)
                                                <div
                                                    class="col-md-6 mb-3 @if (app()->getLocale() == 'ar') {{ $language->code == 'ar' ? 'order-1' : 'order-2' }} @else {{ $language->code == 'en' ? 'order-1' : 'order-2' }} @endif">
                                                    <div class="form-group">
                                                        <label for="meta_title_{{ $language->code }}"
                                                            class="form-label w-100"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>
                                                            @if ($language->code == 'ar')
                                                                عنوان SEO
                                                            @else
                                                                Meta Title SEO
                                                            @endif
                                                        </label>
                                                        <input type="text"
                                                            name="translations[{{ $language->id }}][meta_title]"
                                                            id="meta_title_{{ $language->code }}"
                                                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                            value="{{ isset($vendor) ? $vendor->getTranslation('meta_title', $language->code) : old('translations.' . $language->id . '.meta_title') }}"
                                                            placeholder="{{ $language->code == 'ar' ? 'أدخل عنوان SEO' : 'Meta Title SEO' }}"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>
                                                        @error('translations.' . $language->id . '.meta_title')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="row">
                                            <!-- Meta Description Fields for each language -->
                                            @foreach ($languages as $language)
                                                <div
                                                    class="col-md-6 mb-3 @if (app()->getLocale() == 'ar') {{ $language->code == 'ar' ? 'order-1' : 'order-2' }} @else {{ $language->code == 'en' ? 'order-1' : 'order-2' }} @endif">
                                                    <div class="form-group">
                                                        <label for="meta_description_{{ $language->code }}"
                                                            class="form-label w-100"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>
                                                            @if ($language->code == 'ar')
                                                                وصف SEO
                                                            @else
                                                                Meta Description SEO
                                                            @endif
                                                        </label>
                                                        <textarea name="translations[{{ $language->id }}][meta_description]" id="meta_description_{{ $language->code }}"
                                                            class="form-control ih-medium ip-gray radius-xs b-light px-15" rows="3"
                                                            placeholder="{{ $language->code == 'ar' ? 'أدخل وصف SEO' : 'Enter SEO description' }}"
                                                            @if ($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')) dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>{{ isset($vendor) ? $vendor->getTranslation('meta_description', $language->code) : old('translations.' . $language->id . '.meta_description') }}</textarea>
                                                        @error('translations.' . $language->id . '.meta_description')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="row">
                                            <!-- Meta Keywords Fields for each language -->
                                            @foreach ($languages as $language)
                                                <div
                                                    class="col-md-6 mb-3 @if (app()->getLocale() == 'ar') {{ $language->code == 'ar' ? 'order-1' : 'order-2' }} @else {{ $language->code == 'en' ? 'order-1' : 'order-2' }} @endif">
                                                    <div class="form-group">
                                                        <label for="meta_keywords_{{ $language->code }}"
                                                            class="form-label w-100"
                                                            @if ($language->code == 'ar') dir="rtl"
                                                    @else
                                                        dir="ltr" @endif>
                                                            @if ($language->code == 'ar')
                                                                كلمات SEO المفتاحية
                                                            @else
                                                                Meta Keywords SEO
                                                            @endif
                                                        </label>
                                                        <div>
                                                            <x-tags-input
                                                                name="translations[{{ $language->id }}][meta_keywords]"
                                                                :value="isset($vendor)
                                                                    ? $vendor->getMetaKeywordsString($language->code)
                                                                    : old(
                                                                        'translations.' .
                                                                            $language->id .
                                                                            '.meta_keywords',
                                                                    )"
                                                                placeholder="{{ $language->code == 'ar' ? 'اكتب كلمة مفتاحية واضغط انتر' : 'Type a keyword and press Enter...' }}"
                                                                rtl-placeholder="اكتب كلمة مفتاحية واضغط انتر"
                                                                helpText="{{ $language->code == 'ar' ? 'اضغط انتر أو فاصلة لإنشاء كلمة مفتاحية' : 'Press Enter or comma to create a keyword' }}"
                                                                language="{{ $language->code }}" :allow-duplicates="true"
                                                                theme="primary" size="md"
                                                                id="meta_keywords_{{ $language->code }}"
                                                                dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Step 2: Vendor Documents -->
                            <div class="wizard-step-content" data-step="2" style="display: none;">
                                <h5 class="mb-4"
                                    style="background: var(--color-primary); color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 0;"
                                    @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <i class="uil uil-file-alt" style="font-size: 22px;"></i>
                                        @if (app()->getLocale() == 'ar')
                                            مستندات التاجر
                                        @else
                                            Vendor Documents
                                        @endif
                                    </div>
                                    <button type="button" id="addDocument" class="btn btn-squared"
                                        style="background: #ffffff; color: #0056B7; border: none; font-weight: 500;"
                                        @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                        <i class="uil uil-plus"></i>
                                        @if (app()->getLocale() == 'ar')
                                            إضافة مستند
                                        @else
                                            Add Document
                                        @endif
                                    </button>
                                </h5>
                                <div id="documentsContainer"
                                    @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                    <!-- Documents will be added here dynamically -->
                                    <!-- Document fields will inherit RTL/LTR direction from this container -->
                                </div>
                            </div>

                            <!-- Step 3: Vendor Account Details -->
                            <div class="wizard-step-content" data-step="3" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-4"
                                            style="background: #0056B7; color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px;"
                                            @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                            <i class="uil uil-user-circle" style="font-size: 22px;"></i>
                                            @if (app()->getLocale() == 'ar')
                                                تفاصيل حساب التاجر
                                            @else
                                                Vendor Account Details
                                            @endif
                                        </h5>
                                        <div class="row">
                                            <!-- Email -->
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label for="email" class="form-label"
                                                        @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                                        @if (app()->getLocale() == 'ar')
                                                            البريد الإلكتروني
                                                        @else
                                                            Email
                                                        @endif
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" name="email" id="email"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                        placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل البريد الإلكتروني' : 'Enter email address' }}"
                                                        value="{{ isset($vendor) ? $vendor->user->email ?? '' : old('email') ?? ($vendorRequest ? $vendorRequest->email : '') }}"
                                                        @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
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
                                                    <label for="password" class="form-label"
                                                        @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                                        @if (app()->getLocale() == 'ar')
                                                            كلمة المرور
                                                        @else
                                                            Password
                                                        @endif
                                                        @if (!isset($vendor))
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    <input type="password" name="password" id="password"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                        placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل كلمة المرور' : 'Enter password' }}"
                                                        @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                                    <small class="text-muted"
                                                        @if (app()->getLocale() == 'ar') dir="rtl" style="text-align: right;" @endif>
                                                        @if (isset($vendor))
                                                            @if (app()->getLocale() == 'ar')
                                                                اتركه فارغاً للاحتفاظ بكلمة المرور الحالية
                                                            @else
                                                                Leave empty to keep current password
                                                            @endif
                                                        @else
                                                            @if (app()->getLocale() == 'ar')
                                                                كلمة المرور يجب أن تكون 8 أحرف على الأقل
                                                            @else
                                                                Password must be at least 8 characters
                                                            @endif
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
                                                    <label for="password_confirmation" class="form-label"
                                                        @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
                                                        @if (app()->getLocale() == 'ar')
                                                            تأكيد كلمة المرور
                                                        @else
                                                            Confirm Password
                                                        @endif
                                                        @if (!isset($vendor))
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    <input type="password" name="password_confirmation"
                                                        id="password_confirmation"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                        placeholder="{{ app()->getLocale() == 'ar' ? 'أعد إدخال كلمة المرور' : 'Confirm password' }}"
                                                        @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
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
                                <button type="button" id="prevBtn" class="btn btn-light btn-squared"
                                    style="display: none;">
                                    <i class="uil uil-arrow-left"></i> {{ trans('vendor::vendor.previous') }}
                                </button>
                                <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-squared">
                                    <i class="uil uil-times"></i> {{ trans('vendor::vendor.cancel') }}
                                </a>
                                <button type="button" id="nextBtn" class="btn btn-primary btn-squared">
                                    {{ trans('vendor::vendor.next') }}
                                    @if (app()->getLocale() == 'en')
                                        <i class="uil uil-arrow-right"></i>
                                    @else
                                        <i class="uil uil-arrow-left"></i>
                                    @endif
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-success btn-squared"
                                    style="display: none;">
                                    <i class="uil uil-check"></i>
                                    {{ isset($vendor) ? trans('vendor::vendor.update_vendor') : trans('vendor::vendor.create_vendor_button') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Overlay Component --}}
    <x-loading-overlay loadingText="{{ trans('vendor::vendor.creating_vendor') }}"
        loadingSubtext="{{ trans('vendor::vendor.please_wait') }}" />

    {{-- Hidden Templates for JavaScript --}}
    <template id="document-row-template">
        <div class="document-row mb-4 p-4 border rounded" data-document-index="__INDEX__">
            <div class="row">
                @foreach ($languages as $language)
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="il-gray fs-14 fw-500 mb-10 w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                @if ($language->code == 'en')
                                    {{ trans('vendor::vendor.document_name') }} ({{ $language->name }})
                                @else
                                    الاسم باللغة العربية
                                @endif
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="documents[__INDEX__][translations][{{ $language->id }}][name]"
                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                placeholder="{{ $language->code == 'ar' ? 'مثال: رخصة تجارية' : 'e.g., Business License' }}"
                                {{ $language->rtl ? 'dir=rtl' : '' }}>
                        </div>
                    </div>
                @endforeach

                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.document_file') }} <span
                                class="text-danger">*</span></label>
                        <div class="image-upload-wrapper">
                            <div class="image-preview-container banner-preview" id="__UNIQUEID__-preview-container"
                                data-target="__UNIQUEID__">
                                <div class="image-placeholder" id="__UNIQUEID__-placeholder">
                                    <i class="uil uil-file-upload-alt"></i>
                                    <p>{{ trans('vendor::vendor.click_to_upload_document') }}</p>
                                    <small>{{ trans('vendor::vendor.accepted_document_types') }}</small>
                                </div>
                                <div class="image-overlay">
                                    <button type="button" class="btn-change-image" data-target="__UNIQUEID__">
                                        <i class="uil uil-camera"></i> {{ trans('common.change') ?? 'Change' }}
                                    </button>
                                    <button type="button" class="btn-remove-image" data-target="__UNIQUEID__"
                                        style="display: none;">
                                        <i class="uil uil-trash-alt"></i> {{ trans('common.remove') ?? 'Remove' }}
                                    </button>
                                </div>
                            </div>
                            <input type="file" class="d-none image-file-input" id="__UNIQUEID__"
                                name="documents[__INDEX__][file]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
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
            selectPlaceholder: '{{ trans('vendor::vendor.select_option') }}',
            uploadText: '{{ trans('vendor::vendor.click_to_upload_document') }}',
            notProvided: '{{ trans('vendor::vendor.not_provided') }}',
            noLogoUploaded: '{{ trans('vendor::vendor.no_logo_uploaded') }}',
            noBannerUploaded: '{{ trans('vendor::vendor.no_banner_uploaded') }}',
            noDocumentsUploaded: '{{ trans('vendor::vendor.no_documents_uploaded') }}',
            vendorCreated: '{{ trans('vendor::vendor.vendor_created_successfully') }}',
            creatingVendor: '{{ trans('vendor::vendor.creating_vendor') }}',
            updatingVendor: '{{ trans('vendor::vendor.updating_vendor') }}',
            redirecting: '{{ trans('vendor::vendor.redirecting') }}',
            errorOccurred: '{{ trans('vendor::vendor.error_occurred') }}',
            validationError: '{{ app()->getLocale() == 'ar' ? 'يرجى ملء جميع الحقول المطلوبة قبل الانتقال إلى الخطوة التالية.' : 'Please fill in all required fields before proceeding to the next step.' }}',
            errorLabel: '{{ app()->getLocale() == 'ar' ? 'خطأ' : 'Error' }}',
            indexRoute: '{{ route('admin.vendors.index') }}',
            metaTitle: '{{ trans('vendor::vendor.meta_title') }}',
            metaDescription: '{{ trans('vendor::vendor.meta_description') }}',
            metaKeywords: '{{ trans('vendor::vendor.meta_keywords') }}',
            activeLabel: '{{ trans('vendor::vendor.active') }}',
            inactiveLabel: '{{ trans('vendor::vendor.inactive') }}',
            // Error Messages Translations
            errorMessages: {
                // Step 1 - Vendor Information
                vendorNameRequired: '{{ app()->getLocale() == 'ar' ? 'اسم التاجر مطلوب' : 'Vendor name is required' }}',
                vendorNameEnRequired: '{{ app()->getLocale() == 'ar' ? 'اسم التاجر باللغة الإنجليزية مطلوب' : 'Vendor name in English is required' }}',
                vendorNameArRequired: '{{ app()->getLocale() == 'ar' ? 'اسم التاجر باللغة العربية مطلوب' : 'Vendor name in Arabic is required' }}',
                departmentsRequired: '{{ app()->getLocale() == 'ar' ? 'يرجى اختيار قسم واحد على الأقل' : 'Please select at least one department' }}',
                @if (!$vendorRequest)
                    logoRequired: '{{ app()->getLocale() == 'ar' ? 'الشعار مطلوب' : 'Logo is required' }}',
                @endif
                bannerRequired: '{{ app()->getLocale() == 'ar' ? 'البانر مطلوب' : 'Banner is required' }}',

                // Step 2 - Documents
                documentsRequired: '{{ app()->getLocale() == 'ar' ? 'يجب إضافة مستند واحد على الأقل' : 'At least one document is required' }}',
                documentNameEnRequired: '{{ app()->getLocale() == 'ar' ? 'اسم المستند باللغة الإنجليزية مطلوب' : 'Document name in English is required' }}',
                documentNameArRequired: '{{ app()->getLocale() == 'ar' ? 'اسم المستند باللغة العربية مطلوب' : 'Document name in Arabic is required' }}',
                documentFileRequired: '{{ app()->getLocale() == 'ar' ? 'ملف المستند مطلوب' : 'Document file is required' }}',

                // Step 3 - Account Details
                emailRequired: '{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني مطلوب' : 'Email is required' }}',
                emailInvalid: '{{ app()->getLocale() == 'ar' ? 'يرجى إدخال عنوان بريد إلكتروني صالح' : 'Please enter a valid email address' }}',
                passwordRequired: '{{ app()->getLocale() == 'ar' ? 'كلمة المرور مطلوبة' : 'Password is required' }}',

                // General validation messages
                pleaseCorrectErrors: '{{ app()->getLocale() == 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}'
            },
            languages: [
                @foreach ($languages as $language)
                    {
                        id: {{ $language->id }},
                        code: '{{ $language->code }}',
                        name: '{{ $language->name }}'
                    }
                    {{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
        };
    </script>

    <!-- Vendor Form External JavaScript (All Logic) -->
    @vite(['Modules/Vendor/resources/assets/js/vendor-form.js'])
@endpush
