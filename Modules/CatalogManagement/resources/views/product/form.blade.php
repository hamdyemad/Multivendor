@extends('layout.app')

@section('title')
{{ $title ?? (isset($product) ? 'Edit Product' : 'Create Product') }}
@endsection

@push('styles')
<!-- Wizard styles are now loaded globally via app.scss -->
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => 'Products Management', 'url' => '#'],
                ['title' => isset($product) ? 'Edit Product' : 'Create Product']
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h4>
                </div>
                <div class="card-body">
                    <!-- Wizard Navigation -->
                    <x-wizard :steps="[
                        'Product Information',
                        'Product Details',
                        'Pricing & Inventory',
                        'Review & Submit'
                    ]" :currentStep="1" />

                    <!-- Form -->
                    <form id="productForm" method="POST" action="{{ isset($product) ? '#' : '#' }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($product))
                            @method('PUT')
                        @endif

                        <!-- Step 1: Product Information -->
                        <div class="wizard-step-content active" data-step="1" style="margin-top: 60px;">
                            <!-- Card 1: Product Information -->
                            <div class="card mb-4" style="margin-top: 20px;">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-info-circle"></i>
                                        Product Information
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="title_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Title ({{ $language->name }})
                                                    @else
                                                        العنوان ({{ $language->name }})
                                                    @endif
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="translations[{{ $language->id }}][title]" id="title_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل عنوان المنتج' : 'Enter product title' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                                <input type="text" name="sku" id="sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="Enter SKU">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="points" class="form-label">Points</label>
                                                <input type="number" name="points" id="points" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" value="0" placeholder="Enter points">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="status" class="form-label d-block">Status</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" checked>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="featured" class="form-label d-block">Featured Product</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1">
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
                                        Organization
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                                                <select name="brand_id" id="brand_id" class="form-control select2">
                                                    <option value="">Select Brand</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                                <select name="department_id" id="department_id" class="form-control select2">
                                                    <option value="">Select Department</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="category_id" class="form-label">Main Category <span class="text-danger">*</span></label>
                                                <select name="category_id" id="category_id" class="form-control select2">
                                                    <option value="">Select Category</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="sub_category_id" class="form-label">Sub Category</label>
                                                <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                                                    <option value="">Select Sub Category</option>
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
                                        Logistics & Taxes
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="tax_id" class="form-label">Tax</label>
                                                <select name="tax_id" id="tax_id" class="form-control select2">
                                                    <option value="">Select Tax</option>
                                                    @foreach($taxes as $tax)
                                                        <option value="{{ $tax['id'] }}">{{ $tax['name'] }} ({{ $tax['percentage'] }}%)</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="max_per_order" class="form-label">Max Per Order</label>
                                                <input type="number" name="max_per_order" id="max_per_order" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="1" placeholder="Enter max per order">
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
                                        Product Tags
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="tags_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Tags ({{ $language->name }})
                                                    @else
                                                        الوسوم ({{ $language->name }})
                                                    @endif
                                                </label>
                                                <input type="text" name="translations[{{ $language->id }}][tags]" id="tags_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل الوسوم مفصولة بفواصل' : 'Enter tags separated by commas' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}>
                                                <small class="text-muted">Separate tags with commas</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Product Details -->
                        <div class="wizard-step-content" data-step="2" style="display: none; margin-top: 60px;">
                            <!-- Card 1: Main Descriptions -->
                            <div class="card mb-4" style="margin-top: 20px;">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-file-alt"></i>
                                        Main Descriptions
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="details_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Details ({{ $language->name }})
                                                    @else
                                                        التفاصيل ({{ $language->name }})
                                                    @endif
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][details]" id="details_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="6"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل تفاصيل المنتج' : 'Enter product details' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}></textarea>
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
                                        Additional Information
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="summary_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Summary ({{ $language->name }})
                                                    @else
                                                        الملخص ({{ $language->name }})
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][summary]" id="summary_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل الملخص' : 'Enter summary' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}></textarea>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="features_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Features ({{ $language->name }})
                                                    @else
                                                        المميزات ({{ $language->name }})
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][features]" id="features_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل المميزات' : 'Enter features' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}></textarea>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="instructions_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Instructions ({{ $language->name }})
                                                    @else
                                                        التعليمات ({{ $language->name }})
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][instructions]" id="instructions_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل التعليمات' : 'Enter instructions' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}></textarea>
                                            </div>
                                        </div>
                                        @endforeach

                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="extra_description_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Extra Description ({{ $language->name }})
                                                    @else
                                                        وصف إضافي ({{ $language->name }})
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][extra_description]" id="extra_description_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="4"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل وصف إضافي' : 'Enter extra description' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}></textarea>
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
                                        Material & Media
                                    </h5>
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="material_{{ $language->code }}" class="form-label w-100 {{ $language->rtl ? 'text-end' : '' }}">
                                                    @if($language->code == 'en')
                                                        Material ({{ $language->name }})
                                                    @else
                                                        المواد ({{ $language->name }})
                                                    @endif
                                                </label>
                                                <textarea name="translations[{{ $language->id }}][material]" id="material_{{ $language->code }}"
                                                    class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                    rows="3"
                                                    placeholder="{{ $language->code == 'ar' ? 'أدخل المواد' : 'Enter material' }}"
                                                    {{ $language->rtl ? 'dir=rtl' : '' }}></textarea>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="video_link" class="form-label">Video Link (YouTube, Vimeo, etc.)</label>
                                                <input type="url" name="video_link" id="video_link" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="https://www.youtube.com/watch?v=...">
                                                <small class="text-muted">Enter a valid video URL from YouTube, Vimeo, or other platforms</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Pricing & Inventory -->
                        <div class="wizard-step-content" data-step="3" style="display: none; margin-top: 60px;">
                            <!-- Configuration Type -->
                            <div class="card mb-4" style="margin-top: 20px;">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="uil uil-setting"></i>
                                        Configuration Type
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="configuration_type" class="form-label">Product Type <span class="text-danger">*</span></label>
                                                <select name="configuration_type" id="configuration_type" class="form-control select2">
                                                    <option value="">Choose Configuration</option>
                                                    <option value="simple">Simple Product (No Variants)</option>
                                                    <option value="variants">With Variants</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Simple Product Details (shown when "simple" is selected) -->
                            <div id="simple-product-section" style="display: none;">
                                <!-- Card 1: Product Details -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-receipt"></i>
                                            Product Details
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="simple_sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                                    <input type="text" name="simple_sku" id="simple_sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="Enter SKU">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                                    <input type="number" name="price" id="price" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price">
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <div class="form-check form-switch form-switch-lg">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="has_discount" name="has_discount" value="1">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Discount Fields (shown when discount is checked) -->
                                            <div id="discount-fields" style="display: none;" class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="price_before_discount" class="form-label">Price Before Discount</label>
                                                            <input type="number" name="price_before_discount" id="price_before_discount" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="offer_end_date" class="form-label">Offer End Date</label>
                                                            <input type="date" name="offer_end_date" id="offer_end_date" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Stock per Region -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-package"></i>
                                            Stock per Region
                                        </h5>

                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="stock-table">
                                                <thead>
                                                    <tr class="userDatatable-header">
                                                        <th><span class="userDatatable-title">Region</span></th>
                                                        <th><span class="userDatatable-title">Stock</span></th>
                                                        <th width="100"><span class="userDatatable-title">Actions</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="stock-rows">
                                                    <!-- Stock rows will be added here -->
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-light">
                                                        <td class="text-end fw-bold">Total Stock:</td>
                                                        <td colspan="2">
                                                            <span id="total-stock" class="fw-bold text-primary">0</span>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <button type="button" id="add-stock-row" class="btn btn-primary btn-sm mt-3">
                                            <i class="uil uil-plus"></i> Add New Region
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- With Variants Section (shown when "variants" is selected) -->
                            <div id="variants-section" style="display: none;">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-layer-group"></i>
                                            Product Variants
                                        </h5>
                                        <div class="alert alert-info">
                                            <i class="uil uil-info-circle"></i> Variants feature will be implemented here
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div class="wizard-step-content" data-step="4" style="display: none; margin-top: 60px;">
                            <h5 class="mb-4" style="background: #0056B7; color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                                <i class="uil uil-check-circle" style="font-size: 22px;"></i>
                                Review & Submit
                            </h5>

                            <!-- Validation Errors Alert -->
                            <div id="review-validation-errors" class="alert alert-danger" style="display: none; flex-direction: column;">
                                <h6 class="alert-heading"><i class="uil uil-exclamation-triangle"></i> Validation Errors</h6>
                                <div id="review-errors-list"></div>
                            </div>

                            <div class="alert alert-info">
                                <i class="uil uil-info-circle"></i> Please review your information before submitting
                            </div>

                            <!-- Review Cards will go here -->
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-light btn-squared" style="display: none;">
                                <i class="uil uil-arrow-left"></i> Previous
                            </button>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="#" class="btn btn-light btn-squared">
                                    <i class="uil uil-times"></i> Cancel
                                </a>
                                <button type="button" id="nextBtn" class="btn btn-primary btn-squared">
                                    Next <i class="uil uil-arrow-right"></i>
                                </button>
                                <button type="submit" id="submitBtn" class="btn btn-success btn-squared" style="display: none;">
                                    <i class="uil uil-check"></i> {{ isset($product) ? 'Update Product' : 'Create Product' }}
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
    loadingText="Creating Product"
    loadingSubtext="Please wait..."
/>

@endsection

@push('scripts')
<!-- Product Form Configuration (Data Only) -->
<script>
window.productFormConfig = {
    categoriesRoute: '/api/categories',
    subCategoriesRoute: '/api/sub-categories',
    indexRoute: '/admin/products',
    languages: [
        @foreach($languages as $language)
        {id: {{ $language->id }}, code: '{{ $language->code }}', name: '{{ $language->name }}'}{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    regions: [
        {id: 1, name: 'Cairo'},
        {id: 2, name: 'Alexandria'},
        {id: 3, name: 'Giza'},
        {id: 4, name: 'Dakahlia'},
        {id: 5, name: 'Red Sea'},
        {id: 6, name: 'Beheira'},
        {id: 7, name: 'Fayoum'},
        {id: 8, name: 'Gharbia'},
        {id: 9, name: 'Ismailia'},
        {id: 10, name: 'Menofia'}
    ]
};
</script>

<!-- Product Form External JavaScript (All Logic) -->
@vite(['Modules/CatalogManagement/resources/assets/js/product-form.js'])
@endpush
