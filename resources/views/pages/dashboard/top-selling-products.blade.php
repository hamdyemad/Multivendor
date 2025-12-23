<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500">{{ trans('dashboard.top_selling_products') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.product_name') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.vendor_name') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.sold_count') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.total') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSellingProducts ?? [] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($item->vendorProduct && $item->vendorProduct->product)
                                @php $product = $item->vendorProduct->product; @endphp
                                <a href="{{ route('admin.products.show', $product->id) }}" target="_blank" class="d-flex align-items-center">
                                    @if($product->mainImage)
                                        <img class="rounded-circle"
                                            src="{{ asset('storage/' . $product->mainImage->path) }}"
                                            alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="uil uil-box text-muted"></i>
                                        </div>
                                    @endif
                                    <span class="ms-3">{{ $product->title }}</span>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->vendorData)
                                <a href="{{ route('admin.vendors.show', $item->vendor_id) }}" target="_blank">
                                    @if($item->vendorData->logo)
                                        <img class="rounded-circle"
                                            src="{{ asset('storage/' . $item->vendorData->logo->path) }}"
                                            alt="vendor" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="uil uil-store text-muted"></i>
                                        </div>
                                    @endif
                                    <span class="ms-3">{{ $item->vendorData->getTranslation('name', app()->getLocale()) }}</span>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->total_sold ?? 0 }}</td>
                        <td class="fw-bold text-success">{{ number_format($item->total_revenue ?? 0, 2) }} {{ currency() }}</td>
                        <td class="actions">
                            @if($item->vendorProduct && $item->vendorProduct->product)
                            <a href="{{ route('admin.products.show', $item->vendorProduct->product->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="uil uil-eye m-0"></i>
                            </a>
                            @endif
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
