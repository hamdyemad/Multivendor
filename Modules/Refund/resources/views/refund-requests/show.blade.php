@extends('layout.app')

@section('title', trans('refund::refund.titles.view_refund'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title">{{ trans('refund::refund.titles.view_refund') }}</h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="uil uil-estate"></i>{{ trans('menu.dashboard.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.refunds.index') }}">{{ trans('menu.refunds.title') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $refundRequest->refund_number }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header">
                    <h6>{{ trans('refund::refund.titles.refund_details') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>{{ trans('refund::refund.fields.refund_number') }}:</strong> {{ $refundRequest->refund_number }}</p>
                            <p><strong>{{ trans('refund::refund.fields.customer') }}:</strong> {{ $refundRequest->customer->name }}</p>
                            <p><strong>{{ trans('refund::refund.fields.vendor') }}:</strong> {{ $refundRequest->vendor->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ trans('refund::refund.fields.status') }}:</strong> 
                                <span class="badge badge-{{ $refundRequest->status == 'refunded' ? 'success' : ($refundRequest->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ trans('refund::refund.statuses.' . $refundRequest->status) }}
                                </span>
                            </p>
                            <p><strong>{{ trans('refund::refund.fields.total_refund_amount') }}:</strong> {{ number_format($refundRequest->total_refund_amount, 2) }} {{ trans('common.currency') }}</p>
                            <p><strong>{{ trans('refund::refund.fields.created_at') }}:</strong> {{ $refundRequest->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    <h6 class="mb-3">{{ trans('refund::refund.titles.refund_items') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('refund::refund.fields.product') }}</th>
                                    <th>{{ trans('refund::refund.fields.quantity') }}</th>
                                    <th>{{ trans('refund::refund.fields.unit_price') }}</th>
                                    <th>{{ trans('refund::refund.fields.total_price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($refundRequest->items as $item)
                                <tr>
                                    <td>{{ $item->orderProduct->vendorProduct->product->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($refundRequest->status == 'pending')
                    <div class="mt-4">
                        <form action="{{ route('admin.refunds.approve', $refundRequest) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">{{ trans('refund::refund.actions.approve') }}</button>
                        </form>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                            {{ trans('refund::refund.actions.reject') }}
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.refunds.reject', $refundRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('refund::refund.actions.reject') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans('refund::refund.fields.rejection_reason') }}</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ trans('refund::refund.actions.reject') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
