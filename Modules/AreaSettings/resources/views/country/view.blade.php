@extends('layout.app')
@section('title')
{{ $title }}
@endsection
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
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('areasettings::country.country_details') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.area-settings.countries.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-arrow-left"></i> {{ __('areasettings::country.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.area-settings.countries.edit', $country->id) }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-edit"></i> {{ __('areasettings::country.edit_country') }}
                            </a>
                        </div>
                    </div>
                    <!-- Basic Information Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('areasettings::country.basic_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.country_code') }}</label>
                                        <div class="userDatatable-content">
                                            <span class="badge badge-primary" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                {{ $country->code }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.phone_code') }}</label>
                                        <div class="userDatatable-content">
                                            <span class="badge badge-info" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                {{ $country->phone_code }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.currency') }}</label>
                                        <div class="userDatatable-content">
                                            @if($country->currency)
                                                <span class="badge badge-warning" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    {{ $country->currency->getTranslation('name', app()->getLocale()) }} ({{ $country->currency->code }})
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.active') }}</label>
                                        <div class="userDatatable-content">
                                            @if($country->active)
                                                <span class="badge badge-success" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-check me-1"></i>{{ __('areasettings::country.active') }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-times me-1"></i>{{ __('areasettings::country.inactive') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Translations Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('areasettings::country.translations') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-20">
                                            <label class="il-gray fs-14 fw-500 mb-10 w-100" @if($language->rtl) dir="rtl" @endif>{{ __('areasettings::country.name') }} ({{ $language->name }})</label>
                                            <div class="userDatatable-content" @if($language->rtl) dir="rtl" @endif>
                                                <strong>{{ $country->translations->where('lang_id', $language->id)->first()->lang_value ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Updated At Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('common.timestamps') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.created_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-calendar-alt me-2"></i>
                                            <strong>{{ $country->created_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.updated_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-clock me-2"></i>
                                            <strong>{{ $country->updated_at->format('Y-m-d H:i:s') }}</strong>
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
