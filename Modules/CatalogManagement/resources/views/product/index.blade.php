@extends('layout.app')

@php
    // Determine if user is admin
    $isAdmin = isAdmin();
    
    // Build table headers
    $tableHeaders = [
        ['label' => '<input type="checkbox" id="selectAllProducts" class="form-check-input" style="cursor: pointer;">', 'class' => 'text-center', 'style' => 'width: 40px;', 'raw' => true],
        ['label' => '#', 'class' => 'text-center'],
        ['label' => trans('catalogmanagement::product.product_information')],
    ];
    
    if ($isAdmin) {
        $tableHeaders[] = ['label' => trans('catalogmanagement::product.vendor')];
    }
    
    $tableHeaders[] = ['label' => trans('catalogmanagement::product.approval_status'), 'class' => 'text-center'];
    $tableHeaders[] = ['label' => trans('common.activation'), 'class' => 'text-center'];
    $tableHeaders[] = ['label' => trans('common.actions'), 'class' => 'text-center'];

    // Custom select IDs for filters
    $customSelectIds = [
        'per_page_filter',
        'brand_filter',
        'department_filter',
        'category_filter',
        'product_type',
        'configuration_filter',
        'active',
        'stock_filter'
    ];
    
    if ($isAdmin) {
        $customSelectIds[] = 'vendor_filter';
    }
    
    if (!isset($statusFilter)) {
        $customSelectIds[] = 'status';
    }

    // DataTable configuration
    $datatableConfig = [
        'tableId' => 'productsDataTable',
        'ajaxUrl' => route('admin.products.datatable'),
        'headers' => $tableHeaders,
        'customSelectIds' => $customSelectIds,
        'order' => [[1, 'desc']],
        'pageLength' => 10,
        'columnsJson' => 'COLUMNS_DEFINED_IN_SCRIPT',
        'additionalAjaxData' => isset($statusFilter) && $statusFilter ? "d.status = '{$statusFilter}';" : null,
    ];

    // Page title based on status filter
    $pageTitle = match($statusFilter ?? null) {
        'pending' => trans('menu.products.pending_products'),
        'rejected' => trans('menu.products.rejected_products'),
        'approved' => trans('menu.products.accepted_products'),
        default => trans('catalogmanagement::product.products_management'),
    };

    // Breadcrumb items
    $breadcrumbItems = [
        [
            'title' => trans('dashboard.title'),
            'url' => route('admin.dashboard'),
            'icon' => 'uil uil-estate',
        ],
        ['title' => $pageTitle],
    ];

    // Filter options
    $filterOptions = [
        'perPage' => [
            ['id' => '10', 'name' => '10'],
            ['id' => '25', 'name' => '25'],
            ['id' => '50', 'name' => '50'],
            ['id' => '100', 'name' => '100']
        ],
        'productType' => [
            ['id' => 'bank', 'name' => __('catalogmanagement::product.bank')],
            ['id' => 'product', 'name' => __('catalogmanagement::product.product')]
        ],
        'configuration' => [
            ['id' => 'simple', 'name' => __('catalogmanagement::product.simple_product') ?? 'Simple Product'],
            ['id' => 'variants', 'name' => __('catalogmanagement::product.variant_product') ?? 'Variant Product']
        ],
        'activeStatus' => [
            ['id' => '1', 'name' => __('common.active')],
            ['id' => '2', 'name' => __('common.inactive')]
        ],
        'stockStatus' => [
            ['id' => 'instock', 'name' => __('dashboard.instock')],
            ['id' => 'outofstock', 'name' => __('dashboard.out_of_stock')]
        ],
        'approvalStatus' => collect(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses())
            ->map(fn($label, $value) => ['id' => $value, 'name' => $label])
            ->values()
            ->toArray()
    ];
@endphp

@section('title', $pageTitle ?? trans('catalogmanagement::product.products_management'))

@push('styles')
<style>
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .rotating {
        animation: rotate 1s linear infinite;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="$breadcrumbItems" />
        </div>
    </div>

    {{-- Bank Products Card (Admin Only) --}}
    @can('products.bank')
    <div class="row mb-4">
        <div class="col-lg-12">
            <a href="{{ route('admin.products.bank') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm bg-gradient-primary text-white" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--second-primary) 100%);">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="uil uil-database" style="font-size: 2.5rem; opacity: 0.9;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-white fw-bold">{{ __('catalogmanagement::product.bank_products') }}</h5>
                                    <small class="text-white-50">{{ __('catalogmanagement::product.bank_products_description') ?? 'Manage shared products available for all vendors' }}</small>
                                </div>
                            </div>
                            <div>
                                <i class="uil uil-arrow-right" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endcan

    {{-- DataTable Wrapper Component --}}
    <x-datatable-wrapper
        :title="$pageTitle"
        icon="uil uil-box"
        :showExport="true"
        :createRoute="auth()->user()->can('products.create') ? route('admin.products.create') : null"
        :createText="trans('catalogmanagement::product.add_product')"
        :tableId="$datatableConfig['tableId']"
        :ajaxUrl="$datatableConfig['ajaxUrl']"
        :headers="$datatableConfig['headers']"
        :columnsJson="$datatableConfig['columnsJson']"
        :customSelectIds="$datatableConfig['customSelectIds']"
        :order="$datatableConfig['order']"
        :pageLength="$datatableConfig['pageLength']"
        :additionalAjaxData="$datatableConfig['additionalAjaxData']">
        
        {{-- Additional Buttons Slot --}}
        <x-slot name="additionalButtons">
            @can('products.create')
            <a href="{{ route('admin.products.bulk-upload') }}" class="btn btn-success btn-squared shadow-sm px-4">
                <i class="uil uil-upload"></i> {{ trans('catalogmanagement::product.bulk_upload') }}
            </a>
            @endcan
        </x-slot>

        {{-- Filters Slot --}}
        <x-slot name="filters">
            @include('catalogmanagement::product._filters')
        </x-slot>
    </x-datatable-wrapper>
</div>

{{-- Modals --}}
@include('catalogmanagement::product._modals')

@endsection

{{-- Scripts --}}
@push('scripts')
    @include('catalogmanagement::product._datatable-scripts')
    @include('catalogmanagement::product._custom-handlers')
@endpush
