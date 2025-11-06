@extends('layout.app')

@section('title', __('areasettings::city.view_city'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::city.cities_management'), 'url' => route('admin.area-settings.cities.index')],
                    ['title' => __('areasettings::city.view_city')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('areasettings::city.city_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.area-settings.cities.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('areasettings::city.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.area-settings.cities.edit', $city->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('areasettings::city.edit_city') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-info-circle"></i>{{ __('areasettings::city.basic_information') }}
                                </h6>
                            </div>
                            @foreach($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            {{ __('areasettings::city.name') }} ({{ $language->name }})
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $city->getTranslation('name', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.country') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if($city->country)
                                            <span class="badge badge-primary badge-round badge-lg me-1">
                                                {{ $city->country->getTranslation('name', app()->getLocale()) ?? $city->country->code }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.status') }}</label>
                                    <p class="fs-15">
                                        @if($city->active)
                                            <span class="badge bg-success badge-round badge-lg">
                                                <i class="uil uil-check me-1"></i>{{ __('areasettings::city.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">
                                                <i class="uil uil-times me-1"></i>{{ __('areasettings::city.inactive') }}
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
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.created_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $city->created_at ? $city->created_at->format('Y-m-d H:i:s') : '-' }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $city->updated_at ? $city->updated_at->format('Y-m-d H:i:s') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Related Data --}}
                        @if($city->regions && $city->regions->count() > 0)
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-map-marker"></i>{{ __('areasettings::city.regions') }} ({{ $city->regions->count() }})
                                </h6>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('areasettings::region.id') }}</th>
                                                        <th>{{ __('areasettings::region.name') }}</th>
                                                        <th>{{ __('areasettings::region.status') }}</th>
                                                        <th>{{ __('areasettings::region.created_at') }}</th>
                                                        <th>{{ __('areasettings::region.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($city->regions as $region)
                                                        <tr>
                                                            <td>{{ $region->id }}</td>
                                                            <td>{{ $region->getTranslation('name', app()->getLocale()) ?? '-' }}</td>
                                                            <td>
                                                                @if($region->active)
                                                                    <span class="badge badge-success">{{ __('areasettings::region.active') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ __('areasettings::region.inactive') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $region->created_at->format('Y-m-d') }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.area-settings.regions.show', $region->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="uil uil-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.area-settings.regions.edit', $region->id) }}" class="btn btn-sm btn-warning">
                                                                    <i class="uil uil-edit"></i>
                                                                </a>
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
