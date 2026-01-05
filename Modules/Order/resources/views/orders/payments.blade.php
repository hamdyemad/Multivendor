@extends('layout.app')
@section('title')
    {{ trans('order::order.payment_transactions') }} - {{ $order->order_number }} | Bnaia
@endsection

@push('styles')
<style>
    .payment-summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        color: white;
        padding: 25px;
        margin-bottom: 25px;
    }
    .payment-summary-card .summary-item {
        text-align: center;
        padding: 15px;
        border-right: 1px solid rgba(255,255,255,0.2);
    }
    .payment-summary-card .summary-item:last-child {
        border-right: none;
    }
    .payment-summary-card .summary-label {
        font-size: 12px;
        text-transform: uppercase;
        opacity: 0.8;
        margin-bottom: 8px;
    }
    .payment-summary-card .summary-value {
        font-size: 24px;
        font-weight: 700;
    }
    .payment-summary-card .summary-value.small {
        font-size: 18px;
    }
    .transaction-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .transaction-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        padding: 18px 25px;
    }
    .transaction-card .card-body {
        padding: 0;
    }
    .transaction-table {
        margin-bottom: 0;
    }
    .transaction-table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        color: #6c757d;
        padding: 15px 20px;
    }
    .transaction-table tbody td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    .transaction-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .transaction-id {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 12px;
        background: #e9ecef;
        padding: 5px 10px;
        border-radius: 5px;
        color: #495057;
    }
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-paid {
        background: #d4edda;
        color: #155724;
    }
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-failed {
        background: #f8d7da;
        color: #721c24;
    }
    .status-refunded {
        background: #e2e3e5;
        color: #383d41;
    }
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }
    .empty-state i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    .empty-state h5 {
        color: #6c757d;
        margin-bottom: 10px;
    }
    .empty-state p {
        color: #adb5bd;
    }
    .payment-method-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }
    .payment-method-card {
        background: #e3f2fd;
        color: #1565c0;
    }
    .payment-method-wallet {
        background: #fff3e0;
        color: #e65100;
    }
    .amount-cell {
        font-weight: 600;
        font-size: 15px;
        color: #2e7d32;
    }
    .date-cell {
        color: #6c757d;
        font-size: 13px;
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
                        'title' => trans('order::order.order_management'),
                        'url' => route('admin.orders.index'),
                    ],
                    [
                        'title' => $order->order_number,
                        'url' => route('admin.orders.show', $order->id),
                    ],
                    ['title' => trans('order::order.payment_transactions')],
                ]" />
            </div>
        </div>

        {{-- Payment Summary Card --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="payment-summary-card">
                    <div class="row align-items-center">
                        <div class="col-md-3 summary-item">
                            <div class="summary-label">{{ trans('order::order.order_number') }}</div>
                            <div class="summary-value small">{{ $order->order_number }}</div>
                        </div>
                        <div class="col-md-3 summary-item">
                            <div class="summary-label">{{ trans('order::order.total_price') }}</div>
                            <div class="summary-value">{{ number_format($order->total_price, 2) }} {{ currency() }}</div>
                        </div>
                        <div class="col-md-3 summary-item">
                            <div class="summary-label">{{ trans('order::order.payment_status') }}</div>
                            <div class="summary-value small">
                                @php
                                    // Derive payment status from payments collection
                                    $hasPaidPayment = $payments->where('status', 'paid')->count() > 0;
                                    $derivedPaymentStatus = $hasPaidPayment ? 'success' : ($order->payment_visa_status ?? 'pending');
                                @endphp
                                @if($derivedPaymentStatus === 'success')
                                    <span class="badge bg-success badge-round badge-lg px-3 py-2">
                                        <i class="uil uil-check-circle me-1"></i>{{ trans('order::order.payment_success') }}
                                    </span>
                                @elseif($derivedPaymentStatus === 'pending')
                                    <span class="badge bg-warning badge-round badge-lg px-3 py-2">
                                        <i class="uil uil-clock me-1"></i>{{ trans('order::order.payment_pending') }}
                                    </span>
                                @elseif($derivedPaymentStatus === 'fail' || $derivedPaymentStatus === 'failed')
                                    <span class="badge bg-danger badge-round badge-lg px-3 py-2">
                                        <i class="uil uil-times-circle me-1"></i>{{ trans('order::order.payment_failed') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary badge-round badge-lg px-3 py-2">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 summary-item">
                            <div class="summary-label">{{ trans('order::order.transactions_count') }}</div>
                            <div class="summary-value">{{ $payments->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card transaction-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h5 class="mb-0 fw-bold">
                                <i class="uil uil-transaction me-2 text-primary"></i>
                                {{ trans('order::order.payment_transactions') }}
                            </h5>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="uil uil-arrow-left me-1"></i> {{ trans('order::order.back_to_order') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table transaction-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ trans('order::order.paymob_order_id') }}</th>
                                            <th>{{ trans('order::order.transaction_id') }}</th>
                                            <th>{{ trans('order::order.payment_method') }}</th>
                                            <th>{{ trans('order::order.amount') }}</th>
                                            <th>{{ trans('order::order.status') }}</th>
                                            <th>{{ trans('order::order.created_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $index => $payment)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <span class="transaction-id">{{ $payment->paymob_order_id ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <span class="transaction-id">{{ $payment->transaction_id ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $method = strtolower($payment->payment_method ?? 'card');
                                                    @endphp
                                                    @if(str_contains($method, 'wallet'))
                                                        <span class="payment-method-badge payment-method-wallet">
                                                            <i class="uil uil-wallet"></i>
                                                            {{ ucfirst($payment->payment_method ?? 'Wallet') }}
                                                        </span>
                                                    @else
                                                        <span class="payment-method-badge payment-method-card">
                                                            <i class="uil uil-credit-card"></i>
                                                            {{ ucfirst($payment->payment_method ?? 'Card') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="amount-cell">{{ number_format($payment->amount, 2) }} {{ currency() }}</span>
                                                </td>
                                                <td>
                                                    @if($payment->status === 'paid')
                                                        <span class="status-badge status-paid">
                                                            <i class="uil uil-check me-1"></i>{{ trans('order::order.payment_success') }}
                                                        </span>
                                                    @elseif($payment->status === 'pending')
                                                        <span class="status-badge status-pending">
                                                            <i class="uil uil-clock me-1"></i>{{ trans('order::order.payment_pending') }}
                                                        </span>
                                                    @elseif($payment->status === 'failed')
                                                        <span class="status-badge status-failed">
                                                            <i class="uil uil-times me-1"></i>{{ trans('order::order.payment_failed') }}
                                                        </span>
                                                    @elseif($payment->status === 'refunded')
                                                        <span class="status-badge status-refunded">
                                                            <i class="uil uil-redo me-1"></i>{{ trans('order::order.payment_refunded') }}
                                                        </span>
                                                    @else
                                                        <span class="status-badge" style="background: #e9ecef; color: #495057;">
                                                            {{ $payment->status ?? '-' }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="date-cell">
                                                        <i class="uil uil-calendar-alt me-1"></i>
                                                        {{ $payment->created_at ? $payment->created_at : '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="uil uil-receipt"></i>
                                <h5>{{ trans('order::order.no_transactions_found') }}</h5>
                                <p>{{ trans('order::order.no_payment_transactions_message') ?? 'No payment transactions have been recorded for this order yet.' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer & Order Info --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="uil uil-user me-2 text-primary"></i>
                            {{ trans('order::order.customer_information') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 40%;">{{ trans('order::order.customer_name') }}</td>
                                <td class="fw-bold">{{ $order->customer_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ trans('order::order.customer_email') }}</td>
                                <td>{{ $order->customer_email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ trans('order::order.customer_phone') }}</td>
                                <td>{{ $order->customer_phone ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="uil uil-info-circle me-2 text-primary"></i>
                            {{ trans('order::order.order_information') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 40%;">{{ trans('order::order.order_number') }}</td>
                                <td class="fw-bold">{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ trans('order::order.payment_type') }}</td>
                                <td>
                                    @if($order->payment_type === 'online')
                                        <span class="badge bg-info badge-round badge-lg">{{ trans('order::order.online') }}</span>
                                    @else
                                        <span class="badge bg-secondary badge-round badge-lg">{{ trans('order::order.cod') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ trans('order::order.created_at') }}</td>
                                <td>{{ $order->created_at ? $order->created_at : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
