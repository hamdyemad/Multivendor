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
                    <form id="productForm" method="POST" action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" enctype="multipart/form-data" novalidate onkeydown="return event.key != 'Enter';">
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
                                                    @if($language->code == 'en')
                                                        {{ __('catalogmanagement::product.title') }} ({{ $language->name }})
                                                    @else
                                                        العنوان باللغة العربية
                                                    @endif
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="translations[{{ $language->id }}][title]" id="title_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل عنوان المنتج' : 'Enter product title' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"
                                                    value="{{ isset($product) ? $product->getTranslation('title', $language->code) ?? '' : '' }}">
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-title" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="sku" class="form-label">{{ __('catalogmanagement::product.sku') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="sku" id="sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ __('catalogmanagement::product.sku') }}" value="{{ isset($product) ? $product->sku ?? '' : '' }}">
                                                <div class="error-message text-danger" id="error-sku" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="points" class="form-label">{{ __('catalogmanagement::product.points') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="points" id="points" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" value="{{ isset($product) ? $product->points ?? 0 : 0 }}" placeholder="Enter points" required>
                                                <div class="error-message text-danger" id="error-points" style="display: none;"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label d-block">{{ __('catalogmanagement::product.status') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ isset($product) && $product->is_active ? 'checked' : (isset($product) ? '' : 'checked') }}>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label d-block">{{ __('catalogmanagement::product.featured') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" {{ isset($product) && $product->is_featured ? 'checked' : '' }}>
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
                                                        <option value="{{ $brand['id'] }}" {{ isset($product) && $product->brand_id == $brand['id'] ? 'selected' : '' }}>{{ $brand['name'] }}</option>
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
                                                    @foreach($departments as $dept)
                                                        @php
                                                            $deptModel = \Modules\CategoryManagment\app\Models\Department::find($dept['id']);
                                                            $activityIds = $deptModel ? $deptModel->activities()->pluck('activity_id')->toArray() : [];
                                                        @endphp
                                                        <option value="{{ $dept['id'] }}" data-activities="{{ htmlspecialchars(json_encode($activityIds), ENT_QUOTES, 'UTF-8') }}"
                                                            @if(isset($product) && $product->department_id == $dept['id']) selected @endif>
                                                            {{ $dept['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="category_id" class="form-label">{{ __('catalogmanagement::product.category') }} <span class="text-danger">*</span></label>
                                                <select name="category_id" id="category_id" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                    @if(isset($categories))
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category['id'] }}" {{ isset($product) && $product->category_id == $category['id'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="sub_category_id" class="form-label">{{ __('catalogmanagement::product.sub_category') }}</label>
                                                <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                    @if(isset($subCategories))
                                                        @foreach($subCategories as $subCategory)
                                                            <option value="{{ $subCategory['id'] }}" data-category="{{ $subCategory['category_id'] }}" {{ isset($product) && $product->sub_category_id == $subCategory['id'] ? 'selected' : '' }}>{{ $subCategory['name'] }}</option>
                                                        @endforeach
                                                    @endif
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
                                                <input type="number" name="max_per_order" id="max_per_order" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="1" placeholder="Enter max per order" value="{{ isset($product) ? $product->max_per_order ?? '' : '' }}" required>
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
                                                <label for="tags_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        {{ __('common.tags') }} ({{ $language->name }})
                                                    @else
                                                        الوسوم باللغة العربية
                                                    @endif
                                                </label>
                                                <x-tags-input
                                                    name="translations[{{ $language->id }}][tags]"
                                                    :value="isset($product) ? $product->getTagsString($language->code) : old('translations.'.$language->id.'.tags')"
                                                    placeholder="{{ $language->code == 'ar' ? 'اكتب وسم واضغط انتر' : 'Type a tag and press Enter...' }}"
                                                    rtl-placeholder="اكتب وسم واضغط انتر"
                                                    language="{{ $language->code }}"
                                                    :allow-duplicates="true"
                                                    theme="primary"
                                                    size="md"
                                                    id="tags_{{ $language->code }}"
                                                />
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
                                                :existingImage="isset($product) && $product->main_image ? $product->main_image->path : null"
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
                                                    @if($language->code == 'en')
                                                        {{ __('common.details') }} ({{ $language->name }})
                                                    @else
                                                        التفاصيل باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][details]" id="details_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="6"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل تفاصيل المنتج' : 'Enter product details' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"></textarea>
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
                                                    @if($language->code == 'en')
                                                        {{ __('common.summary') }} ({{ $language->name }})
                                                    @else
                                                        الملخص باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][summary]" id="summary_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل الملخص' : 'Enter summary' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"></textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-summary" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="features_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        {{ __('common.features') }} ({{ $language->name }})
                                                    @else
                                                        المميزات باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][features]" id="features_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل المميزات' : 'Enter features' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"></textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-features" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="instructions_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        {{ __('common.instructions') }} ({{ $language->name }})
                                                    @else
                                                        التعليمات باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][instructions]" id="instructions_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل التعليمات' : 'Enter instructions' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"></textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-instructions" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="extra_description_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        {{ __('catalogmanagement::product.extra_description') }} ({{ $language->name }})
                                                    @else
                                                        وصف إضافي باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][extra_description]" id="extra_description_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل وصف إضافي' : 'Enter extra description' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"></textarea>
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
                                                    @if($language->code == 'en')
                                                        {{ __('catalogmanagement::product.material') }} ({{ $language->name }})
                                                    @else
                                                        المواد باللغة العربية
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][material]" id="material_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15 tinymce-editor"
                                                    rows="3"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل المواد' : 'Enter material' }}"
                                                    dir="{{ $language->code == 'ar' ? 'rtl' : 'ltr' }}"></textarea>
                                                <div class="error-message text-danger" id="error-translations-{{ $language->id }}-material" style="display: none;"></div>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="video_link" class="form-label">{{ __('catalogmanagement::product.video_link') }}</label>
                                                <input type="url" name="video_link" id="video_link" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="https://www.youtube.com/watch?v=...">
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
                                                    <option value="simple">{{ __('common.simple_product') }}</option>
                                                    <option value="variants">{{ __('common.with_variants') }}</option>
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
                                                    @if($language->code == 'en')
                                                        {{ __('catalogmanagement::product.meta_title') }} ({{ $language->name }})
                                                    @else
                                                        العنوان الوصفي باللغة العربية
                                                    @endif
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
                                                    @if($language->code == 'en')
                                                        {{ __('catalogmanagement::product.meta_description') }} ({{ $language->name }})
                                                    @else
                                                        الوصف الوصفي باللغة العربية
                                                    @endif
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
                                                    @if($language->code == 'en')
                                                        {{ __('catalogmanagement::product.meta_keywords') }} ({{ $language->name }})
                                                    @else
                                                        الكلمات المفتاحية باللغة العربية
                                                    @endif
                                                </label>
                                                <x-tags-input
                                                    name="translations[{{ $language->id }}][meta_keywords]"
                                                    :value="isset($product) ? $product->getMetaKeywordsString($language->code) : old('translations.'.$language->id.'.meta_keywords')"
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
    addNewRegion: '{{ __('common.add_new_region') }}',
    actionsLabel: '{{ __('common.actions') }}',
    languages: [
        @foreach($languages as $language)
        {
            id: {{ $language->id }},
            code: '{{ $language->code }}',
            name: '{{ $language->name }}'
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    taxes: [
        @foreach($taxes as $tax)
        {
            id: {{ $tax['id'] }},
            name: '{{ $tax['name'] }}',
            percentage: {{ $tax['percentage'] }}
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
};
</script>

<!-- Product Form External JavaScript (All Logic) -->
@vite(['Modules/CatalogManagement/resources/assets/js/product-form.js'])
@endpush
