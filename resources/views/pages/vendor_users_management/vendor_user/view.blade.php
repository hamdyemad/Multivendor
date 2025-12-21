@extends('layout.app')
@section('title', trans('admin.vendor_user_details'))

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
                        'url' => route('admin.admin-management.vendor-users.index'),
                    ],
                    ['title' => __('admin.vendor_user_details')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('admin.vendor_user_details') }}</h5>
                        <div class="d-flex gap-2">
                            @can('vendor-users.edit')
                                <a href="{{ route('admin.admin-management.vendor-users.edit', $user->id) }}"
                                    class="btn btn-warning btn-default btn-squared">
                                    <i class="uil uil-edit"></i> {{ __('admin.edit_user') }}
                                </a>
                            @endcan
                            <a href="{{ route('admin.admin-management.vendor-users.index') }}"
                                class="btn btn-light btn-default btn-squared">
                                <i class="uil uil-arrow-left"></i> {{ __('admin.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-20">
                                <label class="fw-500 text-dark mb-1 d-block">{{ __('admin.name') }}</label>
                                <div class="bg-light p-10 radius-xs border">
                                    @foreach ($languages as $language)
                                        <div class="mb-1">
                                            <span class="badge badge-secondary me-1">{{ $language->name }}</span>
                                            {{ $user->getTranslation('name', $language->code) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="fw-500 text-dark mb-1 d-block">{{ __('admin.email') }}</label>
                                <div class="bg-light p-10 radius-xs border">
                                    {{ $user->email }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="fw-500 text-dark mb-1 d-block">{{ __('admin.vendor') }}</label>
                                <div class="bg-light p-10 radius-xs border">
                                    {{ $user->vendorById ? $user->vendorById->getTranslation('name', app()->getLocale()) : '-' }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="fw-500 text-dark mb-1 d-block">{{ __('admin.roles') }}</label>
                                <div class="bg-light p-10 radius-xs border">
                                    @foreach ($user->roles as $role)
                                        <span
                                            class="badge badge-primary me-1">{{ $role->getTranslation('name', app()->getLocale()) }}</span>
                                    @endforeach
                                    @if ($user->roles->isEmpty())
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="fw-500 text-dark mb-1 d-block">{{ __('admin.status') }}</label>
                                <div class="bg-light p-10 radius-xs border">
                                    @if ($user->active)
                                        <span class="badge badge-success">{{ __('admin.active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('admin.inactive') }}</span>
                                    @endif

                                    @if ($user->block)
                                        <span class="badge badge-danger ms-2">{{ __('admin.blocked') }}</span>
                                    @else
                                        <span class="badge badge-success ms-2">{{ __('admin.not_blocked') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-20">
                                <label class="fw-500 text-dark mb-1 d-block">{{ __('admin.created_at') }}</label>
                                <div class="bg-light p-10 radius-xs border">
                                    {{ $user->created_at->format('Y-m-d H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
