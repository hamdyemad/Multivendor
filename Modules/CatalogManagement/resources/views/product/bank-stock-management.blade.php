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

                    <!-- Step 2: Select Product -->
                    <div class="selection-step" id="step-products" @if(!$isVendorUser) style="opacity: 0.5; pointer-events: none;" @endif>
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">{{ $isVendorUser ? '1' : '2' }}</span>
                            <h5 class="mb-0">{{ __('catalogmanagement::product.select_product') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="products-container" style="display: none;">
                                    <div class="mb-3">
                                        <input type="text" id="product-search" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ __('catalogmanagement::product.search_products') }}">

                                        <!-- Search Loading Indicator -->
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
                        <div id="selected-product-summary" class="mt-3" style="display: none;">
                            <div class="alert alert-success">
                                <i class="uil uil-check-circle me-2"></i>
                                <span id="selected-product-name" class="me-1"></span> {{ __('catalogmanagement::product.product_selected') }}
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Manage Product Variants & Stock -->
                    <div class="selection-step" id="step-variant-stock-management" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">{{ $isVendorUser ? '2' : '3' }}</span>
                            <h5 class="mb-0">{{ __('catalogmanagement::product.manage_variants_stock') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form id="vendor-product-form">
                                    @csrf
                                    <input type="hidden" id="selected_vendor_id" name="vendor_id" value="{{ $isVendorUser ? $vendors->first()['id'] ?? '' : '' }}">
                                    <input type="hidden" id="selected_product_id" name="product_id">
                                    <input type="hidden" name="configuration_type" value="variants">

                                    <!-- Selected Product Info -->
                                    <div class="card mb-4" id="selected-product-info" style="display: none;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <img id="product-image" src="" alt="Product" class="product-image me-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                                <div>
                                                    <h6 id="product-title" class="mb-1"></h6>
                                                    <p id="product-category" class="text-muted mb-0"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Existing Variants Section -->
                                    <div class="card" id="variants-management-section">
                                        <div class="card-header">
                                            <h5 class="mb-0">
                                                <i class="uil uil-layer-group me-2"></i>
                                                {{ __('catalogmanagement::product.product_variants_stock') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Loading state -->
                                            <div id="variants-loading" class="text-center py-4" style="display: none;">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">{{ __('common.loading') }}...</span>
                                                </div>
                                                <p class="mt-2 mb-0 text-muted">{{ __('catalogmanagement::product.loading_variants') }}...</p>
                                            </div>

                                            <!-- No variants state -->
                                            <div id="no-variants-state" class="text-center py-4" style="display: none;">
                                                <i class="uil uil-layer-group text-muted" style="font-size: 48px;"></i>
                                                <p class="text-muted mb-0">{{ __('catalogmanagement::product.no_variants_found') }}</p>
                                            </div>

                                            <!-- Variants Container -->
                                            <div id="variants-container">
                                                <!-- Existing variants will be loaded here -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Save Button -->
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="button" id="save-variant-stocks" class="btn btn-success">
                                            <i class="uil uil-check me-1"></i>
                                            {{ __('catalogmanagement::product.save_variant_stocks') }}
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Existing Variant Stock Management Template --}}
<template id="existing-variant-template">
    <div class="card mb-3 existing-variant-card" data-variant-id="__VARIANT_ID__">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="uil uil-layer-group me-2"></i>
                <span class="variant-name">__VARIANT_NAME__</span>
                <span class="badge badge-info ms-2">__VARIANT_SKU__</span>
            </h6>
        </div>
        <div class="card-body">
            <input type="hidden" name="variants[__VARIANT_ID__][id]" value="__VARIANT_ID__">
            <input type="hidden" name="variants[__VARIANT_ID__][variant_configuration_id]" value="__VARIANT_CONFIG_ID__">

            <!-- Variant Details -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">{{ __('catalogmanagement::product.sku') }}</label>
                    <input type="text" name="variants[__VARIANT_ID__][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" value="__VARIANT_SKU__" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">{{ __('catalogmanagement::product.price') }}</label>
                    <input type="number" name="variants[__VARIANT_ID__][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" value="__VARIANT_PRICE__" step="0.01" min="0" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">{{ __('catalogmanagement::product.current_total_stock') }}</label>
                    <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15 total-stock-display" value="__TOTAL_STOCK__" readonly>
                </div>
            </div>

            <!-- Stock Management -->
            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('catalogmanagement::product.stock_per_region') }}</label>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="userDatatable-header">
                                <th style="width: 50%;">{{ __('catalogmanagement::product.region') }}</th>
                                <th style="width: 35%;">{{ __('catalogmanagement::product.quantity') }}</th>
                                <th style="width: 15%; text-align: center;">{{ __('catalogmanagement::product.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="variant-stock-rows" id="variant-__VARIANT_ID__-stock-rows">
                            <!-- Stock rows will be populated here -->
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f8f9fa;">
                                <td class="text-end"><strong>{{ __('catalogmanagement::product.total_stock') }}:</strong></td>
                                <td><span class="badge badge-primary total-stock-display">__TOTAL_STOCK__</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="btn btn-primary btn-sm mt-2 add-variant-stock-row" data-variant-id="__VARIANT_ID__">
                    <i class="uil uil-plus me-1"></i> {{ __('catalogmanagement::product.add_region') }}
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Stock Row Template --}}
<template id="stock-row-template">
    <tr class="stock-row" data-stock-id="__STOCK_ID__">
        <td>
            <select name="variants[__VARIANT_ID__][stocks][__STOCK_INDEX__][region_id]" class="form-control select2 region-select" required>
                <option value="">{{ __('catalogmanagement::product.select_region') }}</option>
                <!-- Regions will be populated dynamically -->
            </select>
            <input type="hidden" name="variants[__VARIANT_ID__][stocks][__STOCK_INDEX__][id]" value="__STOCK_ID__">
        </td>
        <td>
            <input type="number" name="variants[__VARIANT_ID__][stocks][__STOCK_INDEX__][quantity]" class="form-control ih-medium ip-gray radius-xs b-light px-15 stock-quantity" value="__STOCK_QUANTITY__" min="0" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-stock-row">
                <i class="uil uil-trash-alt"></i>
            </button>
        </td>
    </tr>
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
