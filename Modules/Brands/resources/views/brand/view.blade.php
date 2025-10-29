@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('brands::brand.brands_management'), 'url' => route('admin.brands.index')],
                    ['title' => trans('brands::brand.view_brand')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('brands::brand.brand_details') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-arrow-left"></i> {{ trans('common.back') }}
                            </a>
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-edit"></i> {{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>

                                        <!-- Translations Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('common.translations') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($languages as $language)
                                    @php
                                        $name = $brand->translations->where('lang_id', $language->id)
                                            ->where('lang_key', 'name')
                                            ->first();
                                        $description = $brand->translations->where('lang_id', $language->id)
                                            ->where('lang_key', 'description')
                                            ->first();
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="form-group mb-20">
                                            <label class="il-gray fs-14 fw-500 mb-10">{{ trans('brands::brand.name') }} ({{ $language->name }})</label>
                                            <div class="userDatatable-content" @if($language->rtl) dir="rtl" @endif>
                                                <strong>{{ $name ? $name->lang_value : '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-20">
                                            <label class="il-gray fs-14 fw-500 mb-10">{{ trans('brands::brand.description') }} ({{ $language->name }})</label>
                                            <div class="userDatatable-content" @if($language->rtl) dir="rtl" @endif>
                                                <strong>{{ $description ? $description->lang_value : '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- Brand Images Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('brands::brand.brand_images') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('brands::brand.logo') }}</label>
                                        <div class="userDatatable-content text-center p-3 border rounded">
                                            @if($brand->logo)
                                                <img src="{{ asset('storage/' . $brand->logo->path) }}" 
                                                     alt="Brand Logo" 
                                                     class="img-fluid" 
                                                     style="max-height: 200px; object-fit: contain;">
                                            @else
                                                <p class="text-muted mb-0">{{ trans('common.no_image') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('brands::brand.cover') }}</label>
                                        <div class="userDatatable-content text-center p-3 border rounded">
                                            @if($brand->cover)
                                                <img src="{{ asset('storage/' . $brand->cover->path) }}" 
                                                     alt="Brand Cover" 
                                                     class="img-fluid" 
                                                     style="max-height: 200px; object-fit: contain;">
                                            @else
                                                <p class="text-muted mb-0">{{ trans('common.no_image') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('common.basic_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.id') }}</label>
                                        <div class="userDatatable-content">
                                            <span class="badge badge-primary" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                {{ $brand->id }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('brands::brand.activation') }}</label>
                                        <div class="userDatatable-content">
                                            @if($brand->active)
                                                <span class="badge badge-success" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-check me-1"></i>{{ trans('brands::brand.active') }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-times me-1"></i>{{ trans('brands::brand.inactive') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('brands::brand.social_media') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('brands::brand.facebook_url') }}
                                        </label>
                                        <div class="userDatatable-content">
                                            @if($brand->facebook_url)
                                                <a href="{{ $brand->facebook_url }}" target="_blank" class="text-decoration-none">
                                                    {{ Str::limit($brand->facebook_url, 40) }}
                                                    <i class="uil uil-external-link-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('brands::brand.twitter_url') }}
                                        </label>
                                        <div class="userDatatable-content">
                                            @if($brand->twitter_url)
                                                <a href="{{ $brand->twitter_url }}" target="_blank" class="text-decoration-none">
                                                    {{ Str::limit($brand->twitter_url, 40) }}
                                                    <i class="uil uil-external-link-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('brands::brand.instagram_url') }}
                                        </label>
                                        <div class="userDatatable-content">
                                            @if($brand->instagram_url)
                                                <a href="{{ $brand->instagram_url }}" target="_blank" class="text-decoration-none">
                                                    {{ Str::limit($brand->instagram_url, 40) }}
                                                    <i class="uil uil-external-link-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('brands::brand.linkedin_url') }}
                                        </label>
                                        <div class="userDatatable-content">
                                            @if($brand->linkedin_url)
                                                <a href="{{ $brand->linkedin_url }}" target="_blank" class="text-decoration-none">
                                                    {{ Str::limit($brand->linkedin_url, 40) }}
                                                    <i class="uil uil-external-link-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">
                                            {{ trans('brands::brand.pinterest_url') }}
                                        </label>
                                        <div class="userDatatable-content">
                                            @if($brand->pinterest_url)
                                                <a href="{{ $brand->pinterest_url }}" target="_blank" class="text-decoration-none">
                                                    {{ Str::limit($brand->pinterest_url, 40) }}
                                                    <i class="uil uil-external-link-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Timestamps Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('common.created_at') }} & {{ trans('common.updated_at') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-calendar-alt me-2"></i>
                                            <strong>{{ $brand->created_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-clock me-2"></i>
                                            <strong>{{ $brand->updated_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('main.cancel') }}</button>
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
                    alert(xhr.responseJSON?.message || '{{ __('common.error_occurred') }}');
                }
            });
        });
    });
</script>
@endpush
