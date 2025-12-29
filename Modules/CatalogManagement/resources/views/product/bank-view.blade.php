@extends('layout.app')

@section('title', __('catalogmanagement::product.view_bank_product'))

@push('styles')
    <style>
        /* Product View HTML Content Styling */
        .fs-15.color-dark {
            line-height: 1.6;
        }

        .fs-15.color-dark table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .fs-15.color-dark table th,
        .fs-15.color-dark table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e3e6f0;
        }

        .fs-15.color-dark table th {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }

        .fs-15.color-dark table tr:hover {
            background-color: #f8f9fa;
        }

        .fs-15.color-dark table tr:last-child td {
            border-bottom: none;
        }

        .fs-15.color-dark strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .fs-15.color-dark em {
            color: #7f8c8d;
            font-style: italic;
        }

        .fs-15.color-dark ul,
        .fs-15.color-dark ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .fs-15.color-dark li {
            margin-bottom: 5px;
            line-height: 1.5;
        }

        .fs-15.color-dark p {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .fs-15.color-dark h1,
        .fs-15.color-dark h2,
        .fs-15.color-dark h3,
        .fs-15.color-dark h4,
        .fs-15.color-dark h5,
        .fs-15.color-dark h6 {
            margin: 15px 0 10px 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .fs-15.color-dark blockquote {
            border-left: 4px solid #4e73df;
            padding-left: 15px;
            margin: 15px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }

        .fs-15.color-dark a {
            color: #4e73df;
            text-decoration: none;
        }

        .fs-15.color-dark a:hover {
            color: #224abe;
            text-decoration: underline;
        }

        /* Arabic content styling */
        .fs-15.color-dark[style*="direction: rtl"] {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .fs-15.color-dark[style*="direction: rtl"] table th,
        .fs-15.color-dark[style*="direction: rtl"] table td {
            text-align: right;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => __('catalogmanagement::product.bank_products_management'),
                        'url' => route('admin.products.bank'),
                    ],
                    ['title' => __('catalogmanagement::product.view_bank_product')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('catalogmanagement::product.bank_product_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.products.bank') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('common.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                {{-- Basic Information --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::product.basic_information') ?? 'Basic Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('catalogmanagement::product.title')" :model="$product" fieldName="title"
                                                :languages="$languages" />

                                            <x-translation-display :label="__('catalogmanagement::product.details')" :model="$product" fieldName="details"
                                                :languages="$languages" type="html" />

                                            <x-translation-display :label="__('common.summary')" :model="$product" fieldName="summary"
                                                :languages="$languages" type="html" />

                                            <x-translation-display :label="__('common.features')" :model="$product"
                                                fieldName="features" :languages="$languages" type="html" />

                                            <x-translation-display :label="__('common.instructions')" :model="$product"
                                                fieldName="instructions" :languages="$languages" type="html" />

                                            <x-translation-display :label="__('common.tags')" :model="$product" fieldName="tags"
                                                :languages="$languages" type="keywords" />
                                            {{-- Product Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.product_type') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">
                                                            {{ ucfirst($product->type) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Configuration Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.configuration_type') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        <span class="badge badge-secondary badge-round badge-lg">
                                                            {{ ucfirst($product->configuration_type) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Slug --}}
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.slug') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        <code>{{ $product->slug ?? '--' }}</code>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Category Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-folder me-1"></i>{{ __('catalogmanagement::product.category_information') ?? 'Category Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Brand --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.brand') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if ($product->brand)
                                                            <span class="badge badge-info badge-round badge-lg">
                                                                {{ $product->brand->name }}
                                                            </span>
                                                        @else
                                                            --
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Department --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.department') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if ($product->department)
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                {{ $product->department->name }}
                                                            </span>
                                                        @else
                                                            --
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Category --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.category') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if ($product->category)
                                                            <span class="badge badge-secondary badge-round badge-lg">
                                                                {{ $product->category->name }}
                                                            </span>
                                                        @else
                                                            --
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Sub Category --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.sub_category') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if ($product->subCategory)
                                                            <span class="badge badge-warning badge-round badge-lg">
                                                                {{ $product->subCategory->name }}
                                                            </span>
                                                        @else
                                                            --
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Product Variants --}}
                                @if ($product->variants && $product->variants->count() > 0)
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i
                                                    class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.product_variants') ?? 'Product Variants' }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($product->variants as $variantIndex => $variant)
                                                <div class="mb-4 pb-4"
                                                    style="@if (!$loop->last) border-bottom: 1px solid #e9ecef; @endif">
                                                    {{-- Variant Header --}}
                                                    <div class="mb-3">
                                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">

                                                            {{-- Variant Configuration --}}
                                                            @if ($variant->variantConfiguration)
                                                                <div class="variant-tree-display">
                                                                    @php
                                                                        // Build the variant hierarchy
                                                                        $values = [];
                                                                        $rootKeyName = '';
                                                                        $current = $variant->variantConfiguration;
                                                                        $visited = [];

                                                                        while (
                                                                            $current &&
                                                                            !in_array($current->id, $visited)
                                                                        ) {
                                                                            $visited[] = $current->id;
                                                                            $valueName =
                                                                                $current->getTranslation(
                                                                                    'name',
                                                                                    app()->getLocale(),
                                                                                ) ??
                                                                                ($current->getTranslation(
                                                                                    'name',
                                                                                    'en',
                                                                                ) ??
                                                                                    ($current->name ??
                                                                                        ($current->value ?? 'Value')));
                                                                            array_unshift($values, $valueName);

                                                                            if ($current->parent_data) {
                                                                                $current = $current->parent_data;
                                                                            } else {
                                                                                $rootKeyName = $current->key
                                                                                    ? $current->key->getTranslation(
                                                                                            'name',
                                                                                            app()->getLocale(),
                                                                                        ) ??
                                                                                        ($current->key->getTranslation(
                                                                                            'name',
                                                                                            'en',
                                                                                        ) ??
                                                                                            ($current->key->name ??
                                                                                                'Key'))
                                                                                    : 'Key';
                                                                                break;
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    @if (count($values) > 0)
                                                                        <div
                                                                            class="d-flex align-items-center flex-wrap gap-2">
                                                                            {{-- Root Key Badge --}}
                                                                            <span class="badge badge-lg"
                                                                                style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
                                                                                     color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                     box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: bold;">
                                                                                <i
                                                                                    class="uil uil-key-skeleton me-1"></i>{{ $rootKeyName }}
                                                                            </span>

                                                                            <span class="text-muted fw-bold">:</span>

                                                                            {{-- Values --}}
                                                                            @foreach ($values as $valueIndex => $value)
                                                                                @if ($valueIndex > 0)
                                                                                    <span
                                                                                        class="text-muted fw-bold">:</span>
                                                                                @endif

                                                                                <span class="badge badge-lg"
                                                                                    style="background: linear-gradient(135deg,
                                                                                         {{ $valueIndex % 3 === 0 ? '#17a2b8' : ($valueIndex % 3 === 1 ? '#28a745' : '#fd7e14') }} 0%,
                                                                                         {{ $valueIndex % 3 === 0 ? '#138496' : ($valueIndex % 3 === 1 ? '#218838' : '#e8590c') }} 100%);
                                                                                         color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                         box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                                                    <i
                                                                                        class="uil uil-tag me-1"></i>{{ $value }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Meta Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-tag me-1"></i>{{ __('catalogmanagement::product.meta_information') ?? 'Meta Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('catalogmanagement::product.meta_title')" :model="$product"
                                                fieldName="meta_title" :languages="$languages" />

                                            <x-translation-display :label="__('catalogmanagement::product.meta_description')" :model="$product"
                                                fieldName="meta_description" :languages="$languages" />

                                            <x-translation-display :label="__('catalogmanagement::product.meta_keywords')" :model="$product"
                                                fieldName="meta_keywords" :languages="$languages" type="keywords" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sidebar --}}
                            <div class="col-md-4 order-1 order-md-2">
                                {{-- Product Image --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-image me-1"></i>{{ __('catalogmanagement::product.product_image') ?? 'Product Image' }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if ($product->mainImage)
                                            <img src="{{ asset('storage/' . $product->mainImage->path) }}"
                                                alt="{{ $product->title }}" class="img-fluid rounded shadow-sm"
                                                style="max-height: 300px; cursor: pointer;"
                                                onclick="openMainImageModal()">

                                            {{-- Main Image Modal --}}
                                            <div class="modal fade" id="mainImageModal" tabindex="-1"
                                                aria-labelledby="mainImageModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-body p-0 d-flex justify-content-center align-items-center"
                                                            style="min-height: 500px; background: #f8f9fa;">
                                                            <img src="{{ asset('storage/' . $product->mainImage->path) }}"
                                                                alt="{{ $product->title }}"
                                                                style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted py-5">
                                                <i class="uil uil-image" style="font-size: 4rem; opacity: 0.3;"></i>
                                                <p class="mt-2">
                                                    {{ __('catalogmanagement::product.no_image_available') ?? 'No image available' }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Additional Images Carousel --}}
                                @if ($product->additionalImages && $product->additionalImages->count() > 0)
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i
                                                    class="uil uil-images me-1"></i>{{ __('catalogmanagement::product.additional_images') }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="slick-slider global-slider slick-dots-bottom"
                                                data-dots-slick='true' data-autoplay-slick='true'>
                                                @foreach ($product->additionalImages as $index => $image)
                                                    <div class="slick-slider__single d-flex justify-content-center align-items-center"
                                                        style="height: 400px; background: #f8f9fa; cursor: pointer;"
                                                        ondblclick="openImageModal({{ $index }})">
                                                        <img src="{{ asset('storage/' . $image->path) }}"
                                                            alt="{{ __('catalogmanagement::product.additional_image') ?? 'Additional Image' }}"
                                                            style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Image Modals (Outside Loop) --}}
                                    @foreach ($product->additionalImages as $index => $image)
                                        <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1"
                                            aria-labelledby="imageModalLabel{{ $index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body p-0 d-flex justify-content-center align-items-center"
                                                        style="min-height: 500px; background: #f8f9fa;">
                                                        <img src="{{ asset('storage/' . $image->path) }}"
                                                            alt="{{ __('catalogmanagement::product.additional_image') ?? 'Additional Image' }}"
                                                            style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- Creation Info --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-clock me-1"></i>{{ __('catalogmanagement::product.creation_info') ?? 'Creation Info' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="view-item mb-3">
                                            <label
                                                class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                            <div class="fs-15 color-dark">
                                                {{ $product->created_at }}
                                            </div>
                                        </div>
                                        <div class="view-item mb-3">
                                            <label
                                                class="il-gray fs-14 fw-500 mb-10">{{ __('common.updated_at') }}</label>
                                            <div class="fs-15 color-dark">
                                                {{ $product->updated_at }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Vendor Products Management (Admin Only) --}}
        @if(isAdmin())
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            <i
                                class="uil uil-store me-2"></i>{{ __('catalogmanagement::product.vendor_products') ?? 'Vendor Products' }}
                            <span class="badge badge-primary badge-round badge-lg ms-2 text-white"
                                id="vendor-products-counter">
                                @php
                                    $totalVendorProducts = $product->vendorProducts()->withTrashed()->count();
                                @endphp
                                {{ $totalVendorProducts }}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Filter Section --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor-filter" class="il-gray fs-14 fw-500 mb-10">
                                        <i class="uil uil-filter me-1"></i>
                                        {{ __('catalogmanagement::product.filter_by_vendor') ?? 'Filter by Vendor' }}
                                    </label>
                                    <select id="vendor-filter" class="form-control select2">
                                        <option value="">{{ __('common.all') }}</option>
                                        @php
                                            $allVendorProducts = $product
                                                ->vendorProducts()
                                                ->with('vendor')
                                                ->withTrashed()
                                                ->get();
                                            $uniqueVendors = $allVendorProducts
                                                ->pluck('vendor')
                                                ->unique('id')
                                                ->filter();
                                        @endphp
                                        @foreach ($uniqueVendors as $vendor)
                                            @if ($vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status-filter" class="il-gray fs-14 fw-500 mb-10">
                                        <i class="uil uil-filter me-1"></i>
                                        {{ __('common.status') }}
                                    </label>
                                    <select id="status-filter" class="form-control select2">
                                        <option value="">{{ __('common.all') }}</option>
                                        <option value="active">{{ __('common.active') }}</option>
                                        <option value="trashed">{{ __('common.trashed') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="productsDataTable" class="table mb-0 table-bordered table-hover text-center">
                                <thead class="userDatatable-header">
                                    <tr>
                                        <th class="text-center" width="80">#</th>
                                        <th class="text-center">{{ __('catalogmanagement::product.vendor') }}</th>
                                        <th class="text-center">{{ __('catalogmanagement::product.orders_count') ?? 'Orders' }}</th>
                                        @canany(['products.bank.vendor-product.trash', 'products.bank.vendor-product.restore'])
                                            <th class="text-center" width="150">{{ __('common.actions') }}</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody id="vendor-products-tbody">
                                    @php
                                        $vendorProducts = $product
                                            ->vendorProducts()
                                                ->with(['vendor.logo'])
                                                ->withTrashed()
                                                ->withCount('orderProducts')
                                                ->get();
                                        @endphp
                                        @forelse($vendorProducts as $index => $vendorProduct)
                                            <tr class="{{ $vendorProduct->trashed() ? 'table-secondary' : '' }} vendor-product-row"
                                                data-vendor-id="{{ $vendorProduct->vendor_id ?? '' }}"
                                                data-status="{{ $vendorProduct->trashed() ? 'trashed' : 'active' }}">
                                                <td class="text-center row-index">{{ $index + 1 }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                                        @if($vendorProduct->vendor && $vendorProduct->vendor->logo)
                                                            <img src="{{ asset('storage/' . $vendorProduct->vendor->logo->path) }}" 
                                                                alt="{{ $vendorProduct->vendor->name }}" 
                                                                class="rounded-circle" 
                                                                style="width: 45px; height: 45px;">
                                                        @else
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" 
                                                                style="width: 45px; height: 45px; font-size: 18px; font-weight: bold;">
                                                                {{ $vendorProduct->vendor ? strtoupper(substr($vendorProduct->vendor->name, 0, 1)) : 'V' }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <span class="fw-semibold">{{ $vendorProduct->vendor->name ?? 'N/A' }}</span>
                                                            @if ($vendorProduct->trashed())
                                                                <span class="badge badge-danger badge-sm badge-round ms-2">{{ __('common.trashed') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info badge-round badge-lg">
                                                        {{ $vendorProduct->order_products_count ?? 0 }}
                                                    </span>
                                                </td>
                                                @canany(['products.bank.vendor-product.trash', 'products.bank.vendor-product.restore'])
                                                    <td class="text-center">
                                                        <div class="d-flex gap-1 justify-content-center">
                                                            @if ($vendorProduct->trashed())
                                                                @can('products.bank.vendor-product.restore')
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm restore-vendor-product"
                                                                        data-vendor-product-id="{{ $vendorProduct->id }}"
                                                                        data-vendor-name="{{ $vendorProduct->vendor->name ?? 'Vendor' }}"
                                                                        title="{{ __('common.restore') }}">
                                                                        <i class="uil uil-redo m-0"></i>
                                                                    </button>
                                                                @endcan
                                                            @else
                                                                @can('products.bank.vendor-product.trash')
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm trash-vendor-product"
                                                                        data-vendor-product-id="{{ $vendorProduct->id }}"
                                                                        data-vendor-name="{{ $vendorProduct->vendor->name ?? 'Vendor' }}"
                                                                        title="{{ __('common.delete') }}">
                                                                        <i class="uil uil-trash-alt m-0"></i>
                                                                    </button>
                                                                @endcan
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endcanany
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ auth()->user()->canany(['products.bank.vendor-product.trash', 'products.bank.vendor-product.restore']) ? '4' : '3' }}" class="text-center text-muted py-4">
                                                    {{ __('catalogmanagement::product.no_vendors_found') ?? 'No vendors found for this product' }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        /**
         * Open image modal for additional images carousel
         */
        function openImageModal(index) {
            const modalId = 'imageModal' + index;
            const modalElement = document.getElementById(modalId);

            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        /**
         * Open main image modal
         */
        function openMainImageModal() {
            const modalElement = document.getElementById('mainImageModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }

        /**
         * Initialize Select2 for vendor filter
         */
        $('#vendor-filter, #status-filter').select2({
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: '{{ __('common.select_option') }}'
        });

        /**
         * Filter vendor products table
         */
        function filterVendorProducts() {
            const selectedVendor = $('#vendor-filter').val();
            const selectedStatus = $('#status-filter').val();

            console.log('Filtering - Vendor:', selectedVendor, 'Status:', selectedStatus);

            let visibleCount = 0;

            $('.vendor-product-row').each(function() {
                const row = $(this);
                const vendorId = String(row.data('vendor-id'));
                const status = String(row.data('status'));

                console.log('Row - Vendor ID:', vendorId, 'Status:', status);

                let showRow = true;

                // Filter by vendor
                if (selectedVendor && selectedVendor !== '' && vendorId !== selectedVendor) {
                    showRow = false;
                }

                // Filter by status
                if (selectedStatus && selectedStatus !== '' && status !== selectedStatus) {
                    showRow = false;
                }

                if (showRow) {
                    row.show();
                    visibleCount++;
                    // Update row index
                    row.find('.row-index').text(visibleCount);
                } else {
                    row.hide();
                }
            });

            console.log('Visible rows:', visibleCount);

            // Update counter badge
            const totalCount = $('.vendor-product-row').length;
            if (selectedVendor || selectedStatus) {
                $('#vendor-products-counter').text(visibleCount + ' / ' + totalCount);
            } else {
                $('#vendor-products-counter').text(totalCount);
            }

            // Show/hide empty message
            $('#no-results-row').remove();
            if (visibleCount === 0) {
                $('#vendor-products-tbody').append(`
            <tr id="no-results-row">
                <td colspan="3" class="text-center text-muted py-4">
                    {{ __('catalogmanagement::product.no_vendors_found') ?? 'No vendors found for this product' }}
                </td>
            </tr>
        `);
            }
        }

        /**
         * Filter change event handlers
         */
        $('#vendor-filter, #status-filter').on('change', function() {
            console.log('Filter changed');
            filterVendorProducts();
        });

        /**
         * Initialize on page load
         */
        $(document).ready(function() {
            console.log('Page ready - initializing filters');
            filterVendorProducts();
        });

        /**
         * Trash Vendor Product Handler
         */
        $(document).on('click', '.trash-vendor-product', function() {
            const btn = $(this);
            const vendorProductId = btn.data('vendor-product-id');
            const vendorName = btn.data('vendor-name');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<i class="uil uil-trash-alt text-danger"></i> {{ __('common.delete') }}',
                    html: `<div class="text-center py-3">
                       <div class="mb-3">
                           <span class="badge bg-danger badge-lg badge-round px-3 py-2 fs-6">${vendorName}</span>
                       </div>
                       <p class="mb-2">{{ __('catalogmanagement::product.confirm_trash_vendor_product') ?? 'Are you sure you want to trash this vendor product?' }}</p>
                       <p class="text-muted small mb-0">{{ __('catalogmanagement::product.trash_vendor_product_note') ?? 'You can restore it later if needed.' }}</p>
                   </div>`,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#868e96',
                    confirmButtonText: '<i class="uil uil-trash-alt me-1"></i> {{ __('common.delete') ?? 'Delete' }}',
                    cancelButtonText: '<i class="uil uil-times me-1"></i> {{ __('common.cancel') ?? 'Cancel' }}',
                    customClass: {
                        popup: 'swal2-lg',
                        title: 'fs-5 fw-bold',
                        confirmButton: 'btn btn-danger px-4 me-1',
                        cancelButton: 'btn btn-secondary px-4 me-1'
                    },
                    buttonsStyling: false,
                    showCloseButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make AJAX request to trash the vendor product
                        $.ajax({
                            url: '{{ route('admin.products.bank.vendor-product.trash', ':id') }}'
                                .replace(':id', vendorProductId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('common.success') ?? 'Success' }}',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('common.error') ?? 'Error' }}',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = '{{ __('common.error_occurred') }}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') ?? 'Error' }}',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            } else {
                // Fallback without SweetAlert
                if (confirm(
                        '{{ __('catalogmanagement::product.confirm_trash_vendor_product') ?? 'Are you sure you want to trash this vendor product?' }}'
                    )) {
                    window.location.href = '{{ route('admin.products.bank.vendor-product.trash', ':id') }}'
                        .replace(':id', vendorProductId);
                }
            }
        });

        /**
         * Restore Vendor Product Handler
         */
        $(document).on('click', '.restore-vendor-product', function() {
            const btn = $(this);
            const vendorProductId = btn.data('vendor-product-id');
            const vendorName = btn.data('vendor-name');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<i class="uil uil-redo text-success"></i> {{ __('common.restore') }}',
                    html: `<div class="text-center py-3">
                       <div class="mb-3">
                           <span class="badge bg-success badge-lg badge-round px-3 py-2 fs-6">${vendorName}</span>
                       </div>
                       <p class="mb-2">{{ __('catalogmanagement::product.confirm_restore_vendor_product') ?? 'Are you sure you want to restore this vendor product?' }}</p>
                       <p class="text-muted small mb-0">{{ __('catalogmanagement::product.restore_vendor_product_note') ?? 'This will make the product available again.' }}</p>
                   </div>`,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#868e96',
                    confirmButtonText: '<i class="uil uil-redo me-1"></i> {{ __('common.restore') ?? 'Restore' }}',
                    cancelButtonText: '<i class="uil uil-times me-1"></i> {{ __('common.cancel') ?? 'Cancel' }}',
                    customClass: {
                        popup: 'swal2-lg',
                        title: 'fs-5 fw-bold',
                        confirmButton: 'btn btn-success px-4 me-1',
                        cancelButton: 'btn btn-secondary px-4 me-1'
                    },
                    buttonsStyling: false,
                    showCloseButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make AJAX request to restore the vendor product
                        $.ajax({
                            url: '{{ route('admin.products.bank.vendor-product.restore', ':id') }}'
                                .replace(':id', vendorProductId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('common.success') ?? 'Success' }}',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('common.error') ?? 'Error' }}',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = '{{ __('common.error_occurred') }}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') ?? 'Error' }}',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            } else {
                // Fallback without SweetAlert
                if (confirm(
                        '{{ __('catalogmanagement::product.confirm_restore_vendor_product') ?? 'Are you sure you want to restore this vendor product?' }}'
                    )) {
                    window.location.href = '{{ route('admin.products.bank.vendor-product.restore', ':id') }}'
                        .replace(':id', vendorProductId);
                }
            }
        });
    </script>
@endpush
