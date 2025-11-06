@extends('layout.app')

@section('title', trans('roles.view_role'))
@section('content')
    {{-- Include Loading Overlay Component --}}
    <x-loading-overlay
        :loadingText="trans('loading.deleting')"
        :loadingSubtext="trans('loading.please_wait')"
    />

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                            class="uil uil-estate"></i>{{ trans('dashboard.title') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.roles.index') }}">{{ trans('menu.admin managment.roles managment') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ trans('roles.view_role') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('roles.role_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.admin-management.roles.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('roles.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.admin-management.roles.edit', $role->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('roles.edit_role') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Role Information --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-info-circle"></i>{{ trans('roles.role_information') }}
                                </h6>
                            </div>
                            @foreach($languages as $language)
                                <div class="col-md-6 mt-3">
                                    <div class="view-item">
                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                            {{ $language->name }}
                                        </label>
                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                            {{ $role->getTranslation('name', $language->code) ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('roles.created_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $role->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="view-item">
                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('roles.updated_at') }}</label>
                                    <p class="fs-15 color-dark">{{ $role->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>


                            {{-- Permissions --}}
                            <div class="col-12">
                                <h6 class="fw-500" style="background: #0056B7; color: white; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                    <i class="uil uil-shield-check"></i>{{ trans('roles.assigned_permissions') }}
                                    <span class="badge badge-success text-white ms-2">{{ $role->permessions->count() }}</span>
                                </h6>
                            </div>

                            @if($role->permessions->count() > 0)
                                @php
                                    $groupedPermissions = $role->permessions->groupBy(function($permission) {
                                        return $permission->getTranslation('group_by', app()->getLocale()) ?? 'Other';
                                    });
                                @endphp

                                @foreach($groupedPermissions as $groupName => $permissions)
                                    <div class="col-12 mt-3">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-normal py-15 px-20 border-bottom">
                                                <h6 class="mb-0 fw-500">
                                                    {{ $groupName }}
                                                    <span class="badge badge-primary badge-sm ms-2 text-white">{{ $permissions->count() }}</span>
                                                </h6>
                                            </div>
                                            <div class="card-body p-20">
                                                <div class="row">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-4 col-lg-3 mb-15">
                                                            <div class="d-flex align-items-center">
                                                                <i class="uil uil-check-circle text-success me-2"></i>
                                                                <span class="fs-14">{{ $permission->getTranslation('name', app()->getLocale()) ?? $permission->key }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                                        <i class="uil uil-exclamation-triangle me-2 fs-20"></i>
                                        <div>{{ __('No permissions assigned to this role') }}</div>
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

    {{-- Delete Confirmation Modal --}}
    <x-delete-modal
        modalId="modal-delete-role"
        :title="trans('roles.confirm_delete')"
        :message="trans('roles.delete_warning')"
        itemNameId="delete-role-name"
        confirmBtnId="confirmDeleteBtn"
        :deleteRoute="route('admin.admin-management.roles.index')"
        :cancelText="trans('roles.cancel')"
        :deleteText="trans('roles.delete_role')"
    />
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
