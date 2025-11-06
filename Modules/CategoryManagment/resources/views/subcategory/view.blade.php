@extends('layout.app')

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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('categorymanagment::subcategory.subcategory_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.category-management.subcategories.edit', $subCategory->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                            <a href="{{ route('admin.category-management.subcategories.index') }}" class="btn btn-light btn-sm">
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
                                                {{ trans('categorymanagment::subcategory.name_english') }}
                                            @else
                                                {{ trans('categorymanagment::subcategory.name') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $subCategory->getTranslation('name', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Description Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-file-alt"></i>{{ trans('categorymanagment::subcategory.description') }}
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
                                                {{ trans('categorymanagment::subcategory.description') }}
                                            @else
                                                {{ trans('categorymanagment::subcategory.description') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $subCategory->getTranslation('description', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Main Category (Category) --}}
                            <div class="col-md-6 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">
                                        {{ trans('categorymanagment::subcategory.category') }}
                                    </label>
                                    <p class="fs-15 color-dark">
                                        @if($subCategory->category)
                                            <span class="badge badge-round badge-info badge-lg">{{ $subCategory->category->getTranslation('name', app()->getLocale()) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Department (if category has department) --}}
                            @if($subCategory->category && $subCategory->category->department)
                            <div class="col-md-6 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">
                                        {{ trans('categorymanagment::category.department') }}
                                    </label>
                                    <p class="fs-15 color-dark">
                                        <span class="badge badge-round badge-secondary badge-lg">{{ $subCategory->category->department->getTranslation('name', app()->getLocale()) }}</span>
                                    </p>
                                </div>
                            </div>
                            @endif

                            {{-- SubCategory Image --}}
                            <div class="col-md-6 mb-25">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::subcategory.image') }}</label>
                                    @if($subCategory->image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $subCategory->image) }}"
                                                 alt="{{ $subCategory->getTranslation('name', app()->getLocale()) }}"
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
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::subcategory.activation') }}</label>
                                    <p class="fs-15">
                                        @if($subCategory->active)
                                            <span class="badge badge-round badge-success badge-lg">{{ trans('categorymanagment::subcategory.active') }}</span>
                                        @else
                                            <span class="badge badge-round badge-danger badge-lg">{{ trans('categorymanagment::subcategory.inactive') }}</span>
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
                                    <p class="fs-15 color-dark">{{ $subCategory->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $subCategory->updated_at->format('Y-m-d H:i:s') }}</p>
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
