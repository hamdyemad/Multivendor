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
                                                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1"
                                                    @if(isset($product) && ($product->product ? $product->product->is_active : $product->is_active)) checked @endif>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label d-block">{{ __('catalogmanagement::product.featured') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" @if(isset($product) && ($product->product ? $product->product->is_featured : $product->is_featured)) checked @endif>
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
                                                <label for="configuration_type" class="form-label">{{ __('common.product_type') }} <span class="text-danger">*</span></label>
                                                <select name="configuration_type" id="configuration_type" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                    <option value="simple" {{ isset($product) && ($product->configuration_type ?? $product->product->configuration_type ?? '') == 'simple' ? 'selected' : '' }}>{{ __('common.simple_product') }}</option>
                                                    <option value="variants" {{ isset($product) && ($product->configuration_type ?? $product->product->configuration_type ?? '') == 'variants' ? 'selected' : '' }}>{{ __('common.with_variants') }}</option>
                                                </select>
                                                <div class="error-message text-danger" id="error-configuration_type" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Simple Product Section (shown when "simple" is selected) -->
                            <div id="simple-product-section" style="display: none;">
                                <!-- Simple Product Details Container (will be populated by JavaScript) -->
                                <div id="simple-product-details-container">
                                    <!-- Product details and stock management boxes will be inserted here -->
                                </div>
                            </div>

                            <!-- With Variants Section (shown when "variants" is selected) -->
                            <div id="variants-section" style="display: none;">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <i class="uil uil-layer-group"></i>
                                                {{ __('common.product_variants') }}
                                            </div>
                                            <button type="button" id="add-variant-btn" class="btn btn-primary btn-sm">
                                                <i class="uil uil-plus"></i> {{ __('common.add_variant') }}
                                            </button>
                                        </h5>

                                        <!-- Empty state message -->
                                        <div id="variants-empty-state" class="text-center py-4">
                                            <i class="uil uil-layer-group text-muted" style="font-size: 48px;"></i>
                                            <p class="text-muted mb-0">{{ __('common.no_variants_added') }}</p>
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
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="uil uil-times me-1"></i>{{ __('common.cancel') }}
                                    </button>
                                    <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">
                                        <i class="uil uil-trash me-1"></i>{{ __('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('after-body')
    {{-- Loading Overlay Component --}}
    <x-loading-overlay
        loadingText="{{ isset($product) ? trans('catalog::product.updating_product') : trans('catalog::product.creating_product') }}"
        loadingSubtext="{{ trans('catalog::product.please_wait') }}"
    />
@endpush

@push('scripts')
<!-- Product Form Configuration (Data Only) -->
<script>
console.log('🔧 Loading productFormConfig...');
window.productFormConfig = {
    selectPlaceholder: '{{ trans("catalog::product.select_option") ?? "Select..." }}',
    uploadText: '{{ trans("catalog::product.click_to_upload_image") ?? "Click to upload image" }}',
    notProvided: '{{ trans("catalog::product.not_provided") ?? "Not provided" }}',
    noImageUploaded: '{{ trans("catalog::product.no_image_uploaded") ?? "No image uploaded" }}',
    productCreated: '{{ trans("catalog::product.product_created_successfully") ?? "Product created successfully!" }}',
    productUpdated: '{{ trans("catalog::product.product_updated_successfully") ?? "Product updated successfully!" }}',
    creatingProduct: '{{ trans("catalog::product.creating_product") ?? "Creating product..." }}',
    updatingProduct: '{{ trans("catalog::product.updating_product") ?? "Updating product..." }}',
    pleaseWait: '{{ trans("catalog::product.please_wait") ?? "Please wait..." }}',
    redirecting: '{{ trans("catalog::product.redirecting") ?? "Redirecting..." }}',
    errorOccurred: '{{ trans("catalog::product.error_occurred") ?? "An error occurred. Please try again." }}',
    validationError: '{{ trans("catalog::product.validation_error") ?? "Validation Error" }}',
    errorLabel: '{{ trans("catalog::product.error") ?? "Error" }}',
    additionalImage: '{{ __('common.additional_image') }}',
    recommendedSize: '{{ __('common.recommended_logo_size') }}',
    clickToUploadImage: '{{ __('common.click_to_upload') }}',
    change: '{{ __('common.change') }}',
    remove: '{{ __('common.remove') }}',
    indexRoute: '{{ route("admin.products.index") }}',
    categoriesRoute: '{{ url("/api/categories") }}',
    subCategoriesRoute: '{{ url("/api/sub-categories") }}',
    departmentsRoute: '{{ url("/api/departments") }}',
    // Variant translations
    variantNumber: '{{ __('common.variant_number') }}',
    productDetails: '{{ __('common.product_details') }}',
    variantSku: '{{ __('common.variant_sku') }}',
    sku: '{{ __('catalogmanagement::product.sku') }}',
    price: '{{ __('common.price') }}',
    enableDiscountOffer: '{{ __('common.enable_discount_offer') }}',
    priceBeforeDiscount: '{{ __('common.price_before_discount') }}',
    offerEndDate: '{{ __('common.offer_end_date') }}',
    stockPerRegion: '{{ __('common.stock_per_region') }}',
    region: '{{ __('common.region') }}',
    stockQuantity: '{{ __('common.stock_quantity') }}',
    totalStock: '{{ __('common.total_stock') }}',
    noRegionsAddedYet: '{{ __('common.no_regions_added_yet') }}',
    variantDetails: '{{ __('common.variant_details') }}',
    loadingVariantKeys: '{{ __('common.loading_variant_keys') }}',
    selectVariantKey: '{{ __('common.select_variant_key') }}',
    selectVariantKeyHelper: '{{ __('common.select_variant_key_helper') }}',
    selectRootVariants: '{{ __('common.select_root_variants') }}',
    selectLevel: '{{ __('common.select_level') }}',
    rootVariantsLabel: '{{ __('common.root_variants_label') }}',
    selectedColon: '{{ __('common.selected_colon') }}',
    pleaseSelectVariant: '{{ __('common.please_select_variant') }}',
    variantSelected: '{{ __('common.variant_selected') ?? 'Variant configuration selected' }}',
    errorLoadingTree: '{{ __('common.error_loading_tree') ?? 'Error loading variant tree' }}',
    noVariantKeys: '{{ __('common.no_variant_keys') ?? 'No variant keys available' }}',
    addNewRegion: '{{ __('common.add_new_region') }}',
    actionsLabel: '{{ __('common.actions') }}',
    selectPlaceholder: '{{ __('common.select') }}',
    // Wizard translations
    validationErrorTitle: '{{ trans('common.validation_error') !== 'common.validation_error' ? trans('common.validation_error') : 'Validation Error' }}',
    validationErrorMessage: '{{ trans('common.validation_error_message') !== 'common.validation_error_message' ? trans('common.validation_error_message') : 'Please fill in all required fields before proceeding.' }}',
    fieldRequired: '{{ trans('common.field_required') !== 'common.field_required' ? trans('common.field_required') : 'This field is required' }}',
    // Validation messages
    titleRequired: '{{ trans('catalogmanagement::product.title_required') !== 'catalogmanagement::product.title_required' ? trans('catalogmanagement::product.title_required') : 'Title is required' }}',
    skuRequired: '{{ trans('catalogmanagement::product.sku_required') !== 'catalogmanagement::product.sku_required' ? trans('catalogmanagement::product.sku_required') : 'SKU is required' }}',
    pointsPositive: '{{ trans('catalogmanagement::product.points_positive') !== 'catalogmanagement::product.points_positive' ? trans('catalogmanagement::product.points_positive') : 'Points must be a positive number' }}',
    brandRequired: '{{ trans('catalogmanagement::product.brand_required') !== 'catalogmanagement::product.brand_required' ? trans('catalogmanagement::product.brand_required') : 'Brand is required' }}',
    vendorRequired: '{{ trans('catalogmanagement::product.vendor_required') !== 'catalogmanagement::product.vendor_required' ? trans('catalogmanagement::product.vendor_required') : 'Vendor is required' }}',
    departmentRequired: '{{ trans('catalogmanagement::product.department_required') !== 'catalogmanagement::product.department_required' ? trans('catalogmanagement::product.department_required') : 'Department is required' }}',
    categoryRequired: '{{ trans('catalogmanagement::product.category_required') !== 'catalogmanagement::product.category_required' ? trans('catalogmanagement::product.category_required') : 'Category is required' }}',
    taxRequired: '{{ trans('catalogmanagement::product.tax_required') !== 'catalogmanagement::product.tax_required' ? trans('catalogmanagement::product.tax_required') : 'Tax is required' }}',
    maxPerOrderMin: '{{ trans('catalogmanagement::product.max_per_order_min') !== 'catalogmanagement::product.max_per_order_min' ? trans('catalogmanagement::product.max_per_order_min') : 'Max per order must be at least 1' }}',
    mainImageRequired: '{{ trans('catalogmanagement::product.main_image_required') !== 'catalogmanagement::product.main_image_required' ? trans('catalogmanagement::product.main_image_required') : 'Main product image is required' }}',
    productTypeRequired: '{{ trans('catalogmanagement::product.product_type_required') !== 'catalogmanagement::product.product_type_required' ? trans('catalogmanagement::product.product_type_required') : 'Product type is required' }}',
    priceGreaterThanZero: '{{ trans('catalogmanagement::product.price_greater_than_zero') !== 'catalogmanagement::product.price_greater_than_zero' ? trans('catalogmanagement::product.price_greater_than_zero') : 'Price must be greater than 0' }}',
    stockEntryRequired: '{{ trans('catalogmanagement::product.stock_entry_required') !== 'catalogmanagement::product.stock_entry_required' ? trans('catalogmanagement::product.stock_entry_required') : 'At least one stock entry is required' }}',
    regionRequired: '{{ trans('catalogmanagement::product.region_required') !== 'catalogmanagement::product.region_required' ? trans('catalogmanagement::product.region_required') : 'Region is required' }}',
    quantityPositive: '{{ trans('catalogmanagement::product.quantity_positive') !== 'catalogmanagement::product.quantity_positive' ? trans('catalogmanagement::product.quantity_positive') : 'Quantity must be 0 or greater' }}',
    variantRequired: '{{ trans('catalogmanagement::product.variant_required') !== 'catalogmanagement::product.variant_required' ? trans('catalogmanagement::product.variant_required') : 'At least one variant is required' }}',
    variantKeyRequired: '{{ trans('catalogmanagement::product.variant_key_required') !== 'catalogmanagement::product.variant_key_required' ? trans('catalogmanagement::product.variant_key_required') : 'Variant key is required' }}',
    variantValueRequired: '{{ trans('catalogmanagement::product.variant_value_required') !== 'catalogmanagement::product.variant_value_required' ? trans('catalogmanagement::product.variant_value_required') : 'Variant value is required' }}',
    variantSkuRequired: '{{ trans('catalogmanagement::product.variant_sku_required') !== 'catalogmanagement::product.variant_sku_required' ? trans('catalogmanagement::product.variant_sku_required') : 'Variant SKU is required' }}',
    variantPriceGreaterThanZero: '{{ trans('catalogmanagement::product.variant_price_greater_than_zero') !== 'catalogmanagement::product.variant_price_greater_than_zero' ? trans('catalogmanagement::product.variant_price_greater_than_zero') : 'Variant price must be greater than 0' }}',
    variantStockRequired: '{{ trans('catalogmanagement::product.variant_stock_required') !== 'catalogmanagement::product.variant_stock_required' ? trans('catalogmanagement::product.variant_stock_required') : 'At least one stock entry is required for this variant' }}',
    metaTitleMax: '{{ trans('catalogmanagement::product.meta_title_max') !== 'catalogmanagement::product.meta_title_max' ? trans('catalogmanagement::product.meta_title_max') : 'Meta title should be 60 characters or less' }}',
    metaDescriptionMax: '{{ trans('catalogmanagement::product.meta_description_max') !== 'catalogmanagement::product.meta_description_max' ? trans('catalogmanagement::product.meta_description_max') : 'Meta description should be 160 characters or less' }}',
    fixErrorsBeforeSubmit: '{{ trans('catalogmanagement::product.fix_errors_before_submit') !== 'catalogmanagement::product.fix_errors_before_submit' ? trans('catalogmanagement::product.fix_errors_before_submit') : 'Please fix {count} error(s) before submitting.' }}',
    updateProduct: '{{ trans('catalogmanagement::product.update_product') !== 'catalogmanagement::product.update_product' ? trans('catalogmanagement::product.update_product') : 'Update Product' }}',
    createProduct: '{{ trans('catalogmanagement::product.create_product') !== 'catalogmanagement::product.create_product' ? trans('catalogmanagement::product.create_product') : 'Create Product' }}',
    languages: [
        @if(isset($languages))
            @foreach($languages as $language)
            {
                id: {{ $language->id ?? 0 }},
                code: '{{ addslashes($language->code ?? 'en') }}',
                name: '{{ addslashes($language->name ?? 'Unknown') }}'
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        @endif
    ],
    taxes: [
        @if(isset($taxes))
            @foreach($taxes as $tax)
            {
                id: {{ $tax['id'] ?? 0 }},
                name: '{{ addslashes($tax['name'] ?? 'Unknown') }}',
                percentage: {{ is_numeric($tax['percentage']) ? $tax['percentage'] : 0 }}
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        @endif
    ],
    variantKeys: [
        @if(isset($variantKeys))
            @foreach($variantKeys as $variantKey)
            {
                id: {{ $variantKey['id'] ?? 0 }},
                name: '{{ addslashes($variantKey['name'] ?? 'Unknown') }}'
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        @endif
    ],
    regions: [
        @if(isset($regions))
            @foreach($regions as $region)
            {
                id: {{ $region['id'] ?? 0 }},
                name: '{{ addslashes($region['name'] ?? 'Unknown') }}'
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        @endif
    ],
    vendorActivitiesMap: {},
    // Edit mode flag
    isEditMode: {{ isset($product) ? 'true' : 'false' }},
    // Debug info
    debugInfo: {
        hasProduct: {{ isset($product) ? 'true' : 'false' }},
        productId: {{ isset($product) ? ($product->id ?? 'null') : 'null' }}
    },
    // Preserve selected values for edit mode
    selectedValues: {
        @if(isset($product))
            brand_id: {{ $product->product->brand_id ?? 'null' }},
            vendor_id: {{ $product->vendor_id ?? 'null' }},
            department_id: {{ $product->product->department_id ?? 'null' }},
            category_id: {{ $product->product->category_id ?? 'null' }},
            sub_category_id: {{ $product->product->sub_category_id ?? 'null' }},
            tax_id: {{ $product->tax_id ?? 'null' }},
            configuration_type: '{{ $product->configuration_type ?? $product->product->configuration_type ?? '' }}',
            hasVariants: {{ isset($product) && $product->variants && $product->variants->count() > 0 ? 'true' : 'false' }},
            variantsCount: {{ isset($product) && $product->variants ? $product->variants->count() : 0 }},
            // Product details for simple products
            @if(isset($product) && ($product->configuration_type ?? $product->product->configuration_type ?? '') == 'simple')
                productSku: '{{ addslashes($product->sku ?? '') }}',
                productPrice: {{ $product->variants && $product->variants->first() ? $product->variants->first()->price : 0 }},
                productHasDiscount: {{ $product->variants && $product->variants->first() && $product->variants->first()->has_offer ? 'true' : 'false' }},
                productPriceBeforeDiscount: {{ $product->variants && $product->variants->first() ? $product->variants->first()->price_before_discount : 0 }},
                productOfferEndDate: '{{ $product->variants && $product->variants->first() && $product->variants->first()->offer_end_date ? \Carbon\Carbon::parse($product->variants->first()->offer_end_date)->format('Y-m-d') : '' }}',
            @else
                productSku: '',
                productPrice: 0,
                productHasDiscount: false,
                productPriceBeforeDiscount: 0,
                productOfferEndDate: '',
            @endif
            // Existing variants data
            @if(isset($product) && $product->variants && $product->variants->count() > 0)
                existingVariants: [
                    @foreach($product->variants as $variant)
                    {
                        id: {{ $variant->id }},
                        sku: '{{ addslashes($variant->sku ?? '') }}',
                        price: {{ $variant->price ?? 0 }},
                        has_discount: {{ ($variant->has_offer ?? false) ? 'true' : 'false' }},
                        price_before_discount: {{ $variant->price_before_discount ?? 0 }},
                        offer_end_date: '{{ $variant->offer_end_date ? \Carbon\Carbon::parse($variant->offer_end_date)->format('Y-m-d') : '' }}',
                        variant_configuration_id: {{ $variant->variant_configuration_id ?? 'null' }},
                        variant_config: @if($variant->variantConfiguration)
                        {
                            id: {{ $variant->variantConfiguration->id }},
                            key_id: {{ $variant->variantConfiguration->key_id ?? 'null' }},
                            parent_id: {{ $variant->variantConfiguration->parent_id ?? 'null' }},
                            name: '{{ addslashes($variant->variantConfiguration->getTranslation('name', app()->getLocale()) ?? $variant->variantConfiguration->getTranslation('name', 'en') ?? '') }}'
                        }
                        @else
                        null
                        @endif,
                        // Add stock data if available
                        stocks: [
                            @if($variant->stocks && $variant->stocks->count() > 0)
                                @foreach($variant->stocks as $stock)
                                {
                                    region_id: {{ $stock->region_id ?? 0 }},
                                    quantity: {{ $stock->quantity ?? 0 }},
                                    region_name: '{{ addslashes($stock->region && method_exists($stock->region, 'getTranslation') ? $stock->region->getTranslation('name', app()->getLocale()) : ($stock->region->name ?? 'Unknown Region')) }}'
                                }{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            @endif
                        ]
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                // Debug info for variants
                debugVariants: {
                    hasProduct: {{ isset($product) ? 'true' : 'false' }},
                    hasVariants: {{ isset($product) && $product->variants ? 'true' : 'false' }},
                    variantsCount: {{ isset($product) && $product->variants ? $product->variants->count() : 0 }},
                    @if(isset($product) && $product->variants && $product->variants->count() > 0)
                        firstVariantHasStocks: {{ $product->variants->first()->stocks && $product->variants->first()->stocks->count() > 0 ? 'true' : 'false' }},
                        firstVariantStocksCount: {{ $product->variants->first()->stocks ? $product->variants->first()->stocks->count() : 0 }}
                    @endif
                },
            @else
                existingVariants: [],
                debugVariants: {
                    hasProduct: {{ isset($product) ? 'true' : 'false' }},
                    hasVariants: false,
                    variantsCount: 0
                },
            @endif
        @else
            brand_id: null,
            vendor_id: null,
            department_id: null,
            category_id: null,
            sub_category_id: null,
            tax_id: null,
            configuration_type: null,
            hasVariants: false,
            variantsCount: 0,
            productSku: '',
            productPrice: 0,
            productHasDiscount: false,
            productPriceBeforeDiscount: 0,
            productOfferEndDate: '',
            existingVariants: [],
        @endif
    }
};
console.log('✅ productFormConfig loaded successfully:', window.productFormConfig);
</script>

<!-- Product Form Modular JavaScript -->
@vite([
    'Modules/CatalogManagement/resources/assets/js/modules/form-init.js',
    'Modules/CatalogManagement/resources/assets/js/modules/form-edit.js',
    'Modules/CatalogManagement/resources/assets/js/modules/form-variants.js',
    'Modules/CatalogManagement/resources/assets/js/modules/form-validation.js',
    'Modules/CatalogManagement/resources/assets/js/modules/form-wizard.js',
    'Modules/CatalogManagement/resources/assets/js/product-form-refactored.js'
])
@endpush
