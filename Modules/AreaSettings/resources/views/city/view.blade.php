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
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Dynamic Language Translations for Name --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                الاسم بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ __('areasettings::city.name_english') }}
                                                            @else
                                                                {{ __('areasettings::city.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $city->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.country') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($city->country)
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                {{ $city->country->getTranslation('name', app()->getLocale()) ?? $city->country->code }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::city.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if($city->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('areasettings::city.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('areasettings::city.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $city->created_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $city->updated_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Related Regions --}}
                        @if($city->regions && $city->regions->count() > 0)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-map-marker me-1"></i>{{ __('areasettings::city.regions') }}
                                                <span class="badge badge-primary badge-round badge-lg ms-2">{{ $city->regions->count() }}</span>
                                            </h3>
                                        </div>
                                        <div class="card-body">
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
                                                                        <span class="badge badge-success badge-round badge-lg">{{ __('areasettings::region.active') }}</span>
                                                                    @else
                                                                        <span class="badge badge-danger badge-round badge-lg">{{ __('areasettings::region.inactive') }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $region->created_at->format('d M, Y') }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.area-settings.regions.show', $region->id) }}" class="btn btn-sm btn-primary">
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
                                    </div>
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
