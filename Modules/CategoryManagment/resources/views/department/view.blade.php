@extends('layout.app')

@section('title', trans('categorymanagment::department.view_department'))

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
                        'title' => trans('categorymanagment::department.departments_management'),
                        'url' => route('admin.category-management.departments.index'),
                    ],
                    ['title' => trans('categorymanagment::department.view_department')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('categorymanagment::department.department_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.category-management.departments.index') }}"
                                class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            @can('departments.edit')
                                <a href="{{ route('admin.category-management.departments.edit', $department->id) }}"
                                    class="btn btn-primary btn-sm">
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
                                            <x-translation-display :label="trans('categorymanagment::department.name')" :model="$department" fieldName="name"
                                                :languages="$languages" />
                                            <x-translation-display :label="trans('categorymanagment::department.description')" :model="$department"
                                                fieldName="description" :languages="$languages" />
                                            {{-- Commission --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::department.commission') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $department->commission ? $department->commission . '%' : '0%' }}
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Activation Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::department.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if ($department->active)
                                                            <span
                                                                class="badge badge-success badge-round badge-lg">{{ trans('categorymanagment::department.active') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-danger badge-round badge-lg">{{ trans('categorymanagment::department.inactive') }}</span>
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $department->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $department->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Department Image --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-image me-1"></i>{{ trans('categorymanagment::department.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if ($department->image)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $department->image) }}"
                                                    alt="{{ $department->getTranslation('name', app()->getLocale()) }}"
                                                    class="department-image img-fluid">
                                            </div>
                                        @else
                                            <img src="{{ asset('assets/img/default.png') }}"
                                                    alt="{{ $department->getTranslation('name', app()->getLocale()) }}"
                                                    class="department-image img-fluid">
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
