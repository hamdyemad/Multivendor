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

    /* Variant Management Boxes */
    .variant-management-box {
        margin-bottom: 24px;
    }
    .variant-management-box .card {
        border: 2px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .variant-management-box .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
        padding: 16px 20px;
    }
    .variant-management-box .card-header h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .variant-management-box .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e9ecef;
    }
    .variant-management-box .pricing-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .variant-management-box .stock-section {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .variant-stock-row {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }
    .variant-stock-row:hover {
        background-color: #e9ecef;
        border-color: var(--color-primary);
    }

    /* Form Group and Error Message Styling */
    .form-group {
        margin-bottom: 1.5rem;
        min-height: 80px; /* Ensure consistent height for form groups */
    }

    .error-message {
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
        width: 100%;
        line-height: 1.4;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .select2-container--bootstrap-5 .select2-selection.is-invalid {
        border-color: #dc3545 !important;
    }

    /* Stock Management Table Styling */
    .table td {
        vertical-align: middle;
        padding: 12px 8px;
    }

    .stock-row td {
        position: relative;
    }

    .stock-row .form-control {
        margin-bottom: 0;
    }

    .stock-row .error-message {
        position: absolute;
        top: 100%;
        left: 8px;
        right: 8px;
        z-index: 10;
        background: rgba(255, 255, 255, 0.95);
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.75rem;
        margin-top: 2px;
    }

    /* Ensure table rows have enough space for error messages */
    .variant-stock-rows tr {
        height: auto;
        min-height: 60px;
    }

    .variant-stock-rows td {
        padding-bottom: 25px; /* Extra space for error messages */
    }

    /* Global vendor product section styling */
    #global-vendor-product-section .form-group {
        min-height: 85px; /* Slightly more space for global fields */
    }

    #global-vendor-product-section .error-message {
        margin-top: 0.5rem;
    }

    /* Additional error message styling for dynamically generated content */
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
        line-height: 1.4;
    }

    /* Ensure proper spacing in variant cards */
    .existing-variant-card .form-group {
        margin-bottom: 1rem;
        min-height: 70px;
    }

    .existing-variant-card .error-message {
        margin-top: 0.25rem;
        font-size: 0.8rem;
    }

    /* Stock table specific adjustments */
    .table .form-control {
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .table .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Alert styling for better error presentation */
    .alert.alert-danger {
        border-left: 4px solid #dc3545;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert.alert-warning {
        border-left: 4px solid #ffc107;
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
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
                                <h5 class="mb-0">{{ trans('catalogmanagement::product.select_vendor') }}</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <select id="vendor_select" class="form-control select2" style="width: 100%;">
                                        <option value="">{{ trans('catalogmanagement::product.select_vendor') }}</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor['id'] }}">{{ $vendor['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="vendor-info" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="uil uil-info-circle me-2"></i>
                                    <span id="vendor-name" class="me-1"></span> {{ trans('catalogmanagement::product.vendor_selected') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 2: Select Product -->
                    <div class="selection-step" id="step-products" @if(!$isVendorUser) style="opacity: 0.5; pointer-events: none;" @endif>
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">{{ $isVendorUser ? '1' : '2' }}</span>
                            <h5 class="mb-0">{{ trans('catalogmanagement::product.select_product') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="products-container" style="display: none;">
                                    <div class="mb-3">
                                        <input type="text" id="product-search" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ trans('catalogmanagement::product.search_products') }}">

                                        <!-- Search Loading Indicator -->
                                        <div id="products-loading" style="display: none;">
                                            <div class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">{{ __('common.loading') }}...</span>
                                                </div>
                                                <p class="mt-2 mb-0 text-muted small">{{ trans('catalogmanagement::product.searching_products') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="products-list" class="row">
                                        <!-- Products will be loaded here -->
                                    </div>
                                    <div id="no-products" class="alert alert-warning" style="display: none;">
                                        <i class="uil uil-exclamation-triangle me-2"></i>
                                        {{ trans('catalogmanagement::product.no_available_products') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="selected-product-summary" class="mt-3" style="display: none;">
                            <div class="alert alert-success">
                                <i class="uil uil-check-circle me-2"></i>
                                <span id="selected-product-name" class="me-1"></span> {{ trans('catalogmanagement::product.product_selected') }}
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Manage Product Variants & Stock -->
                    <div class="selection-step" id="step-variant-stock-management" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">{{ $isVendorUser ? '2' : '3' }}</span>
                            <h5 class="mb-0">{{ trans('catalogmanagement::product.manage_variants_stock') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <form id="vendor-product-form">
                                    @csrf
                                    <input type="hidden" id="selected_vendor_id" name="vendor_id" value="{{ $isVendorUser ? $vendors->first()['id'] ?? '' : '' }}">
                                    <input type="hidden" id="selected_product_id" name="product_id">
                                    <input type="hidden" name="configuration_type" value="variants">

                                    <!-- Global Vendor Product Information (Tax & Max Per Order) -->
                                    <div class="card mb-4" id="global-vendor-product-section">
                                        <div class="card-header">
                                            <h6 class="mb-0" style="font-weight: 600; font-size: 16px;">
                                                <i class="uil uil-building me-2"></i>
                                                {{ __('catalogmanagement::product.vendor_product_settings') ?? 'Vendor Product Settings' }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label for="tax_id" class="form-label fw-bold">{{ __('catalogmanagement::product.tax') }} <span class="text-danger">*</span></label>
                                                        <select name="tax_id" id="tax_id" class="form-control select2">
                                                            <option value="">{{ __('common.select_option') }}</option>
                                                            <!-- Tax options will be populated via JavaScript -->
                                                        </select>
                                                        <div class="error-message text-danger" id="error-tax_id" style="display: none;"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label for="max_per_order" class="form-label fw-bold">{{ __('catalogmanagement::product.max_per_order') }} <span class="text-danger">*</span></label>
                                                        <input type="number" name="max_per_order" id="max_per_order" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="1" required>
                                                        <div class="error-message text-danger" id="error-max_per_order" style="display: none;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                                        <i class="uil uil-info-circle me-2"></i>
                                                        <div>
                                                            <strong>{{ __('common.note') }}:</strong>
                                                            {{ __('catalogmanagement::product.global_settings_note') ?? 'These settings apply to all variants of this product for this vendor.' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Existing Variants Section -->
                                    <div class="card" id="variants-management-section">
                                        <div class="card-body">
                                            <!-- Loading state -->
                                            <div id="variants-loading" class="text-center py-4" style="display: none;">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">{{ __('common.loading') }}...</span>
                                                </div>
                                                <p class="mt-2 mb-0 text-muted">{{ trans('catalogmanagement::product.loading_variants') }}</p>
                                            </div>

                                            <!-- No variants state -->
                                            <div id="no-variants-state" class="text-center py-4" style="display: none;">
                                                <i class="uil uil-layer-group text-muted" style="font-size: 48px;"></i>
                                                <p class="text-muted mb-0">{{ trans('catalogmanagement::product.no_variants_found') }}</p>
                                            </div>

                                            <!-- Variants Container -->
                                            <div id="variants-container">
                                                <!-- Existing variants will be loaded here -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Save Button -->
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="button" id="save-form" class="btn btn-success">
                                            <i class="uil uil-check me-1"></i>
                                            {{ __('common.save') }}
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
<script>
    // Pass taxes data from controller to JavaScript
    window.bankStockConfig = {
        taxes: @json($taxes->map(function($tax) {
            return [
                'id' => $tax->id,
                'name' => $tax->name,
                'percentage' => $tax->percentage ?? 0
            ];
        })),
        isVendorUser: @json($isVendorUser),
        vendors: @json($vendors->map(function($vendor) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name
            ];
        }))
    };
</script>
@include('catalogmanagement::product.partials.bank-stock-scripts')
@endpush
