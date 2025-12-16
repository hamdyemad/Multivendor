@extends('layout.app')

@section('title', __('systemsetting::blogs.view_blog'))

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
                        'title' => __('systemsetting::blogs.blogs_management'),
                        'url' => route('admin.system-settings.blogs.index'),
                    ],
                    ['title' => __('systemsetting::blogs.view_blog')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::blogs.blog_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.blogs.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::blogs.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.system-settings.blogs.edit', $blog->id) }}"
                                class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('systemsetting::blogs.edit') }}
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
                                                class="uil uil-info-circle me-1"></i>{{ __('systemsetting::blogs.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Status --}}
                                            <div class="col-md-4">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blogs.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($blog->active)
                                                            <span class="badge badge-round badge-success badge-lg">
                                                                <i
                                                                    class="uil uil-check-circle me-1"></i>{{ __('systemsetting::blogs.active') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-round badge-danger badge-lg">
                                                                <i
                                                                    class="uil uil-times-circle me-1"></i>{{ __('systemsetting::blogs.inactive') }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Category --}}
                                            <div class="col-md-4">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blogs.category') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($blog->blogCategory)
                                                            <span class="badge badge-round badge-info badge-lg">
                                                                <i
                                                                    class="uil uil-tag me-1"></i>{{ $blog->blogCategory->title }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created At --}}
                                            <div class="col-md-4">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blogs.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $blog->created_at }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated At --}}
                                            <div class="col-md-4">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::blogs.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $blog->updated_at }}
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
                                            <i class="uil uil-image me-1"></i>{{ __('systemsetting::blogs.blog_image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if ($blog->mainImage && $blog->mainImage->path)
                                            <img src="{{ asset('storage/' . $blog->mainImage->path) }}"
                                                alt="{{ $blog->title }}" class="img-fluid round"
                                                style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                        @else
                                            <div class="p-5 bg-light round">
                                                <i class="uil uil-image-slash" style="font-size: 48px; color: #ccc;"></i>
                                                <p class="text-muted mt-2">
                                                    {{ __('systemsetting::blogs.no_image') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Blog Content - Translations --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-file-text me-1"></i>{{ __('systemsetting::blogs.blog_content') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Title --}}
                                        <x-translation-display :label="__('systemsetting::blogs.title')" :model="$blog" fieldName="title"
                                            :languages="$languages" />

                                        {{-- Content --}}
                                        <x-translation-display :label="__('systemsetting::blogs.content')" :model="$blog" fieldName="content"
                                            :languages="$languages" type="html" />
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Information --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-search me-1"></i>{{ __('systemsetting::blogs.seo_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Meta Title --}}
                                        <x-translation-display :label="__('systemsetting::blogs.meta_title')" :model="$blog" fieldName="meta_title"
                                            :languages="$languages" />

                                        {{-- Meta Description --}}
                                        <x-translation-display :label="__('systemsetting::blogs.meta_description')" :model="$blog"
                                            fieldName="meta_description" :languages="$languages" />

                                        {{-- Meta Keywords --}}
                                        <x-translation-display :label="__('systemsetting::blogs.meta_keywords')" :model="$blog"
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
