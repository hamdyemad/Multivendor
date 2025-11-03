@extends('layout.app')

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
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('admin.admin_details') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.admin-management.admins.edit', $admin->id) }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-edit"></i> {{ __('admin.edit_admin') }}
                            </a>
                            <a href="{{ route('admin.admin-management.admins.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-arrow-left"></i> {{ __('admin.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Basic Information Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('admin.basic_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.email') }}</label>
                                        <div class="userDatatable-content text-lowercase">
                                            <i class="uil uil-envelope me-2"></i>
                                            <strong>{{ $admin->email }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.role') }}</label>
                                        <div class="userDatatable-content">
                                            @if($admin->roles->isNotEmpty())
                                                <span class="badge badge-info" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-shield-check me-1"></i>
                                                    {{ $admin->roles->first()->getTranslation('name', app()->getLocale()) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Translations Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('admin.translations') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-20">
                                            <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.name') }} ({{ $language->name }})</label>
                                            <div class="userDatatable-content" @if($language->rtl) dir="rtl" @endif>
                                                <strong>{{ $admin->translations->where('lang_id', $language->id)->where('lang_key', 'name')->first()->lang_value ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Status & Dates Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('admin.status') }} & {{ __('admin.dates') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.active') }}</label>
                                        <div class="userDatatable-content">
                                            @if($admin->active)
                                                <span class="badge badge-success" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-check me-1"></i>{{ __('admin.active') }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger" style="border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                                                    <i class="uil uil-times me-1"></i>{{ __('admin.inactive') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.created_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-calendar-alt me-2"></i>
                                            <strong>{{ $admin->created_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($admin->updated_at)
                    <!-- Updated At Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('admin.updated_at') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('admin.updated_at') }}</label>
                                        <div class="userDatatable-content">
                                            <i class="uil uil-clock me-2"></i>
                                            <strong>{{ $admin->updated_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Permissions Card -->
                    @if($admin->roles->isNotEmpty() && $admin->roles->first()->permessions->isNotEmpty())
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ __('admin.permissions') }} ({{ $admin->roles->first()->permessions->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($admin->roles->first()->permessions->groupBy('group_by') as $group => $permissions)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-primary mb-2">{{ $permissions->first()->getTranslation('group_by', app()->getLocale()) }}</h6>
                                        <ul class="list-unstyled ps-3">
                                            @foreach($permissions as $permission)
                                                <li class="mb-1">
                                                    <i class="uil uil-check-circle text-success me-1"></i>
                                                    {{ $permission->getTranslation('name', app()->getLocale()) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
