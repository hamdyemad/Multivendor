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
                            <a href="{{ route('admin.area-settings.countries.edit', $country->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('areasettings::country.edit_country') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-info-circle"></i>{{ __('areasettings::country.basic_information') }}
                                </h6>
                            </div>
                            @foreach($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            {{ __('areasettings::country.name') }} ({{ $language->name }})
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $country->translations->where('lang_id', $language->id)->first()->lang_value ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.country_code') }}</label>
                                    <p class="fs-15 color-dark">
                                        <span class="badge badge-primary badge-round badge-lg">{{ $country->code }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.phone_code') }}</label>
                                    <p class="fs-15 color-dark">
                                        <span class="badge badge-info badge-round badge-lg">{{ $country->phone_code }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
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
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.active') }}</label>
                                    <p class="fs-15">
                                        @if($country->active)
                                            <span class="badge bg-success badge-round badge-lg">
                                                <i class="uil uil-check me-1"></i>{{ __('areasettings::country.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">
                                                <i class="uil uil-times me-1"></i>{{ __('areasettings::country.inactive') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Timestamps Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-clock"></i>{{ __('common.timestamps') }}
                                </h6>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.created_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $country->created_at ? $country->created_at->format('Y-m-d H:i:s') : '-' }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('areasettings::country.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $country->updated_at ? $country->updated_at->format('Y-m-d H:i:s') : '-' }}</p>
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
