@extends('layout.app')

@section('title', __('systemsetting::blog_categories.view_blog_category'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => __('menu.dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => __('systemsetting::blog_categories.blog_categories_management'),
                        'url' => route('admin.system-settings.blog-categories.index'),
                    ],
                    ['title' => __('systemsetting::blog_categories.view_blog_category')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::blog_categories.category_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.blog-categories.index') }}"
                                class="btn btn-light btn-sm">
                                <i
                                    class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::blog_categories.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.system-settings.blog-categories.edit', $blogCategory->id) }}"
                                class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('systemsetting::blog_categories.edit') }}
                            </a>
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
                                                class="uil uil-info-circle me-1"></i>{{ __('systemsetting::blog_categories.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Title --}}
                                            <x-translation-display :label="__('systemsetting::blog_categories.title')" :model="$blogCategory" fieldName="title"
                                                :languages="$languages" />
                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blog_categories.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($blogCategory->active)
                                                            <span class="badge badge-round badge-success badge-lg">
                                                                <i
                                                                    class="uil uil-check-circle me-1"></i>{{ __('systemsetting::blog_categories.active') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-round badge-danger badge-lg">
                                                                <i
                                                                    class="uil uil-times-circle me-1"></i>{{ __('systemsetting::blog_categories.inactive') }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Blogs Count --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blog_categories.total_blogs') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <span class="badge badge-round badge-info badge-lg">
                                                            <i
                                                                class="uil uil-file-alt me-1"></i>{{ $blogCategory->blogs()->count() }}
                                                            {{ __('systemsetting::blog_categories.blogs') }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blog_categories.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i
                                                            class="uil uil-calendar-alt me-1"></i>{{ $blogCategory->created_at }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blog_categories.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i
                                                            class="uil uil-calendar-alt me-1"></i>{{ $blogCategory->updated_at }}
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
                                            <i
                                                class="uil uil-image me-1"></i>{{ __('systemsetting::blog_categories.category_image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if ($blogCategory->mainImage && $blogCategory->mainImage->path)
                                            <img src="{{ asset('storage/' . $blogCategory->mainImage->path) }}"
                                                alt="{{ $blogCategory->title }}" class="img-fluid round"
                                                style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('assets/img/default.png') }}"
                                                alt="{{ $blogCategory->title }}" class="img-fluid round"
                                                style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            {{-- SEO Information --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i
                                            class="uil uil-search me-1"></i>{{ __('systemsetting::blog_categories.seo_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Meta Title --}}
                                        <x-translation-display :label="__('systemsetting::blog_categories.meta_title')" :model="$blogCategory" fieldName="meta_title"
                                            :languages="$languages" />

                                        {{-- Meta Description --}}
                                        <x-translation-display :label="__('systemsetting::blog_categories.meta_description')" :model="$blogCategory"
                                            fieldName="meta_description" :languages="$languages" />

                                        {{-- Meta Keywords --}}
                                        <x-translation-display :label="__('systemsetting::blog_categories.meta_keywords')" :model="$blogCategory"
                                            fieldName="meta_keywords" :languages="$languages" type="keywords" />
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
