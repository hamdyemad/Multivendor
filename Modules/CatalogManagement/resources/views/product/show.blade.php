@extends('layout.app')

@section('title', __('catalogmanagement::product.view_product'))

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
                        'title' => __('catalogmanagement::product.products_management'),
                        'url' => route('admin.products.index'),
                    ],
                    ['title' => __('catalogmanagement::product.view_product')],
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
                                            <i
                                                class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::product.basic_information') ?? 'Basic Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('catalogmanagement::product.title')" :model="$product->product" fieldName="title"
                                                :languages="$languages" />

                                            <x-translation-display :label="__('catalogmanagement::product.details')" :model="$product->product" fieldName="details"
                                                :languages="$languages" type="html" />

                                            <x-translation-display :label="__('catalogmanagement::product.summary')" :model="$product->product" fieldName="summary"
                                                :languages="$languages" type="html" />

                                            <x-translation-display :label="__('catalogmanagement::product.features')" :model="$product->product"
                                                fieldName="features" :languages="$languages" type="html" />

                                            <x-translation-display :label="__('catalogmanagement::product.instructions')" :model="$product->product"
                                                fieldName="instructions" :languages="$languages" type="html" />
                                            <x-translation-display :label="__('catalogmanagement::product.tags')" :model="$product->product" fieldName="tags"
                                                :languages="$languages" type="keywords" />

                                            {{-- Brand --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.brand') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if ($product->product->brand)
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                {{ $product->product->brand->getTranslation('name', app()->getLocale()) ?? ($product->product->brand->getTranslation('name', 'en') ?? ($product->product->brand->getTranslation('name', 'ar') ?? '-')) }}
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.department') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if ($product->product->department)
                                                            <span class="badge badge-round badge-info badge-round badge-lg">
                                                                {{ $product->product->department->getTranslation('name', app()->getLocale()) ?? ($product->product->department->getTranslation('name', 'en') ?? ($product->product->department->getTranslation('name', 'ar') ?? '-')) }}
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.category') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if ($product->product->category)
                                                            <span
                                                                class="badge badge-round badge-primary badge-round badge-lg">
                                                                {{ $product->product->category->getTranslation('name', app()->getLocale()) ?? ($product->product->category->getTranslation('name', 'en') ?? ($product->product->category->getTranslation('name', 'ar') ?? '-')) }}
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.sub_category') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if ($product->product->subCategory)
                                                            <span
                                                                class="badge badge-round badge-warning badge-round badge-lg">
                                                                {{ $product->product->subCategory->getTranslation('name', app()->getLocale()) ?? ($product->product->subCategory->getTranslation('name', 'en') ?? ($product->product->subCategory->getTranslation('name', 'ar') ?? '-')) }}
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.vendor') }}</label>
                                                    <p class="fs-15">
                                                        @if ($product->vendor)
                                                            <span
                                                                class="badge badge-round badge-primary badge-round badge-lg">
                                                                {{ $product->vendor->getTranslation('name', app()->getLocale()) ?? ($product->vendor->getTranslation('name', 'en') ?? ($product->vendor->getTranslation('name', 'ar') ?? '-')) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Configuration Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.type') ?? 'Type' }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($product->product->configuration_type === 'simple')
                                                            <span
                                                                class="badge badge-round badge-success badge-lg">{{ __('catalogmanagement::product.simple') ?? 'Simple' }}</span>
                                                        @elseif($product->product->configuration_type === 'variants')
                                                            <span
                                                                class="badge badge-round badge-info badge-lg">{{ __('catalogmanagement::product.variants') ?? 'Variants' }}</span>
                                                        @else
                                                            <span
                                                                class="text-muted">{{ $product->product->configuration_type ?? '-' }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Product Type (Bank/Regular) --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.product_type') ?? 'Product Type' }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($product->product->type === 'bank')
                                                            <span
                                                                class="badge badge-round badge-primary badge-lg">{{ __('catalogmanagement::product.bank_product') ?? 'Bank Product' }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-round badge-secondary badge-lg">{{ __('catalogmanagement::product.regular_product') ?? 'Regular Product' }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Product SKU --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.sku') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $product->sku ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Product Slug --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.slug') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code>{{ $product->product->slug ?? '-' }}</code>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Video Link --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.video_link') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($product->video_link)
                                                            <a href="{{ $product->video_link }}" target="_blank" class="btn btn-danger btn-sm text-white" title="{{ __('catalogmanagement::product.open_video_link') }}">
                                                                <i class="uil uil-play-circle me-1"></i>{{ __('catalogmanagement::product.open_video_link') }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Additional Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::product.additional_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Max Per Order --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded" style="background: #e7f3ff;">
                                                    <small
                                                        class="text-muted d-block mb-1">{{ __('catalogmanagement::product.max_per_order') }}</small>
                                                    <div class="fw-bold text-info" style="font-size: 18px;">
                                                        <i
                                                            class="uil uil-shopping-cart me-1"></i>{{ $product->max_per_order ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Taxes --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded" style="background: #f8f9fa;">
                                                    <small
                                                        class="text-muted d-block mb-1">{{ __('catalogmanagement::product.tax') }}</small>
                                                    <div class="fw-bold text-dark" style="font-size: 16px;">
                                                        @if ($product->taxes && $product->taxes->count() > 0)
                                                            @foreach ($product->taxes as $tax)
                                                                <span class="badge badge-round badge-info me-1">
                                                                    {{ $tax->getTranslation('name', app()->getLocale()) ?? $tax->name }}
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Featured --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded"
                                                    style="background: {{ $product->is_featured ? '#d4edda' : '#f8d7da' }};">
                                                    <small
                                                        class="text-muted d-block mb-1">{{ __('catalogmanagement::product.featured') }}</small>
                                                    <div class="fw-bold {{ $product->is_featured ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil {{ $product->is_featured ? 'uil-star' : 'uil-times' }} me-1"></i>
                                                        {{ $product->is_featured ? __('common.yes') : __('common.no') }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded"
                                                    style="background: {{ $product->is_active ? '#d4edda' : '#f8d7da' }};">
                                                    <small
                                                        class="text-muted d-block mb-1">{{ __('common.status') }}</small>
                                                    <div class="fw-bold {{ $product->is_active ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil {{ $product->is_active ? 'uil-check-circle' : 'uil-times-circle' }} me-1"></i>
                                                        {{ $product->is_active ? __('common.active') : __('common.inactive') }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Approval Status --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded"
                                                    style="background:
                                                    @if ($product->status === 'approved') #d4edda
                                                    @elseif($product->status === 'rejected') #f8d7da
                                                    @else #fff3cd @endif;">
                                                    <small
                                                        class="text-muted d-block mb-1">{{ __('catalogmanagement::product.approval_status') }}</small>
                                                    <div class="fw-bold
                                                        @if ($product->status === 'approved') text-success
                                                        @elseif($product->status === 'rejected') text-danger
                                                        @else text-warning @endif"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil
                                                            @if ($product->status === 'approved') uil-check-circle
                                                            @elseif($product->status === 'rejected') uil-times-circle
                                                            @else uil-clock @endif me-1"></i>
                                                        @if ($product->status === 'approved')
                                                            {{ __('common.approved') }}
                                                        @elseif($product->status === 'rejected')
                                                            {{ __('common.rejected') }}
                                                        @else
                                                            {{ __('common.pending') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Refund Status --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded"
                                                    style="background: {{ $product->is_able_to_refund ? '#d4edda' : '#f8d7da' }};">
                                                    <small
                                                        class="text-muted d-block mb-1">{{ __('catalogmanagement::product.is_able_to_refund') }}</small>
                                                    <div class="fw-bold {{ $product->is_able_to_refund ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil {{ $product->is_able_to_refund ? 'uil-redo' : 'uil-times-circle' }} me-1"></i>
                                                        {{ $product->is_able_to_refund ? __('common.yes') : __('common.no') }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Refund Days --}}
                                            @if ($product->is_able_to_refund)
                                            <div class="col-md-4 mb-3">
                                                <div class="p-3 border rounded" style="background: #e7f3ff;">
                                                    <small
                                                        class="text-muted d-block mb-1">{{ __('catalogmanagement::product.refund_days') }}</small>
                                                    <div class="fw-bold text-info" style="font-size: 18px;">
                                                        <i
                                                            class="uil uil-history me-1"></i>{{ $product->refund_days ?? '7' }} {{ __('common.days') }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Rejection Reason (Only show if rejected) --}}
                                            @if ($product->status === 'rejected' && $product->rejection_reason)
                                                <div class="col-md-12 mb-3">
                                                    <div class="alert alert-danger d-flex align-items-start"
                                                        role="alert">
                                                        <i class="uil uil-exclamation-triangle me-2"
                                                            style="font-size: 24px;"></i>
                                                        <div>
                                                            <h6 class="alert-heading mb-2">
                                                                <i
                                                                    class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::product.rejection_reason') }}
                                                            </h6>
                                                            <p class="mb-0">{{ $product->rejection_reason }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- SEO Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-search me-1"></i>{{ __('catalogmanagement::product.seo_information') ?? 'SEO Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('catalogmanagement::product.meta_title')" :model="$product->product"
                                                fieldName="meta_title" :languages="$languages" />
                                            <x-translation-display :label="__('catalogmanagement::product.meta_description')" :model="$product->product"
                                                fieldName="meta_description" :languages="$languages" />
                                            <x-translation-display :label="__('catalogmanagement::product.meta_keywords')" :model="$product->product"
                                                fieldName="meta_keywords" :languages="$languages" type="keywords" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Product Images --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-holder">
                                            <div class="card-header">
                                                <h3>
                                                    <i
                                                        class="uil uil-image me-1"></i>{{ __('catalogmanagement::product.images') }}
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                {{-- Main Image --}}
                                                @if ($product->product->mainImage)
                                                    <div class="mb-3">
                                                        <label
                                                            class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.main_image') }}</label>
                                                        <div class="image-wrapper text-center">
                                                            <img src="{{ $product->product->mainImage->full_url }}"
                                                                alt="{{ $product->product->getTranslation('title') }}"
                                                                class="product-image img-fluid rounded"
                                                                style="max-height: 300px;">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="mb-3">
                                                        <label
                                                            class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.main_image') }}</label>
                                                        <div class="image-wrapper text-center">
                                                            <img src="{{ asset('assets/img/default.png') }}"
                                                                alt="{{ $product->product->getTranslation('title') }}"
                                                                class="product-image img-fluid rounded"
                                                                style="max-height: 300px;">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        {{-- Additional Images Carousel --}}
                                        @if ($product->product->additionalImages && $product->product->additionalImages->count() > 0)
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
                                                        @foreach ($product->product->additionalImages as $index => $image)
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
                                            @foreach ($product->product->additionalImages as $index => $image)
                                                <div class="modal fade" id="imageModal{{ $index }}"
                                                    tabindex="-1" aria-labelledby="imageModalLabel{{ $index }}"
                                                    aria-hidden="true">
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

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                                                {{-- Product Variants & Regional Stock --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.variants_and_stock') ?? 'Variants & Stock' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($product->variants as $variantIndex => $variant)
                                            <div class="mb-4 pb-4"
                                                style="@if (!$loop->last) border-bottom: 1px solid #e9ecef; @endif">
                                                {{-- Variant Header with SKU, Title, and Price --}}
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                                        {{-- SKU Badge --}}
                                                        <span class="badge badge-round badge-lg"
                                                            style="background-color: #17a2b8; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i
                                                                class="uil uil-barcode me-1"></i>{{ __('catalogmanagement::product.sku') }}:
                                                            {{ $variant->sku ?? '-' }}
                                                        </span>

                                                        {{-- Stock Badge --}}
                                                        <span class="badge badge-round badge-lg"
                                                            style="background-color: #28a745; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i
                                                                class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.stock') }}:
                                                            {{ number_format($variant->total_stock ?? 0) }}
                                                        </span>

                                                        {{-- Hierarchical Variant Tree --}}
                                                        @if ($variant->variantConfiguration)
                                                            <div class="variant-tree-display">
                                                                @php
                                                                    // Build the variant hierarchy by traversing up the parent chain
                                                                    $values = [];
                                                                    $rootKeyName = '';
                                                                    $current = $variant->variantConfiguration;
                                                                    $visited = []; // Prevent infinite loops

                                                                    // Collect all values from leaf to root
                                                                    while (
                                                                        $current &&
                                                                        !in_array($current->id, $visited)
                                                                    ) {
                                                                        $visited[] = $current->id;

                                                                        // Get the value name (current node)
                                                                        $valueName =
                                                                            $current->getTranslation(
                                                                                'name',
                                                                                app()->getLocale(),
                                                                            ) ??
                                                                            ($current->getTranslation('name', 'en') ??
                                                                                ($current->name ??
                                                                                    ($current->value ?? 'Value')));

                                                                        // Add value to the beginning of array
                                                                        array_unshift($values, $valueName);

                                                                        // Move to parent
                                                                        if ($current->parent_data) {
                                                                            $current = $current->parent_data;
                                                                        } else {
                                                                            // Reached root, get the key name
                                                                            $rootKeyName = $current->key
                                                                                ? $current->key->getTranslation(
                                                                                        'name',
                                                                                        app()->getLocale(),
                                                                                    ) ??
                                                                                    ($current->key->getTranslation(
                                                                                        'name',
                                                                                        'en',
                                                                                    ) ??
                                                                                        ($current->key->name ?? 'Key'))
                                                                                : 'Key';
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp

                                                                @if (count($values) > 0)
                                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                                        {{-- Display root key badge --}}
                                                                        <span class="badge badge-round badge-lg"
                                                                            style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
                                                                                     color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                     box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: bold;">
                                                                            <i
                                                                                class="uil uil-key-skeleton me-1"></i>{{ $rootKeyName }}
                                                                        </span>

                                                                        <span class="text-muted fw-bold">:</span>

                                                                        {{-- Display all values separated by colons --}}
                                                                        @foreach ($values as $valueIndex => $value)
                                                                            @if ($valueIndex > 0)
                                                                                <span class="text-muted fw-bold">:</span>
                                                                            @endif

                                                                            {{-- Value Badge --}}
                                                                            <span class="badge badge-round badge-lg"
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

                                                    {{-- Pricing Information --}}
                                                    <div class="mt-3">
                                                        <div class="row">
                                                            {{-- Price --}}
                                                            @if ($variant->price)
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="p-3 border rounded"
                                                                        style="background: #f8f9fa;">
                                                                        <small
                                                                            class="text-muted d-block mb-1">{{ __('catalogmanagement::product.price') }}</small>
                                                                        @if ($variant->has_discount && $variant->price_before_discount)
                                                                            <div
                                                                                class="fw-bold text-danger text-decoration-line-through mb-1">
                                                                                <i
                                                                                    class="uil uil-money-bill me-1"></i>{{ number_format($variant->price_before_discount, 2) }} {{ currency() }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="fw-bold text-success">
                                                                            <i
                                                                                class="uil uil-money-bill me-1"></i>{{ number_format($variant->price, 2) }} {{ currency() }}
                                                                        </div>
                                                                        @php
                                                                            // Calculate price after tax
                                                                            $priceAfterTax = $variant->price;
                                                                            if ($product->taxes && $product->taxes->count() > 0) {
                                                                                $totalTaxPercentage = $product->taxes->sum('percentage');
                                                                                $priceAfterTax = $variant->price * (1 + ($totalTaxPercentage / 100));
                                                                            }
                                                                        @endphp
                                                                        @if ($product->taxes && $product->taxes->count() > 0)
                                                                            <div class="fw-bold text-info mt-1">
                                                                                <small class="text-muted">{{ __('catalogmanagement::product.price_after_tax') ?? 'Price after tax' }}:</small>
                                                                                <i class="uil uil-receipt me-1"></i>{{ number_format($priceAfterTax, 2) }} {{ currency() }}
                                                                            </div>
                                                                        @endif
                                                                        @if ($variant->has_discount && $variant->discount_end_date)
                                                                            <small class="text-muted d-block mt-2">
                                                                                <i
                                                                                    class="uil uil-calendar-alt me-1"></i>{{ __('catalogmanagement::product.discount_until') ?? 'Discount until' }}:
                                                                                <strong>{{ $variant->discount_end_date->format('Y-m-d') }}</strong>
                                                                            </small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($variant->stocks && $variant->stocks->count() > 0)
                                                    <div class="mt-3">
                                                        @php
                                                            $variantTotalStock = $variant->stocks->sum('quantity');
                                                            $variantBookedStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                ->where('status', 'booked')
                                                                ->sum('booked_quantity');
                                                            $variantAllocatedStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                ->where('status', 'allocated')
                                                                ->sum('booked_quantity');
                                                            $variantFulfilledStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                ->where('status', 'fulfilled')
                                                                ->sum('booked_quantity');
                                                            $variantRemainingStock = max(0, $variantTotalStock - $variantBookedStock - $variantAllocatedStock - $variantFulfilledStock);
                                                        @endphp
                                                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                                                            <h6 class="fw-600 mb-0">
                                                                {{ __('catalogmanagement::product.stock_summary') ?? 'Stock Summary' }}:
                                                            </h6>
                                                            <span class="badge badge-round badge-lg badge-success">
                                                                <i class="uil uil-package me-1"></i>{{ __('catalogmanagement::product.total') ?? 'Total' }}:
                                                                {{ number_format($variantTotalStock) }}
                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-warning">
                                                                <i class="uil uil-lock me-1"></i>{{ __('common.booked') ?? 'Booked' }}:
                                                                {{ number_format($variantBookedStock) }}
                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-info">
                                                                <i class="uil uil-tag me-1"></i>{{ __('common.allocated') ?? 'Allocated' }}:
                                                                {{ number_format($variantAllocatedStock) }}
                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-primary">
                                                                <i class="uil uil-check-circle me-1"></i>{{ __('common.fulfilled') ?? 'Delivered' }}:
                                                                {{ number_format($variantFulfilledStock) }}
                                                            </span>
                                                            <span class="badge badge-round badge-lg badge-secondary">
                                                                <i class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.remaining') ?? 'Remaining' }}:
                                                                {{ number_format($variantRemainingStock) }}
                                                            </span>
                                                        </div>
                                                        <h6 class="fw-600 mb-3">
                                                            {{ __('catalogmanagement::product.stock_per_region') ?? 'Stock per Region' }}:
                                                        </h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-hover">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th><i class="uil uil-location-point me-1"></i>{{ __('catalogmanagement::product.region') ?? 'Region' }}</th>
                                                                        <th class="text-center"><i class="uil uil-package me-1"></i>{{ __('catalogmanagement::product.total_stock') ?? 'Total Stock' }}</th>
                                                                        <th class="text-center"><i class="uil uil-lock me-1"></i>{{ __('common.booked') ?? 'Booked' }}</th>
                                                                        <th class="text-center"><i class="uil uil-tag me-1"></i>{{ __('common.allocated') ?? 'Allocated' }}</th>
                                                                        <th class="text-center"><i class="uil uil-check-circle me-1"></i>{{ __('common.fulfilled') ?? 'Delivered' }}</th>
                                                                        <th class="text-center"><i class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.remaining') ?? 'Remaining' }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $totalStock = 0;
                                                                        $totalBooked = 0;
                                                                        $totalAllocated = 0;
                                                                        $totalFulfilled = 0;
                                                                        $totalRemaining = 0;
                                                                    @endphp
                                                                    @forelse ($variant->stocks as $stock)
                                                                        @php
                                                                            $bookedInRegion = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                                ->where('region_id', $stock->region_id)
                                                                                ->where('status', 'booked')
                                                                                ->sum('booked_quantity');
                                                                            $allocatedInRegion = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                                ->where('allocated_region_id', $stock->region_id)
                                                                                ->where('status', 'allocated')
                                                                                ->sum('booked_quantity');
                                                                            $fulfilledInRegion = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variant->id)
                                                                                ->where('allocated_region_id', $stock->region_id)
                                                                                ->where('status', 'fulfilled')
                                                                                ->sum('booked_quantity');
                                                                            $remainingInRegion = max(0, $stock->quantity - $bookedInRegion - $allocatedInRegion - $fulfilledInRegion);
                                                                            
                                                                            // Accumulate totals
                                                                            $totalStock += $stock->quantity ?? 0;
                                                                            $totalBooked += $bookedInRegion;
                                                                            $totalAllocated += $allocatedInRegion;
                                                                            $totalFulfilled += $fulfilledInRegion;
                                                                            $totalRemaining += $remainingInRegion;
                                                                        @endphp
                                                                        <tr>
                                                                            <td>
                                                                                @if ($stock->region)
                                                                                    {{ $stock->region->getTranslation('name', app()->getLocale()) ?? ($stock->region->getTranslation('name', 'en') ?? ($stock->region->getTranslation('name', 'ar') ?? ($stock->region->name ?? '-'))) }}
                                                                                @else
                                                                                    {{ __('catalogmanagement::product.default_region') ?? 'Default Region' }}
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-primary badge-lg">{{ number_format($stock->quantity ?? 0) }}</span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-warning badge-lg">{{ number_format($bookedInRegion) }}</span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-info badge-lg">{{ number_format($allocatedInRegion) }}</span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-success badge-lg">{{ number_format($fulfilledInRegion) }}</span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge badge-round badge-secondary badge-lg">{{ number_format($remainingInRegion) }}</span>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="6" class="text-center text-muted">
                                                                                {{ __('catalogmanagement::product.no_regional_stock_data') ?? 'No regional stock data available.' }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                @if($variant->stocks->count() > 0)
                                                                <tfoot class="table-light">
                                                                    <tr>
                                                                        <th class="fw-bold">{{ __('common.total') ?? 'Total' }}</th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-primary badge-lg">{{ number_format($totalStock) }}</span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-warning badge-lg">{{ number_format($totalBooked) }}</span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-info badge-lg">{{ number_format($totalAllocated) }}</span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-success badge-lg">{{ number_format($totalFulfilled) }}</span>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <span class="badge badge-round badge-secondary badge-lg">{{ number_format($totalRemaining) }}</span>
                                                                        </th>
                                                                    </tr>
                                                                </tfoot>
                                                                @endif
                                                            </table>
                                                        </div>

                                                        {{-- All Stock Bookings in One Table --}}
                                                        @php
                                                            $allBookings = \Modules\CatalogManagement\app\Models\StockBooking::with(['order', 'orderProduct', 'region', 'allocatedRegion'])
                                                                ->where('vendor_product_variant_id', $variant->id)
                                                                ->latest()
                                                                ->get();
                                                        @endphp
                                                        <div class="mt-4">
                                                            <h6 class="fw-600 mb-3">
                                                                <i class="uil uil-clipboard-notes me-1"></i>
                                                                {{ __('catalogmanagement::product.stock_bookings') ?? 'Stock Bookings' }}
                                                                <span class="badge badge-round badge-primary ms-2">{{ $allBookings->count() }}</span>
                                                            </h6>
                                                            {{-- Status Legend --}}
                                                            <div class="mb-3 d-flex flex-wrap gap-2">
                                                                <span class="badge badge-round badge-success">
                                                                    <i class="uil uil-check-circle me-1"></i>{{ __('common.fulfilled') }}: {{ $allBookings->where('status', 'fulfilled')->count() }}
                                                                </span>
                                                                <span class="badge badge-round badge-info">
                                                                    <i class="uil uil-tag me-1"></i>{{ __('common.allocated') }}: {{ $allBookings->where('status', 'allocated')->count() }}
                                                                </span>
                                                                <span class="badge badge-round badge-warning">
                                                                    <i class="uil uil-clock me-1"></i>{{ __('common.booked') }}: {{ $allBookings->where('status', 'booked')->count() }}
                                                                </span>
                                                                <span class="badge badge-round badge-danger">
                                                                    <i class="uil uil-times-circle me-1"></i>{{ __('common.released') }}: {{ $allBookings->where('status', 'released')->count() }}
                                                                </span>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-hover table-sm stock-bookings-table">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>{{ __('order::order.order_id') ?? 'Order ID' }}</th>
                                                                            <th>{{ __('catalogmanagement::product.booked_region') ?? 'Booked Region' }}</th>
                                                                            <th>{{ __('catalogmanagement::product.allocated_region') ?? 'Allocated Region' }}</th>
                                                                            <th class="text-center">{{ __('common.quantity') ?? 'Quantity' }}</th>
                                                                            <th class="text-center">{{ __('common.status') ?? 'Status' }}</th>
                                                                            <th>{{ __('common.date') ?? 'Date' }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($allBookings as $booking)
                                                                            <tr class="@if($booking->status === 'fulfilled') table-success @elseif($booking->status === 'allocated') table-info @elseif($booking->status === 'booked') table-warning @else table-danger @endif">
                                                                                <td>
                                                                                    <a href="{{ route('admin.orders.show', $booking->order_id) }}" class="text-primary fw-500">
                                                                                        #{{ $booking->order->order_number ?? $booking->order_id }}
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    @if($booking->region)
                                                                                        {{ $booking->region->getTranslation('name', app()->getLocale()) ?? $booking->region->name }}
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if($booking->allocatedRegion)
                                                                                        <span class="fw-500">
                                                                                            {{ $booking->allocatedRegion->getTranslation('name', app()->getLocale()) ?? $booking->allocatedRegion->name }}
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <span class="badge badge-round @if($booking->status === 'fulfilled') badge-success @elseif($booking->status === 'allocated') badge-info @elseif($booking->status === 'booked') badge-warning @else badge-danger @endif">
                                                                                        {{ $booking->booked_quantity }}
                                                                                    </span>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    @if($booking->status === 'fulfilled')
                                                                                        <span class="badge badge-round badge-success">
                                                                                            <i class="uil uil-check-circle me-1"></i>{{ __('common.fulfilled') }}
                                                                                        </span>
                                                                                    @elseif($booking->status === 'allocated')
                                                                                        <span class="badge badge-round badge-info">
                                                                                            <i class="uil uil-tag me-1"></i>{{ __('common.allocated') }}
                                                                                        </span>
                                                                                    @elseif($booking->status === 'booked')
                                                                                        <span class="badge badge-round badge-warning">
                                                                                            <i class="uil uil-clock me-1"></i>{{ __('common.booked') }}
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="badge badge-round badge-danger">
                                                                                            <i class="uil uil-times-circle me-1"></i>{{ __('common.released') }}
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if($booking->status === 'fulfilled')
                                                                                        {{ $booking->fulfilled_at ? $booking->fulfilled_at : '-' }}
                                                                                    @elseif($booking->status === 'allocated')
                                                                                        {{ $booking->allocated_at ? $booking->allocated_at : '-' }}
                                                                                    @elseif($booking->status === 'booked')
                                                                                        {{ $booking->booked_at ? $booking->booked_at : '-' }}
                                                                                    @else
                                                                                        {{ $booking->released_at ? $booking->released_at : '-' }}
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6" class="text-center text-muted py-3">
                                                                                    {{ __('catalogmanagement::product.no_stock_bookings') ?? 'No stock bookings yet.' }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-muted fs-14 mt-3">
                                                        {{ __('catalogmanagement::product.no_stock_data') ?? 'No stock data available.' }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
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
         * Initialize DataTables for Stock tables
         */
        $(document).ready(function() {
            // Initialize Stock Bookings tables
            $('.stock-bookings-table').each(function() {
                // Only initialize if table has thead and tbody with proper structure
                // Skip tables with colspan cells (empty state rows)
                var $table = $(this);
                var $tbody = $table.find('tbody');
                var $rows = $tbody.find('tr');
                var headerCount = $table.find('thead tr th').length;
                
                // Check if table has proper structure:
                // 1. Has header columns
                // 2. Has at least one row
                // 3. First row has same number of cells as headers (no colspan)
                var hasProperStructure = headerCount > 0 && 
                                         $rows.length > 0 &&
                                         $rows.first().find('td').length === headerCount &&
                                         $rows.first().find('td[colspan]').length === 0;
                
                if (hasProperStructure && !$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        paging: true,
                        pageLength: 10,
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "{{ __('common.all') }}"]],
                        searching: true,
                        ordering: true,
                        order: [[0, 'desc']],
                        info: true,
                        autoWidth: false,
                        responsive: true,
                        language: {
                            search: "{{ __('common.search') }}:",
                            lengthMenu: "{{ __('common.show') }} _MENU_ {{ __('common.entries') }}",
                            info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                            infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                            infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                            zeroRecords: "{{ __('common.no_matching_records_found') }}",
                            paginate: {
                                first: "{{ __('common.first') }}",
                                last: "{{ __('common.last') }}",
                                next: "{{ __('common.next') }}",
                                previous: "{{ __('common.previous') }}"
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
