@extends('layout.app')
@section('title',
    (isset($statusFilter) && $statusFilter === 'pending') ? trans('menu.products.pending_products') :
    ((isset($statusFilter) && $statusFilter === 'rejected') ? trans('menu.products.rejected_products') :
    ((isset($statusFilter) && $statusFilter === 'approved') ? trans('menu.products.accepted_products') :
    trans('catalogmanagement::product.products_management')))
)

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
                    ['title' =>
                        $statusFilter === 'pending' ? trans('menu.products.pending_products') :
                        ($statusFilter === 'rejected' ? trans('menu.products.rejected_products') :
                        ($statusFilter === 'approved' ? trans('menu.products.accepted_products') :
                        trans('catalogmanagement::product.products_management')))
                    ],
                ]" />
            </div>
        </div>

        {{-- Bank Products Card (Admin Only) --}}
        @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
        <div class="row mb-4">
            <div class="col-lg-12">
                <a href="{{ route('admin.products.bank') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm bg-gradient-primary text-white" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--second-primary) 100%);">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="uil uil-database" style="font-size: 2.5rem; opacity: 0.9;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 text-white fw-bold">{{ __('catalogmanagement::product.bank_products') }}</h5>
                                        <small class="text-white-50">{{ __('catalogmanagement::product.bank_products_description') ?? 'Manage shared products available for all vendors' }}</small>
                                    </div>
                                </div>
                                <div>
                                    <i class="uil uil-arrow-right" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-box me-2"></i>
                            @if($statusFilter === 'pending')
                                {{ trans('menu.products.pending_products') }}
                            @elseif($statusFilter === 'rejected')
                                {{ trans('menu.products.rejected_products') }}
                            @elseif($statusFilter === 'approved')
                                {{ trans('menu.products.accepted_products') }}
                            @else
                                {{ trans('catalogmanagement::product.products_management') }}
                            @endif
                        </h4>
                        <a href="{{ route('admin.products.create') }}"
                            class="btn btn-primary btn-squared shadow-sm px-4">
                            <i class="uil uil-plus"></i> {{ trans('catalogmanagement::product.add_product') }}
                        </a>
                    </div>
                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('common.search') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vendor_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-store me-1"></i>
                                                {{ __('catalogmanagement::product.vendor') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="vendor_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($vendors as $vendor)
                                                    <option value="{{ $vendor['id'] }}" @if(request('vendor_id') == $vendor['id']) selected @endif>{{ $vendor['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="brand_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-tag-alt me-1"></i>
                                                {{ __('catalogmanagement::product.brand') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="brand_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand['id'] }}" @if(request('brand_id') == $brand['id']) selected @endif>{{ $brand['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="category_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-folder me-1"></i>
                                                {{ __('catalogmanagement::product.category') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="category_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category['id'] }}" @if(request('category_id') == $brand['id']) selected @endif>{{ $category['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.active_status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="1" @if(request('active') == '1') selected @endif>{{ __('common.active') }}
                                                </option>
                                                <option value="2" @if(request('active') == '2') selected @endif>{{ __('common.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    @if(!isset($statusFilter))
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-file-check me-1"></i>
                                                {{ __('catalogmanagement::product.approval_status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses() as $statusValue => $statusLabel)
                                                    <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('common.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('common.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('common.reset_filters') }}
                                        </button>
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i>
                                            {{ __('common.export_excel') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="productsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.product_information') }}</span></th>
                                    @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::product.vendor') }}</span></th>
                                    @endif
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.brand') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.category') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.approval_status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.activation') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.created_at') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <x-delete-with-loading modalId="modal-delete-product" tableId="productsDataTable" deleteButtonClass="delete-product"
        :title="trans('main.confirm delete')" :message="trans('main.are you sure you want to delete this')" itemNameId="delete-product-name" confirmBtnId="confirmDeleteProductBtn"
        :cancelText="trans('main.cancel')" :deleteText="trans('main.delete')" :loadingDeleting="trans('main.deleting')" :loadingPleaseWait="trans('main.please wait')" :loadingDeletedSuccessfully="trans('main.deleted success')" :loadingRefreshing="trans('main.refreshing')"
        :errorDeleting="trans('main.error on delete')" />

    {{-- Change Status Modal --}}
    <div class="modal fade" id="modal-change-status" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-info" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusModalLabel">{{ __('catalogmanagement::product.change_product_status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-info-body d-flex mb-3">
                        <div class="modal-info-icon primary">
                            <img src="{{ asset('assets/img/svg/info.svg') }}" alt="info" class="svg">
                        </div>
                        <div class="modal-info-text">
                            <p class="fw-500" id="status-product-name"></p>
                            <p class="text-muted fs-13">{{ __('catalogmanagement::product.select_new_status_for_product') }}</p>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="product-status" class="form-label il-gray fs-14 fw-500 align-center">
                            {{ __('catalogmanagement::product.approval_status') }} <span class="text-danger">*</span>
                        </label>
                        <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="product-status" required>
                            <option value="">{{ __('common.select_option') }}</option>
                            @foreach(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses() as $statusValue => $statusLabel)
                                <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="rejection-reason-group" style="display: none;">
                        <label for="rejection-reason" class="form-label il-gray fs-14 fw-500 align-center">
                            {{ __('catalogmanagement::product.rejection_reason') }}
                        </label>
                        <textarea class="form-control ih-medium ip-gray radius-xs b-light px-15" id="rejection-reason" rows="3" placeholder="{{ __('catalogmanagement::product.enter_rejection_reason') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                        <i class="uil uil-times"></i> {{ __('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="confirmChangeStatusBtn">
                        <i class="uil uil-check"></i> {{ __('common.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush



@push('scripts')
    <script>
        $(document).ready(function() {
            const translations = {
                active: '{{ __('common.active') }}',
                inactive: '{{ __('common.inactive') }}'
            };

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('vendor_id')) $('#vendor_filter').val(urlParams.get('vendor_id')).trigger('change');
            if (urlParams.has('brand_id')) $('#brand_filter').val(urlParams.get('brand_id')).trigger('change');
            if (urlParams.has('category_id')) $('#category_filter').val(urlParams.get('category_id')).trigger('change');
            if (urlParams.has('active')) $('#active').val(urlParams.get('active')).trigger('change');
            if (urlParams.has('status')) $('#status').val(urlParams.get('status')).trigger('change');
            if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
            if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

            let table = $('#productsDataTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '{{ route('admin.products.datatable') }}',
                    data: function(d) {
                        d.search = $('#search').val();
                        d.vendor_id = $('#vendor_filter').val();
                        d.brand_id = $('#brand_filter').val();
                        d.category_id = $('#category_filter').val();
                        d.active = $('#active').val();
                        d.status = $('#status').val();
                        @if(isset($statusFilter) && $statusFilter)
                        d.status = '{{ $statusFilter }}';
                        @endif
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.per_page = $('#entriesSelect').val() || 10;
                    }
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'product_information',
                        name: 'product_information',
                        orderable: false,
                        searchable: true,
                        render: function(data, type, row) {
                            if (!data) return '<span class="text-muted">—</span>';
                            let html = '<div class="product-info-container">';

                            if (data.name_en && data.name_en !== '-') {
                                html += `<div class="product-name-item mb-2">
                                    <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
                                    <span class="product-name text-dark fw-semibold">${$('<div/>').text(data.name_en).html()}</span>
                                </div>`;
                            }

                            if (data.name_ar && data.name_ar !== '-') {
                                html += `<div class="product-name-item">
                                    <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
                                    <span class="product-name text-dark fw-semibold" dir="rtl" style="font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${$('<div/>').text(data.name_ar).html()}</span>
                                </div>`;
                            }

                            html += '</div>';
                            return html;
                        },
                        className: 'text-start'
                    },
                    @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                    {
                        data: 'vendor',
                        name: 'vendor',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data || !data.name) {
                                return '<span class="text-muted">—</span>';
                            }
                            return `<span class="badge badge-primary badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                        }
                    },
                    @endif
                    {
                        data: 'brand',
                        name: 'brand',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            if (!data?.name) return '<span class="text-muted">—</span>';
                            return `<span class="badge badge-info badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                        }
                    },
                    {
                        data: 'category',
                        name: 'category',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            if (!data?.name) return '<span class="text-muted">—</span>';
                            return `<span class="badge badge-secondary badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (!data) {
                                return `<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-minus"></i> {{ __('common.none') }}</span>`;
                            }
                            if (data === 'approved') {
                                return `<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check-circle"></i> {{ __('common.approved') }}</span>`;
                            } else if (data === 'rejected') {
                                return `<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-times-circle"></i> {{ __('common.rejected') }}</span>`;
                            } else if (data === 'pending') {
                                return `<span class="badge badge-warning badge-round badge-lg"><i class="uil uil-clock"></i> {{ __('common.pending') }}</span>`;
                            }
                            return `<span class="badge badge-secondary badge-round badge-lg">${data}</span>`;
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            // For sorting, return numeric value
                            if (type === 'sort' || type === 'type') {
                                return data ? 1 : 0;
                            }

                            // For display, return formatted HTML with switcher
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'activation-switch-' + row.id;
                            const productName = row.product_information?.name_en || row.product_information?.name_ar || 'Product #' + row.id;

                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input activation-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-product-id="${row.id}"
                                           data-product-name="${$('<div>').text(productName).html()}"
                                           ${isChecked}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            const showUrl = "{{ route('admin.products.show', ':id') }}".replace(':id', data.id);
                            const editUrl = "{{ route('admin.products.edit', ':id') }}".replace(':id', data.id);
                            const destroyUrl = "{{ route('admin.products.destroy', ':id') }}".replace(':id', data.id);
                            const stockPricingUrl = "{{ route('admin.products.stock-management', ':id') }}".replace(':id', data.id);

                            let actions = `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                <a href="${showUrl}" class="view btn btn-primary table_action_father" title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>`;

                            actions += `
                                <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>
                                <a href="${stockPricingUrl}" class="stock-management btn btn-info table_action_father" title="{{ trans('catalogmanagement::product.stock_management') }}">
                                    <i class="uil uil-box table_action_icon"></i>
                                </a>`;

                            // Add approve/reject button and move to bank for admin users only
                            @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                            actions += `
                                <a href="javascript:void(0);" class="change-status btn btn-success table_action_father"
                                   data-bs-toggle="modal" data-bs-target="#modal-change-status"
                                   data-item-id="${data.id}"
                                   data-item-status="${data.status || ''}"
                                   data-item-name="${data.product_information?.name_en || 'Product'}"
                                   title="{{ trans('catalogmanagement::product.change_status') }}">
                                    <i class="uil uil-check-circle table_action_icon"></i>
                                </a>`;

                            // Add move to bank button (only if not already a bank product)
                            if (data.product_type !== 'bank') {
                                actions += `
                                <a href="javascript:void(0);" class="move-to-bank btn btn-secondary table_action_father"
                                   data-item-id="${data.id}"
                                   data-item-name="${data.product_information?.name_en || data.product_information?.name_ar || 'Product'}"
                                   title="{{ trans('catalogmanagement::product.move_to_bank') }}">
                                    <i class="uil uil-database table_action_icon"></i>
                                </a>`;
                            }
                            @endif

                            actions += `
                                <a href="javascript:void(0);" class="remove delete-product btn btn-danger table_action_father"
                                   data-bs-toggle="modal" data-bs-target="#modal-delete-product"
                                   data-item-id="${data.id}"
                                   data-item-name="${data.product_information?.name_en || data.product_information?.name_ar || 'Product'}"
                                   data-url="${destroyUrl}"
                                   title="{{ trans('common.delete') }}">
                                    <i class="uil uil-trash-alt table_action_icon"></i>
                                </a>
                            </div>`;

                            return actions;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    @if(auth()->user() && in_array(auth()->user()->user_type_id, [\App\Models\UserType::SUPER_ADMIN_TYPE, \App\Models\UserType::ADMIN_TYPE]))
                        [5, 'desc'] // Created at column for admin users (with vendor column)
                    @else
                        [4, 'desc'] // Created at column for vendor users (without vendor column)
                    @endif
                ],
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('catalogmanagement::product.no_products_found') ?? 'No products found' }}",
                    emptyTable: "{{ __('catalogmanagement::product.no_products_found') ?? 'No products found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
                    paginate: {
                        first: '{{ __('common.first') ?? 'First' }}',
                        last: '{{ __('common.last') ?? 'Last' }}',
                        next: '{{ __('common.next') ?? 'Next' }}',
                        previous: '{{ __('common.previous') ?? 'Previous' }}'
                    },
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                },
                dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="uil uil-file-download-alt"></i>',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ trans('catalogmanagement::product.products_management') }}'
                }]
            });

            // Entries Selector
            $('#entriesSelect').html([10, 25, 50, 100].map(n => `<option value="${n}">${n}</option>`).join(''));
            $('#entriesSelect').val(10).on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                // Update URL with filter parameters
                const params = new URLSearchParams();
                const search = $('#search').val();
                const vendor = $('#vendor_filter').val();
                const brand = $('#brand_filter').val();
                const category = $('#category_filter').val();
                const active = $('#active').val();
                const status = $('#status').val();
                const dateFrom = $('#created_date_from').val();
                const dateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
                if (vendor) params.set('vendor_id', vendor);
                if (brand) params.set('brand_id', brand);
                if (category) params.set('category_id', category);
                if (active) params.set('active', active);
                if (status) params.set('status', status);
                if (dateFrom) params.set('created_date_from', dateFrom);
                if (dateTo) params.set('created_date_to', dateTo);

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.pushState({}, '', newUrl);

                table.ajax.reload();
            });

            // Live search for product names only (EN/AR)
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => table.ajax.reload(), 600);
            });

            // Filters - Use 'select2:select' and 'select2:clear' events for Select2 dropdowns
            $('#vendor_filter, #brand_filter, #category_filter, #active, #status').on('select2:select select2:clear change', function() {
                table.ajax.reload();
            });

            $('#created_date_from, #created_date_to').on('change', () => table.ajax.reload());

            // Export
            $('#exportExcel').on('click', () => table.button('.buttons-excel').trigger());

            // Reset
            $('#resetFilters').on('click', function() {
                // Clear URL parameters and reload page
                window.history.pushState({}, '', window.location.pathname);

                // Clear regular inputs
                $('#search, #created_date_from, #created_date_to').val('');

                // Clear dropdowns properly
                $('#vendor_filter, #brand_filter, #category_filter, #active, #status').val(null).trigger('change');

                // Reload table
                table.ajax.reload();
            });

            // RTL Support in DataTables
            if ($('html').attr('dir') === 'rtl') {
                $('.dataTables_wrapper').addClass('text-end');
            }

            // Change Status Modal Handler
            let currentProductId = null;

            $(document).on('click', '.change-status', function() {
                currentProductId = $(this).data('item-id');
                const productName = $(this).data('item-name');
                const currentStatus = $(this).data('item-status');

                $('#status-product-name').text(productName);
                $('#product-status').val(currentStatus);
                $('#rejection-reason').val('');

                // Show/hide rejection reason based on current status
                if (currentStatus === 'rejected') {
                    $('#rejection-reason-group').show();
                } else {
                    $('#rejection-reason-group').hide();
                }
            });

            // Show/hide rejection reason field based on selected status
            $('#product-status').on('change', function() {
                if ($(this).val() === 'rejected') {
                    $('#rejection-reason-group').slideDown();
                } else {
                    $('#rejection-reason-group').slideUp();

                    // Clear rejection reason - check if CKEditor is initialized
                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['rejection-reason']) {
                        CKEDITOR.instances['rejection-reason'].setData('');
                    } else {
                        $('#rejection-reason').val('');
                    }
                }
            });

            // Confirm Status Change
            $('#confirmChangeStatusBtn').on('click', function() {
                const newStatus = $('#product-status').val();

                // Get rejection reason - check if CKEditor is initialized
                let rejectionReason = '';
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['rejection-reason']) {
                    // Get value from CKEditor
                    rejectionReason = CKEDITOR.instances['rejection-reason'].getData();
                    // Strip HTML tags for validation
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = rejectionReason;
                    rejectionReason = tempDiv.textContent || tempDiv.innerText || '';
                } else {
                    // Get value from regular textarea
                    rejectionReason = $('#rejection-reason').val();
                }

                console.log('Status:', newStatus);
                console.log('Rejection Reason:', rejectionReason);

                if (!newStatus) {
                    toastr.error('{{ __("catalogmanagement::product.please_select_status") }}');
                    return;
                }

                if (newStatus === 'rejected' && !rejectionReason.trim()) {
                    toastr.error('{{ __("catalogmanagement::product.rejection_reason_required") }}');
                    return;
                }

                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="uil uil-spinner-alt spin-animation me-1"></i>{{ __("common.updating") }}...');

                $.ajax({
                    url: '{{ route("admin.products.change-status", ":id") }}'.replace(':id', currentProductId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus,
                        rejection_reason: rejectionReason
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || '{{ __("catalogmanagement::product.status_updated_successfully") }}');
                            $('#modal-change-status').modal('hide');
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message || '{{ __("common.error_occurred") }}');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        toastr.error(xhr.responseJSON?.message || '{{ __("common.error_occurred") }}');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset modal when closed
            $('#modal-change-status').on('hidden.bs.modal', function() {
                $('#product-status').val('');

                // Clear rejection reason - check if CKEditor is initialized
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['rejection-reason']) {
                    CKEDITOR.instances['rejection-reason'].setData('');
                } else {
                    $('#rejection-reason').val('');
                }

                $('#rejection-reason-group').hide();
                currentProductId = null;
            });

            // Activation switcher handler
            $(document).on('change', '.activation-switcher', function() {
                const switcher = $(this);
                const productId = switcher.data('product-id');
                const productName = switcher.data('product-name');
                const newStatus = switcher.is(':checked') ? 1 : 2; // 1=active, 2=inactive

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ __('catalogmanagement::product.change_activation') }}',
                        subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                    });
                }

                // Make AJAX request
                $.ajax({
                    url: '{{ route('admin.products.change-activation', ':id') }}'.replace(':id', productId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('common.success') ?? 'Success' }}',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            }

                            // Reload table to reflect changes
                            table.ajax.reload(null, false);
                        } else {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Revert switcher state
                            switcher.prop('checked', !switcher.is(':checked'));

                            // Show error message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') ?? 'Error' }}',
                                    text: response.message
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Hide loading overlay
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        // Revert switcher state
                        switcher.prop('checked', !switcher.is(':checked'));

                        let errorMessage = '{{ __('catalogmanagement::product.error_changing_activation') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Show error message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') ?? 'Error' }}',
                                text: errorMessage
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function() {
                        // Re-enable switcher
                        switcher.prop('disabled', false);
                    }
                });
            });

            // Move to bank handler
            @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
            $(document).on('click', '.move-to-bank', function() {
                const btn = $(this);
                const productId = btn.data('item-id');
                const productName = btn.data('item-name');

                // Show confirmation popup
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '<i class="uil uil-database text-primary"></i> {{ __('catalogmanagement::product.move_to_bank') }}',
                        html: `<div class="text-center py-3">
                                   <div class="mb-3">
                                       <span class="badge bg-primary badge-lg badge-round px-3 py-2 fs-6">${productName}</span>
                                   </div>
                                   <p class="mb-2">{{ __('catalogmanagement::product.confirm_move_to_bank') }}</p>
                                   <p class="text-muted small mb-0">{{ __('catalogmanagement::product.move_to_bank_description') }}</p>
                               </div>`,
                        icon: null,
                        showCancelButton: true,
                        confirmButtonColor: '#5F63F2',
                        cancelButtonColor: '#868e96',
                        confirmButtonText: '<i class="uil uil-check me-1"></i> {{ __('common.confirm') ?? 'Confirm' }}',
                        cancelButtonText: '<i class="uil uil-times me-1"></i> {{ __('common.cancel') ?? 'Cancel' }}',
                        customClass: {
                            popup: 'swal2-lg',
                            title: 'fs-5 fw-bold',
                            confirmButton: 'btn btn-success px-4 me-1',
                            cancelButton: 'btn btn-secondary px-4 me-1'
                        },
                        buttonsStyling: false,
                        showCloseButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.show({
                                    text: '{{ __('catalogmanagement::product.move_to_bank') }}',
                                    subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                                });
                            }

                            // Make AJAX request
                            $.ajax({
                                url: '{{ route('admin.products.move-to-bank', ':id') }}'.replace(':id', productId),
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    // Hide loading overlay
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }

                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: '{{ __('common.success') ?? 'Success' }}',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        });

                                        // Reload table to reflect changes
                                        table.ajax.reload(null, false);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: '{{ __('common.error') ?? 'Error' }}',
                                            text: response.message
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    // Hide loading overlay
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }

                                    let errorMessage = '{{ __('catalogmanagement::product.error_moving_to_bank') }}';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }

                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('common.error') ?? 'Error' }}',
                                        text: errorMessage
                                    });
                                }
                            });
                        }
                    });
                } else {
                    // Fallback without SweetAlert
                    if (confirm('{{ __('catalogmanagement::product.confirm_move_to_bank') }}')) {
                        $.ajax({
                            url: '{{ route('admin.products.move-to-bank', ':id') }}'.replace(':id', productId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message);
                                    table.ajax.reload(null, false);
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function(xhr) {
                                alert('{{ __('catalogmanagement::product.error_moving_to_bank') }}');
                            }
                        });
                    }
                }
            });
            @endif
        });
    </script>
@endpush
