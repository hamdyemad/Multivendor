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
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
                            <a href="{{ route('admin.products.edit', $product->product->id) }}" class="btn btn-primary btn-sm">
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
                                            <i class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::product.basic_information') ?? 'Basic Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Product Title --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.title') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation('title', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Product Description --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.details') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation(
                                                                    'details',
                                                                    $lang->code,
                                                                );
                                                            @endphp
                                                                <div class="col-md-6 mb-3">
                                                                    <small class="text-muted d-block"
                                                                        style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                    <div class="fs-15 color-dark mb-0"
                                                                        style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {!! $translation !!}
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Product Summary --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.summary') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation(
                                                                    'summary',
                                                                    $lang->code,
                                                                );
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {!! $translation !!}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Product Features --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.features') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation(
                                                                    'features',
                                                                    $lang->code,
                                                                );
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {!! $translation !!}
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Product Instructions --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.instructions') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation(
                                                                    'instructions',
                                                                    $lang->code,
                                                                );
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {!! $translation !!}
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Tags --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.tags') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation('tags', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if (str_contains($translation, ','))
                                                                        @foreach (explode(',', $translation) as $tag)
                                                                            <span
                                                                                class="badge badge-primary badge-lg badge-round me-1 mb-1"
                                                                                style="@if ($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">{{ trim($tag) }}</span>
                                                                        @endforeach
                                                                    @else
                                                                        <span class="badge badge-primary"
                                                                            style="@if ($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">{{ $translation }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

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

                                            {{-- Tax is vendor-specific and shown in vendor products section --}}
                                        </div>
                                    </div>
                                </div>
                                {{-- Additional Information --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::product.additional_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Points --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded" style="background: #fff3cd;">
                                                    <small class="text-muted d-block mb-1">{{ __('catalogmanagement::product.points') }}</small>
                                                    <div class="fw-bold text-warning" style="font-size: 18px;">
                                                        <i class="uil uil-star me-1"></i>{{ $product->points ?? 0 }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Max Per Order --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded" style="background: #e7f3ff;">
                                                    <small class="text-muted d-block mb-1">{{ __('catalogmanagement::product.max_per_order') }}</small>
                                                    <div class="fw-bold text-info" style="font-size: 18px;">
                                                        <i class="uil uil-shopping-cart me-1"></i>{{ $product->max_per_order ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tax --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded" style="background: #f8f9fa;">
                                                    <small class="text-muted d-block mb-1">{{ __('catalogmanagement::product.tax') }}</small>
                                                    <div class="fw-bold text-dark" style="font-size: 16px;">
                                                        <i class="uil uil-percentage me-1"></i>
                                                        @if($product->tax)
                                                            {{ $product->tax->getTranslation('name', app()->getLocale()) ?? $product->tax->getTranslation('name', 'en') ?? $product->tax->name ?? '-' }}
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Featured --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded" style="background: {{ $product->is_featured ? '#d4edda' : '#f8d7da' }};">
                                                    <small class="text-muted d-block mb-1">{{ __('catalogmanagement::product.featured') }}</small>
                                                    <div class="fw-bold {{ $product->is_featured ? 'text-success' : 'text-danger' }}" style="font-size: 16px;">
                                                        <i class="uil {{ $product->is_featured ? 'uil-star' : 'uil-times' }} me-1"></i>
                                                        {{ $product->is_featured ? __('common.yes') : __('common.no') }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded" style="background: {{ $product->is_active ? '#d4edda' : '#f8d7da' }};">
                                                    <small class="text-muted d-block mb-1">{{ __('common.status') }}</small>
                                                    <div class="fw-bold {{ $product->is_active ? 'text-success' : 'text-danger' }}" style="font-size: 16px;">
                                                        <i class="uil {{ $product->is_active ? 'uil-check-circle' : 'uil-times-circle' }} me-1"></i>
                                                        {{ $product->is_active ? __('common.active') : __('common.inactive') }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Approval Status --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded" style="background:
                                                    @if($product->status === 'approved') #d4edda
                                                    @elseif($product->status === 'rejected') #f8d7da
                                                    @else #fff3cd
                                                    @endif;">
                                                    <small class="text-muted d-block mb-1">{{ __('catalogmanagement::product.approval_status') }}</small>
                                                    <div class="fw-bold
                                                        @if($product->status === 'approved') text-success
                                                        @elseif($product->status === 'rejected') text-danger
                                                        @else text-warning
                                                        @endif" style="font-size: 16px;">
                                                        <i class="uil
                                                            @if($product->status === 'approved') uil-check-circle
                                                            @elseif($product->status === 'rejected') uil-times-circle
                                                            @else uil-clock
                                                            @endif me-1"></i>
                                                        @if($product->status === 'approved')
                                                            {{ __('common.approved') }}
                                                        @elseif($product->status === 'rejected')
                                                            {{ __('common.rejected') }}
                                                        @else
                                                            {{ __('common.pending') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Rejection Reason (Only show if rejected) --}}
                                            @if($product->status === 'rejected' && $product->rejection_reason)
                                            <div class="col-md-12 mb-3">
                                                <div class="alert alert-danger d-flex align-items-start" role="alert">
                                                    <i class="uil uil-exclamation-triangle me-2" style="font-size: 24px;"></i>
                                                    <div>
                                                        <h6 class="alert-heading mb-2">
                                                            <i class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::product.rejection_reason') }}
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
                                            <i class="uil uil-search me-1"></i>{{ __('catalogmanagement::product.seo_information') ?? 'SEO Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Meta Title --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_title') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation(
                                                                    'meta_title',
                                                                    $lang->code,
                                                                );
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Meta Description --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_description') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation(
                                                                    'meta_description',
                                                                    $lang->code,
                                                                );
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Meta Keywords --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_keywords') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->product->getTranslation(
                                                                    'meta_keywords',
                                                                    $lang->code,
                                                                );
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <small class="text-muted d-block"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">{{ $lang->code }}:</small>
                                                                <div class="fs-15 color-dark mb-0"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        @if (str_contains($translation, ','))
                                                                            @foreach (explode(',', $translation) as $keyword)
                                                                                <span
                                                                                    class="badge badge-primary badge-lg badge-round me-1 mb-1"
                                                                                    style="@if ($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">{{ trim($keyword) }}</span>
                                                                            @endforeach
                                                                        @else
                                                                            <span class="badge badge-primary badge-lg badge-round"
                                                                                style="@if ($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">{{ $translation }}</span>
                                                                        @endif
                                                                    @else
                                                                        --
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>



                                {{-- Product Variants & Regional Stock --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.variants_and_stock') ?? 'Variants & Stock' }}
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
                                                        <span class="badge badge-lg"
                                                            style="background-color: #17a2b8; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i class="uil uil-barcode me-1"></i>{{ __('catalogmanagement::product.sku') }}:
                                                            {{ $variant->sku ?? '-' }}
                                                        </span>

                                                        {{-- Hierarchical Variant Tree --}}
                                                        @if($variant->variantConfiguration)
                                                            <div class="variant-tree-display">
                                                                @php
                                                                    // Build the variant hierarchy by traversing up the parent chain
                                                                    $values = [];
                                                                    $rootKeyName = '';
                                                                    $current = $variant->variantConfiguration;
                                                                    $visited = []; // Prevent infinite loops

                                                                    // Collect all values from leaf to root
                                                                    while($current && !in_array($current->id, $visited)) {
                                                                        $visited[] = $current->id;

                                                                        // Get the value name (current node)
                                                                        $valueName = $current->getTranslation('name', app()->getLocale()) ??
                                                                                    $current->getTranslation('name', 'en') ??
                                                                                    $current->name ?? 'Value';

                                                                        // Add value to the beginning of array
                                                                        array_unshift($values, $valueName);

                                                                        // Move to parent
                                                                        if($current->parent_data) {
                                                                            $current = $current->parent_data;
                                                                        } else {
                                                                            // Reached root, get the key name
                                                                            $rootKeyName = $current->key ?
                                                                                ($current->key->getTranslation('name', app()->getLocale()) ??
                                                                                 $current->key->getTranslation('name', 'en') ??
                                                                                 $current->key->name ?? 'Key') : 'Key';
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp

                                                                @if(count($values) > 0)
                                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                                        {{-- Display root key badge --}}
                                                                        <span class="badge badge-lg"
                                                                              style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
                                                                                     color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                     box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: bold;">
                                                                            <i class="uil uil-key-skeleton me-1"></i>{{ $rootKeyName }}
                                                                        </span>

                                                                        <span class="text-muted fw-bold">:</span>

                                                                        {{-- Display all values separated by colons --}}
                                                                        @foreach($values as $valueIndex => $value)
                                                                            @if($valueIndex > 0)
                                                                                <span class="text-muted fw-bold">:</span>
                                                                            @endif

                                                                            {{-- Value Badge --}}
                                                                            <span class="badge badge-lg"
                                                                                  style="background: linear-gradient(135deg,
                                                                                         {{ $valueIndex % 3 === 0 ? '#17a2b8' : ($valueIndex % 3 === 1 ? '#28a745' : '#fd7e14') }} 0%,
                                                                                         {{ $valueIndex % 3 === 0 ? '#138496' : ($valueIndex % 3 === 1 ? '#218838' : '#e8590c') }} 100%);
                                                                                         color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                         box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                                                <i class="uil uil-tag me-1"></i>{{ $value }}
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
                                                            @if($variant->price)
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="p-3 border rounded" style="background: #f8f9fa;">
                                                                        <small class="text-muted d-block mb-1">{{ __('catalogmanagement::product.price') }}</small>
                                                                        @if($variant->has_discount && $variant->price_before_discount)
                                                                            <div class="fw-bold text-danger text-decoration-line-through mb-1">
                                                                                <i class="uil uil-money-bill me-1"></i>{{ number_format($variant->price_before_discount, 2) }}
                                                                            </div>
                                                                        @endif
                                                                        <div class="fw-bold text-success">
                                                                            <i class="uil uil-money-bill me-1"></i>{{ number_format($variant->price, 2) }}
                                                                        </div>
                                                                        @if($variant->has_discount && $variant->discount_end_date)
                                                                            <small class="text-muted d-block mt-2">
                                                                                <i class="uil uil-calendar-alt me-1"></i>{{ __('catalogmanagement::product.discount_until') ?? 'Discount until' }}:
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
                                                        <div class="d-flex align-items-center gap-3 mb-3">
                                                            <h6 class="fw-600 mb-0">{{ __('catalogmanagement::product.stock_summary') ?? 'Stock Summary' }}:</h6>
                                                            <span class="badge badge-round badge-lg badge-success">
                                                                <i class="uil uil-package me-1"></i>{{ __('catalogmanagement::product.total') ?? 'Total' }}:
                                                                {{ $variant->stocks->sum('quantity') }}
                                                                {{ __('catalogmanagement::product.units') ?? 'Units' }}
                                                            </span>
                                                        </div>
                                                        <h6 class="fw-600 mb-3">{{ __('catalogmanagement::product.stock_per_region') ?? 'Stock per Region' }}:</h6>
                                                        <div class="row">
                                                            @forelse ($variant->stocks as $stock)
                                                                <div class="col-md-4 mb-3">
                                                                    <div class="p-3 border rounded"
                                                                        style="background: #f8f9fa;">
                                                                        <div class="text-muted small mb-2">
                                                                            @if ($stock->region)
                                                                                <i class="uil uil-location-point me-1"></i>
                                                                                {{ $stock->region->getTranslation('name', app()->getLocale()) ?? ($stock->region->getTranslation('name', 'en') ?? ($stock->region->getTranslation('name', 'ar') ?? $stock->region->name ?? '-')) }}
                                                                            @else
                                                                                <i class="uil uil-location-point me-1"></i>
                                                                                {{ __('catalogmanagement::product.default_region') ?? 'Default Region' }}
                                                                            @endif
                                                                        </div>
                                                                        <div class="fw-bold"
                                                                            style="color: #0066cc; font-size: 18px;">
                                                                            <i class="uil uil-package me-1"></i>
                                                                            {{ $stock->quantity ?? 0 }} {{ __('catalogmanagement::product.units') ?? 'Units' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="col-12">
                                                                    <div class="alert alert-info">
                                                                        <i class="uil uil-info-circle me-2"></i>
                                                                        {{ __('catalogmanagement::product.no_regional_stock_data') ?? 'No regional stock data available for this variant.' }}
                                                                    </div>
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-muted fs-14 mt-3">{{ __('catalogmanagement::product.no_stock_data') ?? 'No stock data available.' }}</p>
                                                @endif
                                            </div>
                                        @endforeach
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
                                                            <img src="{{ asset('storage/' . $product->product->mainImage->path) }}"
                                                                alt="{{ $product->product->getTranslation('title') }}"
                                                                class="product-image img-fluid rounded"
                                                                style="max-height: 300px; object-fit: cover;">
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
    </script>
@endpush
