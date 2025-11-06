@extends('layout.app')

@section('title', __('areasettings::subregion.view_subregion'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::subregion.subregions_management'), 'url' => route('admin.area-settings.subregions.index')],
                    ['title' => __('areasettings::subregion.view_subregion')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('areasettings::subregion.subregion_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.area-settings.subregions.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('areasettings::subregion.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.area-settings.subregions.edit', $subregion->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('areasettings::subregion.edit_subregion') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-info-circle"></i>{{ __('areasettings::subregion.basic_information') }}
                                </h6>
                            </div>
                            @foreach($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            {{ __('areasettings::subregion.name') }} ({{ $language->name }})
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $subregion->getTranslation('name', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::subregion.country') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if($subregion->region && $subregion->region->city && $subregion->region->city->country)
                                            <span class="badge badge-primary badge-round badge-lg me-1">
                                                {{ $subregion->region->city->country->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->country->code }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::subregion.city') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if($subregion->region && $subregion->region->city)
                                            <span class="badge badge-info badge-round badge-lg me-1">
                                                {{ $subregion->region->city->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->id }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::subregion.region') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if($subregion->region)
                                            <span class="badge badge-warning badge-round badge-lg me-1">
                                                {{ $subregion->region->getTranslation('name', app()->getLocale()) ?? $subregion->region->id }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::subregion.status') }}</label>
                                    <p class="fs-15">
                                        @if($subregion->active)
                                            <span class="badge bg-success badge-round badge-lg">
                                                <i class="uil uil-check me-1"></i>{{ __('areasettings::subregion.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">
                                                <i class="uil uil-times me-1"></i>{{ __('areasettings::subregion.inactive') }}
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
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::subregion.created_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $subregion->created_at ? $subregion->created_at->format('Y-m-d H:i:s') : '-' }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::subregion.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $subregion->updated_at ? $subregion->updated_at->format('Y-m-d H:i:s') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Hierarchy Navigation --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-sitemap"></i>{{ __('areasettings::subregion.hierarchy_navigation') }}
                                </h6>
                            </div>
                            <div class="col-12 mt-3">
                                <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb">
                                                @if($subregion->region && $subregion->region->city && $subregion->region->city->country)
                                                    <li class="breadcrumb-item">
                                                        <a href="{{ route('admin.area-settings.countries.show', $subregion->region->city->country->id) }}">
                                                            <i class="uil uil-globe"></i> {{ $subregion->region->city->country->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->country->code }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($subregion->region && $subregion->region->city)
                                                    <li class="breadcrumb-item">
                                                        <a href="{{ route('admin.area-settings.cities.show', $subregion->region->city->id) }}">
                                                            <i class="uil uil-building"></i> {{ $subregion->region->city->getTranslation('name', app()->getLocale()) ?? $subregion->region->city->id }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($subregion->region)
                                                    <li class="breadcrumb-item">
                                                        <a href="{{ route('admin.area-settings.regions.show', $subregion->region->id) }}">
                                                            <i class="uil uil-map-marker"></i> {{ $subregion->region->getTranslation('name', app()->getLocale()) ?? $subregion->region->id }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="breadcrumb-item active" aria-current="page">
                                                    <i class="uil uil-location-point"></i> {{ $subregion->getTranslation('name', app()->getLocale()) ?? $subregion->id }}
                                                </li>
                                            </ol>
                                </nav>
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
