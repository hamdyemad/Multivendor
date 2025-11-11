@extends('layout.app')

@section('title', __('systemsetting::currency.view_currency'))

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
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::currency.currency_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.currencies.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::currency.back_to_list') }}
                            </a>
                            @can('system.currency.edit')
                                <a href="{{ route('admin.system-settings.currencies.edit', $currency->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ __('systemsetting::currency.edit_currency') }}
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
                                            {{-- Dynamic Language Translations for Name --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                الاسم بالعربية
                                                            @else
                                                                {{ __('systemsetting::currency.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $currency->translations->where('lang_id', $language->id)->first()->lang_value ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Currency Code --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.currency_code') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-primary badge-round badge-lg">{{ $currency->code }}</span>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Currency Symbol --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.currency_symbol') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">{{ $currency->symbol }}</span>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Activation Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.status') }}</label>
                                                    <p class="fs-15">
                                                        @if($currency->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('systemsetting::currency.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('systemsetting::currency.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-holder mt-3">
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
                                                    <p class="fs-15 color-dark">{{ $currency->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $currency->updated_at }}</p>
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
@endsection
