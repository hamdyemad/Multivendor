@extends('layout.app')
@section('title', trans('catalogmanagement::product.products_management'))

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
                    ['title' => trans('catalogmanagement::product.products_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-box me-2"></i>
                            {{ trans('catalogmanagement::product.products_management') }}
                        </h4>
                        <a href="{{ route('admin.products.create') }}"
                            class="btn btn-primary btn-squared shadow-sm px-4">
                            <i class="uil uil-plus"></i> {{ trans('catalogmanagement::product.add_product') }}
                        </a>
                    </div>
                    <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
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

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="1">{{ __('common.active') }}
                                                </option>
                                                <option value="0">{{ __('common.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

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
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared me-1"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i>
                                            {{ __('common.export_excel') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('common.reset_filters') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('common.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('common.entries') }}</label>
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
                                    <th><span class="userDatatable-title">{{ __('common.status') }}</span></th>
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
            let table = $('#productsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.products.datatable') }}',
                    data: function(d) {
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.per_page = $('#entriesSelect').val() || 10;
                    }
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'product_information',
                        name: 'product_information',
                        orderable: false,
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
                                html += `<div class="product-name-item" dir="rtl" style="text-align: right !important;">
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
                        render: function(data, type, row) {
                            console.log('Vendor data:', data); // Debug log
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
                        render: function(data) {
                            if (!data?.name) return '<span class="text-muted">—</span>';
                            return `<span class="badge badge-info badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                        }
                    },
                    {
                        data: 'category',
                        name: 'category',
                        render: function(data) {
                            if (!data?.name) return '<span class="text-muted">—</span>';
                            return `<span class="badge badge-secondary badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: true,
                        className: 'text-center',
                        render: function(data) {
                            return data ?
                                `<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check"></i> ${translations.active}</span>` :
                                `<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-times"></i> ${translations.inactive}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('en-EG') : '—';
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

                            return `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                <a href="${showUrl}" class="view btn btn-primary table_action_father" title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>
                                <a href="${stockPricingUrl}" class="stock-pricing btn btn-info table_action_father" title="{{ trans('catalogmanagement::product.manage_stock_pricing') }}">
                                    <i class="uil uil-package table_action_icon"></i>
                                </a>
                                <a href="javascript:void(0);" class="remove delete-product btn btn-danger table_action_father"
                                   data-bs-toggle="modal" data-bs-target="#modal-delete-product"
                                   data-item-id="${data.id}"
                                   data-item-name="${data.translations?.{{ app()->getLocale() }}?.name || 'Product'}"
                                   data-url="${destroyUrl}"
                                   title="{{ trans('common.delete') }}">
                                    <i class="uil uil-trash-alt table_action_icon"></i>
                                </a>
                            </div>`;
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

            // Search Debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => table.ajax.reload(), 600);
            });

            // Filters
            $('#active, #created_date_from, #created_date_to').on('change', () => table.ajax.reload());

            // Export
            $('#exportExcel').on('click', () => table.button('.buttons-excel').trigger());

            // Reset
            $('#resetFilters').on('click', function() {
                $('#search, #active, #created_date_from, #created_date_to').val('');
                $('#entriesSelect').val(10);
                table.search('').page.len(10).ajax.reload();
            });

            // RTL Support in DataTables
            if ($('html').attr('dir') === 'rtl') {
                $('.dataTables_wrapper').addClass('text-end');
            }
        });
    </script>
@endpush
