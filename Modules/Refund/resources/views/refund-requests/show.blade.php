@extends('layout.app')

@section('title', trans('refund::refund.titles.view_refund'))

@push('styles')
<style>
    .timeline-wrapper {
        position: relative;
        padding-left: 30px;
    }

    .timeline-wrapper::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
    }

    .marker-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #5f63f2;
    }
</style>
@endpush

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
                    'title' => trans('refund::refund.titles.refunds'),
                    'url' => route('admin.refunds.index'),
                ],
                ['title' => trans('refund::refund.titles.view_refund')],
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-500">{{ trans('refund::refund.titles.refund_details') }}</h5>
                    <div class="d-flex gap-10">
                        <a href="{{ route('admin.refunds.index') }}" class="btn btn-light btn-sm">
                            <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Main Details --}}
                        <div class="col-md-8">
                            {{-- Basic Information --}}
                            <div class="card card-holder">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-info-circle me-1"></i>{{ trans('refund::refund.titles.refund_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Refund Number --}}
                                        <div class="col-md-6">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.refund_number') }}</label>
                                                <p class="fs-15 color-dark fw-500">
                                                    <code>{{ $refundRequest->refund_number }}</code>
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Order Number --}}
                                        <div class="col-md-6">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.order_number') }}</label>
                                                <p class="fs-15 color-dark fw-500">
                                                    @if($refundRequest->order)
                                                        <a href="{{ route('admin.orders.show', $refundRequest->order_id) }}" class="text-primary">
                                                            {{ $refundRequest->order->order_number }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Customer --}}
                                        <div class="col-md-6">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.customer') }}</label>
                                                <p class="fs-15 color-dark">
                                                    @if($refundRequest->customer)
                                                        <span class="badge badge-round badge-primary badge-lg">
                                                            {{ $refundRequest->customer->full_name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Vendor --}}
                                        @if(isAdmin())
                                        <div class="col-md-6">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.vendor') }}</label>
                                                <p class="fs-15 color-dark">
                                                    @if($refundRequest->vendor)
                                                        <span class="badge badge-round badge-info badge-lg">
                                                            {{ $refundRequest->vendor->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Reason --}}
                                        @if($refundRequest->reason)
                                        <div class="col-md-12">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.reason') }}</label>
                                                <p class="fs-15 color-dark">
                                                    {{ $refundRequest->reason }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Return Shipping Responsibility --}}
                                        <div class="col-md-12">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.return_shipping_responsibility') }}</label>
                                                <p class="fs-15">
                                                    @if($refundRequest->shouldCustomerPayReturnShipping())
                                                        <span class="badge badge-lg badge-round badge-warning badge-lg">
                                                            <i class="uil uil-user me-1"></i>{{ trans('refund::refund.customer_pays_shipping') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-lg badge-round badge-success badge-lg">
                                                            <i class="uil uil-store me-1"></i>{{ trans('refund::refund.vendor_pays_shipping') }}
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Customer Notes --}}
                                        @if($refundRequest->customer_notes)
                                        <div class="col-md-12">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.customer_notes') }}</label>
                                                <p class="fs-15 color-dark">
                                                    {{ $refundRequest->customer_notes }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Vendor Notes --}}
                                        @if($refundRequest->vendor_notes)
                                        <div class="col-md-12">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.vendor_notes') }}</label>
                                                <p class="fs-15 color-dark">
                                                    {{ $refundRequest->vendor_notes }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Admin Notes --}}
                                        @if(isAdmin() && $refundRequest->admin_notes)
                                        <div class="col-md-12">
                                            <div class="view-item">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.admin_notes') }}</label>
                                                <p class="fs-15 color-dark">
                                                    {{ $refundRequest->admin_notes }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Financial Information --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-money-bill me-1"></i>{{ trans('refund::refund.titles.financial_details') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Total Products Amount --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #e7f3ff;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.total_products_amount') }}</small>
                                                <div class="fw-bold text-info" style="font-size: 18px;">
                                                    <i class="uil uil-box me-1"></i>{{ number_format($refundRequest->total_products_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Total Tax Amount --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #fff3cd;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.total_tax_amount') }}</small>
                                                <div class="fw-bold text-warning" style="font-size: 18px;">
                                                    <i class="uil uil-percentage me-1"></i>{{ number_format($refundRequest->total_tax_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Total Shipping Amount --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #f8f9fa;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.total_shipping_amount') }}</small>
                                                <div class="fw-bold text-dark" style="font-size: 18px;">
                                                    <i class="uil uil-truck me-1"></i>{{ number_format($refundRequest->total_shipping_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Total Discount Amount --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #d4edda;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.total_discount_amount') }}</small>
                                                <div class="fw-bold text-success" style="font-size: 18px;">
                                                    <i class="uil uil-tag-alt me-1"></i>{{ number_format($refundRequest->total_discount_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Return Shipping Cost --}}
                                        @if($refundRequest->return_shipping_cost > 0)
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: {{ $refundRequest->shouldCustomerPayReturnShipping() ? '#fff3cd' : '#d4edda' }};">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.return_shipping_cost') }}</small>
                                                <div class="fw-bold {{ $refundRequest->shouldCustomerPayReturnShipping() ? 'text-warning' : 'text-success' }}" style="font-size: 18px;">
                                                    <i class="uil uil-truck me-1"></i>{{ number_format($refundRequest->return_shipping_cost, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                                <small class="d-block mt-1" style="font-size: 12px;">
                                                    @if($refundRequest->shouldCustomerPayReturnShipping())
                                                        <span class="badge badge-warning">{{ trans('refund::refund.customer_pays_shipping') }}</span>
                                                        <span class="text-muted ms-1">({{ trans('refund::refund.based_on_vendor_settings') }})</span>
                                                    @else
                                                        <span class="badge badge-success">{{ trans('refund::refund.vendor_pays_shipping') }}</span>
                                                        <span class="text-muted ms-1">({{ trans('refund::refund.based_on_vendor_settings') }})</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Vendor Fees --}}
                                        @if($refundRequest->vendor_fees_amount > 0)
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #e7f3ff;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.vendor_fees_amount') }}</small>
                                                <div class="fw-bold text-info" style="font-size: 18px;">
                                                    <i class="uil uil-plus-circle me-1"></i>{{ number_format($refundRequest->vendor_fees_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Vendor Discounts --}}
                                        @if($refundRequest->vendor_discounts_amount > 0)
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #f8d7da;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.vendor_discounts_amount') }}</small>
                                                <div class="fw-bold text-danger" style="font-size: 18px;">
                                                    <i class="uil uil-minus-circle me-1"></i>{{ number_format($refundRequest->vendor_discounts_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Promo Code Amount --}}
                                        @if($refundRequest->promo_code_amount > 0)
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #f8d7da;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.promo_code_amount') }}</small>
                                                <div class="fw-bold text-danger" style="font-size: 18px;">
                                                    <i class="uil uil-ticket me-1"></i>{{ number_format($refundRequest->promo_code_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Points Used --}}
                                        @if($refundRequest->points_used > 0)
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded" style="background: #f8d7da;">
                                                <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.points_used') }}</small>
                                                <div class="fw-bold text-danger" style="font-size: 18px;">
                                                    <i class="uil uil-star me-1"></i>{{ number_format($refundRequest->points_used, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Total Refund Amount --}}
                                        <div class="col-md-12 mb-3">
                                            <div class="p-3 border rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                <small class="d-block mb-1" style="opacity: 0.9;">{{ trans('refund::refund.fields.total_refund_amount') }}</small>
                                                <div class="fw-bold" style="font-size: 24px;">
                                                    <i class="uil uil-money-withdraw me-1"></i>{{ number_format($refundRequest->total_refund_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Actions Sidebar --}}
                        <div class="col-md-4">
                            <div class="card card-holder">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-info-circle me-1"></i>{{ trans('refund::refund.titles.status_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    {{-- Status --}}
                                    <div class="mb-3">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.status') }}</label>
                                        <div class="p-3 border rounded text-center"
                                            style="background: {{ $refundRequest->getStatusBackgroundColor() }};">
                                            <div class="fw-bold {{ $refundRequest->getStatusTextColor() }}"
                                                style="font-size: 18px;">
                                                <i class="uil {{ $refundRequest->getStatusIcon() }} me-1"></i>
                                                {{ $refundRequest->status_label }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Created At --}}
                                    <div class="mb-3">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                        <p class="fs-15 color-dark">
                                            <i class="uil uil-calendar-alt me-1"></i>{{ $refundRequest->created_at }}
                                        </p>
                                    </div>

                                    {{-- Status Change Actions --}}
                                    <x-refund::refund-actions :refund="$refundRequest" :showButtons="true" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            {{-- Refund Items --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-list-ul me-1"></i>{{ trans('refund::refund.titles.refund_items') }}
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <x-refund::refund-items-table :refundRequest="$refundRequest" />
                                </div>
                            </div>
                        </div>
                        
                        {{-- Points & Accounting Information (Only show if refunded) --}}
                        @if($refundRequest->status === 'refunded')
                        <div class="col-md-12">
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-chart-line me-1"></i>{{ trans('refund::refund.titles.refund_processing_details') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- Points Information --}}
                                        @if($refundRequest->points_used > 0 || $refundRequest->points_to_deduct > 0)
                                        <div class="col-md-12 mb-3">
                                            <h5 class="mb-3">
                                                <i class="uil uil-star me-1"></i>{{ trans('refund::refund.titles.points_information') }}
                                            </h5>
                                            <div class="row">
                                                @if($refundRequest->points_used > 0)
                                                <div class="col-md-6 mb-2">
                                                    <div class="p-3 border rounded" style="background: #d4edda;">
                                                        <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.points_returned') }}</small>
                                                        <div class="fw-bold text-success" style="font-size: 16px;">
                                                            <i class="uil uil-plus-circle me-1"></i>{{ number_format($refundRequest->points_used, 0) }} {{ trans('refund::refund.points') }}
                                                        </div>
                                                        <small class="text-muted">{{ trans('refund::refund.points_returned_desc') }}</small>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @if($refundRequest->points_to_deduct > 0)
                                                <div class="col-md-6 mb-2">
                                                    <div class="p-3 border rounded" style="background: #f8d7da;">
                                                        <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.points_deducted') }}</small>
                                                        <div class="fw-bold text-danger" style="font-size: 16px;">
                                                            <i class="uil uil-minus-circle me-1"></i>{{ number_format($refundRequest->points_to_deduct, 0) }} {{ trans('refund::refund.points') }}
                                                        </div>
                                                        <small class="text-muted">{{ trans('refund::refund.points_deducted_desc') }}</small>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                        
                                        {{-- Accounting Information --}}
                                        @php
                                            $accountingEntry = \Modules\Accounting\app\Models\AccountingEntry::where('order_id', $refundRequest->order_id)
                                                ->where('type', 'refund')
                                                ->whereJsonContains('metadata->refund_request_id', $refundRequest->id)
                                                ->first();
                                        @endphp
                                        
                                        @if($accountingEntry)
                                        <div class="col-md-12 mb-3">
                                            <h5 class="mb-3">
                                                <i class="uil uil-calculator me-1"></i>{{ trans('refund::refund.titles.accounting_information') }}
                                            </h5>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="p-3 border rounded" style="background: #e7f3ff;">
                                                        <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.refund_amount') }}</small>
                                                        <div class="fw-bold text-info" style="font-size: 16px;">
                                                            {{ number_format($accountingEntry->amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4 mb-2">
                                                    <div class="p-3 border rounded" style="background: #fff3cd;">
                                                        <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.commission_returned') }}</small>
                                                        <div class="fw-bold text-warning" style="font-size: 16px;">
                                                            {{ number_format($accountingEntry->commission_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4 mb-2">
                                                    <div class="p-3 border rounded" style="background: #d4edda;">
                                                        <small class="text-muted d-block mb-1">{{ trans('refund::refund.fields.vendor_refund_amount') }}</small>
                                                        <div class="fw-bold text-success" style="font-size: 16px;">
                                                            {{ number_format($accountingEntry->vendor_amount, 2) }} {{ trans('common.currency') ?? 'EGP' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        {{-- Stock Information --}}
                                        @php
                                            $stockBookings = \Modules\CatalogManagement\app\Models\StockBooking::where('order_id', $refundRequest->order_id)
                                                ->where('status', 'released')
                                                ->where('notes', 'like', '%' . $refundRequest->refund_number . '%')
                                                ->get();
                                        @endphp
                                        
                                        @if($stockBookings->count() > 0)
                                        <div class="col-md-12">
                                            <h5 class="mb-3">
                                                <i class="uil uil-box me-1"></i>{{ trans('refund::refund.titles.stock_information') }}
                                            </h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>{{ trans('refund::refund.fields.product') }}</th>
                                                            <th>{{ trans('refund::refund.fields.variant') }}</th>
                                                            <th>{{ trans('refund::refund.fields.region') }}</th>
                                                            <th>{{ trans('refund::refund.fields.quantity_released') }}</th>
                                                            <th>{{ trans('refund::refund.fields.released_at') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($stockBookings as $booking)
                                                        <tr>
                                                            <td>{{ $booking->vendorProductVariant->vendorProduct->product->name ?? '-' }}</td>
                                                            <td>{{ $booking->vendorProductVariant->variant_name ?? '-' }}</td>
                                                            <td>{{ $booking->region->name ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge badge-success">{{ $booking->quantity }}</span>
                                                            </td>
                                                            <td>{{ $booking->updated_at->format('d M, Y h:i A') }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- History Section --}}
                    @if($refundRequest->history && $refundRequest->history->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-holder">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-history me-1"></i>{{ trans('refund::refund.titles.status_history') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="timeline-wrapper">
                                        @foreach($refundRequest->history as $history)
                                            <div class="timeline-item d-flex gap-3 mb-4 position-relative">
                                                <div class="timeline-marker position-relative">
                                                    <div class="marker-dot shadow-sm" style="background: #5f63f2; width: 12px; height: 12px; border-radius: 50%;"></div>
                                                </div>
                                                <div class="timeline-content flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            @if($history->old_status)
                                                                <span class="badge badge-secondary badge-round">
                                                                    {{ trans('refund::refund.statuses.' . $history->old_status) }}
                                                                </span>
                                                                <i class="uil uil-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} text-muted"></i>
                                                            @endif
                                                            <span class="badge badge-primary badge-round">
                                                                {{ trans('refund::refund.statuses.' . $history->new_status) }}
                                                            </span>
                                                        </div>
                                                        <small class="text-muted fw-500">
                                                            <i class="uil uil-clock me-1"></i>{{ $history->created_at->format('d M, Y h:i A') }}
                                                        </small>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-3 mt-2">
                                                        <small class="text-muted d-flex align-items-center">
                                                            <i class="uil uil-user me-1"></i>
                                                            {{ $history->user ? $history->user->name : trans('common.system') }}
                                                        </small>
                                                        @if($history->notes)
                                                            <small class="px-2 py-1 rounded border" style="font-style: italic; color: #555;">
                                                                <i class="uil uil-notes me-1"></i>
                                                                @php
                                                                    // Check if notes is a translation key (starts with module name)
                                                                    $notesText = $history->notes;
                                                                    if (str_contains($notesText, '::') && trans($notesText) !== $notesText) {
                                                                        $notesText = trans($notesText);
                                                                    }
                                                                @endphp
                                                                {{ $notesText }}
                                                            </small>
                                                        @endif
                                                    </div>
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
