@extends('layout.app')

@section('title', trans('admin.view_vendor_user'))

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
                        'title' => __('admin.vendor_users_management'),
                        'url' => route('admin.vendor-users-management.vendor-users.index'),
                    ],
                    ['title' => __('admin.view_vendor_user')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('admin.vendor_user_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.vendor-users-management.vendor-users.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('admin.back_to_list') }}
                            </a>
                            @can('vendor-users.edit')
                                <a href="{{ route('admin.vendor-users-management.vendor-users.edit', $user->id) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ __('admin.edit_user') }}
                                </a>
                            @endcan
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
                                            <x-translation-display :label="__('admin.name')" :model="$user" fieldName="name"
                                                :languages="$languages" />

                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('admin.email') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <i
                                                            class="uil uil-envelope me-1 text-primary"></i>{{ $user->email }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.vendor') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <i class="uil uil-store me-1 text-primary"></i>
                                                        {{ $user->vendorById ? $user->vendorById->getTranslation('name', app()->getLocale()) : '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.roles') }}</label>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @forelse ($user->roles as $role)
                                                            <span class="badge badge-round badge-info badge-lg">
                                                                <i class="uil uil-shield-check me-1"></i>
                                                                {{ $role->getTranslation('name', app()->getLocale()) }}
                                                            </span>
                                                        @empty
                                                            <span class="text-muted">-</span>
                                                        @endforelse
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
                                                <div class="p-3 border rounded @if ($user->active) border-success @else border-danger @endif"
                                                    style="background: {{ $user->active ? '#f6fff9' : '#fff5f5' }};">
                                                    <small class="text-muted d-block mb-1">{{ __('admin.active') }}</small>
                                                    <div class="fw-bold {{ $user->active ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil {{ $user->active ? 'uil-check-circle' : 'uil-times-circle' }} me-1"></i>
                                                        {{ $user->active ? __('admin.active') : __('admin.inactive') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 border rounded @if (!$user->block) border-success @else border-danger @endif"
                                                    style="background: {{ !$user->block ? '#f6fff9' : '#fff5f5' }};">
                                                    <small class="text-muted d-block mb-1">{{ __('admin.block') }}</small>
                                                    <div class="fw-bold {{ !$user->block ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 16px;">
                                                        <i
                                                            class="uil {{ !$user->block ? 'uil-unlock' : 'uil-lock' }} me-1"></i>
                                                        {{ $user->block ? __('admin.blocked') : __('admin.not_blocked') }}
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
                                                    <p class="fs-15 color-dark">{{ $user->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $user->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Vendor User Image --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder border-0 shadow-sm overflow-hidden"
                                    style="border-radius: 15px;">
                                    <div class="card-header card-header-gradient">
                                        <h3 class="mb-0">
                                            <i class="uil uil-image me-2"></i>{{ __('admin.vendor_user_image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body p-4">
                                        {{-- Main Image Display --}}
                                        @if ($user->image)
                                            <div class="mb-4">
                                                <div class="image-wrapper text-center radius-xl p-2">
                                                    <img src="{{ asset('storage/' . $user->image) }}"
                                                        alt="{{ $user->getTranslation('name', app()->getLocale()) }}"
                                                        class="img-fluid rounded shadow-sm"
                                                        style="max-height: 300px; width: 100%; cursor: pointer;"
                                                        onclick="openUserImageModal()">
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
    @if ($user->image)
        <div class="modal fade" id="userImageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: fit-content;">
                <div class="modal-content border-0 bg-transparent shadow-none">
                    <div class="modal-body p-0 text-center">
                        <img src="{{ asset('storage/' . $user->image) }}" class="img-fluid rounded shadow-lg"
                            style="max-height: 90vh; width: auto;">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function openUserImageModal() {
            const modal = new bootstrap.Modal(document.getElementById('userImageModal'));
            modal.show();
        }
    </script>
@endpush
