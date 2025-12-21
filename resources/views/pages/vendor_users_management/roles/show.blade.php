@extends('layout.app')
@section('title', trans('roles.view_role'))

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
                        'title' => trans('menu.admin managment.roles managment'),
                        'url' => route('admin.admin-management.roles.index'),
                    ],
                    ['title' => trans('roles.view_role')],
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
                            @if (!$role->is_system_protected && $role->is_system_protected != 1)
                                <a href="{{ route('admin.admin-management.roles.edit', $role->id) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i>{{ trans('roles.edit_role') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('roles.role_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Dynamic Language Translations for Role Name --}}
                                            @foreach ($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10"
                                                            @if ($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if ($language->code == 'ar')
                                                                الاسم بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('roles.name') }}
                                                            @else
                                                                {{ trans('roles.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500"
                                                            @if ($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $role->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Permissions Count --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('roles.permissions') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">
                                                            <i class="uil uil-shield-check"></i>
                                                            {{ $role->permessions->count() }}
                                                            {{ trans('roles.permissions') }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Permissions Card --}}
                                @if ($role->permessions->count() > 0)
                                    @php
                                        // Group by module instead of non-existent group_by translation
                                        $groupedPermissions = $role->permessions->groupBy('module');
                                        $locale = app()->getLocale();
                                    @endphp

                                    @foreach ($groupedPermissions as $groupName => $permissions)
                                        <div class="card card-holder mt-3">
                                            <div class="card-header">
                                                <h3>
                                                    {{-- Display Module Name (Try config or fallback to key) --}}
                                                    <i class="uil uil-shield-check me-1"></i>
                                                    {{ config("permissions.{$groupName}.name.{$locale}") ?? ucfirst($groupName) }}
                                                    <span
                                                        class="badge badge-primary fw-bold badge-round badge-lg ms-2 text-white">{{ $permissions->count() }}</span>
                                                </h3>
                                            </div>
                                            <div class="card-body rounded-bottom">
                                                <div class="row g-3">
                                                    @foreach ($permissions as $permission)
                                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                                            <div
                                                                class="d-flex align-items-center p-3 bg-white rounded shadow-sm h-100 position-relative overflow-hidden">
                                                                <div class="position-absolute top-0 start-0 bottom-0 {{ $permission->color ?? 'bg-primary' }}"
                                                                    style="width: 4px;"></div>
                                                                <div class="flex-shrink-0">
                                                                    <span
                                                                        class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $permission->color ?? 'bg-primary' }} text-white"
                                                                        style="width: 32px; height: 32px;">
                                                                        <i class="uil uil-check fs-16"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="mb-0 fs-14 fw-600 text-dark">
                                                                        {{ $permission->{'name_' . $locale} ?? $permission->key }}
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="card card-holder mt-3">
                                        <div class="card-body">
                                            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                                <i class="uil uil-exclamation-triangle me-2 fs-20"></i>
                                                <div>{{ trans('roles.no_permissions_assigned') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ trans('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $role->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $role->updated_at }}</p>
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
