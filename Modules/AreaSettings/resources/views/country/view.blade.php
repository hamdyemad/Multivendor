@extends('layout.app')

@section('title', __('areasettings::country.view_country'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('areasettings::country.countries_management'), 'url' => route('admin.area-settings.countries.index')],
                    ['title' => __('areasettings::country.view_country')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('areasettings::country.country_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.area-settings.countries.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('areasettings::country.back_to_list') }}
                            </a>
                            @can('countries.edit')
                                <a href="{{ route('admin.area-settings.countries.edit', $country->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ __('areasettings::country.edit_country') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="__('areasettings::country.name')" :model="$country" fieldName="name" :languages="$languages" />
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.country_code') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-primary badge-round badge-lg">{{ $country->code }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.phone_code') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">{{ $country->phone_code }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.phone_length') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">{{ $country->phone_length }} {{ __('common.digits') ?? 'digits' }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.currency') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($country->currency)
                                                            <span class="badge badge-warning badge-round badge-lg">
                                                                {{ $country->currency->getTranslation('name', app()->getLocale()) }} ({{ $country->currency->code }})
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if($country->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('areasettings::country.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('areasettings::country.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.default') }}</label>
                                                    <p class="fs-15">
                                                        @if($country->default)
                                                            <span class="badge badge-success badge-round badge-lg">
                                                                <i class="uil uil-star me-1"></i>{{ __('areasettings::country.default') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                <i class="uil uil-minus me-1"></i>{{ __('common.no') ?? 'No' }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($country->image)
                            {{-- Country Image --}}
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ __('areasettings::country.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($country->image)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $country->image->path) }}"
                                                alt="{{ $country->getTranslation('name', app()->getLocale()) }}"
                                                class="category-image img-fluid">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">{{ __('common.no_image') ?? 'No image uploaded' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">
                                <div class="card card-holder">
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
                                                    <p class="fs-15 color-dark">{{ $country->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $country->updated_at }}</p>
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
    {{-- Image Modal Component --}}
    <x-image-modal />
@endsection
