@extends('layout.app')

@section('title', __('systemsetting::ads.view_ad'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => __('common.dashboard'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => __('systemsetting::ads.ads_management'),
                        'url' => route('admin.system-settings.ads.index'),
                    ],
                    ['title' => __('systemsetting::ads.view_ad')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::ads.ad_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.ads.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::ads.back_to_list') }}
                            </a>
                            @can('ads.edit')
                                <a href="{{ route('admin.system-settings.ads.edit', $ad->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ __('systemsetting::ads.edit') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                {{-- Basic Information --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-info-circle me-1"></i>{{ __('systemsetting::ads.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Position --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.position') }}</label>
                                                    <p class="fs-15">
                                                        <span class="badge badge-round badge-info badge-lg">
                                                            {{ $ad->position_label }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($ad->active)
                                                            <span class="badge badge-round badge-success badge-lg">
                                                                <i
                                                                    class="uil uil-check-circle me-1"></i>{{ __('systemsetting::ads.active') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-round badge-danger badge-lg">
                                                                <i
                                                                    class="uil uil-times-circle me-1"></i>{{ __('systemsetting::ads.inactive') }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.type') }}</label>
                                                    <p class="fs-15">
                                                        @if ($ad->type && is_array($ad->type))
                                                            @foreach ($ad->type as $type)
                                                                @php $color = $type == 'mobile' ? 'primary' : 'secondary'; @endphp
                                                                <span
                                                                    class="badge badge-round badge-sm badge-{{ $color }} me-1">
                                                                    {{ __('systemsetting::ads.' . $type) }}
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Link --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.link') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($ad->link)
                                                            <a href="{{ $ad->link }}" target="_blank"
                                                                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
                                                                <i
                                                                    class="uil uil-external-link-alt me-1"></i>{{ __('systemsetting::ads.visit_link') }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $ad->created_at }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $ad->updated_at }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Sidebar --}}
                            <div class="col-md-4 order-1 order-md-2">
                                {{-- Image --}}
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ __('systemsetting::ads.ad_image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if ($ad->image)
                                            <img src="{{ asset('storage/' . $ad->image) }}" alt="Ad Image"
                                                class="img-fluid round"
                                                style="max-width: 100%; max-height: 300px;">
                                        @else
                                            <img src="{{ asset('assets/img/default.png') }}" alt="Ad Image"
                                                class="img-fluid round"
                                                style="max-width: 100%; max-height: 300px;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            {{-- Ad Content - Translations --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-file-text me-1"></i>{{ __('systemsetting::ads.ad_content') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Title --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label
                                                    class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.title') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $ad->getTranslation('title', $lang->code);
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div
                                                                style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span
                                                                        class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold"
                                                                        style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if ($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Subtitle --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label
                                                    class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::ads.subtitle') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $ad->getTranslation('subtitle', $lang->code);
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div
                                                                style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span
                                                                        class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold"
                                                                        style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500"
                                                                    style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if ($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
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

@push('after-body')
    {{-- Loading Overlay Component --}}
    <x-loading-overlay />
@endpush
