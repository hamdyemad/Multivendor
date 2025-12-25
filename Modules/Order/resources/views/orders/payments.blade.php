@extends('layout.app')
@section('title')
    {{ trans('order::order.payment_transactions') }} - {{ $order->order_number }} | Bnaia
@endsection

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

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="uil uil-credit-card me-2"></i>
                                {{ trans('order::order.payment_transactions') }} - {{ $order->order_number }}
                            </h5>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="uil uil-arrow-left me-1"></i> {{ trans('order::order.back_to_order') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Order Summary --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6 class="text-muted mb-1">{{ trans('order::order.total_price') }}</h6>
                                    <h4 class="mb-0 text-primary">{{ number_format($order->total_price, 2) }} {{ currency() }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6 class="text-muted mb-1">{{ trans('order::order.payment_type') }}</h6>
                                    <h4 class="mb-0">
                                        @if($order->payment_type === 'online')
                                            <span class="badge bg-info">{{ trans('order::order.online') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ trans('order::order.cod') }}</span>
                                        @endif
                                    </h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6 class="text-muted mb-1">{{ trans('order::order.payment_status') }}</h6>
                                    <h4 class="mb-0">
                                        @if($order->payment_visa_status === 'success')
                                            <span class="badge bg-success">{{ trans('order::order.payment_success') }}</span>
                                        @elseif($order->payment_visa_status === 'pending')
                                            <span class="badge bg-warning">{{ trans('order::order.payment_pending') }}</span>
                                        @elseif($order->payment_visa_status === 'fail' || $order->payment_visa_status === 'failed')
                                            <span class="badge bg-danger">{{ trans('order::order.payment_failed') }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <h6 class="text-muted mb-1">{{ trans('order::order.transactions_count') }}</h6>
                                    <h4 class="mb-0">{{ $payments->count() }}</h4>
                                </div>
                            </div>
                        </div>

                        {{-- Transactions Table --}}
                        @if($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
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
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <code>{{ $payment->paymob_order_id ?? '-' }}</code>
                                                </td>
                                                <td>
                                                    <code>{{ $payment->transaction_id ?? '-' }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($payment->payment_method ?? '-') }}</span>
                                                </td>
                                                <td>{{ number_format($payment->amount, 2) }} {{ currency() }}</td>
                                                <td>
                                                    @if($payment->status === 'paid')
                                                        <span class="badge bg-success">{{ trans('order::order.payment_success') }}</span>
                                                    @elseif($payment->status === 'pending')
                                                        <span class="badge bg-warning">{{ trans('order::order.payment_pending') }}</span>
                                                    @elseif($payment->status === 'failed')
                                                        <span class="badge bg-danger">{{ trans('order::order.payment_failed') }}</span>
                                                    @elseif($payment->status === 'refunded')
                                                        <span class="badge bg-secondary">{{ trans('order::order.payment_refunded') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $payment->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="uil uil-info-circle me-2"></i>
                                {{ trans('order::order.no_transactions_found') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
