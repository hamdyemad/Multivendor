@extends('layout.app')

@section('title', __('catalogmanagement::product.import_product_from_bank'))

@push('styles')
@vite(['Modules/CatalogManagement/resources/assets/scss/product-form.scss'])
<style>
    .product-preview-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    .product-preview-card.has-product {
        border-color: #28a745;
    }
    .product-preview-card .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    .vendor-product-status {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }
    .vendor-product-status.new {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    .vendor-product-status.existing {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    .selection-step {
        padding: 20px;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .selection-step.completed {
        border-color: #28a745;
        border-style: solid;
        background-color: #f8fff8;
    }
    .selection-step .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #6c757d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 12px;
    }
    .selection-step.completed .step-number {
        background: #28a745;
    }
    .product-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .product-card:hover {
        border-color: var(--color-primary);
        box-shadow: 0 4px 12px rgba(0,123,255,0.15);
    }
    .product-card.selected {
        border-color: #28a745;
        background-color: #f8fff8;
    }
    .product-card .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .product-card .product-checkbox {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .product-card .product-info h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
    .product-card .product-info .product-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::product.bank_products'), 'url' => route('admin.products.bank')],
                ['title' => __('catalogmanagement::product.import_product_from_bank')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title m-0">
                        <i class="uil uil-box me-2"></i>
                        {{ __('catalogmanagement::product.import_product_from_bank') }}
                    </h4>
                </div>
                <div class="card-body">
                    @if(!$isVendorUser)
                        <!-- Step 1: Select Vendor (Admin Only) -->
                        <div class="selection-step" id="step-vendor">
                            <div class="d-flex align-items-center mb-3">
                                <span class="step-number">1</span>
                                <h5 class="mb-0">{{ __('catalogmanagement::product.select_vendor') }}</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <select id="vendor_select" class="form-control select2" style="width: 100%;">
                                        <option value="">{{ __('catalogmanagement::product.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor['id'] }}">{{ $vendor['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="vendor-info" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="uil uil-info-circle me-2"></i>
                                    <span id="vendor-name" class="me-1"></span> {{ __('catalogmanagement::product.vendor_selected') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 2: Select Products Not in VendorProduct -->
                    <div class="selection-step" id="step-products" @if(!$isVendorUser) style="opacity: 0.5; pointer-events: none;" @endif>
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">{{ $isVendorUser ? '1' : '2' }}</span>
                            <h5 class="mb-0">{{ __('catalogmanagement::product.select_products_not_in_vendor') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="products-container" style="display: none;">
                                    <div class="mb-3">
                                        <input type="text" id="product-search" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ __('catalogmanagement::product.search_products') }}">

                                        <!-- Search Loading Indicator - Positioned under search input -->
                                        <div id="products-loading" style="display: none;">
                                            <div class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">{{ __('common.loading') }}...</span>
                                                </div>
                                                <p class="mt-2 mb-0 text-muted small">{{ __('catalogmanagement::product.searching_products') }}...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="products-list" class="row">
                                        <!-- Products will be loaded here -->
                                    </div>
                                    <div id="no-products" class="alert alert-warning" style="display: none;">
                                        <i class="uil uil-exclamation-triangle me-2"></i>
                                        {{ __('catalogmanagement::product.no_available_products') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="selected-products-summary" class="mt-3" style="display: none;">
                            <div class="alert alert-success">
                                <i class="uil uil-check-circle me-2"></i>
                                <span id="selected-count" class="me-1">0</span> {{ __('catalogmanagement::product.products_selected') }}
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: VendorProduct Data Form -->
                    <div class="selection-step" id="step-vendor-product-data" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">{{ $isVendorUser ? '2' : '3' }}</span>
                            <h5 class="mb-0">{{ __('catalogmanagement::product.vendor_product_data') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form id="vendor-product-form">
                                    @csrf
                                    <input type="hidden" id="selected_vendor_id" name="vendor_id" value="{{ $isVendorUser ? $vendors->first()['id'] ?? '' : '' }}">
                                    <input type="hidden" id="selected_product_ids" name="product_ids">

                                    <!-- Product Type Selection -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('catalogmanagement::product.product_type') }} <span class="text-danger">*</span></label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <label class="form-check-label" for="simple_product">
                                                            <i class="uil uil-cube me-1"></i>
                                                            {{ __('catalogmanagement::product.simple_product') }}
                                                        </label>
                                                        <input class="form-check-input" type="radio" name="product_type" id="simple_product" value="simple" checked>
                                                    </div>
                                                    <div class="form-check">
                                                        <label class="form-check-label" for="variant_product">
                                                            <i class="uil uil-layer-group me-1"></i>
                                                            {{ __('catalogmanagement::product.variant_product') }}
                                                        </label>
                                                        <input class="form-check-input" type="radio" name="product_type" id="variant_product" value="variants">
                                                    </div>
                                                </div>
                                                <small class="form-text text-muted">
                                                    {{ __('catalogmanagement::product.product_type_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="tax_id">{{ __('catalogmanagement::product.tax') }} <span class="text-danger">*</span></label>
                                                <select id="tax_id" name="tax_id" class="form-control select2" required>
                                                    <option value="">{{ __('common.select_option') }}</option>
                                                    @foreach($taxes as $tax)
                                                        <option value="{{ $tax['id'] }}">{{ $tax['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="points">{{ __('catalogmanagement::product.points') }} <span class="text-danger">*</span></label>
                                                <input type="number" id="points" name="points" class="form-control ih-medium ip-gray radius-xs b-light px-15" value="0" min="0" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="max_per_order">{{ __('catalogmanagement::product.max_per_order') }} <span class="text-danger">*</span></label>
                                                <input type="number" id="max_per_order" name="max_per_order" class="form-control ih-medium ip-gray radius-xs b-light px-15" value="10" min="1" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="video_link">{{ __('catalogmanagement::product.video_link') }}</label>
                                                <input type="url" id="video_link" name="video_link" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="https://...">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-label mb-2">{{ __('catalogmanagement::product.is_active') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-label mb-2">{{ __('catalogmanagement::product.is_featured') }}</label>
                                                <div class="form-check form-switch form-switch-lg">
                                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add New Variants Section (shown when "variants" is selected) -->
                                    <div class="card mt-4" id="add-new-variants-section" style="display: none;">
                                        <div class="card-body">
                                            <h5 class="d-flex justify-content-between align-items-center mb-4">
                                                <div>
                                                    <i class="uil uil-plus-circle"></i>
                                                    {{ __('catalogmanagement::product.add_new_variants') }}
                                                </div>
                                                <button type="button" id="add-variant-btn" class="btn btn-primary btn-sm">
                                                    <i class="uil uil-plus"></i> {{ __('catalogmanagement::product.add_variant') }}
                                                </button>
                                            </h5>

                                            <!-- Empty state message -->
                                            <div id="variants-empty-state" class="text-center py-4">
                                                <i class="uil uil-layer-group text-muted" style="font-size: 48px;"></i>
                                                <p class="text-muted mb-0">{{ __('catalogmanagement::product.click_add_variant_to_create_new') }}</p>
                                            </div>

                                            <!-- New Variants Container -->
                                            <div id="variants-container">
                                                <!-- New variant boxes will be added here dynamically -->
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Stock Management Form (Only for Simple Products) -->
                    <div class="selection-step" id="step-stock-management" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">{{ $isVendorUser ? '3' : '4' }}</span>
                            <h5 class="mb-0">{{ __('catalogmanagement::product.stock_management') }}</h5>
                        </div>
                        <div id="stock-management-container">
                            <!-- Stock management forms will be dynamically generated here -->
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" id="save-vendor-products" class="btn btn-success">
                                <i class="uil uil-check me-1"></i>
                                {{ __('catalogmanagement::product.save_vendor_products') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Variant Box Template --}}
<template id="variant-box-template">
    <div class="card mb-3 variant-box" data-variant-index="__VARIANT_INDEX__" id="variant-__VARIANT_INDEX__">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="uil uil-layer-group"></i>
                {{ __('common.variant') }} #__VARIANT_NUMBER__
            </h6>
            <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                <i class="uil uil-trash-alt m-0"></i> {{ __('common.remove') }}
            </button>
        </div>
        <div class="card-body">
            <!-- Variant Key Selection -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">{{ __('catalogmanagement::product.variant_key') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2 variant-key-select" required>
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
                <input type="hidden" name="variants[__VARIANT_INDEX__][variant_configuration_id]" class="selected-variant-id">
                <div class="alert alert-info mt-2 selected-variant-path" style="display: none;">
                    <strong>{{ __('catalogmanagement::product.selected_variant') }}:</strong> <span class="path-text"></span>
                </div>
            </div>

            <!-- Pricing & Stock will be inserted here after variant selection -->
            <div id="variant-__VARIANT_INDEX__-pricing-stock" style="display: none;"></div>
        </div>
    </div>
</template>

@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@vite(['Modules/CatalogManagement/resources/assets/scss/product-form.scss'])
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('catalogmanagement::product.partials.bank-stock-scripts')
@endpush
