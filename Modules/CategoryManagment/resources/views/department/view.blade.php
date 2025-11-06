@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('categorymanagment::department.departments_management'), 'url' => route('admin.category-management.departments.index')],
                    ['title' => trans('categorymanagment::department.view_department')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('categorymanagment::department.department_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.category-management.departments.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.category-management.departments.edit', $department->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-info-circle"></i>{{ trans('common.basic_information') }}
                                </h6>
                            </div>

                            {{-- Dynamic Language Translations for Name --}}
                            @foreach($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            @if($language->code == 'ar')
                                                الاسم بالعربية
                                            @elseif($language->code == 'en')
                                                {{ trans('categorymanagment::department.name_english') }}
                                            @else
                                                {{ trans('categorymanagment::department.name') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $department->getTranslation('name', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Description Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-file-alt"></i>{{ trans('categorymanagment::department.description') }}
                                </h6>
                            </div>

                            {{-- Dynamic Language Translations for Description --}}
                            @foreach($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            @if($language->code == 'ar')
                                                الوصف بالعربية
                                            @elseif($language->code == 'en')
                                                {{ trans('categorymanagment::department.description') }}
                                            @else
                                                {{ trans('categorymanagment::department.description') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $department->getTranslation('description', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-12 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">
                                        {{ trans('categorymanagment::department.activities') }}
                                    </label>
                                    <p class="fs-15 color-dark">
                                        @foreach ($department->activities as $activity)
                                            <span class="badge badge-primary">{{ $activity->getTranslation('name', app()->getLocale()) }}</span>
                                        @endforeach
                                    </p>
                                </div>
                            </div>

                            {{-- Department Image --}}
                            <div class="col-md-6 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::department.image') }}</label>
                                    @if($department->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $department->image) }}"
                                                 alt="{{ $department->getTranslation('name', app()->getLocale()) }}"
                                                 class="img-fluid rounded shadow-sm"
                                                 style="max-width: 300px; height: auto; object-fit: cover;">
                                        </div>
                                    @else
                                        <p class="fs-15 color-light fst-italic">{{ trans('common.no_image') ?? 'No image uploaded' }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::department.activation') }}</label>
                                    <p class="fs-15">
                                        @if($department->active)
                                            <span class="badge bg-success">{{ trans('categorymanagment::department.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ trans('categorymanagment::department.inactive') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Timestamps --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-clock"></i>{{ trans('common.timestamps') }}
                                </h6>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $department->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $department->updated_at->format('Y-m-d H:i:s') }}</p>
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
