@extends('layout.app')

@section('title', __('systemsetting::activity_log.view_activity_log'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('systemsetting::activity_log.activity_logs_management'), 'url' => route('admin.system-settings.activity-logs.index')],
                    ['title' => __('systemsetting::activity_log.view_activity_log')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::activity_log.activity_log_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.system-settings.activity-logs.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::activity_log.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::activity_log.user') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if($activity_log->user)
                                                            {{ $activity_log->user->email }}
                                                            <br>
                                                            <small class="text-muted">{{ $activity_log->user->name ?? 'N/A' }}</small>
                                                        @else
                                                            <span class="badge badge-secondary">{{ __('System') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::activity_log.action') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @php
                                                            $actionColors = [
                                                                'created' => 'success',
                                                                'updated' => 'warning',
                                                                'deleted' => 'danger',
                                                                'viewed' => 'info'
                                                            ];
                                                            $actionColor = $actionColors[$activity_log->action] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge badge-{{ $actionColor }} badge-round badge-lg">
                                                            {{ __("activity_log.actions.{$activity_log->action}") }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::activity_log.model') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($activity_log->model)
                                                            <code>{{ class_basename($activity_log->model) }}</code>
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::activity_log.description') }}</label>
                                                    <p class="fs-15 color-dark">{{ $activity_log->translated_description ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-shield-check me-1"></i>{{ __('systemsetting::activity_log.technical_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::activity_log.ip_address') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <code>{{ $activity_log->ip_address ?? '-' }}</code>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::activity_log.user_agent') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <small>{{ $activity_log->user_agent ?? '-' }}</small>
                                                    </p>
                                                </div>
                                            </div>
                                            @if($activity_log->properties)
                                                <div class="col-md-12">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::activity_log.properties') }}</label>
                                                        <div class="fs-15 color-dark">
                                                            <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow-y: auto;">{{ json_encode($activity_log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $activity_log->created_at }}</p>
                                                    <small class="text-muted">{{ $activity_log->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $activity_log->updated_at }}</p>
                                                    <small class="text-muted">{{ $activity_log->updated_at->diffForHumans() }}</small>
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
