@extends('layout.app')

@section('title', __('catalogmanagement::product.view_product'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::product.products_management'), 'url' => route('admin.products.index')],
                    ['title' => __('catalogmanagement::product.view_product')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('catalogmanagement::product.product_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('common.edit') }}
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
                                            <i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Product Title --}}
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.title') }}</label>
                                                    <div class="row">
                                                        @foreach(['en' => 'English', 'ar' => 'العربية'] as $lang => $langName)
                                                            @php
                                                                $translation = $product->getTranslation('title', $lang);
                                                            @endphp
                                                            @if($translation)
                                                                <div class="col-md-6 mb-2">
                                                                    <small class="text-muted d-block">{{ $langName }}:</small>
                                                                    <p class="fs-15 color-dark fw-500 mb-0">{{ $translation }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Product SKU --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.sku') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $product->sku ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Product Description --}}
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.description') }}</label>
                                                    <div class="row">
                                                        @foreach(['en' => 'English', 'ar' => 'العربية'] as $lang => $langName)
                                                            @php
                                                                $translation = $product->getTranslation('description', $lang);
                                                            @endphp
                                                            @if($translation)
                                                                <div class="col-md-6 mb-3">
                                                                    <small class="text-muted d-block">{{ $langName }}:</small>
                                                                    <p class="fs-15 color-dark mb-0">{{ $translation }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Brand --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.brand') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($product->brand)
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                {{ $product->brand->getTranslation('name', app()->getLocale()) ?? $product->brand->getTranslation('name', 'en') ?? $product->brand->getTranslation('name', 'ar') ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Department --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.department') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($product->department)
                                                            <span class="badge badge-round badge-info badge-round badge-lg">
                                                                {{ $product->department->getTranslation('name', app()->getLocale()) ?? $product->department->getTranslation('name', 'en') ?? $product->department->getTranslation('name', 'ar') ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Category --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.category') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($product->category)
                                                            <span class="badge badge-round badge-primary badge-round badge-lg">
                                                                {{ $product->category->getTranslation('name', app()->getLocale()) ?? $product->category->getTranslation('name', 'en') ?? $product->category->getTranslation('name', 'ar') ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Sub Category --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.sub_category') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($product->subCategory)
                                                            <span class="badge badge-round badge-warning badge-round badge-lg">
                                                                {{ $product->subCategory->getTranslation('name', app()->getLocale()) ?? $product->subCategory->getTranslation('name', 'en') ?? $product->subCategory->getTranslation('name', 'ar') ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Vendor --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.vendor') }}</label>
                                                    <p class="fs-15">
                                                        @if($product->vendor)
                                                            <span class="badge badge-round badge-primary badge-round badge-lg">
                                                                {{ $product->vendor->getTranslation('name', app()->getLocale()) ?? $product->vendor->getTranslation('name', 'en') ?? $product->vendor->getTranslation('name', 'ar') ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Tax --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.tax') }}</label>
                                                    <p class="fs-15">
                                                        @if($product->tax)
                                                            <span class="badge badge-round badge-primary badge-round badge-lg">
                                                                {{ $product->tax->getTranslation('name', app()->getLocale()) ?? $product->tax->getTranslation('name', 'en') ?? $product->tax->getTranslation('name', 'ar') ?? '-' }} ({{ $product->tax->rate }}%)
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pricing & Stock Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-dollar-alt me-1"></i>{{ __('catalogmanagement::product.pricing_stock') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Cost Price --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.cost_price') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $product->cost_price ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Selling Price --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.selling_price') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $product->selling_price ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Stock Quantity --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.stock_quantity') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $product->stock_quantity ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Max Per Order --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.max_per_order') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $product->max_per_order ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Product Variants & Regional Stock --}}
                                @if($product->variants->count() > 0)
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.variants_stock') }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @foreach($product->variants as $variant)
                                                <div class="mb-4 pb-3 border-bottom">
                                                    <h5 class="fw-600 mb-3">
                                                        {{ $variant->getTranslation('title', app()->getLocale()) ?? $variant->getTranslation('title', 'en') ?? $variant->getTranslation('title', 'ar') ?? 'Variant' }}
                                                        <span class="badge badge-round badge-primary badge-lg ms-2">{{ __('catalogmanagement::product.total') }}: {{ $variant->getTotalStock() }}</span>
                                                    </h5>

                                                    @if($variant->stocks->count() > 0)
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered mb-0">
                                                                <thead>
                                                                    <tr class="table-light">
                                                                        <th>{{ __('catalogmanagement::product.region') }}</th>
                                                                        <th class="text-center">{{ __('catalogmanagement::product.stock') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($variant->stocks as $stock)
                                                                        <tr>
                                                                            <td>
                                                                                @if($stock->region)
                                                                                    <span class="badge badge-round badge-secondary badge-lg">{{ $stock->region->getTranslation('name', app()->getLocale()) ?? $stock->region->getTranslation('name', 'en') ?? $stock->region->getTranslation('name', 'ar') ?? '-' }}</span>
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center fw-600">
                                                                                <span class="badge badge-round badge-primary badge-lg">{{ $stock->stock }}</span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <p class="text-muted fs-14">{{ __('catalogmanagement::product.no_stock_data') }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- SEO Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-search me-1"></i>{{ __('common.seo') ?? 'SEO' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Meta Title --}}
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_title') }}</label>
                                                    <div class="row">
                                                        @foreach(['en' => 'English', 'ar' => 'العربية'] as $lang => $langName)
                                                            @php
                                                                $translation = $product->getTranslation('meta_title', $lang);
                                                            @endphp
                                                            @if($translation)
                                                                <div class="col-md-6 mb-2">
                                                                    <small class="text-muted d-block">{{ $langName }}:</small>
                                                                    <p class="fs-15 color-dark fw-500 mb-0">{{ $translation }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Meta Description --}}
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_description') }}</label>
                                                    <div class="row">
                                                        @foreach(['en' => 'English', 'ar' => 'العربية'] as $lang => $langName)
                                                            @php
                                                                $translation = $product->getTranslation('meta_description', $lang);
                                                            @endphp
                                                            @if($translation)
                                                                <div class="col-md-6 mb-3">
                                                                    <small class="text-muted d-block">{{ $langName }}:</small>
                                                                    <p class="fs-15 color-dark mb-0">{{ $translation }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Meta Keywords --}}
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_keywords') }}</label>
                                                    <div class="row">
                                                        @foreach(['en' => 'English', 'ar' => 'العربية'] as $lang => $langName)
                                                            @php
                                                                $keywords = $product->getTranslation('meta_keywords', $lang) ?? '';
                                                                if ($keywords) {
                                                                    // Try to decode as JSON
                                                                    $decoded = json_decode($keywords, true);
                                                                    if (is_array($decoded)) {
                                                                        $keywords = implode(', ', $decoded);
                                                                    }
                                                                }
                                                            @endphp
                                                            @if($keywords)
                                                                <div class="col-md-6 mb-2">
                                                                    <small class="text-muted d-block">{{ $langName }}:</small>
                                                                    <p class="fs-15 color-dark mb-0">{{ $keywords }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status & Timestamps --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.status') }}</label>
                                                    <p class="fs-15">
                                                        @if($product->is_active)
                                                            <span class="badge badge-round badge-success badge-round badge-lg">{{ __('catalogmanagement::product.active') }}</span>
                                                        @else
                                                            <span class="badge badge-round badge-danger badge-round badge-lg">{{ __('catalogmanagement::product.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Featured --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.featured') }}</label>
                                                    <p class="fs-15">
                                                        @if($product->is_featured)
                                                            <span class="badge badge-round badge-success badge-round badge-lg">{{ __('Yes') }}</span>
                                                        @else
                                                            <span class="badge badge-round badge-secondary badge-round badge-lg">{{ __('No') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $product->created_at }}</p>
                                                </div>
                                            </div>

                                            {{-- Updated At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $product->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Product Images --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ __('catalogmanagement::product.images') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- Main Image --}}
                                        @if($product->mainImage)
                                            <div class="mb-3">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.main_image') }}</label>
                                                <div class="image-wrapper text-center">
                                                    <img src="{{ asset('storage/' . $product->mainImage->path) }}"
                                                        alt="{{ $product->getTranslation('title') }}"
                                                        class="product-image img-fluid rounded"
                                                        style="max-height: 300px; object-fit: cover;">
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Additional Images --}}
                                        @if($product->additionalImages->count() > 0)
                                            <div class="mt-3">
                                                <label class="il-gray fs-14 fw-500 mb-10 d-block">{{ __('catalogmanagement::product.additional_images') }}</label>
                                                <div class="row g-3">
                                                    @foreach($product->additionalImages as $image)
                                                        <div class="col-6">
                                                            <div class="image-wrapper image-preview-container" style="position: relative; width: 100%; height: 180px; border: 2px dashed #0056B7; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all 0.3s ease; background: #f8f9fa;">
                                                                <img src="{{ asset('storage/' . $image->path) }}"
                                                                    alt="{{ $product->getTranslation('title') }}"
                                                                    class="preview-image"
                                                                    style="width: 100%; height: 100%; object-fit: contain;">
                                                                <div class="image-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; gap: 10px; opacity: 0; transition: opacity 0.3s ease;">
                                                                    <button type="button" class="btn-change-image" style="background: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: #333;">
                                                                        <i class="uil uil-eye"></i> View
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic text-center">{{ __('common.no_images') ?? 'No additional images' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Modal Component --}}
    <x-image-modal />
@endsection
