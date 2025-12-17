@extends('layout.app')

@section('title', __('systemsetting::messages.view_message'))

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
                    ['title' => __('systemsetting::messages.messages'), 'url' => route('admin.messages.index')],
                    ['title' => __('systemsetting::messages.view_message')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('systemsetting::messages.message_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.messages.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('systemsetting::messages.back_to_list') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Message Title --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::messages.title') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $message->title }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Message Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::messages.status') }}</label>
                                                    <p class="fs-15">
                                                        @php
                                                            $statusColors = [
                                                                'pending' => 'warning',
                                                                'read' => 'success',
                                                                'archived' => 'secondary',
                                                            ];
                                                            $color = $statusColors[$message->status] ?? 'secondary';
                                                        @endphp
                                                        <span
                                                            class="badge badge-{{ $color }} badge-round badge-lg text-capitalize">{{ $message->status }}</span>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Sender Name --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::messages.sender_name') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $message->name }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Sender Email --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::messages.sender_email') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-message me-1"></i>{{ __('systemsetting::messages.message_content') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <div class="message-content p-4 rounded"
                                                        style="min-height: 200px; line-height: 1.8; border-left: 4px solid #0056B7;">
                                                        {!! nl2br(e($message->content)) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $message->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $message->updated_at }}</p>
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
