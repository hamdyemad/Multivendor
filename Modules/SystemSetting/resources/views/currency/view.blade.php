@extends('layout.app')

@section('title', __('systemsetting::currency.view_currency'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => __('systemsetting::currency.currencies_management'),
                        'url' => route('admin.system-settings.currencies.index'),
                    ],
                    ['title' => __('systemsetting::currency.view_currency')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::currency.currency_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.currencies.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::currency.back_to_list') }}
                            </a>
                            @can('system.currency.edit')
                                <a href="{{ route('admin.system-settings.currencies.edit', $currency->id) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ __('systemsetting::currency.edit_currency') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information Section --}}
                            <div class="col-12">
                                <h6 class="fw-500"
                                    style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-info-circle"></i>{{ __('systemsetting::currency.basic_information') }}
                                </h6>
                            </div>
                            @foreach ($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10"
                                            @if ($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            {{ __('systemsetting::currency.name') }} ({{ $language->name }})
                                        </label>
                                        <p class="fs-15 color-dark fw-500"
                                            @if ($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $currency->translations->where('lang_id', $language->id)->first()->lang_value ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.currency_code') }}</label>
                                    <p class="fs-15 color-dark">
                                        <span class="badge badge-primary badge-round badge-lg">{{ $currency->code }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.currency_symbol') }}</label>
                                    <p class="fs-15 color-dark">
                                        <span class="badge badge-info badge-round badge-lg">{{ $currency->symbol }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.status') }}</label>
                                    <p class="fs-15">
                                        @if ($currency->active)
                                            <span class="badge bg-success badge-round badge-lg">
                                                <i
                                                    class="uil uil-check me-1"></i>{{ __('systemsetting::currency.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">
                                                <i
                                                    class="uil uil-times me-1"></i>{{ __('systemsetting::currency.inactive') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>




                            {{-- Timestamps Section --}}
                            <div class="col-12">
                                <h6 class="fw-500"
                                    style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-clock"></i>{{ __('common.timestamps') }}
                                </h6>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.created_at') }}</label>
                                    <p class="fs-15 color-dark">
                                        {{ $currency->created_at ? $currency->created_at->format('Y-m-d H:i:s') : '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.updated_at') }}</label>
                                    <p class="fs-15 color-dark">
                                        {{ $currency->updated_at ? $currency->updated_at->format('Y-m-d H:i:s') : '-' }}
                                    </p>
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
