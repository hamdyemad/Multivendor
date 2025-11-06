@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::tax.taxes_management'), 'url' => route('admin.taxes.index')],
                    ['title' => trans('catalogmanagement::tax.view_tax')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::tax.tax_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.taxes.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back') }}
                            </a>
                            <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="btn btn-primary btn-sm">
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
                            @foreach($languages as $language)
                                @php
                                    $name = $tax->translations->where('lang_id', $language->id)
                                        ->where('lang_key', 'name')
                                        ->first();
                                @endphp
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            @if($language->code == 'ar')
                                                اسم الضريبة بالعربية
                                            @elseif($language->code == 'en')
                                                {{ trans('catalogmanagement::tax.name') }} (English)
                                            @else
                                                {{ trans('catalogmanagement::tax.name') }} ({{ $language->name }})
                                            @endif
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $name ? $name->lang_value : '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::tax.tax_rate') }}</label>
                                    <p class="fs-15 color-dark">
                                        <span class="badge badge-info badge-round badge-lg">{{ number_format($tax->tax_rate, 2) }}%</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-4 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::tax.activation') }}</label>
                                    <p class="fs-15">
                                        @if($tax->active)
                                            <span class="badge bg-success badge-round badge-lg">
                                                <i class="uil uil-check me-1"></i>{{ trans('catalogmanagement::tax.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger badge-round badge-lg">
                                                <i class="uil uil-times me-1"></i>{{ trans('catalogmanagement::tax.inactive') }}
                                            </span>
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
                                    <p class="fs-15 color-dark">{{ $tax->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $tax->updated_at->format('Y-m-d H:i:s') }}</p>
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
                    <p class="fw-500">{{ $tax->translations->where('lang_key', 'name')->first()->lang_value ?? '' }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('main.cancel') }}</button>
                    <form action="{{ route('admin.taxes.destroy', $tax->id) }}" method="POST" id="deleteForm">
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
@endpush
