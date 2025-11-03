@extends('layout.app')

@push('styles')
<!-- Vendor Form Custom CSS -->
@vite(['Modules/Vendor/resources/assets/css/vendor-form.css'])
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
                        trans('vendor::vendor.vendor_account_details'),
                        trans('vendor::vendor.review_submit')
                    ]" :currentStep="1" />

                    <!-- Form -->
                    <form id="vendorForm" method="POST" action="{{ isset($vendor) ? route('admin.vendors.update', $vendor->id) : route('admin.vendors.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($vendor))
                            @method('PUT')
                        @endif

                        <!-- Step 1: Vendor Information -->
                        <div class="wizard-step-content active" data-step="1">
                            <h5 class="mb-4 mt-4">{{ trans('vendor::vendor.vendor_information') }}</h5>

                            <div class="row">
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
                                            class="form-control"
                                            value="{{ isset($vendor) ? $vendor->getTranslation('name', $language->code) : old('translations.'.$language->id.'.name') }}"
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
                                            class="form-control"
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

                            <!-- Logo and Banner Section -->
                            <h5 class="mb-3 mt-4">{{ trans('vendor::vendor.branding') }}</h5>
                            
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
                                            class="form-control"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            value="{{ (isset($vendor) && $vendor->commission) ? $vendor->commission->commission : old('commission') }}"
                                            placeholder="{{ trans('vendor::vendor.enter_commission') }}"
                                        >
                                        @error('commission')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Active -->
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="active" class="form-label d-block">
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

                            <div class="row">
                                <!-- Activities Selection -->
                                <div class="col-md-12 mb-3">
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
                            </div>

                            <!-- SEO Information -->
                            <h5 class="mb-3 mt-4">{{ trans('vendor::vendor.seo_information') }}</h5>
                            
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
                                            class="form-control"
                                            value="{{ isset($vendor) ? $vendor->getTranslation('meta_title', $language->code) : old('translations.'.$language->id.'.meta_title') }}"
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
                                            class="form-control"
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
                                        <input 
                                            type="text" 
                                            name="translations[{{ $language->id }}][meta_keywords]"
                                            id="meta_keywords_{{ $language->code }}"
                                            class="form-control"
                                            value="{{ isset($vendor) ? $vendor->getTranslation('meta_keywords', $language->code) : old('translations.'.$language->id.'.meta_keywords') }}"
                                            {{ $language->rtl ? 'dir=rtl' : '' }}
                                        >
                                        <small class="text-muted">{{ trans('vendor::vendor.separate_keywords_commas') }}</small>
                                        @error('translations.'.$language->id.'.meta_keywords')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 2: Vendor Documents -->
                        <div class="wizard-step-content" data-step="2" style="display: none;">
                            <div class="d-flex justify-content-between">
                                <h5 class="mb-4 mt-4">{{ trans('vendor::vendor.vendor_documents') }}</h5>
                                <button type="button" id="addDocument" class="btn btn-success btn-squared">
                                    <i class="uil uil-plus"></i> {{ trans('vendor::vendor.add_document') }}
                                </button>
                            </div>
                            <div id="documentsContainer">
                                <!-- Documents will be added here dynamically -->
                            </div>
                            
                            
                        </div>

                        <!-- Step 3: Vendor Account Details -->
                        <div class="wizard-step-content" data-step="3" style="display: none;">
                            <h5 class="mb-4 mt-4">{{ trans('vendor::vendor.vendor_account_details') }}</h5>
                            
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
                                            class="form-control"
                                            placeholder="{{ trans('vendor::vendor.enter_email') }}"
                                            value="{{ isset($vendor) ? $vendor->user->email ?? '' : old('email') }}"
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
                                            class="form-control"
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
                                            class="form-control"
                                            placeholder="{{ trans('vendor::vendor.confirm_password') }}"
                                        >
                                        @error('password_confirmation')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div class="wizard-step-content" data-step="4" style="display: none;">
                            <h5 class="mb-4">{{ trans('vendor::vendor.review_submit') }}</h5>
                            
                            <!-- Validation Errors Alert -->
                            <div id="review-validation-errors" class="alert alert-danger" style="display: none; flex-direction: column;">
                                <h6 class="alert-heading"><i class="uil uil-exclamation-triangle"></i> {{ trans('vendor::vendor.validation_errors') }}</h6>
                                <div id="review-errors-list"></div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="uil uil-info-circle"></i> {{ trans('vendor::vendor.please_review_info') }}
                            </div>

                            <!-- Review: Vendor Information -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="uil uil-info-circle"></i> {{ trans('vendor::vendor.vendor_information') }}
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary float-end edit-step" data-step="1">
                                        <i class="uil uil-edit"></i> {{ trans('common.edit') }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ trans('vendor::vendor.name') }} ({{ $language->name }}):</strong>
                                            <span class="review-name-{{ $language->code }}">-</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-2">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ trans('vendor::vendor.description') }} ({{ $language->name }}):</strong>
                                            <p class="review-description-{{ $language->code }} text-muted mb-0">-</p>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ trans('vendor::vendor.logo') }}:</strong>
                                            <div class="review-logo">
                                                <span class="text-muted">{{ trans('vendor::vendor.no_logo_uploaded') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ trans('vendor::vendor.banner') }}:</strong>
                                            <div class="review-banner">
                                                <span class="text-muted">{{ trans('vendor::vendor.no_banner_uploaded') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ trans('vendor::vendor.country') }}:</strong>
                                            <span class="review-country">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ trans('vendor::vendor.commission') }}:</strong>
                                            <span class="review-commission">-</span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ trans('vendor::vendor.status') }}:</strong>
                                            <span class="review-active">
                                                <span class="badge badge-success">{{ trans('vendor::vendor.active') }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>{{ trans('vendor::vendor.activities') }}:</strong>
                                            <span class="review-activities">-</span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <strong>{{ trans('vendor::vendor.seo_information') }}:</strong>
                                            <span class="review-seo">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Review: Documents -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="uil uil-file-alt"></i> {{ trans('vendor::vendor.vendor_documents') }}
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary float-end edit-step" data-step="2">
                                        <i class="uil uil-edit"></i> {{ trans('common.edit') }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="review-documents">
                                        <p class="text-muted">{{ trans('vendor::vendor.no_documents_uploaded') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Review: Account Details -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="uil uil-user"></i> {{ trans('vendor::vendor.vendor_account_details') }}
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary float-end edit-step" data-step="3">
                                        <i class="uil uil-edit"></i> {{ trans('common.edit') }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <strong>{{ trans('vendor::vendor.email') }}:</strong>
                                            <span class="review-email">-</span>
                                        </div>
                                        <div class="col-md-12">
                                            <strong>{{ trans('vendor::vendor.password') }}:</strong>
                                            <span class="review-password text-muted">
                                                {{ isset($vendor) ? trans('vendor::vendor.password_will_be_updated_if_provided') : trans('vendor::vendor.password_is_set') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-light btn-squared" style="display: none;">
                                <i class="uil uil-arrow-left"></i> {{ trans('vendor::vendor.previous') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-squared">
                                    <i class="uil uil-times"></i> {{ trans('vendor::vendor.cancel') }}
                                </a>
                                <button type="button" id="nextBtn" class="btn btn-primary btn-squared">
                                    {{ trans('vendor::vendor.next') }} <i class="uil uil-arrow-right"></i>
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-success btn-squared" style="display: none;">
                                    <i class="uil uil-check"></i> {{ isset($vendor) ? trans('vendor::vendor.update_vendor') : trans('vendor::vendor.create_vendor_button') }}
                                </button>
                            </div>
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
                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.document_file') }}</label>
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
    selectPlaceholder: '{{ trans("vendor::vendor.please_select_activity") }}',
    uploadText: '{{ trans("vendor::vendor.click_to_upload_document") }}',
    notProvided: '{{ trans("vendor::vendor.not_provided") }}',
    noLogoUploaded: '{{ trans("vendor::vendor.no_logo_uploaded") }}',
    noBannerUploaded: '{{ trans("vendor::vendor.no_banner_uploaded") }}',
    noDocumentsUploaded: '{{ trans("vendor::vendor.no_documents_uploaded") }}',
    vendorCreated: '{{ trans("vendor::vendor.vendor_created_successfully") }}',
    redirecting: '{{ trans("vendor::vendor.redirecting") }}',
    errorOccurred: '{{ trans("vendor::vendor.error_occurred") }}',
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
