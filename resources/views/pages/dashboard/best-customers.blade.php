<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500">{{ trans('dashboard.best_customers') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.name') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.orders_count') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.revenue') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.joined_at') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bestCustomers ?? [] as $index => $customer)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($customer->image)
                                <img class="rounded-circle" src="{{ asset('storage/' . $customer->image) }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="uil uil-user text-muted"></i>
                                </div>
                            @endif
                            <span class="ms-3">{{ $customer->full_name ?? $customer->email }}</span>
                        </td>
                        <td>{{ $customer->orders_count ?? 0 }}</td>
                        <td class="fw-bold text-success">{{ number_format($customer->orders_sum_total_price ?? 0, 2) }} {{ currency() }}</td>
                        <td>{{ $customer->created_at ? $customer->created_at : '-' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="uil uil-eye m-0"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ trans('common.no_data_available') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
