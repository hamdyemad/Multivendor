<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500">{{ trans('dashboard.latest_orders') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.order_number') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.customer') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.total') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.actions') }}</span></th>
                </thead>
                <tbody>
                    @forelse($latestOrders ?? [] as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="fw-medium">#{{ $order->order_number ?? $order->id }}</span></td>
                        <td>
                            @if($order->customer)
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-2">
                                        @if($order->customer->image)
                                            <img src="{{ asset($order->customer->image) }}" alt="{{ $order->customer->full_name }}" class="rounded-circle" style="width: 35px; height: 35px;">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px; font-size: 14px;">
                                                {{ strtoupper(substr($order->customer->first_name ?? 'C', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span>{{ $order->customer->full_name ?? $order->customer->email }}</span>
                                </div>
                            @elseif($order->customer_name)
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-2">
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px; font-size: 14px;">
                                            {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <span>{{ $order->customer_name }}</span>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success">{{ number_format($order->vendor_product_total ?? $order->total_price ?? 0, 2) }} {{ currency() }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="uil uil-eye m-0"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">{{ trans('common.no_data_available') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
