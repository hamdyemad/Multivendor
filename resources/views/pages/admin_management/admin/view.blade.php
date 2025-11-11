@extends('layout.app')
@section('title', trans('admin.view_admin'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('admin.admins_management'), 'url' => route('admin.admin-management.admins.index')],
                    ['title' => __('admin.view_admin')]
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
                            <a href="{{ route('admin.admin-management.admins.edit', $admin->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('admin.edit_admin') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('admin.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Email --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.email') }}</label>
                                                    <p class="fs-15 color-dark fw-500 text-lowercase">
                                                        <i class="uil uil-envelope me-2"></i>{{ $admin->email }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            {{-- Role --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.role') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($admin->roles->isNotEmpty())
                                                            <span class="badge badge-info badge-round badge-lg">
                                                                <i class="uil uil-shield-check"></i> {{ $admin->roles->first()->getTranslation('name', app()->getLocale()) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.status') }}</label>
                                                    <p class="fs-15">
                                                        @if($admin->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('admin.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('admin.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Translations Card --}}
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-language me-1"></i>{{ __('admin.translations') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                الاسم بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ __('admin.name') }}
                                                            @else
                                                                {{ __('admin.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $admin->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Permissions Card --}}
                                @if($admin->roles->isNotEmpty() && $admin->roles->first()->permessions->isNotEmpty())
                                    @php
                                        $groupedPermissions = $admin->roles->first()->permessions->groupBy(function($permission) {
                                            return $permission->getTranslation('group_by', app()->getLocale()) ?? 'Other';
                                        });
                                    @endphp

                                    @foreach($groupedPermissions as $groupName => $permissions)
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h3>
                                                    <i class="uil uil-shield-check me-1"></i>{{ $groupName }}
                                                    <span class="badge badge-primary badge-round badge-lg ms-2">{{ $permissions->count() }}</span>
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-4 col-lg-3 mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="uil uil-check-circle text-success me-2"></i>
                                                                <span class="fs-14">{{ $permission->getTranslation('name', app()->getLocale()) }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
                                                    <p class="fs-15 color-dark">{{ $admin->created_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $admin->updated_at->format('d M, Y h:i A') }}</p>
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
