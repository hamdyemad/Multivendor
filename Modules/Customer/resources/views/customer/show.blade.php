@extends('layout.app')

@section('title', __('customer::customer.view_customer'))

@section('content')
    <div class="container-fluid mb-2">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('customer::customer.customers_management'), 'url' => route('admin.customers.index')],
                    ['title' => __('customer::customer.view_customer')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('customer::customer.customer_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('customer::customer.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('customer::customer.edit_customer') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information --}}
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ __('customer::customer.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Full Name --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.full_name') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $customer->full_name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- First Name --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.first_name') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->first_name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Last Name --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.last_name') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->last_name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>


                                            {{-- Phone --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.phone') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->phone ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Account Information --}}
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-lock me-1"></i>{{ __('customer::customer.account_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Email --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.email') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->email ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.status') }}</label>
                                                    <p class="fs-15">
                                                        @if($customer->status)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('customer::customer.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ __('customer::customer.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Email Verified --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.email_verified') }}</label>
                                                    <p class="fs-15">
                                                        @if($customer->email_verified_at)
                                                            <span class="badge badge-success badge-round badge-lg">{{ __('customer::customer.verified') }}</span>
                                                        @else
                                                            <span class="badge badge-warning badge-round badge-lg">{{ __('customer::customer.pending') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Email Verified At --}}
                                            @if($customer->email_verified_at)
                                            <div class="col-md-12">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('Email Verified At') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ \Carbon\Carbon::parse($customer->email_verified_at) }}
                                                    </p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Timestamps Section --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
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
                                                    <p class="fs-15 color-dark">{{ $customer->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $customer->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Information --}}
                        @if($customer->addresses && $customer->addresses->count() > 0)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card card-holder">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-map-marker me-1"></i>{{ __('Addresses') }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($customer->addresses as $address)
                                                <div class="col-md-6 mb-3">
                                                    <div class="border rounded p-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="mb-0">{{ $address->type ?? 'Address' }}</h6>
                                                            @if($address->is_primary)
                                                                <span class="badge badge-primary badge-sm">{{ __('Primary') }}</span>
                                                            @endif
                                                        </div>
                                                        <p class="mb-1 text-muted">{{ $address->address_line_1 }}</p>
                                                        @if($address->address_line_2)
                                                            <p class="mb-1 text-muted">{{ $address->address_line_2 }}</p>
                                                        @endif
                                                        <p class="mb-0 text-muted">
                                                            {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
