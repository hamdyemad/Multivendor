@extends('layout.app')

@section('title', __('areasettings::region.view_region'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::region.regions_management'), 'url' => route('admin.area-settings.regions.index')],
                    ['title' => __('areasettings::region.view_region')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('areasettings::region.region_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.area-settings.regions.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('areasettings::region.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.area-settings.regions.edit', $region->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('areasettings::region.edit_region') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-info-circle"></i>{{ __('areasettings::region.basic_information') }}
                                </h6>
                            </div>
                            @foreach($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            {{ __('areasettings::region.name') }} ({{ $language->name }})
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $region->getTranslation('name', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::region.country') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if($region->city && $region->city->country)
                                            <span class="badge badge-primary badge-round badge-lg me-1">
                                                {{ $region->city->country->getTranslation('name', app()->getLocale()) ?? $region->city->country->code }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::region.city') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if($region->city)
                                            <span class="badge badge-info badge-round badge-lg me-1">
                                                {{ $region->city->getTranslation('name', app()->getLocale()) ?? $region->city->id }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::region.status') }}</label>
                                    <p class="fs-15">
                                        @if($region->active)
                                            <span class="badge bg-success badge-round badge-lg">
                                                <i class="uil uil-check me-1"></i>{{ __('areasettings::region.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">
                                                <i class="uil uil-times me-1"></i>{{ __('areasettings::region.inactive') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>



                            {{-- Timestamps Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-clock"></i>{{ __('common.timestamps') }}</h6>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::region.created_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $region->created_at ? $region->created_at->format('Y-m-d H:i:s') : '-' }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::region.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $region->updated_at ? $region->updated_at->format('Y-m-d H:i:s') : '-' }}</p>
                                </div>
                            </div>
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
