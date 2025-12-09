@extends('layout.app')

@section('title', trans('catalogmanagement::bundle.view_bundle'))

@push('styles')
<style>
/* Bundle View HTML Content Styling */
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
                        'title' => trans('catalogmanagement::bundle.bundles_management'),
                        'url' => route('admin.bundles.index'),
                    ],
                    ['title' => trans('catalogmanagement::bundle.view_bundle')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::bundle.bundle_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.bundles.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.bundles.edit', $bundle->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
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
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('catalogmanagement::bundle.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Bundle Name --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.name') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $bundle->getTranslation('name', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {{ $translation }}
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Bundle Description --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.description') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $bundle->getTranslation('description', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {!! nl2br(e($translation)) !!}
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Vendor --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.vendor') }}</label>
                                                    <p class="fs-15">
                                                        @if ($bundle->vendor)
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                {{ $bundle->vendor->getTranslation('name', app()->getLocale()) ?? $bundle->vendor->getTranslation('name', 'en') ?? $bundle->vendor->getTranslation('name', 'ar') ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Bundle Category --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.category') }}</label>
                                                    <p class="fs-15">
                                                        @if ($bundle->bundleCategory)
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                {{ $bundle->bundleCategory->getTranslation('name', app()->getLocale()) ?? $bundle->bundleCategory->getTranslation('name', 'en') ?? $bundle->bundleCategory->getTranslation('name', 'ar') ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- SKU --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.sku') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code>{{ $bundle->sku ?? '-' }}</code>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($bundle->is_active)
                                                            <span class="badge badge-round badge-success badge-lg">
                                                                <i class="uil uil-check-circle me-1"></i>{{ trans('catalogmanagement::bundle.active') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-round badge-danger badge-lg">
                                                                <i class="uil uil-times-circle me-1"></i>{{ trans('catalogmanagement::bundle.inactive') }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $bundle->created_at ? $bundle->created_at : '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Sidebar --}}
                            <div class="col-md-4 order-1 order-md-2">
                                {{-- Image --}}
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('catalogmanagement::bundle.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @php
                                            $imageAttachment = $bundle->main_image;
                                        @endphp
                                        @if($imageAttachment)
                                            <img src="{{ asset('storage/' . $imageAttachment->path) }}" alt="Bundle Image" class="img-fluid round" style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                        @else
                                            <div class="p-5 bg-light round">
                                            <img src="{{ asset('assets/img/default.png') }}" alt="Bundle Image" class="img-fluid round" style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- SEO Information --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-search me-1"></i>{{ trans('catalogmanagement::bundle.seo_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- SEO Title --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.seo_title') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $bundle->getTranslation('seo_title', $lang->code);
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SEO Description --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.seo_description') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $bundle->getTranslation('seo_description', $lang->code);
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SEO Keywords --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::bundle.seo_keywords') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $bundle->getTranslation('seo_keywords', $lang->code);
                                                            $keywords = [];
                                                            if ($translation) {
                                                                // Try to decode as JSON first (if stored as JSON array)
                                                                $decoded = json_decode($translation, true);
                                                                if (is_array($decoded)) {
                                                                    $keywords = $decoded;
                                                                } else {
                                                                    // Otherwise split by comma
                                                                    $keywords = array_map('trim', explode(',', $translation));
                                                                    $keywords = array_filter($keywords); // Remove empty values
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span class="badge badge-lg badge-round @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if(count($keywords) > 0)
                                                                        <div class="d-flex flex-wrap gap-2">
                                                                            @foreach($keywords as $keyword)
                                                                                <span class="badge badge-lg badge-round bg-info text-white" style="font-size: 12px; padding: 6px 10px;">
                                                                                    {{ trim($keyword) }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bundle Products --}}
                            @include('catalogmanagement::bundles.bundle-products-table', ['bundle' => $bundle, 'showDragHandle' => false, 'showActions' => true])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-body')
{{-- Loading Overlay Component --}}
<x-loading-overlay
    loadingText="{{ trans('main.deleting') }}"
    loadingSubtext="{{ trans('main.please wait') }}"
/>
@endpush
