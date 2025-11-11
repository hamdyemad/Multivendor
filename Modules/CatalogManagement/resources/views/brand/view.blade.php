@extends('layout.app')
@section('title', trans('catalogmanagement::brand.view_brand'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::brand.brands_management'), 'url' => route('admin.brands.index')],
                    ['title' => trans('catalogmanagement::brand.view_brand')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::brand.brand_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('common.basic_information') }}
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
                                                                {{ trans('catalogmanagement::brand.name') }}
                                                            @else
                                                                {{ trans('catalogmanagement::brand.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $brand->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            {{-- Dynamic Language Translations for Description --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                الوصف بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('catalogmanagement::brand.description') }}
                                                            @else
                                                                {{ trans('catalogmanagement::brand.description') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $brand->getTranslation('description', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Activation Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if($brand->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ trans('catalogmanagement::brand.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ trans('catalogmanagement::brand.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Social Media Links Card --}}
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-share-alt me-1"></i>{{ trans('catalogmanagement::brand.social_media') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.facebook_url') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($brand->facebook_url)
                                                            <a href="{{ $brand->facebook_url }}" target="_blank" class="text-decoration-none">
                                                                {{ Str::limit($brand->facebook_url, 40) }} <i class="uil uil-external-link-alt"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.twitter_url') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($brand->twitter_url)
                                                            <a href="{{ $brand->twitter_url }}" target="_blank" class="text-decoration-none">
                                                                {{ Str::limit($brand->twitter_url, 40) }} <i class="uil uil-external-link-alt"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.instagram_url') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($brand->instagram_url)
                                                            <a href="{{ $brand->instagram_url }}" target="_blank" class="text-decoration-none">
                                                                {{ Str::limit($brand->instagram_url, 40) }} <i class="uil uil-external-link-alt"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.linkedin_url') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($brand->linkedin_url)
                                                            <a href="{{ $brand->linkedin_url }}" target="_blank" class="text-decoration-none">
                                                                {{ Str::limit($brand->linkedin_url, 40) }} <i class="uil uil-external-link-alt"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.pinterest_url') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($brand->pinterest_url)
                                                            <a href="{{ $brand->pinterest_url }}" target="_blank" class="text-decoration-none">
                                                                {{ Str::limit($brand->pinterest_url, 40) }} <i class="uil uil-external-link-alt"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Timestamps Card --}}
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ trans('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $brand->created_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $brand->updated_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Brand Images --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('catalogmanagement::brand.logo') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($brand->logo)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $brand->logo->path) }}"
                                                alt="{{ $brand->getTranslation('name', app()->getLocale()) }}"
                                                class="brand-image img-fluid">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">{{ trans('common.no_image') ?? 'No image uploaded' }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('catalogmanagement::brand.cover') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($brand->cover)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $brand->cover->path) }}"
                                                alt="{{ $brand->getTranslation('name', app()->getLocale()) }}"
                                                class="brand-image img-fluid">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">{{ trans('common.no_image') ?? 'No image uploaded' }}</p>
                                        @endif
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
