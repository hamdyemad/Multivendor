<form id="stockForm" method="POST">
    @csrf
    <input type="hidden" name="product_id" id="form_product_id">
    <input type="hidden" name="vendor_id" id="form_vendor_id">
    <input type="hidden" name="vendor_product_id" id="form_vendor_product_id">

    <!-- General Settings (only shown for NEW vendor products) -->
    <div class="card mb-4" id="general-settings-section">
        <div class="card-header">
            <h6 class="mb-0"><i class="uil uil-setting me-2"></i>{{ __('catalogmanagement::product.general_settings') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="configuration_type" class="form-label">{{ __('catalogmanagement::product.product_type') }} <span class="text-danger">*</span></label>
                    <select name="configuration_type" id="configuration_type" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                        <option value="">{{ __('catalogmanagement::product.select_product_type') }}</option>
                        <option value="simple">{{ __('catalogmanagement::product.simple_product') }}</option>
                        <option value="variants">{{ __('catalogmanagement::product.with_variants') }}</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tax_id" class="form-label">{{ __('catalogmanagement::product.tax') }}</label>
                    <select name="tax_id" id="tax_id" class="form-control ih-medium ip-gray radius-xs b-light px-15 select2">
                        <option value="">{{ __('catalogmanagement::product.select_tax') }}</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax['id'] }}">{{ $tax['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="vendor_sku" class="form-label">{{ __('catalogmanagement::product.vendor_sku') }}</label>
                    <input type="text" name="sku" id="vendor_sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="{{ __('catalogmanagement::product.enter_vendor_sku') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="points" class="form-label">{{ __('catalogmanagement::product.points') }}</label>
                    <input type="number" name="points" id="points" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" value="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_per_order" class="form-label">{{ __('catalogmanagement::product.max_per_order') }}</label>
                    <input type="number" name="max_per_order" id="max_per_order" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="1" value="10">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="video_link" class="form-label">{{ __('catalogmanagement::product.video_link') }}</label>
                    <input type="url" name="video_link" id="video_link" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="https://youtube.com/...">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label mb-2">{{ __('catalogmanagement::product.is_active') }}</label>
                    <div class="form-check form-switch form-switch-lg">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label mb-2">{{ __('catalogmanagement::product.is_featured') }}</label>
                    <div class="form-check form-switch form-switch-lg">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label mb-2">{{ __('catalogmanagement::product.offer_date_view') }}</label>
                    <div class="form-check form-switch form-switch-lg">
                        <input type="hidden" name="offer_date_view" value="0">
                        <input class="form-check-input" type="checkbox" name="offer_date_view" id="offer_date_view" value="1">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Product Section -->
    <div id="simple-product-section" style="display: none;">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="uil uil-package me-2"></i>{{ __('catalogmanagement::product.simple_product_configuration') }}</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('catalogmanagement::product.sku') }} <span class="text-danger">*</span></label>
                        <input type="text" name="sku" id="simple_sku" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('catalogmanagement::product.price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="simple_price" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold mb-2">{{ __('catalogmanagement::product.enable_discount_offer') }}</label>
                    <div class="form-check form-switch form-switch-lg">
                        <input type="hidden" name="has_discount" value="0">
                        <input type="checkbox" name="has_discount" class="form-check-input" id="simple_discount" value="1">
                    </div>
                    <div id="simple_discount_fields" class="mt-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('catalogmanagement::product.price_before_discount') }}</label>
                                <input type="number" name="price_before_discount" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('catalogmanagement::product.discount_end_date') }}</label>
                                <input type="date" name="discount_end_date" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">{{ __('catalogmanagement::product.stock_per_region') }} <span class="text-danger">*</span></label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="simple-stock-table">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th style="width: 50%;">{{ __('catalogmanagement::product.region') }}</th>
                                    <th style="width: 35%;">{{ __('catalogmanagement::product.quantity') }}</th>
                                    <th style="width: 15%; text-align: center;">{{ __('catalogmanagement::product.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="simple-stock-rows"></tbody>
                            <tfoot>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-end"><strong>{{ __('catalogmanagement::product.total_stock') }}:</strong></td>
                                    <td><span class="badge badge-primary total-stock-display">0</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-primary mt-3" id="add-simple-stock-row">
                        <i class="uil uil-plus me-1"></i> {{ __('catalogmanagement::product.add_region') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Variants Section -->
    <div id="variants-section" style="display: none;">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="d-flex justify-content-between align-items-center mb-4">
                    <div><i class="uil uil-layer-group"></i> {{ __('catalogmanagement::product.product_variants') }}</div>
                    <button type="button" id="add-variant-btn" class="btn btn-primary btn-sm">
                        <i class="uil uil-plus"></i> {{ __('catalogmanagement::product.add_variant') }}
                    </button>
                </h5>
                <div id="variants-empty-state" class="text-center py-4">
                    <i class="uil uil-layer-group text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mb-0">{{ __('catalogmanagement::product.no_variants_added') }}</p>
                </div>
                <div id="existing-variants-container"></div>
                <div id="variants-container"></div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.products.bank') }}" class="btn btn-light btn-squared">
            <i class="uil uil-arrow-left"></i> {{ __('common.back') }}
        </a>
        <button type="submit" id="submitBtn" class="btn btn-success btn-squared">
            <i class="uil uil-check"></i> {{ __('catalogmanagement::product.save_stock') }}
        </button>
    </div>
</form>

{{-- Variant Box Template --}}
<template id="variant-box-template">
    <div class="card mb-3 variant-box" data-variant-index="__VARIANT_INDEX__" id="variant-__VARIANT_INDEX__">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="uil uil-layer-group"></i> {{ __('common.variant') }} #__VARIANT_NUMBER__</h6>
            <button type="button" class="btn btn-danger btn-sm remove-variant-btn"><i class="uil uil-trash-alt"></i></button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">{{ __('catalogmanagement::product.variant_key') }} <span class="text-danger">*</span></label>
                    <select class="form-control ih-medium ip-gray radius-xs b-light px-15 select2 variant-key-select" required>
                        <option value="">{{ __('catalogmanagement::product.select_variant_key') }}</option>
                    </select>
                </div>
            </div>
            <div class="variant-tree-container" style="display: none;">
                <label class="form-label">{{ __('catalogmanagement::product.variant_selection') }}</label>
                <div class="variant-tree-levels"></div>
                <input type="hidden" name="variants[__VARIANT_INDEX__][variant_configuration_id]" class="selected-variant-id">
                <div class="alert alert-info mt-2 selected-variant-path" style="display: none;">
                    <strong>{{ __('catalogmanagement::product.selected_variant') }}:</strong> <span class="path-text"></span>
                </div>
            </div>
            <div id="variant-__VARIANT_INDEX__-pricing-stock" class="variant-pricing-stock" style="display: none;">
                <hr>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('catalogmanagement::product.sku') }} <span class="text-danger">*</span></label>
                        <input type="text" name="variants[__VARIANT_INDEX__][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('catalogmanagement::product.price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="variants[__VARIANT_INDEX__][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold mb-2">{{ __('catalogmanagement::product.enable_discount_offer') }}</label>
                    <div class="form-check form-switch form-switch-lg">
                        <input type="hidden" name="variants[__VARIANT_INDEX__][has_discount]" value="0">
                        <input type="checkbox" name="variants[__VARIANT_INDEX__][has_discount]" class="form-check-input variant-discount-switch" value="1">
                    </div>
                    <div class="variant-discount-fields mt-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('catalogmanagement::product.price_before_discount') }}</label>
                                <input type="number" name="variants[__VARIANT_INDEX__][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" step="0.01" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('catalogmanagement::product.discount_end_date') }}</label>
                                <input type="date" name="variants[__VARIANT_INDEX__][discount_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                            </div>
                        </div>
                    </div>
                </div>
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
                            <tbody class="variant-stock-rows" id="variant-__VARIANT_INDEX__-stock-rows"></tbody>
                            <tfoot>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-end"><strong>{{ __('catalogmanagement::product.total_stock') }}:</strong></td>
                                    <td><span class="badge badge-primary total-stock-display">0</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-2 add-variant-stock-row" data-variant-index="__VARIANT_INDEX__">
                        <i class="uil uil-plus me-1"></i> {{ __('catalogmanagement::product.add_region') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
