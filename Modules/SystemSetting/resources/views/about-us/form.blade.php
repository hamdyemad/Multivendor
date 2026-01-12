@extends('layout.app')

@section('title', $platform === 'website' ? __('systemsetting::about-us.about_us_website') : __('systemsetting::about-us.about_us_mobile'))

@push('styles')
<style>
    #aboutUsAccordion .accordion-button::after {
        margin-left: 0;
        margin-right: auto;
    }
    [dir="rtl"] #aboutUsAccordion .accordion-button::after {
        margin-right: 0;
        margin-left: auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => $platform === 'website' ? __('systemsetting::about-us.about_us_website') : __('systemsetting::about-us.about_us_mobile')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $platform === 'website' ? __('systemsetting::about-us.about_us_website') : __('systemsetting::about-us.about_us_mobile') }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('admin.system-settings.about-us.website') }}" 
                           class="btn btn-sm {{ $platform === 'website' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="uil uil-desktop me-1"></i> {{ __('systemsetting::about-us.website') }}
                        </a>
                        <a href="{{ route('admin.system-settings.about-us.mobile') }}" 
                           class="btn btn-sm {{ $platform === 'mobile' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="uil uil-mobile-android me-1"></i> {{ __('systemsetting::about-us.mobile') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alertContainer"></div>
                    
                    <form id="aboutUsForm" action="{{ route('admin.system-settings.about-us.update', $platform) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="accordion" id="aboutUsAccordion">
                            {{-- Section 1 --}}
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="heading1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                        <i class="uil uil-layer-group me-2"></i>
                                        {{ __('systemsetting::about-us.section') }} 1
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#aboutUsAccordion">
                                    <div class="accordion-body">
                                        {{-- Section Image --}}
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h6 class="fw-bold mb-3"><i class="uil uil-image me-1"></i> {{ __('systemsetting::about-us.section_image') }}</h6>
                                                <x-image-upload 
                                                    id="section_1_image" 
                                                    name="section_1_image"
                                                    :label="__('systemsetting::about-us.image')"
                                                    :placeholder="__('systemsetting::about-us.click_to_upload')"
                                                    :existingImage="$aboutUs->section_1_image"
                                                    aspectRatio="1:1"
                                                    recommendedSize="Recommended size: 320×320"
                                                />
                                            </div>
                                        </div>

                                        {{-- Title & Text --}}
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="fw-bold mb-3"><i class="uil uil-text me-1"></i> {{ __('systemsetting::about-us.main_content') }}</h6>
                                            </div>
                                            <x-multilingual-input 
                                                name="section_1_title" 
                                                label="Title"
                                                labelAr="العنوان"
                                                placeholder="Enter title"
                                                placeholderAr="أدخل العنوان"
                                                :languages="$languages" 
                                                :model="$aboutUs"
                                                type="textarea"
                                            />
                                            <x-multilingual-input 
                                                name="section_1_text" 
                                                label="Text"
                                                labelAr="النص"
                                                placeholder="Enter text"
                                                placeholderAr="أدخل النص"
                                                :languages="$languages" 
                                                :model="$aboutUs"
                                                type="textarea"
                                            />
                                        </div>

                                        {{-- Sub Section 1 --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-crosshairs me-1"></i> {{ __('systemsetting::about-us.sub_section') }} 1</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <x-image-upload 
                                                            id="section_1_sub_section_1_icon" 
                                                            name="section_1_sub_section_1_icon"
                                                            :label="__('systemsetting::about-us.icon')"
                                                            :placeholder="__('systemsetting::about-us.click_to_upload')"
                                                            :existingImage="$aboutUs->section_1_sub_section_1_icon"
                                                            aspectRatio="1:1"
                                                            recommendedSize="Recommended size: 112×112"
                                                        />
                                                    </div>
                                                    <div class="col-md-8">
                                                        <x-multilingual-input 
                                                            name="section_1_sub_section_1_subtitle" 
                                                            label="Subtitle"
                                                            labelAr="العنوان الفرعي"
                                                            placeholder="Enter subtitle"
                                                            placeholderAr="أدخل العنوان الفرعي"
                                                            :languages="$languages" 
                                                            :model="$aboutUs"
                                                            type="textarea"
                                                        />
                                                        <x-multilingual-input 
                                                            name="section_1_sub_section_1_text" 
                                                            label="Text"
                                                            labelAr="النص"
                                                            placeholder="Enter text"
                                                            placeholderAr="أدخل النص"
                                                            :languages="$languages" 
                                                            :model="$aboutUs"
                                                            type="textarea"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Sub Section 2 --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-eye me-1"></i> {{ __('systemsetting::about-us.sub_section') }} 2</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <x-image-upload 
                                                            id="section_1_sub_section_2_icon" 
                                                            name="section_1_sub_section_2_icon"
                                                            :label="__('systemsetting::about-us.icon')"
                                                            :placeholder="__('systemsetting::about-us.click_to_upload')"
                                                            :existingImage="$aboutUs->section_1_sub_section_2_icon"
                                                            aspectRatio="1:1"
                                                            recommendedSize="Recommended size: 112×112"
                                                        />
                                                    </div>
                                                    <div class="col-md-8">
                                                        <x-multilingual-input 
                                                            name="section_1_sub_section_2_subtitle" 
                                                            label="Subtitle"
                                                            labelAr="العنوان الفرعي"
                                                            placeholder="Enter subtitle"
                                                            placeholderAr="أدخل العنوان الفرعي"
                                                            :languages="$languages" 
                                                            :model="$aboutUs"
                                                            type="textarea"
                                                        />
                                                        <x-multilingual-input 
                                                            name="section_1_sub_section_2_text" 
                                                            label="Text"
                                                            labelAr="النص"
                                                            placeholder="Enter text"
                                                            placeholderAr="أدخل النص"
                                                            :languages="$languages" 
                                                            :model="$aboutUs"
                                                            type="textarea"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Bullets --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-list-ul me-1"></i> {{ __('systemsetting::about-us.bullets') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <x-multilingual-input 
                                                        name="section_1_bullet_1" 
                                                        label="Bullet 1"
                                                        labelAr="نقطة 1"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_1_bullet_2" 
                                                        label="Bullet 2"
                                                        labelAr="نقطة 2"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Link --}}
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="fw-bold mb-3"><i class="uil uil-link me-1"></i> {{ __('systemsetting::about-us.link') }}</h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="section_1_link" class="form-label">{{ __('systemsetting::about-us.link') }}</label>
                                                    <input type="url" 
                                                           class="form-control nockeditor" 
                                                           id="section_1_link" 
                                                           name="section_1_link" 
                                                           value="{{ $aboutUs->section_1_link ?? '' }}"
                                                           placeholder="https://example.com">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2 --}}
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="heading2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                        <i class="uil uil-layer-group me-2"></i>
                                        {{ __('systemsetting::about-us.section') }} 2
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#aboutUsAccordion">
                                    <div class="accordion-body">
                                        {{-- Section Image --}}
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h6 class="fw-bold mb-3"><i class="uil uil-image me-1"></i> {{ __('systemsetting::about-us.section_image') }}</h6>
                                                <x-image-upload 
                                                    id="section_2_image" 
                                                    name="section_2_image"
                                                    :label="__('systemsetting::about-us.image')"
                                                    :placeholder="__('systemsetting::about-us.click_to_upload')"
                                                    :existingImage="$aboutUs->section_2_image"
                                                    aspectRatio="1:1"
                                                    recommendedSize="Recommended size: 320×320"
                                                />
                                            </div>
                                        </div>

                                        {{-- Title & Text --}}
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="fw-bold mb-3"><i class="uil uil-text me-1"></i> {{ __('systemsetting::about-us.main_content') }}</h6>
                                            </div>
                                            <x-multilingual-input 
                                                name="section_2_title" 
                                                label="Title"
                                                labelAr="العنوان"
                                                placeholder="Enter title"
                                                placeholderAr="أدخل العنوان"
                                                :languages="$languages" 
                                                :model="$aboutUs"
                                                type="textarea"
                                            />
                                            <x-multilingual-input 
                                                name="section_2_text" 
                                                label="Text"
                                                labelAr="النص"
                                                placeholder="Enter text"
                                                placeholderAr="أدخل النص"
                                                :languages="$languages" 
                                                :model="$aboutUs"
                                                type="textarea"
                                            />
                                        </div>

                                        {{-- Sub Section --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-question-circle me-1"></i> {{ __('systemsetting::about-us.sub_section') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <x-multilingual-input 
                                                    name="section_2_sub_section_1_subtitle" 
                                                    label="Subtitle"
                                                    labelAr="العنوان الفرعي"
                                                    placeholder="Enter subtitle"
                                                    placeholderAr="أدخل العنوان الفرعي"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                                <x-multilingual-input 
                                                    name="section_2_sub_section_1_text" 
                                                    label="Text"
                                                    labelAr="النص"
                                                    placeholder="Enter text"
                                                    placeholderAr="أدخل النص"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                            </div>
                                        </div>

                                        {{-- Bullets --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-list-ul me-1"></i> {{ __('systemsetting::about-us.bullets') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <x-multilingual-input 
                                                        name="section_2_bullet_1" 
                                                        label="Bullet 1"
                                                        labelAr="نقطة 1"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_2_bullet_2" 
                                                        label="Bullet 2"
                                                        labelAr="نقطة 2"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_2_bullet_3" 
                                                        label="Bullet 3"
                                                        labelAr="نقطة 3"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_2_bullet_4" 
                                                        label="Bullet 4"
                                                        labelAr="نقطة 4"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Video Link --}}
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="fw-bold mb-3"><i class="uil uil-video me-1"></i> {{ __('systemsetting::about-us.video_link') }}</h6>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="section_2_video_link" class="form-label">{{ __('systemsetting::about-us.video_link') }}</label>
                                                    <input type="url" 
                                                           class="form-control nockeditor" 
                                                           id="section_2_video_link" 
                                                           name="section_2_video_link" 
                                                           value="{{ $aboutUs->section_2_video_link ?? '' }}"
                                                           placeholder="https://youtube.com/watch?v=...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3 --}}
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="heading3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                        <i class="uil uil-layer-group me-2"></i>
                                        {{ __('systemsetting::about-us.section') }} 3
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#aboutUsAccordion">
                                    <div class="accordion-body">
                                        {{-- Side 1 --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-bookmark me-1"></i> {{ __('systemsetting::about-us.side') }} 1</h6>
                                            </div>
                                            <div class="card-body">
                                                <x-multilingual-input 
                                                    name="section_3_title" 
                                                    label="Side Title"
                                                    labelAr="عنوان الجانب"
                                                    placeholder="Enter title"
                                                    placeholderAr="أدخل العنوان"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                                <x-multilingual-input 
                                                    name="section_3_text" 
                                                    label="Side Text"
                                                    labelAr="نص الجانب"
                                                    placeholder="Enter text"
                                                    placeholderAr="أدخل النص"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                            </div>
                                        </div>

                                        {{-- Side 2 --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-bookmark me-1"></i> {{ __('systemsetting::about-us.side') }} 2</h6>
                                            </div>
                                            <div class="card-body">
                                                <x-multilingual-input 
                                                    name="section_3_sub_section_1_subtitle" 
                                                    label="Side Title"
                                                    labelAr="عنوان الجانب"
                                                    placeholder="Enter title"
                                                    placeholderAr="أدخل العنوان"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                                <x-multilingual-input 
                                                    name="section_3_sub_section_1_text" 
                                                    label="Side Text"
                                                    labelAr="نص الجانب"
                                                    placeholder="Enter text"
                                                    placeholderAr="أدخل النص"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                            </div>
                                        </div>

                                        {{-- Side 3 --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-bookmark me-1"></i> {{ __('systemsetting::about-us.side') }} 3</h6>
                                            </div>
                                            <div class="card-body">
                                                <x-multilingual-input 
                                                    name="section_3_sub_section_2_subtitle" 
                                                    label="Side Title"
                                                    labelAr="عنوان الجانب"
                                                    placeholder="Enter title"
                                                    placeholderAr="أدخل العنوان"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                                <x-multilingual-input 
                                                    name="section_3_sub_section_2_text" 
                                                    label="Side Text"
                                                    labelAr="نص الجانب"
                                                    placeholder="Enter text"
                                                    placeholderAr="أدخل النص"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                            </div>
                                        </div>

                                        {{-- About Bullets --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-info-circle me-1"></i> {{ __('systemsetting::about-us.about_bullets') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <x-multilingual-input 
                                                        name="section_3_bullet_1" 
                                                        label="Bullet 1"
                                                        labelAr="نقطة 1"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_3_bullet_2" 
                                                        label="Bullet 2"
                                                        labelAr="نقطة 2"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_3_bullet_3" 
                                                        label="Bullet 3"
                                                        labelAr="نقطة 3"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Objective Bullets --}}
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-crosshairs me-1"></i> {{ __('systemsetting::about-us.objective_bullets') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <x-multilingual-input 
                                                        name="section_3_bullet_4" 
                                                        label="Bullet 1"
                                                        labelAr="نقطة 1"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 4 --}}
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="heading4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                        <i class="uil uil-layer-group me-2"></i>
                                        {{ __('systemsetting::about-us.section') }} 4
                                    </button>
                                </h2>
                                <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#aboutUsAccordion">
                                    <div class="accordion-body">
                                        {{-- Title & Text --}}
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="fw-bold mb-3"><i class="uil uil-text me-1"></i> {{ __('systemsetting::about-us.main_content') }}</h6>
                                            </div>
                                            <x-multilingual-input 
                                                name="section_4_title" 
                                                label="Title"
                                                labelAr="العنوان"
                                                placeholder="Enter title"
                                                placeholderAr="أدخل العنوان"
                                                :languages="$languages" 
                                                :model="$aboutUs"
                                                type="textarea"
                                            />
                                            <x-multilingual-input 
                                                name="section_4_text" 
                                                label="Text"
                                                labelAr="النص"
                                                placeholder="Enter text"
                                                placeholderAr="أدخل النص"
                                                :languages="$languages" 
                                                :model="$aboutUs"
                                                type="textarea"
                                            />
                                        </div>

                                        {{-- Objective Bullets --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-crosshairs me-1"></i> {{ __('systemsetting::about-us.objective_bullets') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <x-multilingual-input 
                                                        name="section_4_bullet_1" 
                                                        label="Bullet 2"
                                                        labelAr="نقطة 2"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_4_bullet_2" 
                                                        label="Bullet 3"
                                                        labelAr="نقطة 3"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Excellent Bullets --}}
                                        <div class="card mb-4 border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-star me-1"></i> {{ __('systemsetting::about-us.excellent_bullets') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <x-multilingual-input 
                                                        name="section_4_bullet_3" 
                                                        label="Bullet 1"
                                                        labelAr="نقطة 1"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_4_bullet_4" 
                                                        label="Bullet 2"
                                                        labelAr="نقطة 2"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                    <x-multilingual-input 
                                                        name="section_4_sub_section_1_subtitle" 
                                                        label="Bullet 3"
                                                        labelAr="نقطة 3"
                                                        placeholder="Enter bullet point"
                                                        placeholderAr="أدخل النقطة"
                                                        :languages="$languages" 
                                                        :model="$aboutUs"
                                                        type="textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Meta Description --}}
                                        <div class="card border">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="uil uil-file-info-alt me-1"></i> {{ __('systemsetting::about-us.meta_description') }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <x-multilingual-input 
                                                    name="section_4_sub_section_1_text" 
                                                    label="Meta Description"
                                                    labelAr="وصف الميتا"
                                                    placeholder="Enter meta description"
                                                    placeholderAr="أدخل وصف الميتا"
                                                    :languages="$languages" 
                                                    :model="$aboutUs"
                                                    type="textarea"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                <i class="uil uil-check me-1"></i>
                                <span>{{ __('systemsetting::about-us.save') }}</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#submitBtn').on('click', function(e) {
        e.preventDefault();
        
        const form = $('#aboutUsForm')[0];
        const submitBtn = $('#submitBtn');
        const spinner = submitBtn.find('.spinner-border');
        const alertContainer = $('#alertContainer');
        
        spinner.removeClass('d-none');
        submitBtn.prop('disabled', true);
        
        if (typeof CKEDITOR !== 'undefined') {
            for (const instanceName in CKEDITOR.instances) {
                const instance = CKEDITOR.instances[instanceName];
                const textarea = document.getElementById(instanceName);
                if (textarea) {
                    textarea.value = instance.getData();
                }
            }
        }
        
        const formData = new FormData(form);
        
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                spinner.addClass('d-none');
                submitBtn.prop('disabled', false);
                
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || '{{ __("systemsetting::about-us.saved_successfully") }}');
                }
                
                alertContainer.html('<div class="alert alert-success alert-dismissible fade show"><i class="uil uil-check-circle me-2"></i>' + (response.message || '{{ __("systemsetting::about-us.saved_successfully") }}') + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr) {
                spinner.addClass('d-none');
                submitBtn.prop('disabled', false);
                
                const response = xhr.responseJSON;
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(response?.message || '{{ __("systemsetting::about-us.error_saving") }}');
                }
                
                let errorHtml = '<div class="alert alert-danger alert-dismissible fade show"><i class="uil uil-exclamation-triangle me-2"></i>';
                if (response?.errors) {
                    errorHtml += '<ul class="mb-0">';
                    for (const key in response.errors) {
                        errorHtml += '<li>' + response.errors[key][0] + '</li>';
                    }
                    errorHtml += '</ul>';
                } else {
                    errorHtml += (response?.message || '{{ __("systemsetting::about-us.error_saving") }}');
                }
                errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                
                alertContainer.html(errorHtml);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
});
</script>
@endpush
