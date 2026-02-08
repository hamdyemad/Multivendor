@extends('layout.app')

@section('title', __('order::request-quotation.vendors_and_offers'))

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
                        'title' => trans('menu.vendors.request_quotations.title'),
                        'url' => route('admin.request-quotations.index'),
                    ],
                    ['title' => __('order::request-quotation.vendors_and_offers')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-600">
                            <i class="uil uil-file-question-alt me-2"></i>
                            {{ __('order::request-quotation.quotation_details') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('order::request-quotation.quotation_number') }}:</strong> {{ $quotation->quotation_number }}</p>
                                <p class="mb-2"><strong>{{ __('order::request-quotation.customer_name') }}:</strong> {{ $quotation->customer_name }}</p>
                                <p class="mb-2"><strong>{{ __('common.email') }}:</strong> {{ $quotation->customer_email }}</p>
                                <p class="mb-0"><strong>{{ __('common.phone') }}:</strong> {{ $quotation->customer_phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('common.address') }}:</strong> {{ $quotation->full_address }}</p>
                                @if($quotation->notes)
                                    <p class="mb-0"><strong>{{ __('common.notes') }}:</strong> {{ $quotation->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-600">
                            <i class="uil uil-users-alt me-2"></i>
                            {{ __('order::request-quotation.vendors_and_offers') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($quotation->vendors->isEmpty())
                            <div class="alert alert-info">
                                <i class="uil uil-info-circle"></i>
                                {{ __('order::request-quotation.no_vendors_assigned') }}
                            </div>
                        @else
                            <div class="row">
                                @foreach($quotation->vendors as $quotationVendor)
                                    @php
                                        $vendor = $quotationVendor->vendor;
                                        if (!$vendor) continue;
                                    @endphp
                                    
                                    <div class="col-md-6 mb-4">
                                        <div class="card border h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    @if($vendor->logo)
                                                        <img src="{{ asset('storage/' . $vendor->logo->path) }}" 
                                                             alt="{{ $vendor->name }}" 
                                                             style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                                                    @else
                                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                            <i class="uil uil-store" style="font-size: 24px; color: #999;"></i>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fw-600">{{ $vendor->name }}</h6>
                                                        <span class="badge badge-{{ $quotationVendor->status_badge_color }} badge-round">
                                                            {{ $quotationVendor->status_label }}
                                                        </span>
                                                    </div>
                                                </div>

                                                @if($quotationVendor->offer_price)
                                                    <hr class="my-3">
                                                    <div class="mb-2">
                                                        <strong>{{ __('order::request-quotation.offer_price') }}:</strong>
                                                        <span class="text-success fw-bold fs-18">
                                                            {{ number_format($quotationVendor->offer_price, 2) }} {{ currency() }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if($quotationVendor->offer_notes)
                                                        <div class="mb-2">
                                                            <strong>{{ __('order::request-quotation.offer_notes') }}:</strong>
                                                            <p class="mb-0 text-muted">{{ $quotationVendor->offer_notes }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="mb-2">
                                                        <strong>{{ __('order::request-quotation.offer_sent_at') }}:</strong>
                                                        <span class="text-muted">
                                                            {{ $quotationVendor->offer_sent_at ? $quotationVendor->offer_sent_at->format('Y-m-d H:i') : '-' }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="uil uil-clock"></i>
                                                        {{ __('order::request-quotation.waiting_for_offers') }}
                                                    </div>
                                                @endif

                                                @if($quotationVendor->order)
                                                    <hr class="my-3">
                                                    <div class="alert alert-success mb-0">
                                                        <i class="uil uil-check-circle"></i>
                                                        <strong>{{ __('order::request-quotation.order_created') }}:</strong>
                                                        <a href="{{ route('admin.orders.show', ['order' => $quotationVendor->order_id]) }}" 
                                                           class="text-primary fw-500">
                                                            {{ $quotationVendor->order->order_number }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('admin.request-quotations.index') }}" class="btn btn-light">
                                <i class="uil uil-arrow-left"></i> {{ __('common.back') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .fs-18 {
            font-size: 18px;
        }
    </style>
@endpush
