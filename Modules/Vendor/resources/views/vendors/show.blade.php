@extends('layout.app')

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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ trans('vendor::vendor.vendor_details') }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-sm">
                            <i class="uil uil-arrow-left"></i> {{ trans('vendor::vendor.back_to_list') }}
                        </a>
                        <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-primary btn-sm">
                            <i class="uil uil-edit"></i> {{ trans('vendor::vendor.edit_vendor') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Vendor Information -->
                        <div class="col-lg-6">
                            <div class="card border mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ trans('vendor::vendor.vendor_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Names -->
                                    @foreach($languages as $language)
                                    <div class="mb-3">
                                        <label class="fw-bold w-100" @if($language->rtl) dir="rtl" @endif>
                                            @if($language->code == 'ar')
                                                الاسم بالعربية
                                            @else
                                                {{ trans('vendor::vendor.name') }} ({{ $language->name }}):
                                            @endif
                                        </label>
                                        <p class="mb-0 w-100" @if($language->rtl) dir="rtl" @endif>
                                            {{ $vendor->getTranslation('name', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                    @endforeach

                                    <!-- Country -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.country') }}:</label>
                                        <p class="mb-0">
                                            @if($vendor->country)
                                                {{ $vendor->country->getTranslation('name', app()->getLocale()) ?? $vendor->country->code }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Commission -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.commission') }}:</label>
                                        <p class="mb-0">{{ $vendor->commission->commission ?? 0 }}%</p>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.status') }}:</label>
                                        <p class="mb-0">
                                            @if($vendor->active)
                                                <span class="badge badge-success badge-lg badge-round">{{ trans('vendor::vendor.active') }}</span>
                                            @else
                                                <span class="badge badge-danger badge-lg badge-round">{{ trans('vendor::vendor.inactive') }}</span>
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Activities -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.activities') }}:</label>
                                        <p class="mb-0">
                                            @if($vendor->activities && $vendor->activities->count() > 0)
                                                @foreach($vendor->activities as $activity)
                                                    <span class="badge badge-primary  badge-lg badge-round me-1">
                                                        {{ $activity->getTranslation('name', app()->getLocale()) }}
                                                    </span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="col-lg-6">
                            <div class="card border mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ trans('vendor::vendor.vendor_account_details') }}</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.email') }}:</label>
                                        <p class="mb-0">{{ $vendor->user->email ?? '-' }}</p>
                                    </div>

                                    <!-- Logo -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.logo') }}:</label>
                                        <div>
                                            @if($vendor->logo && $vendor->logo->path)
                                                <img src="{{ asset('storage/' . $vendor->logo->path) }}" alt="Logo" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                            @else
                                                <p class="text-muted mb-0">{{ trans('vendor::vendor.no_logo_uploaded') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Banner -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.banner') }}:</label>
                                        <div>
                                            @if($vendor->banner && $vendor->banner->path)
                                                <img src="{{ asset('storage/' . $vendor->banner->path) }}" alt="Banner" class="img-thumbnail" style="max-width: 400px; max-height: 150px;">
                                            @else
                                                <p class="text-muted mb-0">{{ trans('vendor::vendor.no_banner_uploaded') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Created At -->
                                    <div class="mb-3">
                                        <label class="fw-bold">{{ trans('vendor::vendor.created_at') }}:</label>
                                        <p class="mb-0">{{ $vendor->created_at ? $vendor->created_at->format('Y-m-d H:i') : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Descriptions -->
                        <div class="col-lg-12">
                            <div class="card border mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ trans('vendor::vendor.description') }}</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($languages as $language)
                                    <div class="mb-3">
                                        <label class="fw-bold w-100" @if($language->rtl) dir="rtl" @endif>
                                            @if($language->code == 'ar')
                                                الوصف باللغة العربية
                                            @else
                                                {{ trans('vendor::vendor.description') }} ({{ $language->name }}):
                                            @endif
                                        </label>
                                        <p class="mb-0 w-100" @if($language->rtl) dir="rtl" @endif>
                                            {{ $vendor->getTranslation('description', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- SEO Information -->
                        <div class="col-lg-12">
                            <div class="card border mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ trans('vendor::vendor.seo_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($languages as $language)
                                    <div class="mb-4">
                                        <h6 class="text-muted mb-3">{{ $language->name }}</h6>
                                        <div class="mb-3">
                                            <label class="fw-bold">{{ trans('vendor::vendor.meta_title') }}:</label>
                                            <p class="mb-0 {{ $language->rtl ? 'text-end' : '' }}" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                                {{ $vendor->getTranslation('meta_title', $language->code) ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold">{{ trans('vendor::vendor.meta_description') }}:</label>
                                            <p class="mb-0 {{ $language->rtl ? 'text-end' : '' }}" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                                {{ $vendor->getTranslation('meta_description', $language->code) ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold">{{ trans('vendor::vendor.meta_keywords') }}:</label>
                                            <p class="mb-0 {{ $language->rtl ? 'text-end' : '' }}" {{ $language->rtl ? 'dir=rtl' : '' }}>
                                                {{ $vendor->getTranslation('meta_keywords', $language->code) ?? '-' }}
                                            </p>
                                        </div>
                                        @if(!$loop->last)
                                        <hr>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        @if($vendor->attachments && $vendor->attachments->where('type', 'document')->count() > 0)
                        <div class="col-lg-12">
                            <div class="card border mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ trans('vendor::vendor.vendor_documents') }}</h5>
                                </div>
                                <div class="card-body">
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
