@extends('layout.app')

@section('title', trans('categorymanagment::category.view_category'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('categorymanagment::category.categories_management'), 'url' => route('admin.category-management.categories.index')],
                    ['title' => trans('categorymanagment::category.view_category')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('categorymanagment::category.category_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.category-management.categories.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.category-management.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
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
                                                                {{ trans('categorymanagment::category.name_english') }}
                                                            @else
                                                                {{ trans('categorymanagment::category.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $category->getTranslation('name', $language->code) ?? '-' }}
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
                                                                {{ trans('categorymanagment::category.description_english') }}
                                                            @else
                                                                {{ trans('categorymanagment::category.description') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $category->getTranslation('description', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            {{-- Category Department --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::category.department') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($category->department)
                                                            <span class="badge badge-primary badge-round badge-lg">{{ $category->department->getTranslation('name', app()->getLocale()) }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Activation Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::category.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if($category->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ trans('categorymanagment::category.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ trans('categorymanagment::category.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                    <p class="fs-15 color-dark">{{ $category->created_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $category->updated_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Category Image --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('categorymanagment::category.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($category->image)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->getTranslation('name', app()->getLocale()) }}"
                                                class="category-image img-fluid">
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

    {{-- Image Modal Component --}}
    <x-image-modal />
@endsection

