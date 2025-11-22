@extends('layout.app')
@section('title', __('catalogmanagement::product.stock_management'))

@push('styles')
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                [
                    'title' => trans('dashboard.title'),
                    'url' => route('admin.dashboard'),
                    'icon' => 'uil uil-estate',
                ],
                [
                    'title' => trans('catalogmanagement::product.products_management'),
                    'url' => route('admin.products.index'),
                    'icon' => 'uil uil-box',
                ],
                [
                    'title' => __('catalogmanagement::product.stock_management'),
                    'url' => '#',
                    'icon' => 'uil uil-package',
                ]
            ]" />

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="uil uil-package me-2"></i>
                                {{ __('catalogmanagement::product.stock_management') }}
                            </h4>
                            <p class="text-muted mb-0">
                                {{ __('catalogmanagement::product.manage_product_pricing_stock') }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-light btn-sm">
                                <i class="uil uil-eye"></i> {{ __('catalogmanagement::product.view_product') }}
                            </a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit"></i> {{ __('catalogmanagement::product.edit_product') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Product Information Summary -->
                    <div class="alert alert-info mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2">
                                    <i class="uil uil-info-circle me-2"></i>
                                    {{ $product->product->getTranslation('title', app()->getLocale()) ?? $product->product->getTranslation('title', 'en') ?? 'Product' }}
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block"><strong>{{ __('catalogmanagement::product.sku') }}:</strong> {{ $product->sku ?? '-' }}</small>
                                        <small class="text-muted d-block"><strong>{{ __('catalogmanagement::product.brand') }}:</strong> {{ $product->product->brand ? $product->product->brand->getTranslation('name', app()->getLocale()) : '-' }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block"><strong>{{ __('catalogmanagement::product.category') }}:</strong> {{ $product->product->category ? $product->product->category->getTranslation('name', app()->getLocale()) : '-' }}</small>
                                        <small class="text-muted d-block"><strong>{{ __('catalogmanagement::product.vendor') }}:</strong> {{ $product->vendor ? $product->vendor->getTranslation('name', app()->getLocale()) : '-' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge badge-{{ $product->product->configuration_type === 'simple' ? 'primary' : 'success' }} badge-lg">
                                    {{ $product->product->configuration_type === 'simple' ? __('catalogmanagement::product.simple_product') : __('catalogmanagement::product.with_variants') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form id="stockManagementForm" action="{{ route('admin.products.update-stock-pricing', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                                                <option value="simple" {{ ($product->configuration_type ?? $product->product->configuration_type ?? '') == 'simple' ? 'selected' : '' }}>{{ __('catalogmanagement::product.simple_product') }}</option>
                                                <option value="variants" {{ ($product->configuration_type ?? $product->product->configuration_type ?? '') == 'variants' ? 'selected' : '' }}>{{ __('catalogmanagement::product.with_variants') }}</option>
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

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-squared">
                                <i class="uil uil-arrow-left"></i> {{ __('common.back') }}
                            </a>
                            <button type="submit" class="btn btn-success btn-squared">
                                <i class="uil uil-check"></i> {{ __('catalogmanagement::product.update_stock_pricing') }}
                            </button>
                        </div>
                    </form>
                </div>
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
                @foreach($regions as $region)
                    <option value="{{ $region['id'] }}">{{ $region['name'] }}</option>
                @endforeach
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

{{-- Variant Configuration Template --}}
<template id="variant-template">
    <div class="variant-box border rounded p-4 mb-4" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="uil uil-layer-group"></i>
                {{ __('catalogmanagement::product.variant') }} <span class="variant-number">__INDEX__</span>
            </h6>
            <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                <i class="uil uil-trash"></i>
            </button>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('catalogmanagement::product.variant_key') }} <span class="text-danger">*</span></label>
                <select name="variants[__INDEX__][variant_key_id]" class="form-control select2 variant-key-select" required>
                    <option value="">{{ __('catalogmanagement::product.select_variant_key') }}</option>
                    @foreach($variantKeys as $key)
                        <option value="{{ $key['id'] }}">{{ $key['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('catalogmanagement::product.variant_value') }} <span class="text-danger">*</span></label>
                <select name="variants[__INDEX__][variant_value_id]" class="form-control select2 variant-value-select" required>
                    <option value="">{{ __('catalogmanagement::product.select_variant_value') }}</option>
                </select>
            </div>
        </div>

        <!-- Pricing & Stock will be inserted here -->
        <div class="variant-pricing-stock"></div>
    </div>
</template>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // ============================================
    // Configuration & Global Variables
    // ============================================
    const config = {
        locale: '{{ app()->getLocale() }}',
        apiBaseUrl: '{{ url("/api") }}',
        translations: {
            loading: '{{ __("common.loading") }}',
            selectOption: '{{ __("common.select_option") }}',
            error: '{{ __("common.error") }}',
            success: '{{ __("common.success") }}'
        },
        selectedValues: {
            configuration_type: '{{ $product->configuration_type ?? $product->product->configuration_type ?? '' }}',
            @php
                $configurationType = $product->configuration_type ?? $product->product->configuration_type ?? '';
                $firstVariant = $product->variants->first();
            @endphp
            @if($configurationType === 'simple')
                {{-- Simple product: use first variant data --}}
                price: {{ $firstVariant ? $firstVariant->price : 0 }},
                has_discount: {{ ($firstVariant && $firstVariant->has_discount) ? 'true' : 'false' }},
                price_before_discount: {{ $firstVariant ? $firstVariant->price_before_discount : 0 }},
                discount_end_date: '{{ $firstVariant && $firstVariant->discount_end_date ? $firstVariant->discount_end_date->format('Y-m-d') : '' }}',
                stocks: @json($firstVariant && $firstVariant->stocks ? $firstVariant->stocks : []),
            @else
                {{-- Variant product: pass all variants with their data --}}
                @php
                    $variantsData = $product->variants->map(function($variant) {
                        return [
                            'id' => $variant->id,
                            'variant_configuration_id' => $variant->variant_configuration_id,
                            'sku' => $variant->sku,
                            'price' => $variant->price,
                            'has_discount' => $variant->has_discount,
                            'price_before_discount' => $variant->price_before_discount,
                            'discount_end_date' => $variant->discount_end_date ? $variant->discount_end_date->format('Y-m-d') : null,
                            'stocks' => $variant->stocks,
                            'variant_configuration' => $variant->variantConfiguration
                        ];
                    });
                @endphp
                variantsData: @json($variantsData),
            @endif
            variants: @json($product->variants ?: [])
        }
    };

    // ============================================
    // Initialize Select2
    // ============================================
    function initializeSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: config.translations.selectOption,
                allowClear: true
            });
            console.log('✅ Select2 initialized for stock management');
        } else {
            setTimeout(initializeSelect2, 200);
        }
    }
    initializeSelect2();

    // ============================================
    // Configuration Type Handler
    // ============================================
    function handleConfigurationTypeChange() {
        const configurationType = $('#configuration_type').val();
        console.log('📍 Configuration type changed to:', configurationType);

        // Hide both sections first
        $('#simple-product-section, #variants-section').hide();

        if (configurationType === 'simple') {
            $('#simple-product-section').show();
            populateSimpleProductFields();
        } else if (configurationType === 'variants') {
            $('#variants-section').show();
            populateExistingVariants();
        }
    }

    // Configuration type change event
    $('#configuration_type').on('change', handleConfigurationTypeChange);

    // ============================================
    // Simple Product Functions
    // ============================================
    function populateSimpleProductFields() {
        console.log('📍 Populating simple product fields');

        // Clear existing content
        $('#simple-product-pricing-stock').empty();

        // Create pricing & stock box for simple product
        const pricingStockHtml = createPricingStockBox(0, 'simple_product');
        $('#simple-product-pricing-stock').append(pricingStockHtml);

        // Initialize Select2 for new elements
        setTimeout(() => {
            $('#simple-product-pricing-stock .select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: config.translations.selectOption,
                allowClear: true
            });
        }, 100);

        // Populate with existing data if available
        if (config.selectedValues.price) {
            $('input[name="simple_product[price]"]').val(config.selectedValues.price);
        }
        if (config.selectedValues.has_discount === 'true') {
            $('input[name="simple_product[has_discount]"]').prop('checked', true).trigger('change');
            $('input[name="simple_product[price_before_discount]"]').val(config.selectedValues.price_before_discount);
            $('input[name="simple_product[discount_end_date]"]').val(config.selectedValues.discount_end_date);
        }

        // Populate stocks
        if (config.selectedValues.stocks && config.selectedValues.stocks.length > 0) {
            const stockContainer = $('#simple-product-pricing-stock .stock-rows');
            config.selectedValues.stocks.forEach((stock, index) => {
                addStockRow(stockContainer, 'simple_product', index, stock);
            });
            updateTotalQuantity(stockContainer.closest('.pricing-stock-box'));
        }
    }

    // ============================================
    // Variants Functions
    // ============================================
    function populateExistingVariants() {
        console.log('📍 Populating existing variants');

        if (config.selectedValues.variantsData && config.selectedValues.variantsData.length > 0) {
            $('#variants-empty-state').hide();

            config.selectedValues.variantsData.forEach((variantData, index) => {
                setTimeout(() => {
                    addVariantBox(variantData, index);
                }, index * 200); // Stagger the population
            });
        } else {
            $('#variants-empty-state').show();
        }
    }

    function addVariantBox(variantData = null, index = null) {
        const variantIndex = index !== null ? index : $('.variant-box').length;
        const variantTemplate = $('#variant-template').html();
        const variantHtml = variantTemplate.replace(/__INDEX__/g, variantIndex);

        $('#variants-container').append(variantHtml);
        $('#variants-empty-state').hide();

        const $variantBox = $(`.variant-box[data-index="${variantIndex}"]`);

        // Initialize Select2 for variant selects
        setTimeout(() => {
            $variantBox.find('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: config.translations.selectOption,
                allowClear: true
            });

            // If we have variant data, populate it
            if (variantData) {
                populateVariantData($variantBox, variantData, variantIndex);
            }
        }, 100);

        // Add pricing & stock box
        const pricingStockHtml = createPricingStockBox(variantIndex, `variants[${variantIndex}]`);
        $variantBox.find('.variant-pricing-stock').html(pricingStockHtml);

        // Initialize Select2 for pricing & stock elements
        setTimeout(() => {
            $variantBox.find('.variant-pricing-stock .select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: config.translations.selectOption,
                allowClear: true
            });
        }, 200);
    }

    function populateVariantData($variantBox, variantData, variantIndex) {
        console.log('📍 Populating variant data for index:', variantIndex, variantData);

        // Set variant configuration if available
        if (variantData.variant_configuration) {
            const keyId = variantData.variant_configuration.variant_key_id;
            const valueId = variantData.variant_configuration.variant_value_id;

            $variantBox.find('.variant-key-select').val(keyId).trigger('change');

            // Load variant values and then set the selected value
            setTimeout(() => {
                loadVariantValues(keyId, $variantBox.find('.variant-value-select'), () => {
                    $variantBox.find('.variant-value-select').val(valueId).trigger('change');
                });
            }, 100);
        }

        // Populate pricing data
        if (variantData.sku) {
            $variantBox.find('input[name$="[sku]"]').val(variantData.sku);
        }
        if (variantData.price) {
            $variantBox.find('input[name$="[price]"]').val(variantData.price);
        }
        if (variantData.has_discount) {
            $variantBox.find('input[name$="[has_discount]"]').prop('checked', true).trigger('change');
            $variantBox.find('input[name$="[price_before_discount]"]').val(variantData.price_before_discount);
            $variantBox.find('input[name$="[discount_end_date]"]').val(variantData.discount_end_date);
        }

        // Populate stocks
        if (variantData.stocks && variantData.stocks.length > 0) {
            const stockContainer = $variantBox.find('.stock-rows');
            variantData.stocks.forEach((stock, stockIndex) => {
                addStockRow(stockContainer, `variants[${variantIndex}]`, stockIndex, stock);
            });
            updateTotalQuantity($variantBox.find('.pricing-stock-box'));
        }
    }

    // ============================================
    // Pricing & Stock Functions
    // ============================================
    function createPricingStockBox(index, namePrefix) {
        const template = $('#pricing-stock-template').html();
        return template.replace(/__INDEX__/g, index).replace(/__NAME_PREFIX__/g, namePrefix);
    }

    function addStockRow(container, namePrefix, stockIndex, stockData = null) {
        const template = $('#stock-row-template').html();
        const stockHtml = template
            .replace(/__NAME_PREFIX__/g, namePrefix)
            .replace(/__STOCK_INDEX__/g, stockIndex);

        container.append(stockHtml);

        const $row = container.find('.stock-row').last();

        // Initialize Select2 for the new row
        setTimeout(() => {
            $row.find('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: config.translations.selectOption,
                allowClear: true
            });

            // Populate with existing data if available
            if (stockData) {
                $row.find('.region-select').val(stockData.region_id).trigger('change');
                $row.find('.quantity-input').val(stockData.stock);
            }
        }, 100);
    }

    // ============================================
    // Event Handlers
    // ============================================

    // Add stock row
    $(document).on('click', '.add-stock-row', function() {
        const $pricingBox = $(this).closest('.pricing-stock-box');
        const stockContainer = $pricingBox.find('.stock-rows');
        const stockIndex = stockContainer.find('.stock-row').length;
        const namePrefix = $pricingBox.find('input[name$="[price]"]').attr('name').replace('[price]', '');

        addStockRow(stockContainer, namePrefix, stockIndex);
    });

    // Remove stock row
    $(document).on('click', '.remove-stock-row', function() {
        const $row = $(this).closest('.stock-row');
        const $pricingBox = $row.closest('.pricing-stock-box');
        $row.remove();
        updateTotalQuantity($pricingBox);
    });

    // Update total quantity when quantity changes
    $(document).on('input', '.quantity-input', function() {
        const $pricingBox = $(this).closest('.pricing-stock-box');
        updateTotalQuantity($pricingBox);
    });

    // Discount toggle
    $(document).on('change', '.has-discount-switch', function() {
        const $pricingBox = $(this).closest('.pricing-stock-box');
        const $discountFields = $pricingBox.find('.discount-fields');

        if ($(this).is(':checked')) {
            $discountFields.show();
        } else {
            $discountFields.hide();
            $discountFields.find('input').val('');
        }
    });

    // Add variant
    $(document).on('click', '#add-variant-btn', function() {
        addVariantBox();
    });

    // Remove variant
    $(document).on('click', '.remove-variant-btn', function() {
        $(this).closest('.variant-box').remove();

        if ($('.variant-box').length === 0) {
            $('#variants-empty-state').show();
        }
    });

    // Variant key change - load variant values
    $(document).on('change', '.variant-key-select', function() {
        const keyId = $(this).val();
        const $valueSelect = $(this).closest('.variant-box').find('.variant-value-select');

        if (keyId) {
            loadVariantValues(keyId, $valueSelect);
        } else {
            $valueSelect.empty().append('<option value="">{{ __("catalogmanagement::product.select_variant_value") }}</option>');
        }
    });

    // ============================================
    // Utility Functions
    // ============================================
    function updateTotalQuantity($pricingBox) {
        let total = 0;
        $pricingBox.find('.quantity-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            total += qty;
        });
        $pricingBox.find('.total-quantity-display').text(total);
    }

    function loadVariantValues(keyId, $valueSelect, callback = null) {
        $valueSelect.empty().append('<option value="">{{ __("common.loading") }}...</option>');

        $.get(`{{ url('/api/variant-configuration-keys') }}/${keyId}/values`)
            .done(function(response) {
                $valueSelect.empty().append('<option value="">{{ __("catalogmanagement::product.select_variant_value") }}</option>');

                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(value) {
                        $valueSelect.append(`<option value="${value.id}">${value.name}</option>`);
                    });
                }

                if (callback) callback();
            })
            .fail(function() {
                $valueSelect.empty().append('<option value="">{{ __("common.error_loading_data") }}</option>');
            });
    }

    // ============================================
    // Form Submission
    // ============================================
    $('#stockManagementForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Show loading
        LoadingOverlay.show({
            text: '{{ __("catalogmanagement::product.updating_stock_pricing") }}',
            subtext: '{{ __("common.please_wait") }}'
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                LoadingOverlay.showSuccess(
                    '{{ __("catalogmanagement::product.stock_pricing_updated") }}',
                    '{{ __("common.redirecting") }}'
                );

                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                LoadingOverlay.hide();

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    // Handle validation errors
                    Object.keys(errors).forEach(key => {
                        console.error('Validation error:', key, errors[key]);
                    });
                }

                toastr.error('{{ __("common.error_occurred") }}');
            }
        });
    });

    // ============================================
    // Initialize on page load
    // ============================================

    // Set initial configuration type and populate data
    if (config.selectedValues.configuration_type) {
        $('#configuration_type').val(config.selectedValues.configuration_type).trigger('change');

        // Trigger the change handler after a short delay
        setTimeout(() => {
            handleConfigurationTypeChange();
        }, 300);
    }
});
</script>
@endpush
