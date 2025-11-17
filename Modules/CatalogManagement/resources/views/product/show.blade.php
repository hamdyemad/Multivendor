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

                                {{-- Additional Images Carousel --}}
                                @if($product->additionalImages && $product->additionalImages->count() > 0)
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-images me-1"></i>{{ __('catalogmanagement::product.additional_images') ?? 'Additional Images' }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="slick-slider global-slider slick-dots-bottom" data-dots-slick='true' data-autoplay-slick='true'>
                                                @foreach($product->additionalImages as $index => $image)
                                                    <div class="slick-slider__single d-flex justify-content-center align-items-center" style="height: 400px; background: #f8f9fa; cursor: pointer;" ondblclick="openImageModal({{ $index }})">
                                                        <img src="{{ asset('storage/' . $image->path) }}"
                                                             alt="{{ __('common.additional_image') }}"
                                                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Image Modals (Outside Loop) --}}
                                    @foreach($product->additionalImages as $index => $image)
                                        <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body p-0 d-flex justify-content-center align-items-center" style="min-height: 500px; background: #f8f9fa;">
                                                        <img src="{{ asset('storage/' . $image->path) }}"
                                                             alt="{{ __('common.additional_image') }}"
                                                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- Configuration Type --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-cog me-1"></i>{{ __('catalogmanagement::product.configuration_type') ?? 'Configuration Type' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::product.type') ?? 'Type' }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if($product->configuration_type === 'simple')
                                                            <span class="badge badge-round badge-success badge-lg">{{ __('catalogmanagement::product.simple') ?? 'Simple' }}</span>
                                                        @elseif($product->configuration_type === 'variants')
                                                            <span class="badge badge-round badge-info badge-lg">{{ __('catalogmanagement::product.variants') ?? 'Variants' }}</span>
                                                        @else
                                                            <span class="text-muted">{{ $product->configuration_type ?? '-' }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Product Variants & Regional Stock --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-box me-1"></i>{{ __('common.variants_and_stock') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($product->variants as $variantIndex => $variant)
                                            <div class="mb-4 pb-4" style="@if(!$loop->last) border-bottom: 1px solid #e9ecef; @endif">
                                                {{-- Variant Header with SKU, Title, and Price --}}
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                                        {{-- SKU Badge --}}
                                                        <span class="badge badge-lg" style="background-color: #17a2b8; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i class="uil uil-barcode me-1"></i>{{ __('common.sku') }}: {{ $variant->sku ?? '-' }}
                                                        </span>

                                                        {{-- Variant Title Badge --}}
                                                        <span class="badge badge-lg" style="background-color: #17a2b8; color: white; padding: 8px 12px; border-radius: 20px;">
                                                            <i class="uil uil-tag me-1"></i>{{ $variant->getTranslation('title', app()->getLocale()) ?? $variant->getTranslation('title', 'en') ?? $variant->getTranslation('title', 'ar') ?? __('common.variant') . ' ' . ($variantIndex + 1) }}
                                                        </span>

                                                        {{-- Price Section --}}
                                                        <div class="ms-auto d-flex align-items-center gap-2">
                                                            @if($variant->has_discount)
                                                                <span style="text-decoration: line-through; color: #999; font-size: 14px;">
                                                                    {{ $variant->discount_price ?? '-' }} {{ __('common.currency') ?? 'EGP' }}
                                                                </span>
                                                            @endif
                                                            <span class="fw-bold" style="color: #28a745; font-size: 18px;">
                                                                {{ $variant->price ?? '-' }} {{ $product->currency->code }}
                                                            </span>
                                                            @if($variant->has_discount)
                                                                <span class="badge badge-primary badge-round badge-lg" style="padding: 4px 8px;">
                                                                    -{{ round((($variant->discount_price - $variant->price) / $variant->discount_price) * 100) }}%
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Offer Valid Until --}}
                                                    @if($variant->has_discount && $variant->discount_end_date)
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i class="uil uil-clock me-1"></i>{{ __('common.offer_valid_until') }}: {{ \Carbon\Carbon::parse($variant->discount_end_date)->format('M d, Y') }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Stock per Region --}}
                                                @if($variant->stocks->count() > 0)
                                                    <div class="mt-3">
                                                        <h6 class="fw-600 mb-3">{{ __('common.stock_per_region') }}:</h6>
                                                        <div class="row">
                                                            @foreach($variant->stocks as $stock)
                                                                <div class="col-md-4 mb-3">
                                                                    <div class="p-3 border rounded" style="background: #f8f9fa;">
                                                                        <div class="text-muted small mb-2">
                                                                            @if($stock->region)
                                                                                {{ $stock->region->getTranslation('name', app()->getLocale()) ?? $stock->region->getTranslation('name', 'en') ?? $stock->region->getTranslation('name', 'ar') ?? '-' }}
                                                                            @else
                                                                                {{ __('common.region') }}
                                                                            @endif
                                                                        </div>
                                                                        <div class="fw-bold" style="color: #0066cc; font-size: 18px;">
                                                                            {{ $stock->stock }} {{ __('common.units') }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-muted fs-14 mt-3">{{ __('common.no_stock_data') }}</p>
                                                @endif
                                            </div>
                                        @endforeach
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
