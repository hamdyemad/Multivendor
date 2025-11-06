@extends('layout.app')

@section('title', trans('vendor::vendor.vendor_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                ['title' => trans('vendor::vendor.vendor_details')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-500">{{ trans('vendor::vendor.vendor_details') }}</h5>
                    <div class="d-flex gap-10">
                        <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-sm">
                            <i class="uil uil-arrow-left me-2"></i>{{ trans('vendor::vendor.back_to_list') }}
                        </a>
                        <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-primary btn-sm">
                            <i class="uil uil-edit me-2"></i>{{ trans('vendor::vendor.edit_vendor') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Vendor Information --}}
                        <div class="col-12">
                            <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                <i class="uil uil-info-circle"></i>{{ trans('vendor::vendor.vendor_information') }}
                            </h6>
                        </div>

                        {{-- Names --}}
                        @foreach($languages as $language)
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                        @if($language->code == 'ar')
                                            الاسم بالعربية
                                        @else
                                            {{ trans('vendor::vendor.name') }} ({{ $language->name }})
                                        @endif
                                    </label>
                                    <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                        {{ $vendor->getTranslation('name', $language->code) ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.country') }}</label>
                                <p class="fs-15 color-dark">
                                    @if($vendor->country)
                                        <span class="badge badge-primary badge-round badge-lg me-1">
                                            {{ $vendor->country->getTranslation('name', app()->getLocale()) ?? $vendor->country->code }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.commission') }}</label>
                                <p class="fs-15 color-dark">
                                    <span class="badge badge-info badge-round badge-lg">{{ $vendor->commission->commission ?? 0 }}%</span>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.status') }}</label>
                                <p class="fs-15">
                                    @if($vendor->active)
                                        <span class="badge bg-success badge-round badge-lg">{{ trans('vendor::vendor.active') }}</span>
                                    @else
                                        <span class="badge bg-danger badge-round badge-lg">{{ trans('vendor::vendor.inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.activities') }}</label>
                                <p class="fs-15 color-dark">
                                    @if($vendor->activities && $vendor->activities->count() > 0)
                                        @foreach($vendor->activities as $activity)
                                            <span class="badge badge-primary badge-round badge-lg me-1">
                                                {{ $activity->getTranslation('name', app()->getLocale()) }}
                                            </span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.email') }}</label>
                                <p class="fs-15 color-dark">{{ $vendor->user->email ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.created_at') }}</label>
                                <p class="fs-15 color-dark">{{ $vendor->created_at ? $vendor->created_at->format('Y-m-d H:i:s') : '-' }}</p>
                            </div>
                        </div>

                        {{-- Media Section --}}
                        <div class="col-12">
                            <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                <i class="uil uil-image"></i>{{ trans('vendor::vendor.branding') }}
                            </h6>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.logo') }}</label>
                                <div class="text-center p-3 border rounded">
                                    @if($vendor->logo && $vendor->logo->path)
                                        <img src="{{ asset('storage/' . $vendor->logo->path) }}" alt="Logo" class="img-fluid rounded shadow-sm" style="max-height: 200px; object-fit: contain;">
                                    @else
                                        <p class="text-muted mb-0">{{ trans('vendor::vendor.no_logo_uploaded') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.banner') }}</label>
                                <div class="text-center p-3 border rounded">
                                    @if($vendor->banner && $vendor->banner->path)
                                        <img src="{{ asset('storage/' . $vendor->banner->path) }}" alt="Banner" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: contain;">
                                    @else
                                        <p class="text-muted mb-0">{{ trans('vendor::vendor.no_banner_uploaded') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Description Section --}}
                        <div class="col-12">
                            <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                <i class="uil uil-file-alt"></i>{{ trans('vendor::vendor.description') }}
                            </h6>
                        </div>

                        @foreach($languages as $language)
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                        @if($language->code == 'ar')
                                            الوصف بالعربية
                                        @else
                                            {{ trans('vendor::vendor.description') }} ({{ $language->name }})
                                        @endif
                                    </label>
                                    <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                        {{ $vendor->getTranslation('description', $language->code) ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        {{-- SEO Information --}}
                        <div class="col-12">
                            <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                <i class="uil uil-search"></i>{{ trans('vendor::vendor.seo_information') }}
                            </h6>
                        </div>

                        @foreach($languages as $language)
                            <div class="col-12 mt-3">
                                <h6 class="text-muted mb-3">{{ $language->name }}</h6>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.meta_title') }}</label>
                                    <p class="fs-15 color-dark {{ $language->rtl ? 'text-end' : '' }}" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                        {{ $vendor->getTranslation('meta_title', $language->code) ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.meta_description') }}</label>
                                    <p class="fs-15 color-dark {{ $language->rtl ? 'text-end' : '' }}" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                        {{ $vendor->getTranslation('meta_description', $language->code) ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.meta_keywords') }}</label>
                                    <p class="fs-15 color-dark {{ $language->rtl ? 'text-end' : '' }}" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                        {{ $vendor->getTranslation('meta_keywords', $language->code) ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        {{-- Documents --}}
                        @if($vendor->attachments && $vendor->attachments->where('type', 'document')->count() > 0)
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-file"></i>{{ trans('vendor::vendor.vendor_documents') }}
                                </h6>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ trans('vendor::vendor.document_name') }}</th>
                                                <th>{{ trans('vendor::vendor.document_file') }}</th>
                                                <th>{{ trans('common.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendor->attachments->where('type', 'document') as $index => $document)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if($document->translations && $document->translations->first())
                                                        {{ $document->translations->first()->lang_value }}
                                                    @else
                                                        {{ trans('vendor::vendor.document') }} {{ $index + 1 }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($document->path)
                                                        <span class="badge badge-info">{{ basename($document->path) }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($document->path)
                                                        <a href="{{ asset($document->path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="uil uil-download-alt"></i> {{ trans('common.download') ?? 'Download' }}
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .view-item label {
        color: #9299b8;
        margin-bottom: 8px;
    }
    .view-item p {
        margin-bottom: 0;
        font-weight: 500;
    }
</style>
@endpush
