@extends('layout.app')
@section('title', trans('roles.view_role'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('menu.admin managment.roles managment'), 'url' => route('admin.admin-management.roles.index')],
                    ['title' => trans('roles.view_role')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
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
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('roles.role_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Dynamic Language Translations for Role Name --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                الاسم بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('roles.name') }}
                                                            @else
                                                                {{ trans('roles.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $role->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Permissions Count --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('roles.permissions') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">
                                                            <i class="uil uil-shield-check"></i> {{ $role->permessions->count() }} {{ trans('roles.permissions') }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Permissions Card --}}
                                @if($role->permessions->count() > 0)
                                    @php
                                        $groupedPermissions = $role->permessions->groupBy(function($permission) {
                                            return $permission->getTranslation('group_by', app()->getLocale()) ?? 'Other';
                                        });
                                    @endphp

                                    @foreach($groupedPermissions as $groupName => $permissions)
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h3>
                                                    <i class="uil uil-shield-check me-1"></i>{{ $groupName }}
                                                    <span class="badge badge-primary badge-round badge-lg ms-2 text-white">{{ $permissions->count() }}</span>
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-4 col-lg-3 mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="uil uil-check-circle text-success me-2"></i>
                                                                <span class="fs-14">{{ $permission->getTranslation('name', app()->getLocale()) ?? $permission->key }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                                <i class="uil uil-exclamation-triangle me-2 fs-20"></i>
                                                <div>{{ trans('roles.no_permissions_assigned') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ trans('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $role->created_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $role->updated_at->format('d M, Y h:i A') }}</p>
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
        </div>
    </div>
@endsection
