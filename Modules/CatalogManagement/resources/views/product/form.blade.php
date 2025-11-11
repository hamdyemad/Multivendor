@extends('layout.app')

@push('styles')
@vite(['Modules/CatalogManagement/resources/assets/sass/product-form.scss'])
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
                    <x-wizard :steps="[
                        'Product Details',
                        'Images & Media',
                        'Pricing & Inventory',
                        'Review & Submit'
                    ]" :currentStep="1" />

                    <form id="productForm" method="POST" action="{{ isset($product) ? '#' : '#' }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($product))
                            @method('PUT')
                        @endif

                        <!-- Step 1: Product Details -->
                        <div class="wizard-step-content active" data-step="1" style="margin-top: 60px;">
                            <h5 class="mb-4" style="background: #0056B7; color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; margin-top: 0;">
                                <i class="uil uil-box" style="font-size: 22px;"></i>
                                Product Details
                            </h5>

                            <!-- Box 1: Product Information -->
                            <div class="info-box mb-4" style="margin-top: 20px;">
                                <h6 class="box-title" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                    <i class="uil uil-info-circle" style="font-size: 18px;"></i>
                                    Product Information
                                </h6>
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

                            <!-- Box 2: Organization -->
                            <div class="info-box mb-4">
                                <h6 class="box-title" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                    <i class="uil uil-sitemap" style="font-size: 18px;"></i>
                                    Organization
                                </h6>
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

                            <!-- Box 3: Logistics & Taxes -->
                            <div class="info-box mb-4">
                                <h6 class="box-title" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                    <i class="uil uil-truck" style="font-size: 18px;"></i>
                                    Logistics & Taxes
                                </h6>
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
                                            <input type="number" name="max_per_order" id="max_per_order" class="form-control" min="1" placeholder="Enter max per order">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Box 4: Product Tags -->
                            <div class="info-box mb-4">
                                <h6 class="box-title" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                    <i class="uil uil-tag-alt" style="font-size: 18px;"></i>
                                    Product Tags
                                </h6>
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
                                                class="form-control" placeholder="Enter tags separated by commas" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                            <small class="text-muted">Separate tags with commas</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Images & Media -->
                        <div class="wizard-step-content" data-step="2" style="display: none; margin-top: 60px;">
                            <h5 class="mb-4" style="background: #0056B7; color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; margin-top: 0;">
                                <i class="uil uil-images" style="font-size: 22px;"></i>
                                Images & Media
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <x-image-upload
                                        id="main_image"
                                        name="main_image"
                                        label="Main Image"
                                        placeholder="Click to upload image"
                                        recommendedSize="800x800px recommended"
                                        aspectRatio="square"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Pricing & Inventory -->
                        <div class="wizard-step-content" data-step="3" style="display: none; margin-top: 60px;">
                            <h5 class="mb-4" style="background: #0056B7; color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; margin-top: 0;">
                                <i class="uil uil-dollar-alt" style="font-size: 22px;"></i>
                                Pricing & Inventory
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" placeholder="Enter price">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="discount_price" class="form-label">Discount Price</label>
                                        <input type="number" name="discount_price" id="discount_price" class="form-control" min="0" step="0.01">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" min="0" value="0">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                        <input type="number" name="low_stock_threshold" id="low_stock_threshold" class="form-control" min="0" value="5">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div class="wizard-step-content" data-step="4" style="display: none; margin-top: 60px;">
                            <h5 class="mb-4" style="background: #0056B7; color: white; padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; margin-top: 0;">
                                <i class="uil uil-check-circle" style="font-size: 22px;"></i>
                                Review & Submit
                            </h5>

                            <div id="review-validation-errors" class="alert alert-danger" style="display: none;">
                                <h6 class="alert-heading"><i class="uil uil-exclamation-triangle"></i> Validation Errors</h6>
                                <div id="review-errors-list"></div>
                            </div>

                            <div class="alert alert-info">
                                <i class="uil uil-info-circle"></i> Please review your information before submitting
                            </div>

                            <div class="card mb-3">
                                <div class="card-header" style="background: #0056B7; color: white; display: flex; justify-content: space-between; align-items: center;">
                                    <h6 class="mb-0" style="color: white;"><i class="uil uil-info-circle"></i> Product Details</h6>
                                    <button type="button" class="btn btn-sm edit-step" data-step="1" style="background: white; color: #0056B7; border-color: white;">
                                        <i class="uil uil-edit"></i> Edit
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($languages as $language)
                                        <div class="col-md-6 mb-2">
                                            <strong>Title ({{ $language->name }}):</strong>
                                            <span class="review-title-{{ $language->code }}">-</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 mb-2">
                                            <strong>SKU:</strong> <span class="review-sku">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Brand:</strong> <span class="review-brand">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header" style="background: #0056B7; color: white; display: flex; justify-content: space-between; align-items: center;">
                                    <h6 class="mb-0" style="color: white;"><i class="uil uil-usd-circle"></i> Pricing</h6>
                                    <button type="button" class="btn btn-sm edit-step" data-step="3" style="background: white; color: #0056B7; border-color: white;">
                                        <i class="uil uil-edit"></i> Edit
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>Price:</strong> <span class="review-price">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Stock:</strong> <span class="review-stock">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-light btn-squared" style="display: none;">
                                <i class="uil uil-arrow-left"></i> Previous
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <a href="#" class="btn btn-light btn-squared">
                                    <i class="uil uil-times"></i> Cancel
                                </a>
                                <button type="button" id="nextBtn" class="btn btn-squared" style="background: #0056B7; color: white; border-color: #0056B7;">
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

<x-loading-overlay loadingText="Creating Product" loadingSubtext="Please wait..." />

@endsection

@push('scripts')
<script>
// Product form configuration
window.productFormConfig = {
    categoriesRoute: '/api/categories',
    subCategoriesRoute: '/api/sub-categories',
    languages: [@foreach($languages as $language){id:{{ $language->id }},code:'{{ $language->code }}',name:'{{ $language->name }}'}{{ !$loop->last ? ',' : '' }}@endforeach]
};
</script>
@vite(['Modules/CatalogManagement/resources/assets/js/product-form.js'])
@endpush
