@extends('layout.app')

@section('title', trans('catalogmanagement::bundle_category.view_bundle_category'))

@push('styles')
<style>
/* Custom styling for bundle category show view */
.card-holder {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border-bottom: 1px solid #e3e6f0;
    padding: 1rem 1.25rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
}

.view-item {
    margin-bottom: 1rem;
}

.view-item label {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.box-items-translations {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    border: 1px solid #e3e6f0;
}

.badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.badge-success {
    background-color: #1cc88a;
}

.badge-secondary {
    background-color: #858796;
}

/* Image hover and click styles */
.bundle-image-wrapper {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.bundle-image-wrapper:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.bundle-image-wrapper::after {
    content: '🔍';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 10px;
    border-radius: 50%;
    font-size: 1.2rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.bundle-image-wrapper:hover::after {
    opacity: 1;
}
</style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::bundle_category.bundle_categories_management'), 'url' => route('admin.bundle-categories.index')],
                    ['title' => trans('catalogmanagement::bundle_category.view_bundle_category')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::bundle_category.view_bundle_category') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.bundle-categories.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.bundle-categories.edit', $bundleCategory->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information --}}
                            <div class="col-md-8 order-2 order-md-1">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('catalogmanagement::bundle_category.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Bundle Category Names --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::bundle_category.name') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('name', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
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

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::bundle_category.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if($bundleCategory->active)
                                                            <span class="badge badge-success badge-lg badge-round">{{ __('catalogmanagement::bundle_category.active') }}</span>
                                                        @else
                                                            <span class="badge badge-secondary badge-lg badge-round">{{ __('catalogmanagement::bundle_category.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Slug --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.slug') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code>{{ $bundleCategory->slug ?? '-' }}</code>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $bundleCategory->created_at }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $bundleCategory->updated_at }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bundle Category Image --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ __('catalogmanagement::bundle_category.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($bundleCategory->image)
                                            <div class="bundle-image-wrapper" onclick="openBundleImageModal()">
                                                <img src="{{ asset('storage/' . $bundleCategory->image) }}"
                                                     alt="{{ $bundleCategory->getTranslation('name') }}"
                                                     class="img-fluid rounded border shadow-sm"
                                                     style="max-height: 300px; object-fit: cover; width: 100%;">
                                            </div>
                                        @else
                                            <div class="d-flex flex-column align-items-center justify-content-center" style="height: 200px;">
                                                <i class="uil uil-image-slash text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">{{ __('catalogmanagement::bundle_category.no_image') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEO Information --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-search me-1"></i>{{ __('catalogmanagement::bundle_category.seo_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- SEO Titles --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::bundle_category.seo_title') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('seo_title', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
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

                                            {{-- SEO Descriptions --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::bundle_category.seo_description') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('seo_description', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
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
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('catalogmanagement::bundle_category.seo_keywords') }}</label>
                                                    <div class="row">
                                                        @foreach($languages as $lang)
                                                            @php
                                                                $translation = $bundleCategory->getTranslation('seo_keywords', $lang->code);
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
                                                                    <small class="text-muted d-block mb-2" style="@if($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0" style="@if($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if(count($keywords) > 0)
                                                                            <div class="d-flex flex-wrap gap-2">
                                                                                @foreach($keywords as $keyword)
                                                                                    <span class="badge badge-lg badge-round bg-info text-white" style="font-size: 12px; padding: 6px 10px; @if($lang->code == 'ar') font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    @if($bundleCategory->image)
        <div class="modal fade" id="bundleImageModal" tabindex="-1" aria-labelledby="bundleImageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-0 d-flex justify-content-center align-items-center" style="min-height: 500px; background: #f8f9fa;">
                        <img src="{{ asset('storage/' . $bundleCategory->image) }}"
                             alt="{{ $bundleCategory->getTranslation('name') }}"
                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    /**
     * Open bundle category image modal
     */
    function openBundleImageModal() {
        const modalElement = document.getElementById('bundleImageModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }
</script>
@endpush
