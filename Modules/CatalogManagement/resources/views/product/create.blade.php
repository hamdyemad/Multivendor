@extends('layout.app')

@section('title')
{{ $title ?? (isset($product) ? __('catalogmanagement::product.edit_product') : __('catalogmanagement::product.create_product')) }}
@endsection

@push('styles')
@vite(['Modules/CatalogManagement/resources/assets/scss/product-form.scss'])

@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::product.products_management'), 'url' => route('admin.products.index')],
                ['title' => isset($product) ? __('catalogmanagement::product.edit_product') : __('catalogmanagement::product.create_product')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card product-form">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($product) ? __('catalogmanagement::product.edit_product') : __('catalogmanagement::product.create_product') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Wizard Navigation -->
                    <x-wizard :steps="[
                        __('common.basic_information'),
                        __('common.details'),
                        __('common.variant_configurations'),
                        __('common.seo')
                    ]" :currentStep="1" />

                    <!-- Validation Alerts Container -->
                    <div id="validation-alerts-container" class="mb-3"></div>

                    <!-- Form -->
                    <form id="productForm" method="POST" action="{{ isset($product) ? route('admin.products.update', $product->product ? $product->product->id : $product->id) : route('admin.products.store') }}" enctype="multipart/form-data" novalidate onkeydown="return event.key != 'Enter';">
                        @csrf
                        @if(isset($product))
                            @method('PUT')
                        @endif

                        <!-- Step 1: Product Information -->
                        <div class="wizard-step-content active" data-step="1">
                            <!-- Card 1: Product Information -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-info-circle"></i>
                                        {{ __('catalogmanagement::product.product_details') }}
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="title_{{ $language->code }}" class="form-label w-100 {{ (app()->getLocale() == 'ar') ? 'text-start' : '' }}">
                                                    {{ __('catalogmanagement::product.title') }} ({{ strtoupper($language->code) }})
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="translations[{{ $language->id }}][title]" id="title_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل عنوان المنتج' : 'Enter product title' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"
                                                    value="{{ isset($product) ? ($product->product && method_exists($product->product, 'getTranslation') ? $product->product->getTranslation('title', $language->code) : (method_exists($product, 'getTranslation') ? $product->getTranslation('title', $language->code) : '')) ?? '' : '' }}">
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-title" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="sku" class="form-label">{{ __('catalogmanagement::product.sku') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="sku" id="sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ __('catalogmanagement::product.sku') }}" value="{{ isset($product) ? $product->sku : '' }}">
                                                <div class="error-message text-danger" id="error-sku" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="points" class="form-label">{{ __('catalogmanagement::product.points') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="points" id="points" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" value="{{ isset($product) ? $product->points : 0 }}" placeholder="Enter points" required>
                                                <div class="error-message text-danger" id="error-points" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label d-block">{{ __('catalogmanagement::product.status') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                                    @if(isset($product) && ($product->product ? $product->product->is_active : $product->is_active)) checked @endif>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label d-block">{{ __('catalogmanagement::product.featured') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" @if(isset($product) && ($product->product ? $product->product->is_featured : $product->is_featured)) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Organization -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-sitemap"></i>
                                        {{ __('common.organization') }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="brand_id" class="form-label">{{ __('catalogmanagement::product.brand') }} <span class="text-danger">*</span></label>
                                                <select name="brand_id" id="brand_id" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{ $brand['id'] }}" {{ isset($product) && $product->product->brand_id == $brand['id'] ? 'selected' : '' }}>{{ $brand['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::vendorIds()))
                                            <!-- Hidden input for vendor users -->
                                            <input class="form-control" type="hidden" name="vendor_id" id="vendor_id" value="{{ auth()->user()->vendor->id ?? '' }}">
                                        @else
                                            <!-- Vendor select for admin users -->
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="vendor_id" class="form-label">
                                                        {{ __('catalogmanagement::product.vendor') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="vendor_id" id="vendor_id" class="form-control select2">
                                                        <option value="">{{ __('common.select_option') }}</option>
                                                        @foreach($vendors as $vendor)
                                                            <option value="{{ $vendor['id'] }}" {{ isset($product) && $product->vendor_id == $vendor['id'] ? 'selected' : '' }}>
                                                                {{ $vendor['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="error-message text-danger" id="error-vendor_id" style="display: none;"></div>
                                                </div>
                                            </div>
                                        @endif



                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="department_id" class="form-label">{{ __('catalogmanagement::product.department') }} <span class="text-danger">*</span></label>
                                                <select name="department_id" id="department_id" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="category_id" class="form-label">{{ __('catalogmanagement::product.category') }} <span class="text-danger">*</span></label>
                                                <select name="category_id" id="category_id" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="sub_category_id" class="form-label">{{ __('catalogmanagement::product.sub_category') }}</label>
                                                <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Logistics & Taxes -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-truck"></i>
                                        {{ __('common.logistics') }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="tax_id" class="form-label">{{ __('catalogmanagement::product.tax') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select name="tax_id" id="tax_id" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                    @foreach($taxes as $tax)
                                                        <option value="{{ $tax['id'] }}" {{ isset($product) && $product->tax_id == $tax['id'] ? 'selected' : '' }}>{{ $tax['name'] }} ({{ $tax['percentage'] }}%)</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="max_per_order" class="form-label">{{ __('catalogmanagement::product.max_per_order') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="max_per_order" id="max_per_order" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="1" placeholder="Enter max per order" value="{{ isset($product) ? ($product->max_per_order ?? ($product->product ? $product->product->max_per_order : null)) ?? '' : '' }}" required>
                                                <div class="error-message text-danger" id="error-max_per_order" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4: Product Tags -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-tag-alt"></i>
                                        {{ __('common.tags') }}
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="tags_{{ $language->code }}" class="form-label w-100
                                                     @if(app()->getLocale() == 'ar') dir='rtl' @endif
                                                     ">
                                                    {{ __('common.tags') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <div>
                                                    <x-tags-input
                                                        name="translations[{{ $language->id }}][tags]"
                                                        :value="isset($product) ? (isset($product->product) && method_exists($product->product, 'getTagsString') ? $product->product->getTagsString($language->code) : (method_exists($product, 'getTagsString') ? $product->getTagsString($language->code) : '')) : old('translations.'.$language->id.'.tags', '')"
                                                        placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب وسم واضغط انتر' : 'Type a tag and press Enter...' }}"
                                                        rtl-placeholder="اكتب وسم واضغط انتر"
                                                        language="{{ $language->code }}"
                                                        :allow-duplicates="true"
                                                        theme="primary"
                                                        size="md"
                                                        id="tags_{{ $language->code }}"
                                                        dir="{{ (app()->getLocale() == 'ar' && $language->code == 'ar') ? 'rtl' : 'ltr' }}"
                                                    />
                                                </div>
                                                <small class="text-muted w-100 d-block" @if($language->code == 'ar') dir="rtl" style="text-align: right;" @endif>{{ $language->code == 'ar' ? 'اضغط انتر أو فاصلة لإنشاء وسم' : 'Press Enter or comma to create a tag' }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Product Details -->
                        <div class="wizard-step-content" data-step="2" style="display: none;">

                            <!-- Main Product Image -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-image"></i>
                                        {{ __('catalogmanagement::product.main_image') }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <x-image-upload
                                                id="main_image"
                                                name="main_image"
                                                label="{{ __('common.product_image') }}"
                                                :required="true"
                                                :existingImage="isset($product) && $product->product && $product->product->mainImage ? $product->product->mainImage->path : null"
                                                placeholder="{{ __('common.click_to_upload') }}"
                                                recommendedSize="{{ __('common.recommended_logo_size') }}"
                                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                                aspectRatio="square"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Additional Images -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-image"></i>
                                        {{ __('catalogmanagement::product.additional_images') }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <input type="file" multiple class="form-control" accept="image/*" name="additional_images[]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 1: Main Descriptions -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-file-alt"></i>
                                        {{ __('common.description') }}
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="details_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('common.details') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][details]" id="details_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="6"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل تفاصيل المنتج' : 'Enter product details' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}">{{ isset($product) ? ($product->product && method_exists($product->product, 'getTranslation') ? $product->product->getTranslation('details', $language->code) : (method_exists($product, 'getTranslation') ? $product->getTranslation('details', $language->code) : '')) ?? '' : '' }}</textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-details" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Additional Information -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-info-circle"></i>
                                        {{ __('common.additional_information') }}
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="summary_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('common.summary') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][summary]" id="summary_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل الملخص' : 'Enter summary' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}">{{ isset($product) ? ($product->product && method_exists($product->product, 'getTranslation') ? $product->product->getTranslation('summary', $language->code) : (method_exists($product, 'getTranslation') ? $product->getTranslation('summary', $language->code) : '')) ?? '' : '' }}</textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-summary" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="features_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('common.features') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][features]" id="features_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل المميزات' : 'Enter features' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}">{{ isset($product) ? ($product->product && method_exists($product->product, 'getTranslation') ? $product->product->getTranslation('features', $language->code) : (method_exists($product, 'getTranslation') ? $product->getTranslation('features', $language->code) : '')) ?? '' : '' }}</textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-features" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="instructions_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('common.instructions') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][instructions]" id="instructions_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل التعليمات' : 'Enter instructions' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}">{{ isset($product) ? ($product->product && method_exists($product->product, 'getTranslation') ? $product->product->getTranslation('instructions', $language->code) : (method_exists($product, 'getTranslation') ? $product->getTranslation('instructions', $language->code) : '')) ?? '' : '' }}</textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-instructions" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="extra_description_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('catalogmanagement::product.extra_description') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][extra_description]" id="extra_description_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل وصف إضافي' : 'Enter extra description' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}">{{ isset($product) ? ($product->product && method_exists($product->product, 'getTranslation') ? $product->product->getTranslation('extra_description', $language->code) : (method_exists($product, 'getTranslation') ? $product->getTranslation('extra_description', $language->code) : '')) ?? '' : '' }}</textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-extra_description" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Material & Media -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-play-circle"></i>
                                        {{ __('common.media') }}
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="material_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('catalogmanagement::product.material') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][material]" id="material_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="3"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل المواد' : 'Enter material' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}">{{ isset($product) ? ($product->product && method_exists($product->product, 'getTranslation') ? $product->product->getTranslation('material', $language->code) : (method_exists($product, 'getTranslation') ? $product->getTranslation('material', $language->code) : '')) ?? '' : '' }}</textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-material" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="video_link" class="form-label">{{ __('catalogmanagement::product.video_link') }}</label>
                                                <input type="url" name="video_link" id="video_link" value="{{ isset($product) ? $product->video_link : '' }}" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="https://www.youtube.com/watch?v=...">
                                                <small class="text-muted">{{ __('common.enter_valid_video_url') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Variant Configurations -->
                        <div class="wizard-step-content" data-step="3" style="display: none;">
                            <!-- Configuration Type -->
                            <div class="card mb-4">
                                <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-setting"></i>
                                            {{ __('catalogmanagement::product.configuration_type') }}
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label for="configuration_type" class="form-label">{{ __('catalogmanagement::product.product_type') }} <span class="text-danger">*</span></label>
                                                    <select name="configuration_type" id="configuration_type" class="form-control select2">
                                                        <option value="">{{ __('catalogmanagement::product.select_product_type') }}</option>
                                                        <option value="simple" {{ isset($product) && ($product->configuration_type ?? $product->product->configuration_type ?? '') == 'simple' ? 'selected' : '' }}>{{ __('catalogmanagement::product.simple_product') }}</option>
                                                        <option value="variants" {{ isset($product) && ($product->configuration_type ?? $product->product->configuration_type ?? '') == 'variants' ? 'selected' : '' }}>{{ __('catalogmanagement::product.with_variants') }}</option>
                                                    </select>
                                                    <div class="error-message text-danger" id="error-configuration_type" style="display: none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>

                            <!-- Simple Product Section (shown when "simple" is selected) -->
                            <div id="simple-product-section" style="display: none;">
                                <div id="simple-product-pricing-stock">
                                    <!-- Pricing & Stock boxes will be inserted here -->
                                </div>
                            </div>

                            <!-- With Variants Section (shown when "variants" is selected) -->
                            <div id="variants-section" style="display: none;">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <i class="uil uil-layer-group"></i>
                                                {{ __('catalogmanagement::product.product_variants') }}
                                            </div>
                                            <button type="button" id="add-variant-btn" class="btn btn-primary btn-sm">
                                                <i class="uil uil-plus"></i> {{ __('catalogmanagement::product.add_variant') }}
                                            </button>
                                        </h5>

                                        <!-- Empty state message -->
                                        <div id="variants-empty-state" class="text-center py-4">
                                            <i class="uil uil-layer-group text-muted" style="font-size: 48px;"></i>
                                            <p class="text-muted mb-0">{{ __('catalogmanagement::product.no_variants_added') }}</p>
                                        </div>

                                        <!-- Variants Container -->
                                        <div id="variants-container">
                                            <!-- Variant boxes will be added here dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: SEO & Images -->
                        <div class="wizard-step-content" data-step="4" style="display: none;">
                            <!-- SEO Information -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-search"></i>
                                        {{ __('common.seo') }}
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="meta_title_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('catalogmanagement::product.meta_title') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <input type="text" name="translations[{{ $language->id }}][meta_title]" id="meta_title_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل العنوان الوصفي' : 'Enter meta title' }}"
                                                    maxlength="60"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}">
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-meta_title" style="display: none;"></div>
                                                <small class="text-muted">{{ __('common.recommended_50_60_chars') }}</small>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="meta_description_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('catalogmanagement::product.meta_description') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][meta_description]" id="meta_description_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="3"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل الوصف الوصفي' : 'Enter meta description' }}"
                                                    maxlength="160"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"></textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-meta_description" style="display: none;"></div>
                                                <small class="text-muted">{{ __('common.recommended_150_160_chars') }}</small>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="meta_keywords_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    {{ __('catalogmanagement::product.meta_keywords') }} ({{ strtoupper($language->code) }})
                                                </label>
                                                <div @if($language->code == 'ar') dir="rtl" @endif>
                                                    <x-tags-input
                                                        name="translations[{{ $language->id }}][meta_keywords]"
                                                        :value="isset($product) ? (isset($product->product) && method_exists($product->product, 'getMetaKeywordsString') ? $product->product->getMetaKeywordsString($language->code) : (method_exists($product, 'getMetaKeywordsString') ? $product->getMetaKeywordsString($language->code) : '')) : old('translations.'.$language->id.'.meta_keywords', '')"
                                                        placeholder="{{ $language->code == 'ar' ? 'اكتب كلمة مفتاحية واضغط انتر' : 'Type a keyword and press Enter...' }}"
                                                        rtl-placeholder="اكتب كلمة مفتاحية واضغط انتر"
                                                        language="{{ $language->code }}"
                                                        :allow-duplicates="true"
                                                        theme="primary"
                                                        size="md"
                                                        id="meta_keywords_{{ $language->code }}"
                                                        dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"
                                                    />
                                                </div>
                                                <small class="text-muted w-100 d-block" @if($language->code == 'ar') dir="rtl" style="text-align: right;" @endif>{{ $language->code == 'ar' ? 'اضغط انتر أو فاصلة لإنشاء كلمة مفتاحية' : 'Press Enter or comma to create a keyword' }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between wizard-navigation">
                            <button type="button" id="prevBtn" class="btn btn-light btn-squared" style="display: none;">
                                <i class="uil uil-arrow-left"></i> {{ __('common.previous') }}
                            </button>
                            <div class="d-flex justify-content-end gap-2 w-100">
                                <a href="#" class="btn btn-light btn-squared">
                                    <i class="uil uil-times"></i> {{ __('common.cancel') }}
                                </a>
                                <button type="button" id="nextBtn" class="btn btn-primary btn-squared">
                                    {{ __('common.next') }}
                                    @if(app()->getLocale() == 'ar')
                                    <i class="uil uil-arrow-left"></i>
                                    @else
                                    <i class="uil uil-arrow-right"></i>
                                    @endif
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-success btn-squared" style="display: none;">
                                    <i class="uil uil-check"></i> {{ isset($product) ? __('catalogmanagement::product.update_product') : __('catalogmanagement::product.create_product') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Image Confirmation Modal -->
                    <div class="modal fade" id="deleteImageConfirmModal" tabindex="-1" aria-labelledby="deleteImageConfirmLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="deleteImageConfirmLabel">
                                        <i class="uil uil-exclamation-triangle me-2"></i>{{ __('common.confirm_deletion') ?? 'Confirm Deletion' }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">{{ __('common.are_you_sure_delete_image') ?? 'Are you sure you want to delete this image? This action cannot be undone.' }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                        <i class="uil uil-times me-1"></i>{{ __('common.cancel') }}
                                    </button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">
                                        <i class="uil uil-trash me-1"></i>{{ __('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reusable Pricing & Stock Template --}}
                    <template id="pricing-stock-template">
                        <div class="pricing-stock-box" data-index="__INDEX__">
                            <!-- Pricing Card -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-dollar-sign"></i>
                                        {{ __('catalogmanagement::product.pricing') }}
                                    </h5>
                                    <div class="row">
                                        <!-- SKU Field (only for variants) -->
                                        <div class="col-md-6 mb-3 variant-sku-field" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('catalogmanagement::product.sku') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="__NAME_PREFIX__[sku]" class="form-control sku-input" placeholder="{{ __('catalogmanagement::product.sku') }}" required>
                                                <div class="error-message text-danger" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('catalogmanagement::product.price') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="__NAME_PREFIX__[price]" class="form-control price-input" step="0.01" min="0" placeholder="0.00" required>
                                                <div class="error-message text-danger" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label d-block">{{ __('catalogmanagement::product.has_discount') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input has-discount-switch" type="checkbox" name="__NAME_PREFIX__[has_discount]" value="1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3 discount-fields" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('catalogmanagement::product.price_before_discount') }}</label>
                                                <input type="number" name="__NAME_PREFIX__[price_before_discount]" class="form-control" step="0.01" min="0" placeholder="0.00">
                                                <div class="error-message text-danger" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3 discount-fields" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('catalogmanagement::product.discount_end_date') }}</label>
                                                <input type="date" name="__NAME_PREFIX__[discount_end_date]" class="form-control">
                                                <div class="error-message text-danger" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Card -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <i class="uil uil-box"></i>
                                            {{ __('catalogmanagement::product.stock_management') }}
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm add-stock-row">
                                            <i class="uil uil-plus"></i> {{ __('catalogmanagement::product.add_region') }}
                                        </button>
                                    </h5>

                                    <div class="table-responsive">
                                        <table class="table table-bordered stock-table">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th width="50%">{{ __('catalogmanagement::product.region') }}</th>
                                                    <th width="35%">{{ __('catalogmanagement::product.quantity') }}</th>
                                                    <th width="15%" class="text-center">{{ __('catalogmanagement::product.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="stock-rows">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-active">
                                                    <td class="text-end"><strong>{{ __('catalogmanagement::product.total_quantity') }}</strong></td>
                                                    <td><strong class="total-quantity-display">0</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Stock Row Template --}}
                    <template id="stock-row-template">
                        <tr class="stock-row">
                            <td>
                                <select name="__NAME_PREFIX__[stocks][__STOCK_INDEX__][region_id]" class="form-control select2 region-select" required>
                                    <option value="">{{ __('catalogmanagement::product.select_region') }}</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="__NAME_PREFIX__[stocks][__STOCK_INDEX__][quantity]" class="form-control quantity-input" min="0" placeholder="0" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-stock-row">
                                    <i class="uil uil-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>

                    {{-- Variant Box Template --}}
                    <template id="variant-box-template">
                        <div class="card mb-3 variant-box" data-variant-index="__VARIANT_INDEX__" id="variant-__VARIANT_INDEX__">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="uil uil-layer-group"></i>
                                    {{ __('common.variant') }} #__VARIANT_NUMBER__
                                </h6>
                                <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                                    <i class="uil uil-trash"></i> {{ __('common.remove') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <!-- Variant Key Selection -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">{{ __('catalogmanagement::product.variant_key') }} <span class="text-danger">*</span></label>
                                        <select name="variants[__VARIANT_INDEX__][variant_key_id]" class="form-control select2 variant-key-select" required>
                                            <option value="">{{ __('catalogmanagement::product.select_variant_key') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Variant Tree Container -->
                                <div class="variant-tree-container" style="display: none;">
                                    <label class="form-label">{{ __('catalogmanagement::product.variant_selection') }} <span class="text-danger">*</span></label>
                                    <div class="variant-tree-levels">
                                        <!-- Dynamic variant levels will be added here -->
                                    </div>
                                    <input type="hidden" name="variants[__VARIANT_INDEX__][value_id]" class="selected-variant-id">
                                    <div class="alert alert-info mt-2 selected-variant-path" style="display: none;">
                                        <strong>{{ __('catalogmanagement::product.selected_variant') }}:</strong> <span class="path-text"></span>
                                    </div>
                                </div>

                                <!-- Pricing & Stock will be inserted here after variant selection -->
                                <div id="variant-__VARIANT_INDEX__-pricing-stock" style="display: none;"></div>
                            </div>
                        </div>
                    </template>

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
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function($) {
    'use strict';

    // ============================================
    // Configuration & Global Variables
    // ============================================
    const config = {
        currentStep: 1,
        totalSteps: 4,
        locale: '{{ app()->getLocale() }}',
        apiBaseUrl: '{{ url("/api") }}',
        translations: {
            loading: '{{ __("common.loading") }}',
            selectOption: '{{ __("common.select_option") }}',
            error: '{{ __("common.error") }}',
            success: '{{ __("common.success") }}'
        }
    };

    // ============================================
    // Step Navigation Functions
    // ============================================
    function showStep(stepNumber) {
        console.log('📍 Showing step:', stepNumber);

        // Sync CKEditor data to textareas before changing steps
        if (typeof CKEDITOR !== 'undefined') {
            for (let instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
                console.log('✅ CKEditor data synced for:', instance);
            }
        }

        // Clear validation errors when changing steps
        $('#validation-alerts-container').empty();
        $('.error-message').hide().text('');
        $('.is-invalid').removeClass('is-invalid');
        $('.select2-selection').removeClass('is-invalid');

        // Hide all steps
        $('.wizard-step-content').each(function() {
            $(this).hide().removeClass('active');
        });

        // Show current step
        const $currentStep = $(`.wizard-step-content[data-step="${stepNumber}"]`);
        console.log('Current step element found:', $currentStep.length);
        $currentStep.show().addClass('active');

        // Update wizard navigation
        $('.wizard-step-nav').removeClass('current completed');
        $('.wizard-step-nav').each(function() {
            const step = parseInt($(this).data('step'));
            if (step < stepNumber) {
                $(this).addClass('completed');
            } else if (step === stepNumber) {
                $(this).addClass('current');
            }
        });

        // Update buttons
        updateNavigationButtons(stepNumber);

        // Update current step
        config.currentStep = stepNumber;

        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    function updateNavigationButtons(stepNumber) {
        const $prevBtn = $('#prevBtn');
        const $nextBtn = $('#nextBtn');
        const $submitBtn = $('#submitBtn');

        // Previous button
        if (stepNumber === 1) {
            $prevBtn.hide();
        } else {
            $prevBtn.show();
        }

        // Next/Submit buttons
        if (stepNumber === config.totalSteps) {
            $nextBtn.hide();
            $submitBtn.show();
        } else {
            $nextBtn.show();
            $submitBtn.hide();
        }
    }

    function validateStep(stepNumber) {
        console.log('🔍 Validating step:', stepNumber);
        let isValid = true;
        const errors = [];

        // Clear previous errors
        $('.error-message').hide().text('');
        $('.is-invalid').removeClass('is-invalid');
        $('.select2-selection').removeClass('is-invalid');

        switch(stepNumber) {
            case 1:
                // Validate basic information
                // Title validation
                let hasTitles = false;
                $('[name^="translations"][name$="[title]"]').each(function() {
                    const $input = $(this);
                    if ($input.val().trim()) {
                        hasTitles = true;
                    } else {
                        // Add is-invalid class to empty title fields
                        $input.addClass('is-invalid');
                        $input.next('.error-message').text('{{ __("catalogmanagement::product.title_required") }}').show();
                    }
                });
                if (!hasTitles) {
                    errors.push('{{ __("catalogmanagement::product.title_required") }}');
                    isValid = false;
                }

                // SKU validation
                if (!$('#sku').val().trim()) {
                    $('#error-sku').text('{{ __("catalogmanagement::product.sku_required") }}').show();
                    $('#sku').addClass('is-invalid');
                    isValid = false;
                }

                // Brand validation
                if (!$('#brand_id').val()) {
                    $('#error-brand_id').text('{{ __("catalogmanagement::product.brand_required") }}').show();
                    $('#brand_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                    isValid = false;
                }

                // Vendor validation (for admin users)
                @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                if (!$('#vendor_id').val()) {
                    $('#error-vendor_id').text('{{ __("catalogmanagement::product.vendor_required") }}').show();
                    $('#vendor_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                    isValid = false;
                }
                @endif

                // Department validation
                if (!$('#department_id').val()) {
                    $('#error-department_id').text('{{ __("catalogmanagement::product.department_required") }}').show();
                    $('#department_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                    isValid = false;
                }

                // Category validation
                if (!$('#category_id').val()) {
                    $('#error-category_id').text('{{ __("catalogmanagement::product.category_required") }}').show();
                    $('#category_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                    isValid = false;
                }

                // Tax validation
                if (!$('#tax_id').val()) {
                    $('#error-tax_id').text('{{ __("catalogmanagement::product.tax_required") }}').show();
                    $('#tax_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                    isValid = false;
                }

                // Max per order validation
                if (!$('#max_per_order').val() || $('#max_per_order').val() < 1) {
                    $('#error-max_per_order').text('{{ __("catalogmanagement::product.max_per_order_required") }}').show();
                    $('#max_per_order').addClass('is-invalid');
                    isValid = false;
                }
                break;

            case 2:
                // Main image validation
                const mainImageInput = $('#main_image')[0];
                const hasExistingImage = $('#main_image').data('existing-image');

                // Check if there's a new file selected or an existing image
                if (!mainImageInput.files || mainImageInput.files.length === 0) {
                    if (!hasExistingImage) {
                        $('#main_image').addClass('is-invalid');
                        // Add is-invalid class to image preview container
                        $('#main_image').closest('.image-upload-wrapper').find('.image-preview-container').addClass('is-invalid');
                        // Show error message under the image upload
                        const $errorContainer = $('#main_image').closest('.image-upload-wrapper').find('.error-message');
                        if ($errorContainer.length === 0) {
                            $('#main_image').closest('.image-upload-wrapper').append('<div class="error-message text-danger mt-2" style="display: block;">{{ __("catalogmanagement::product.main_image_required") }}</div>');
                        } else {
                            $errorContainer.text('{{ __("catalogmanagement::product.main_image_required") }}').show();
                        }
                        isValid = false;
                    }
                }
                break;

            case 3:
                // Configuration type validation
                if (!$('#configuration_type').val()) {
                    $('#error-configuration_type').text('{{ __("catalogmanagement::product.configuration_type_required") }}').show();
                    $('#configuration_type').addClass('is-invalid');
                    isValid = false;
                } else {
                    const configurationType = $('#configuration_type').val();

                    if (configurationType === 'simple') {
                        // Validate simple product pricing and stock
                        const $priceInput = $('#simple-product-pricing-stock .price-input');
                        if ($priceInput.length > 0) {
                            if (!$priceInput.val() || parseFloat($priceInput.val()) < 0) {
                                $priceInput.addClass('is-invalid');
                                $priceInput.next('.error-message').text('{{ __("catalogmanagement::product.price_required") }}').show();
                                isValid = false;
                            }

                            // Validate at least one stock row
                            const stockRows = $('#simple-product-pricing-stock .stock-row').length;
                            if (stockRows === 0) {
                                errors.push('{{ __("catalogmanagement::product.stock_required") }}');
                                isValid = false;
                            } else {
                                // Validate each stock row
                                $('#simple-product-pricing-stock .stock-row').each(function() {
                                    const $row = $(this);
                                    const regionId = $row.find('.region-select').val();
                                    const quantity = $row.find('.quantity-input').val();

                                    if (!regionId) {
                                        $row.find('.region-select').next('.select2').find('.select2-selection').addClass('is-invalid');
                                        isValid = false;
                                    }

                                    if (!quantity || parseInt(quantity) < 0) {
                                        $row.find('.quantity-input').addClass('is-invalid');
                                        isValid = false;
                                    }
                                });
                            }
                        }
                    } else if (configurationType === 'variants') {
                        // Validate at least one variant
                        const variantCount = $('.variant-box').length;
                        if (variantCount === 0) {
                            errors.push('{{ __("catalogmanagement::product.variants_required") }}');
                            isValid = false;
                        } else {
                            // Validate each variant
                            $('.variant-box').each(function() {
                                const $variant = $(this);
                                const variantIndex = $variant.data('variant-index');

                                // Check if variant key is selected
                                const keySelected = $variant.find('.variant-key-select').val();
                                if (!keySelected) {
                                    $variant.find('.variant-key-select').next('.select2').find('.select2-selection').addClass('is-invalid');
                                    errors.push(`Variant #${variantIndex + 1}: {{ __("catalogmanagement::product.variant_key_required") }}`);
                                    isValid = false;
                                }

                                // Check if variant value is selected
                                const valueId = $variant.find('.selected-variant-id').val();
                                if (!valueId) {
                                    errors.push(`Variant #${variantIndex + 1}: {{ __("catalogmanagement::product.variant_selection_required") }}`);
                                    isValid = false;
                                }

                                // Check if pricing/stock is filled
                                const $pricingStock = $(`#variant-${variantIndex}-pricing-stock`);
                                if ($pricingStock.is(':visible')) {
                                    const sku = $pricingStock.find('.sku-input').val();
                                    if (!sku || !sku.trim()) {
                                        $pricingStock.find('.sku-input').addClass('is-invalid');
                                        $pricingStock.find('.sku-input').next('.error-message').text('{{ __("catalogmanagement::product.sku_required") }}').show();
                                        errors.push(`Variant #${variantIndex + 1}: {{ __("catalogmanagement::product.sku_required") }}`);
                                        isValid = false;
                                    }

                                    const price = $pricingStock.find('.price-input').val();
                                    if (!price || parseFloat(price) < 0) {
                                        $pricingStock.find('.price-input').addClass('is-invalid');
                                        $pricingStock.find('.price-input').next('.error-message').text('{{ __("catalogmanagement::product.price_required") }}').show();
                                        errors.push(`Variant #${variantIndex + 1}: {{ __("catalogmanagement::product.price_required") }}`);
                                        isValid = false;
                                    }

                                    const stockRows = $pricingStock.find('.stock-row').length;
                                    if (stockRows === 0) {
                                        errors.push(`Variant #${variantIndex + 1}: {{ __("catalogmanagement::product.stock_required") }}`);
                                        isValid = false;
                                    } else {
                                        // Validate each stock row
                                        $pricingStock.find('.stock-row').each(function() {
                                            const $row = $(this);
                                            const regionId = $row.find('.region-select').val();
                                            const quantity = $row.find('.quantity-input').val();

                                            if (!regionId) {
                                                $row.find('.region-select').next('.select2').find('.select2-selection').addClass('is-invalid');
                                                isValid = false;
                                            }

                                            if (!quantity || parseInt(quantity) < 0) {
                                                $row.find('.quantity-input').addClass('is-invalid');
                                                isValid = false;
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    }
                }
                break;

            case 4:
                // SEO validation (optional)
                break;
        }

        // Log errors but don't show alert (inline errors are enough)
        if (errors.length > 0) {
            console.log('❌ Validation errors found:', errors);
            // Don't show alert - inline errors are displayed under each field
        } else {
            console.log('✅ Validation passed for step:', stepNumber);
        }

        return isValid;
    }

    function showValidationErrors(errors) {
        const $container = $('#validation-alerts-container');
        $container.empty();

        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                <strong><i class="uil uil-exclamation-triangle"></i> ${config.translations.error}:</strong>
                <ul class="mb-0 mt-2">
                    ${errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        $container.html(alertHtml);
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    // ============================================
    // Cascading Dropdowns Functions
    // ============================================
    function loadDepartmentsByVendor(vendorId) {
        const $departmentSelect = $('#department_id');
        const $categorySelect = $('#category_id');
        const $subCategorySelect = $('#sub_category_id');

        // Reset dependent dropdowns
        resetSelect($departmentSelect);
        resetSelect($categorySelect);
        resetSelect($subCategorySelect);

        if (!vendorId) {
            return;
        }

        console.log('📦 Loading departments for vendor:', vendorId);

        // Show loading
        $departmentSelect.prop('disabled', true);
        $departmentSelect.html(`<option value="">${config.translations.loading}...</option>`);

        // Make AJAX request to your existing API
        $.ajax({
            url: `${config.apiBaseUrl}/departments`,
            type: 'GET',
            dataType: 'json',
            data: {
                vendor_id: vendorId,
                select2: true,
                params: true
            },
            success: function(response) {
                console.log('✅ Departments loaded:', response);

                // Destroy Select2 first
                if ($departmentSelect.hasClass('select2-hidden-accessible')) {
                    $departmentSelect.select2('destroy');
                }

                $departmentSelect.prop('disabled', false);
                $departmentSelect.html(`<option value="">${config.translations.selectOption}</option>`);

                // Handle response data structure
                const departments = response.data || response;

                if (departments && departments.length > 0) {
                    departments.forEach(function(dept) {
                        $departmentSelect.append(`<option value="${dept.id}">${dept.name || dept.text}</option>`);
                    });
                }

                // Reinitialize Select2
                $departmentSelect.select2({
                    placeholder: config.translations.selectOption,
                    width: '100%',
                    theme: 'bootstrap-5'
                });
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading departments:', error);
                $departmentSelect.prop('disabled', false);
                $departmentSelect.html(`<option value="">${config.translations.error}</option>`);

                showNotification('error', '{{ __("common.error_loading_departments") }}');
            }
        });
    }

    function loadCategoriesByDepartment(departmentId) {
        const $categorySelect = $('#category_id');
        const $subCategorySelect = $('#sub_category_id');

        // Reset dependent dropdowns
        resetSelect($categorySelect);
        resetSelect($subCategorySelect);

        if (!departmentId) {
            return;
        }

        console.log('🏢 Loading categories for department:', departmentId);

        // Show loading
        $categorySelect.prop('disabled', true);
        $categorySelect.html(`<option value="">${config.translations.loading}...</option>`);

        // Make AJAX request to your existing API
        $.ajax({
            url: `${config.apiBaseUrl}/categories`,
            type: 'GET',
            dataType: 'json',
            data: {
                department_id: departmentId,
                select2: true,
                params: true
            },
            success: function(response) {
                console.log('✅ Categories loaded:', response);

                // Destroy Select2 first
                if ($categorySelect.hasClass('select2-hidden-accessible')) {
                    $categorySelect.select2('destroy');
                }

                $categorySelect.prop('disabled', false);
                $categorySelect.html(`<option value="">${config.translations.selectOption}</option>`);

                // Handle response data structure
                const categories = response.data || response;

                if (categories && categories.length > 0) {
                    categories.forEach(function(cat) {
                        $categorySelect.append(`<option value="${cat.id}">${cat.name || cat.text}</option>`);
                    });
                }

                // Reinitialize Select2
                $categorySelect.select2({
                    placeholder: config.translations.selectOption,
                    width: '100%',
                    theme: 'bootstrap-5',
                });
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading categories:', error);
                $categorySelect.prop('disabled', false);
                $categorySelect.html(`<option value="">${config.translations.error}</option>`);

                showNotification('error', '{{ __("common.error_loading_categories") }}');
            }
        });
    }

    function loadSubCategoriesByCategory(categoryId) {
        const $subCategorySelect = $('#sub_category_id');

        // Reset dropdown
        resetSelect($subCategorySelect);

        if (!categoryId) {
            return;
        }

        console.log('📁 Loading subcategories for category:', categoryId);

        // Show loading
        $subCategorySelect.prop('disabled', true);
        $subCategorySelect.html(`<option value="">${config.translations.loading}...</option>`);

        // Make AJAX request to your existing API
        $.ajax({
            url: `${config.apiBaseUrl}/subcategories`,
            type: 'GET',
            dataType: 'json',
            data: {
                category_id: categoryId,
                select2: true,
                params: true
            },
            success: function(response) {
                console.log('✅ Subcategories loaded:', response);

                // Destroy Select2 first
                if ($subCategorySelect.hasClass('select2-hidden-accessible')) {
                    $subCategorySelect.select2('destroy');
                }

                $subCategorySelect.prop('disabled', false);
                $subCategorySelect.html(`<option value="">${config.translations.selectOption}</option>`);

                // Handle response data structure
                const subcategories = response.data || response;

                if (subcategories && subcategories.length > 0) {
                    subcategories.forEach(function(subCat) {
                        $subCategorySelect.append(`<option value="${subCat.id}">${subCat.name || subCat.text}</option>`);
                    });
                }

                // Reinitialize Select2
                $subCategorySelect.select2({
                    placeholder: config.translations.selectOption,
                    width: '100%',
                    theme: 'bootstrap-5',
                });
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading subcategories:', error);
                $subCategorySelect.prop('disabled', false);
                $subCategorySelect.html(`<option value="">${config.translations.error}</option>`);

                showNotification('error', '{{ __("common.error_loading_subcategories") }}');
            }
        });
    }

    function resetSelect($select) {
        $select.val(null).trigger('change');
    }

    function showNotification(type, message) {
        console.log(`[${type.toUpperCase()}] ${message}`);

        // Optional: Show toast notification if available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        }
    }

    // ============================================
    // Configuration Type Handler
    // ============================================
    function handleConfigurationTypeChange() {
        const configurationType = $('#configuration_type').val();

        $('#simple-product-section').hide();
        $('#variants-section').hide();

        if (configurationType === 'simple') {
            $('#simple-product-section').show();
            // Create pricing & stock box for simple product (no prefix for simple products)
            createPricingStockBox('simple-product-pricing-stock', '');
        } else if (configurationType === 'variants') {
            $('#variants-section').show();
            // Clear simple product pricing/stock
            $('#simple-product-pricing-stock').empty();
        }
    }

    // ============================================
    // Pricing & Stock Management Functions
    // ============================================
    let stockRowCounter = 0;
    let regionsData = []; // Store regions globally

    // Calculate and update total quantity
    function updateTotalQuantity($table) {
        let total = 0;
        $table.find('.quantity-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            total += qty;
        });
        $table.find('.total-quantity-display').text(total);
        console.log('📊 Total quantity updated:', total);
    }

    function createPricingStockBox(containerId, namePrefix, index = 0) {
        const template = $('#pricing-stock-template').html();

        if (!template) {
            console.error('❌ Pricing stock template not found!');
            return;
        }

        let html = template.replace(/__INDEX__/g, index);

        // Handle empty prefix (for simple products, fields should be at root level)
        if (namePrefix) {
            // With prefix: variants[0][price]
            html = html.replace(/__NAME_PREFIX__\[/g, `${namePrefix}[`);
        } else {
            // Without prefix: price (remove __NAME_PREFIX__[ and the closing ])
            html = html.replace(/__NAME_PREFIX__\[([^\]]+)\]/g, '$1');
        }

        $(`#${containerId}`).html(html);

        // For simple products, remove the SKU field from the template (it's in Step 1)
        if (!namePrefix) {
            $(`#${containerId} .variant-sku-field`).remove();
        }

        // Initialize Select2 for region selects
        setTimeout(function() {
            $(`#${containerId} .select2`).select2({
                theme: 'bootstrap-5',
                width: '100%',
            });
        }, 100);

        // Add first stock row
        addStockRow(containerId, namePrefix);

        console.log('✅ Pricing & Stock box created for:', namePrefix);
    }

    function addStockRow(containerId, namePrefix) {
        const $template = $('#stock-row-template');

        if ($template.length === 0) {
            console.error('❌ Stock row template not found!');
            return;
        }

        // Get template HTML content
        let template = $template.html();

        if (!template) {
            console.error('❌ Stock row template content is empty!');
            return;
        }

        let html = template.replace(/__STOCK_INDEX__/g, stockRowCounter);

        // Handle empty prefix (for simple products)
        if (namePrefix) {
            // With prefix: variants[0][stocks][0][region_id]
            html = html.replace(/__NAME_PREFIX__\[stocks\]/g, `${namePrefix}[stocks]`);
        } else {
            // Without prefix: stocks[0][region_id] (remove __NAME_PREFIX__ completely)
            html = html.replace(/__NAME_PREFIX__\[stocks\]/g, 'stocks');
        }

        $(`#${containerId} .stock-rows`).append(html);

        // Populate region select with pre-loaded data
        setTimeout(function() {
            const $regionSelect = $(`#${containerId} .stock-rows tr:last .region-select`);

            // Add regions from cached data
            regionsData.forEach(function(region) {
                $regionSelect.append(`<option value="${region.id}">${region.name}</option>`);
            });

            // Initialize Select2
            $regionSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '{{ __("common.select_region") }}'
            });

            console.log('✅ Region select populated with', regionsData.length, 'regions');
        }, 100);

        stockRowCounter++;
        console.log('✅ Stock row added');
    }

    // Load regions once on page load
    function loadRegions() {
        console.log('🌍 Loading regions from API...');

        $.ajax({
            url: '{{ url("/api/area/regions") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                select2: true
            },
            success: function(response) {
                const data = response.data || response;
                regionsData = data.map(function(region) {
                    return {
                        id: region.id,
                        name: region.name || region.text
                    };
                });
                console.log('✅ Regions loaded:', regionsData.length, 'regions');
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading regions:', error);
            }
        });
    }

    function createVariantPricingStockBox(variantIndex, variantName) {
        const containerId = `variant-${variantIndex}-pricing-stock`;
        const namePrefix = `variants[${variantIndex}]`;

        // Show the pricing stock container
        $(`#${containerId}`).show();

        createPricingStockBox(containerId, namePrefix, variantIndex);

        // Show SKU field for variants
        $(`#${containerId} .variant-sku-field`).show();

        console.log('✅ Variant pricing & stock created for:', variantName);
    }

    // ============================================
    // Variant Management Functions
    // ============================================
    let variantCounter = 0;
    let variantKeysData = [];

    // Load variant keys from API
    function loadVariantKeys() {
        console.log('🔑 Loading variant keys from API...');

        $.ajax({
            url: '{{ route("admin.api.variant-keys") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                variantKeysData = response.data || response;
                console.log('✅ Variant keys loaded:', variantKeysData.length, 'keys');
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading variant keys:', error);
            }
        });
    }

    // Add new variant box
    function addVariantBox() {
        const template = $('#variant-box-template').html();
        const html = template
            .replace(/__VARIANT_INDEX__/g, variantCounter)
            .replace(/__VARIANT_NUMBER__/g, variantCounter + 1);

        $('#variants-container').append(html);
        $('#variants-empty-state').hide();

        // Populate variant keys
        const $keySelect = $(`#variant-${variantCounter} .variant-key-select`);
        variantKeysData.forEach(function(key) {
            $keySelect.append(`<option value="${key.id}">${key.name}</option>`);
        });

        // Initialize Select2
        setTimeout(function() {
            $keySelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
            });
        }, 100);

        variantCounter++;
        console.log('✅ Variant box added');
    }

    // Load variants by key (root level - no parent)
    function loadVariantsByKey(variantIndex, keyId) {
        console.log('🌳 Loading root variants for key:', keyId);

        const $container = $(`#variant-${variantIndex} .variant-tree-container`);
        const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

        // Clear previous tree and pricing/stock
        $levelsContainer.empty();
        $container.hide();
        $(`#variant-${variantIndex}-pricing-stock`).hide().empty();
        $(`#variant-${variantIndex} .selected-variant-path`).hide();

        // Store keyId in the variant box for later use
        $(`#variant-${variantIndex}`).data('current-key-id', keyId);

        $.ajax({
            url: '{{ route("admin.api.variants-by-key") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                key_id: keyId,
            },
            success: function(response) {
                const variants = response.data || response;
                console.log('✅ Root variants loaded:', variants.length);

                if (variants.length > 0) {
                    $container.show();
                    addVariantLevel($levelsContainer, variants, variantIndex, 0, []);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading variants:', error);
            }
        });
    }

    // Add a level to the variant tree
    function addVariantLevel($container, variants, variantIndex, level, selectedPath) {
        const levelDiv = $('<div>', {
            class: 'variant-level mb-3',
            'data-level': level
        });

        const select = $('<select>', {
            class: 'form-control select2 variant-value-select',
            'data-variant-index': variantIndex,
            'data-level': level
        });

        select.append('<option value="">{{ __("common.select_option") }}</option>');

        variants.forEach(function(variant) {
            const hasChildren = variant.has_children || false;
            const treeIcon = hasChildren ? ' 🌳' : '';
            select.append(`<option value="${variant.id}" data-has-children="${hasChildren}">${variant.name}${treeIcon}</option>`);
        });

        levelDiv.append(select);
        $container.append(levelDiv);

        // Initialize Select2
        setTimeout(function() {
            select.select2({
                theme: 'bootstrap-5',
                width: '100%',
            });
        }, 100);
    }


    // Load child variants
    function loadChildVariants(variantIndex, parentId, level, selectedPath, keyId) {
        console.log('🌳 Loading child variants for parent:', parentId, 'at level:', level);

        const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

        // Remove all levels after current level
        $levelsContainer.find('.variant-level').each(function() {
            if (parseInt($(this).data('level')) > level) {
                $(this).remove();
            }
        });

        $.ajax({
            url: '{{ route("admin.api.variants-by-key") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                key_id: keyId,
                parent_id: parentId
            },
            success: function(response) {
                const variants = response.data || response;
                console.log('✅ Child variants loaded:', variants.length);

                if (variants.length > 0) {
                    addVariantLevel($levelsContainer, variants, variantIndex, level + 1, selectedPath);
                } else {
                    // No more children - this is the final selection
                    finalizeVariantSelection(variantIndex, parentId, selectedPath);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading child variants:', error);
            }
        });
    }

    // Finalize variant selection and show pricing/stock
    function finalizeVariantSelection(variantIndex, variantId, path) {
        console.log('✅ Final variant selected:', variantId, 'Path:', path);

        // Set hidden input
        $(`#variant-${variantIndex} .selected-variant-id`).val(variantId);

        // Show selected path
        const $pathAlert = $(`#variant-${variantIndex} .selected-variant-path`);
        $pathAlert.find('.path-text').text(path.join(' > '));
        $pathAlert.show();

        // Create pricing & stock box
        createVariantPricingStockBox(variantIndex, path[path.length - 1]);
    }

    // ============================================
    // Event Handlers
    // ============================================
    $(document).ready(function() {
        // Load regions and variant keys once on page load
        loadRegions();
        loadVariantKeys();

        // Show first step
        showStep(1);

        // Next button click
        $('#nextBtn').on('click', function(e) {
            e.preventDefault();
            console.log('⏭️ Next button clicked, current step:', config.currentStep);
            if (validateStep(config.currentStep)) {
                if (config.currentStep < config.totalSteps) {
                    showStep(config.currentStep + 1);
                }
            }
        });

        // Previous button click
        $('#prevBtn').on('click', function(e) {
            e.preventDefault();
            console.log('⏮️ Previous button clicked, current step:', config.currentStep);
            if (config.currentStep > 1) {
                showStep(config.currentStep - 1);
            }
        });

        // Wizard step click
        $(document).on('click', '.wizard-step-nav', function(e) {
            e.preventDefault();
            const targetStep = parseInt($(this).data('step'));
            console.log('🎯 Wizard step clicked:', targetStep, 'Current:', config.currentStep);

            // If moving forward, validate current step first
            if (targetStep > config.currentStep) {
                console.log('⚠️ Moving forward, validating current step...');
                if (!validateStep(config.currentStep)) {
                    console.log('❌ Validation failed, staying on current step');
                    return;
                }
            }

            showStep(targetStep);
        });

        // Auto-load departments on page load if vendor is already selected
        const initialVendorId = $('#vendor_id').val();
        if (initialVendorId) {
            console.log('📦 Auto-loading departments for vendor:', initialVendorId);
            loadDepartmentsByVendor(initialVendorId);
        }

        // Vendor change event - Load departments based on vendor
        $('#vendor_id').on('change', function() {
            const vendorId = $(this).val();
            console.log('📦 Vendor changed:', vendorId);
            loadDepartmentsByVendor(vendorId);
        });

        // Department change event - Load categories based on department
        $('#department_id').on('change', function() {
            const departmentId = $(this).val();
            console.log('🏢 Department changed:', departmentId);
            loadCategoriesByDepartment(departmentId);
        });

        // Category change event - Load subcategories based on category
        $('#category_id').on('change', function() {
            const categoryId = $(this).val();
            console.log('📁 Category changed:', categoryId);
            loadSubCategoriesByCategory(categoryId);
        });

        // Configuration type change
        $('#configuration_type').on('change', function() {
            handleConfigurationTypeChange();
        });

        // ============================================
        // Clear validation errors on input change
        // ============================================

        // Clear error on title input
        $(document).on('input', '[name^="translations"][name$="[title]"]', function() {
            const $input = $(this);
            if ($input.val().trim()) {
                $input.removeClass('is-invalid');
                $input.next('.error-message').hide();
            }
        });

        // Clear error on SKU input
        $('#sku').on('input', function() {
            if ($(this).val().trim()) {
                $(this).removeClass('is-invalid');
                $('#error-sku').hide();
            }
        });

        // Clear error on Max Per Order input
        $('#max_per_order').on('input', function() {
            if ($(this).val() && $(this).val() >= 1) {
                $(this).removeClass('is-invalid');
                $('#error-max_per_order').hide();
            }
        });

        // Clear error on select2 change
        $('#brand_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                $('#error-brand_id').hide();
            }
        });

        $('#vendor_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                $('#error-vendor_id').hide();
            }
        });

        $('#department_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                $('#error-department_id').hide();
            }
        });

        $('#category_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                $('#error-category_id').hide();
            }
        });

        $('#tax_id').on('change', function() {
            if ($(this).val()) {
                $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                $('#error-tax_id').hide();
            }
        });

        $('#configuration_type').on('change', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
                $('#error-configuration_type').hide();
            }
        });

        // Clear error on main image upload
        $('#main_image').on('change', function() {
            if (this.files && this.files.length > 0) {
                $(this).removeClass('is-invalid');
                // Remove is-invalid class from image preview container
                $(this).closest('.image-upload-wrapper').find('.image-preview-container').removeClass('is-invalid');
                // Hide error message
                $(this).closest('.image-upload-wrapper').find('.error-message').hide();
            }
        });

        // Clear error on price input (for both simple and variant products)
        $(document).on('input', '.price-input', function() {
            const $input = $(this);
            if ($input.val() && parseFloat($input.val()) >= 0) {
                $input.removeClass('is-invalid');
                $input.next('.error-message').hide();
            }
        });

        // Clear error on SKU input (for variants)
        $(document).on('input', '.sku-input', function() {
            const $input = $(this);
            if ($input.val() && $input.val().trim()) {
                $input.removeClass('is-invalid');
                $input.next('.error-message').hide();
            }
        });

        // Clear error on quantity input
        $(document).on('input', '.quantity-input', function() {
            const $input = $(this);
            if ($input.val() && parseInt($input.val()) >= 0) {
                $input.removeClass('is-invalid');
            }
        });

        // Clear error on region select
        $(document).on('change', '.region-select', function() {
            const $select = $(this);
            if ($select.val()) {
                $select.next('.select2').find('.select2-selection').removeClass('is-invalid');
            }
        });

        // Clear error on variant key select
        $(document).on('change', '.variant-key-select', function() {
            const $select = $(this);
            if ($select.val()) {
                $select.next('.select2').find('.select2-selection').removeClass('is-invalid');
            }
        });

        // Submit button click handler
        $('#submitBtn').on('click', function(e) {
            e.preventDefault();
            console.log('🔘 Submit button clicked');
            $('#productForm').submit();
        });

        // Form submission
        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            console.log('📝 Form submitted');

            // Validate all steps
            let allValid = true;
            for (let i = 1; i <= config.totalSteps; i++) {
                if (!validateStep(i)) {
                    allValid = false;
                    showStep(i);
                    break;
                }
            }

            if (allValid) {
                console.log('✅ Validation passed, showing loader');

                // Disable submit button
                const $submitBtn = $('#submitBtn');
                const originalBtnHtml = $submitBtn.html();
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}');

                // Update loading text dynamically
                const loadingText = @json(isset($product) ? trans('loading.updating') : trans('loading.creating'));
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
                    const formData = new FormData(document.getElementById('productForm'));

                    // Send AJAX request
                    return fetch($('#productForm').attr('action'), {
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
                        const successMessage = @json(isset($product) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                        LoadingOverlay.showSuccess(
                            successMessage,
                            '{{ trans("loading.redirecting") }}'
                        );

                        // Redirect after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = data.redirect || '{{ route("admin.products.index") }}';
                        }, 1500);
                    });
                })
                .catch(error => {
                    // Hide loading overlay
                    LoadingOverlay.hide();

                    // Remove previous validation errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                    $('.error-message').hide().text('');

                    // Handle validation errors
                    if (error.errors) {
                        const errorMessages = [];

                        Object.keys(error.errors).forEach(key => {
                            const errorMessage = error.errors[key][0];
                            errorMessages.push(errorMessage);

                            // Show inline error
                            const $errorElement = $(`#error-${key.replace(/\./g, '-')}`);
                            if ($errorElement.length) {
                                $errorElement.text(errorMessage).show();
                            }

                            // Add invalid class
                            $(`[name="${key}"]`).addClass('is-invalid');

                            // Also show toastr notification
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMessage);
                            }
                        });

                        showValidationErrors(errorMessages);
                    } else if (error.message) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(error.message);
                        }
                        showValidationErrors([error.message]);
                    }

                    // Re-enable submit button
                    $submitBtn.prop('disabled', false);
                    $submitBtn.html(originalBtnHtml);
                });
            }
        });

        // Prevent Enter key from submitting form
        $('#productForm').on('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                return false;
            }
        });

        // ============================================
        // Pricing & Stock Event Handlers
        // ============================================

        // Toggle discount fields
        $(document).on('change', '.has-discount-switch', function() {
            const $box = $(this).closest('.pricing-stock-box');
            const $discountFields = $box.find('.discount-fields');

            if ($(this).is(':checked')) {
                $discountFields.show();
                console.log('💰 Discount fields shown');
            } else {
                $discountFields.hide();
                $discountFields.find('input').val('');
                console.log('💰 Discount fields hidden');
            }
        });

        // Add stock row
        $(document).on('click', '.add-stock-row', function() {
            const $box = $(this).closest('.pricing-stock-box');
            const containerId = $box.parent().attr('id');

            // Try to get name prefix from price input
            const $priceInput = $box.find('.price-input');
            let namePrefix = '';

            if ($priceInput.length > 0) {
                const priceName = $priceInput.attr('name');
                // For variants: variants[0][price] -> variants[0]
                // For simple: price -> empty string
                if (priceName && priceName.includes('[price]')) {
                    namePrefix = priceName.replace('[price]', '');
                }
            }

            console.log('Add stock row clicked', {containerId, namePrefix});
            addStockRow(containerId, namePrefix);
        });

        // Remove stock row
        $(document).on('click', '.remove-stock-row', function() {
            const $row = $(this).closest('tr');
            const $table = $(this).closest('.stock-table');

            // Don't allow removing if it's the last row
            if ($table.find('.stock-row').length > 1) {
                $row.remove();
                updateTotalQuantity($table);
                console.log('🗑️ Stock row removed');
            } else {
                alert('{{ __("catalogmanagement::product.cannot_remove_last_stock_row") }}');
            }
        });

        // Update total quantity when quantity input changes
        $(document).on('input change', '.quantity-input', function() {
            const $table = $(this).closest('.stock-table');
            updateTotalQuantity($table);
        });

        // ============================================
        // Variant Event Handlers
        // ============================================

        // Add variant button
        $('#add-variant-btn').on('click', function() {
            addVariantBox();
        });

        // Remove variant
        $(document).on('click', '.remove-variant-btn', function() {
            const $variantBox = $(this).closest('.variant-box');
            $variantBox.remove();

            // Show empty state if no variants
            if ($('#variants-container .variant-box').length === 0) {
                $('#variants-empty-state').show();
            }

            console.log('🗑️ Variant removed');
        });

        // Variant key selection
        $(document).on('change', '.variant-key-select', function() {
            const keyId = $(this).val();
            const variantIndex = $(this).closest('.variant-box').data('variant-index');

            if (keyId) {
                console.log('🔑 Variant key selected:', keyId, 'for variant:', variantIndex);
                loadVariantsByKey(variantIndex, keyId);
            } else {
                // Clear tree if key is deselected
                $(`#variant-${variantIndex} .variant-tree-container`).hide();
                $(`#variant-${variantIndex}-pricing-stock`).hide().empty();
            }
        });

        // Variant value selection (tree navigation)
        $(document).on('change', '.variant-value-select', function() {
            const $select = $(this);
            const variantId = $select.val();
            const variantIndex = $select.data('variant-index');
            const level = $select.data('level');

            // Get the stored key ID
            const keyId = $(`#variant-${variantIndex}`).data('current-key-id');
            const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

            // Clear all child levels after the current level
            $levelsContainer.find('.variant-level').each(function() {
                if (parseInt($(this).data('level')) > level) {
                    $(this).remove();
                }
            });

            // Hide pricing/stock when changing selection
            $(`#variant-${variantIndex}-pricing-stock`).hide().empty();
            $(`#variant-${variantIndex} .selected-variant-path`).hide();

            if (!variantId) {
                console.log('🗑️ Variant deselected at level:', level);
                return;
            }

            // Build selected path
            const selectedPath = [];
            $(`#variant-${variantIndex} .variant-value-select`).each(function(index) {
                if (index <= level && $(this).val()) {
                    const selectedText = $(this).find('option:selected').text();
                    selectedPath.push(selectedText);
                }
            });

            const $selectedOption = $select.find('option:selected');
            const hasChildren = $selectedOption.data('has-children');

            console.log('🌳 Variant selected:', variantId, 'Has children:', hasChildren);

            if (hasChildren) {
                // Load children
                loadChildVariants(variantIndex, variantId, level, selectedPath, keyId);
            } else {
                // This is a leaf node - finalize selection
                finalizeVariantSelection(variantIndex, variantId, selectedPath);
            }
        });

        console.log('✅ Product Form Ready');
        console.log('📍 API Base URL:', config.apiBaseUrl);
    });

})(jQuery);
</script>
@endpush
