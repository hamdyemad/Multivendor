@extends('layout.app')

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
                        'title' => trans('catalogmanagement::brand.brands_management'),
                        'url' => route('admin.brands.index'),
                    ],
                    ['title' => trans('catalogmanagement::brand.view_brand')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::brand.brand_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back') }}
                            </a>
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-primary btn-sm">
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
                                @php
                                    $name = $brand->translations->where('lang_id', $language->id)
                                        ->where('lang_key', 'name')
                                        ->first();
                                @endphp
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            @if(app()->getLocale() == 'ar')
                                                @if($language->code == 'ar')
                                                    الاسم بالعربية
                                                @elseif($language->code == 'en')
                                                    الاسم بالإنجليزية
                                                @else
                                                    {{ trans('catalogmanagement::brand.name') }} ({{ $language->name }})
                                                @endif
                                            @else
                                                @if($language->code == 'ar')
                                                    Name (Arabic)
                                                @elseif($language->code == 'en')
                                                    Name (English)
                                                @else
                                                    {{ trans('catalogmanagement::brand.name') }} ({{ $language->name }})
                                                @endif
                                            @endif
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $name ? $name->lang_value : '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Description Section --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-file-alt"></i>{{ trans('catalogmanagement::brand.description') }}
                                </h6>
                            </div>

                            {{-- Dynamic Language Translations for Description --}}
                            @foreach($languages as $language)
                                @php
                                    $description = $brand->translations->where('lang_id', $language->id)
                                        ->where('lang_key', 'description')
                                        ->first();
                                @endphp
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            @if(app()->getLocale() == 'ar')
                                                @if($language->code == 'ar')
                                                    الوصف بالعربية
                                                @elseif($language->code == 'en')
                                                    الوصف بالإنجليزية
                                                @else
                                                    {{ trans('catalogmanagement::brand.description') }} ({{ $language->name }})
                                                @endif
                                            @else
                                                @if($language->code == 'ar')
                                                    Description (Arabic)
                                                @elseif($language->code == 'en')
                                                    Description (English)
                                                @else
                                                    {{ trans('catalogmanagement::brand.description') }} ({{ $language->name }})
                                                @endif
                                            @endif
                                        </label>
                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $description ? $description->lang_value : '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.activation') }}</label>
                                    <p class="fs-15">
                                        @if ($brand->active)
                                            <span class="badge bg-success badge-round badge-lg">
                                                <i class="uil uil-check me-1"></i>{{ trans('catalogmanagement::brand.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">
                                                <i class="uil uil-times me-1"></i>{{ trans('catalogmanagement::brand.inactive') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Brand Images --}}
                            <div class="col-12">
                                <h6 class="fw-500"
                                    style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-image"></i>{{ trans('catalogmanagement::brand.brand_images') }}
                                </h6>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.logo') }}</label>
                                    <div class="text-center p-3 border rounded">
                                        @if ($brand->logo)
                                            <img src="{{ asset('storage/' . $brand->logo->path) }}" alt="Brand Logo"
                                                class="img-fluid" style="max-height: 200px; object-fit: contain;">
                                        @else
                                            <p class="text-muted mb-0">{{ trans('common.no_image') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.cover') }}</label>
                                    <div class="text-center p-3 border rounded">
                                        @if ($brand->cover)
                                            <img src="{{ asset('storage/' . $brand->cover->path) }}" alt="Brand Cover"
                                                class="img-fluid" style="max-height: 200px; object-fit: contain;">
                                        @else
                                            <p class="text-muted mb-0">{{ trans('common.no_image') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Social Media Links --}}
                            <div class="col-12">
                                <h6 class="fw-500"
                                    style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-share-alt"></i>{{ trans('catalogmanagement::brand.social_media') }}
                                </h6>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.facebook_url') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if ($brand->facebook_url)
                                            <a href="{{ $brand->facebook_url }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ Str::limit($brand->facebook_url, 40) }}
                                                <i class="uil uil-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.twitter_url') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if ($brand->twitter_url)
                                            <a href="{{ $brand->twitter_url }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ Str::limit($brand->twitter_url, 40) }}
                                                <i class="uil uil-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.instagram_url') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if ($brand->instagram_url)
                                            <a href="{{ $brand->instagram_url }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ Str::limit($brand->instagram_url, 40) }}
                                                <i class="uil uil-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.linkedin_url') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if ($brand->linkedin_url)
                                            <a href="{{ $brand->linkedin_url }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ Str::limit($brand->linkedin_url, 40) }}
                                                <i class="uil uil-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label
                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::brand.pinterest_url') }}</label>
                                    <p class="fs-15 color-dark">
                                        @if ($brand->pinterest_url)
                                            <a href="{{ $brand->pinterest_url }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ Str::limit($brand->pinterest_url, 40) }}
                                                <i class="uil uil-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Timestamps --}}
                            <div class="col-12">
                                <h6 class="fw-500"
                                    style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-clock"></i>{{ trans('common.timestamps') }}
                                </h6>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $brand->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $brand->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ trans('main.confirm delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ trans('main.are you sure you want to delete this') }}</p>
                    <p class="fw-500">{{ $brand->translations->where('lang_key', 'name')->first()->lang_value ?? '' }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">{{ trans('main.cancel') }}</button>
                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ trans('main.delete') }}</button>
                    </form>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'DELETE',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message ||
                        '{{ __('common.error_occurred') }}');
                    }
                });
            });
        });
    </script>
@endpush
