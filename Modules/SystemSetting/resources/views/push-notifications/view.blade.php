@extends('layout.app')

@section('title', __('systemsetting::push-notification.view_notification'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('systemsetting::push-notification.view_notification')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">
                            <i class="uil uil-bell me-2"></i>{{ __('systemsetting::push-notification.notification') }}
                        </h5>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="uil uil-arrow-left me-2"></i>{{ __('common.back') }}
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- Notification Image --}}
                        @if($notification->image)
                            <div class="text-center mb-4">
                                <img src="{{ formatImage($notification->image) }}"
                                    alt="{{ __('systemsetting::push-notification.image') }}"
                                    class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        @endif

                        {{-- Title --}}
                        <h4 class="mb-3">
                            {{ $notification->getTranslation('title', app()->getLocale()) ?? $notification->getTranslation('title', 'en') }}
                        </h4>

                        {{-- Description --}}
                        <div class="notification-content mb-4">
                            {!! $notification->getTranslation('description', app()->getLocale()) ?? $notification->getTranslation('description', 'en') !!}
                        </div>

                        {{-- Timestamp --}}
                        <div class="text-muted small">
                            <i class="uil uil-clock me-1"></i>
                            {{ $notification->created_at }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
