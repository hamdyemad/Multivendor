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
                                                                $translation = $product->getTranslation('title', $lang->code);
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
                                                                $translation = $product->getTranslation('details', $lang->code);
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
                                            {{-- Product Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.product_type') }}</label>
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
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.configuration_type') }}</label>
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
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.slug') }}</label>
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
                                            <i class="uil uil-folder me-1"></i>{{ __('catalogmanagement::product.category_information') ?? 'Category Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Brand --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.brand') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if($product->brand)
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
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.department') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if($product->department)
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
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.category') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if($product->category)
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
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.sub_category') }}</label>
                                                    <div class="fs-15 color-dark">
                                                        @if($product->subCategory)
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
                                @if($product->variants && $product->variants->count() > 0)
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-box me-1"></i>{{ __('catalogmanagement::product.product_variants') ?? 'Product Variants' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($product->variants as $variantIndex => $variant)
                                            <div class="mb-4 pb-4"
                                                style="@if(!$loop->last) border-bottom: 1px solid #e9ecef; @endif">
                                                {{-- Variant Header --}}
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                                        {{-- SKU Badge --}}
                                                        <span class="badge badge-lg"
                                                            style="background-color: #17a2b8; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i class="uil uil-barcode me-1"></i>{{ __('catalogmanagement::product.sku') }}:
                                                            {{ $variant->sku ?? '-' }}
                                                        </span>

                                                        {{-- Variant Configuration --}}
                                                        @if($variant->variantConfiguration)
                                                            <div class="variant-tree-display">
                                                                @php
                                                                    // Build the variant hierarchy
                                                                    $values = [];
                                                                    $rootKeyName = '';
                                                                    $current = $variant->variantConfiguration;
                                                                    $visited = [];

                                                                    while($current && !in_array($current->id, $visited)) {
                                                                        $visited[] = $current->id;
                                                                        $valueName = $current->getTranslation('name', app()->getLocale()) ?? $current->name ?? 'Value';
                                                                        array_unshift($values, $valueName);

                                                                        if($current->parent_data) {
                                                                            $current = $current->parent_data;
                                                                        } else {
                                                                            $rootKeyName = $current->key ?
                                                                                ($current->key->getTranslation('name', app()->getLocale()) ?? $current->key->name ?? 'Key') : 'Key';
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp

                                                                @if(count($values) > 0)
                                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                                        {{-- Root Key Badge --}}
                                                                        <span class="badge badge-lg"
                                                                              style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
                                                                                     color: white; padding: 6px 10px; border-radius: 15px; font-size: 12px;
                                                                                     box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: bold;">
                                                                            <i class="uil uil-key-skeleton me-1"></i>{{ $rootKeyName }}
                                                                        </span>

                                                                        <span class="text-muted fw-bold">:</span>

                                                                        {{-- Values --}}
                                                                        @foreach($values as $valueIndex => $value)
                                                                            @if($valueIndex > 0)
                                                                                <span class="text-muted fw-bold">:</span>
                                                                            @endif

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
                                            <i class="uil uil-tag me-1"></i>{{ __('catalogmanagement::product.meta_information') ?? 'Meta Information' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Meta Title --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_title') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->getTranslation('meta_title', $lang->code);
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
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.meta_description') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $product->getTranslation('meta_description', $lang->code);
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
                                            <i class="uil uil-image me-1"></i>{{ __('catalogmanagement::product.product_image') ?? 'Product Image' }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($product->mainImage)
                                            <img src="{{ asset('storage/' . $product->mainImage->path) }}"
                                                 alt="{{ $product->title }}"
                                                 class="img-fluid rounded shadow-sm"
                                                 style="max-height: 300px; object-fit: cover;">
                                        @else
                                            <div class="text-muted py-5">
                                                <i class="uil uil-image" style="font-size: 4rem; opacity: 0.3;"></i>
                                                <p class="mt-2">{{ __('catalogmanagement::product.no_image_available') ?? 'No image available' }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Additional Images Carousel --}}
                                @if($product->additionalImages && $product->additionalImages->count() > 0)
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-images me-1"></i>{{ __('catalogmanagement::product.additional_images') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="slick-slider global-slider slick-dots-bottom"
                                            data-dots-slick='true' data-autoplay-slick='true'>
                                            @foreach($product->additionalImages as $index => $image)
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
                                @foreach($product->additionalImages as $index => $image)
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

                                {{-- Creation Info --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('catalogmanagement::product.creation_info') ?? 'Creation Info' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="view-item mb-3">
                                            <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                            <div class="fs-15 color-dark">
                                                {{ $product->created_at }}
                                            </div>
                                        </div>
                                        <div class="view-item mb-3">
                                            <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.updated_at') }}</label>
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
</script>
@endpush
