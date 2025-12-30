@extends('layout.app')

@section('title', trans('categorymanagment::subcategory.view_subcategory'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('categorymanagment::subcategory.subcategories_management'), 'url' => route('admin.category-management.subcategories.index')],
                    ['title' => trans('categorymanagment::subcategory.view_subcategory')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('categorymanagment::subcategory.subcategory_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.category-management.subcategories.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            @can('sub-categories.edit')
                                <a href="{{ route('admin.category-management.subcategories.edit', $subCategory->id) }}" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="trans('categorymanagment::subcategory.name')" :model="$subCategory" fieldName="name" :languages="$languages" />
                                            <x-translation-display :label="trans('categorymanagment::subcategory.description')" :model="$subCategory" fieldName="description" :languages="$languages" />
                                            {{-- Parent Category --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::subcategory.category') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($subCategory->category)
                                                            <span class="badge badge-info badge-round badge-lg">{{ $subCategory->category->getTranslation('name', app()->getLocale()) }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Activation Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::subcategory.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if($subCategory->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ trans('categorymanagment::subcategory.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ trans('categorymanagment::subcategory.inactive') }}</span>
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
                                            <i class="uil uil-clock me-1"></i>{{ trans('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $subCategory->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $subCategory->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- SubCategory Image --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('categorymanagment::subcategory.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($subCategory->image)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $subCategory->image) }}"
                                                alt="{{ $subCategory->getTranslation('name', app()->getLocale()) }}"
                                                class="category-image img-fluid">
                                            </div>
                                        @else
                                            <div class="image-wrapper">
                                                <img src="{{ asset('assets/img/default.png')}}"
                                                alt="{{ $subCategory->getTranslation('name', app()->getLocale()) }}"
                                                class="category-image img-fluid">
                                            </div>
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
