@extends('layout.app')

@section('title', trans('admin.view_admin'))

@push('styles')
    <style>
        .fs-15.color-dark {
            line-height: 1.6;
        }

        .fs-15.color-dark strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .fs-15.color-dark p {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .card-holder .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Gradient backgrounds for card headers to match screenshot */
        .card-header-gradient {
            background: linear-gradient(90deg, #1e40af 0%, #db2777 100%) !important;
            color: white !important;
            border-radius: 10px 10px 0 0 !important;
        }

        .card-header-gradient h3,
        .card-header-gradient h5 {
            color: white !important;
        }

        .box-items-translations {
            padding: 15px;
            border: 1px solid #f1f2f6;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
@endpush

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
                        'title' => __('admin.admins_management'),
                        'url' => route('admin.admin-management.admins.index'),
                    ],
                    ['title' => __('admin.view_admin')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('admin.admin_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.admin-management.admins.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('admin.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.admin-management.admins.edit', $admin->id) }}"
                                class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('admin.edit_admin') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                {{-- Basic Information --}}
                                <div class="card card-holder overflow-hidden border-0 shadow-sm">
                                    <div class="card-header card-header-gradient">
                                        <h3 class="mb-0">
                                            <i class="uil uil-info-circle me-1"></i>{{ __('admin.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Use the translation display component for name --}}
                                            <x-translation-display :label="__('admin.name')" :model="$admin" fieldName="name"
                                                :languages="$languages" />

                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('admin.email') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <i
                                                            class="uil uil-envelope me-1 text-primary"></i>{{ $admin->email }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.role') }}</label>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($admin->roles as $role)
                                                            <span class="badge badge-round badge-info badge-lg">
                                                                <i class="uil uil-shield-check me-1"></i>
                                                                {{ $role->getTranslation('name', app()->getLocale()) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Account Status --}}
                                <div class="card card-holder mt-3 overflow-hidden border-0 shadow-sm">
                                    <div class="card-header card-header-gradient">
                                        <h3 class="mb-0">
                                            <i class="uil uil-user-check me-1"></i>{{ __('admin.status') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 border rounded @if ($admin->active) border-success @else border-danger @endif"
                                                    style="background: {{ $admin->active ? '#f6fff9' : '#fff5f5' }};">
                                                    <small class="text-muted d-block mb-1">{{ __('admin.active') }}</small>
                                                    <div class="fw-bold {{ $admin->active ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil {{ $admin->active ? 'uil-check-circle' : 'uil-times-circle' }} me-1"></i>
                                                        {{ $admin->active ? __('admin.active') : __('admin.inactive') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 border rounded @if (!$admin->block) border-success @else border-danger @endif"
                                                    style="background: {{ !$admin->block ? '#f6fff9' : '#fff5f5' }};">
                                                    <small class="text-muted d-block mb-1">{{ __('admin.block') }}</small>
                                                    <div class="fw-bold {{ !$admin->block ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil {{ !$admin->block ? 'uil-unlock' : 'uil-lock' }} me-1"></i>
                                                        {{ $admin->block ? __('admin.blocked') : __('admin.not_blocked') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Timestamps --}}
                                <div class="card card-holder mt-3 overflow-hidden border-0 shadow-sm">
                                    <div class="card-header card-header-gradient">
                                        <h3 class="mb-0">
                                            <i
                                                class="uil uil-clock me-1"></i>{{ trans('common.timestamps') ?? 'Timestamps' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $admin->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $admin->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Admin Image (Matching Product View Style) --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder border-0 shadow-sm overflow-hidden"
                                    style="border-radius: 15px;">
                                    <div class="card-header card-header-gradient">
                                        <h3 class="mb-0">
                                            <i class="uil uil-image me-2"></i>{{ __('admin.admin_image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body p-4">
                                        {{-- Main Image Display --}}
                                        @if ($admin->image)
                                            <div class="mb-4">
                                                <div class="image-wrapper text-center radius-xl p-2">
                                                    <img src="{{ asset('storage/' . $admin->image) }}"
                                                        alt="{{ $admin->getTranslation('name', app()->getLocale()) }}"
                                                        class="img-fluid rounded shadow-sm"
                                                        style="max-height: 300px; width: 100%; cursor: pointer;"
                                                        onclick="openAdminImageModal()">
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-4 text-center">
                                                <div class="rounded d-flex align-items-center justify-content-center mx-auto"
                                                    style="height: 200px; width: 100%;">
                                                    <i class="uil uil-user text-muted" style="font-size: 80px;"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    @if ($admin->image)
        <div class="modal fade" id="adminImageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: fit-content;">
                <div class="modal-content border-0 bg-transparent shadow-none">
                    <div class="modal-body p-0 text-center">
                        <img src="{{ asset('storage/' . $admin->image) }}" class="img-fluid rounded shadow-lg"
                            style="max-height: 90vh; width: auto;">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function openAdminImageModal() {
            const modal = new bootstrap.Modal(document.getElementById('adminImageModal'));
            modal.show();
        }
    </script>
@endpush
