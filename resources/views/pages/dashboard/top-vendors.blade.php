<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500">{{ trans('dashboard.top_vendors') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.vendor_name') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.orders_count') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.total_selling') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.joined_at') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topVendors ?? [] as $index => $vendor)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('admin.vendors.show', $vendor->id) }}" target="_blank">
                                @if($vendor->media && $vendor->media->first())
                                    <img class="rounded-circle"
                                        src="{{ asset('storage/' . $vendor->media->first()->path) }}"
                                        alt="vendor" style="width: 40px; height: 40px;">
                                @else
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="uil uil-store text-muted"></i>
                                    </div>
                                @endif
                                <span class="ms-3">{{ $vendor->getTranslation('name', app()->getLocale()) }}</span>
                            </a>
                        </td>
                        <td>{{ $vendor->total_orders_count ?? 0 }}</td>
                        <td class="fw-bold text-success">{{ number_format($vendor->total_orders_sum_price ?? 0, 2) }} {{ currency() }}</td>
                        <td>{{ $vendor->created_at ? $vendor->created_at : '-' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.vendors.show', $vendor->id) }}" target="_blank" class="btn btn-sm btn-primary">
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
