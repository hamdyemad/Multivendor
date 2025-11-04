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
                    ['title' => __('systemsetting::currency.currencies_management'), 'url' => route('admin.system-settings.currencies.index')],
                    ['title' => __('systemsetting::currency.view_currency')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('systemsetting::currency.currency_details') }}</h4>
                        <div class="d-flex gap-2">
                            @can('system.currency.edit')
                            <a href="{{ route('admin.system-settings.currencies.edit', $currency->id) }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-edit"></i> {{ __('systemsetting::currency.edit_currency') }}
                            </a>
                            @endcan
                            <a href="{{ route('admin.system-settings.currencies.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-arrow-left"></i> {{ __('systemsetting::currency.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <!-- Basic Information Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('systemsetting::currency.basic_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.currency_code') }}</label>
                                        <div class="userDatatable-content">
                                            <span class="badge badge-primary" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                {{ $currency->code }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.currency_symbol') }}</label>
                                        <div class="userDatatable-content">
                                            <span class="badge badge-info" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                {{ $currency->symbol }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Translations Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('systemsetting::currency.translations') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-20">
                                            <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.name') }} ({{ $language->name }})</label>
                                            <div class="userDatatable-content" @if($language->rtl) dir="rtl" @endif>
                                                <strong>{{ $currency->translations->where('lang_id', $language->id)->first()->lang_value ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Status & Dates Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('systemsetting::currency.status') }} & {{ __('systemsetting::currency.dates') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.active') }}</label>
                                        <div class="userDatatable-content">
                                            @if($currency->active)
                                                <span class="badge badge-success" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-check me-1"></i>{{ __('systemsetting::currency.active') }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-times me-1"></i>{{ __('systemsetting::currency.inactive') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.created_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-calendar-alt me-2"></i>
                                            <strong>{{ $currency->created_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($currency->updated_at)
                    <!-- Updated At Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('systemsetting::currency.updated_at') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.updated_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-clock me-2"></i>
                                            <strong>{{ $currency->updated_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
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
