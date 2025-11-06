@extends('layout.app')

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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('categorymanagment::category.category_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.category-management.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                            <a href="{{ route('admin.category-management.categories.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
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

                            {{-- Description Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-file-alt"></i>{{ trans('categorymanagment::category.description') }}
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
                                                {{ trans('categorymanagment::category.description') }}
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

                            {{-- Department --}}
                            <div class="col-md-6 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">
                                        {{ trans('categorymanagment::category.department') }}
                                    </label>
                                    <p class="fs-15 color-dark">
                                        @if($category->department)
                                            <span class="badge badge-info badge-round badge-lg">{{ $category->department->getTranslation('name', app()->getLocale()) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Category Image --}}
                            <div class="col-md-6 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::category.image') }}</label>
                                    @if($category->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                 alt="{{ $category->getTranslation('name', app()->getLocale()) }}"
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
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::category.activation') }}</label>
                                    <p class="fs-15">
                                        @if($category->active)
                                            <span class="badge bg-success badge-round badge-lg">{{ trans('categorymanagment::category.active') }}</span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">{{ trans('categorymanagment::category.inactive') }}</span>
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
                                    <p class="fs-15 color-dark">{{ $category->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $category->updated_at->format('Y-m-d H:i:s') }}</p>
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
