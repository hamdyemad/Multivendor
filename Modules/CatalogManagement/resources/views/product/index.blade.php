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

                                    @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vendor_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-store me-1"></i>
                                                {{ __('catalogmanagement::product.vendor') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="vendor_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($vendors as $vendor)
                                                    <option value="{{ $vendor['id'] }}">{{ $vendor['name'] }}</option>
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
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="brand_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
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
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="category_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
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
                                            <label for="status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-file-check me-1"></i>
                                                {{ __('catalogmanagement::product.approval_status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses() as $statusValue => $statusLabel)
                                                    <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                                @endforeach
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

            let table = $('#productsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.products.datatable') }}',
                    data: function(d) {
                        d.search = $('#search').val();
                        d.vendor_id = $('#vendor_filter').val();
                        d.brand_id = $('#brand_filter').val();
                        d.category_id = $('#category_filter').val();
                        d.active = $('#active').val();
                        d.status = $('#status').val();
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
                        data: 'status',
                        name: 'status',
                        orderable: true,
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

                            let actions = `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                <a href="${showUrl}" class="view btn btn-primary table_action_father" title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>`;

                            // Add approve/reject button for admin users only
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
                            @endif

                            actions += `
                                <a href="javascript:void(0);" class="remove delete-product btn btn-danger table_action_father"
                                   data-bs-toggle="modal" data-bs-target="#modal-delete-product"
                                   data-item-id="${data.id}"
                                   data-item-name="${data.translations?.{{ app()->getLocale() }}?.name || 'Product'}"
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

            // Search Debounce
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
                // Clear regular inputs
                $('#search, #created_date_from, #created_date_to').val('');

                // Clear dropdowns properly
                $('#vendor_filter, #brand_filter, #category_filter, #active, #status').val(null).trigger('change');

                $('#entriesSelect').val(10);
                table.search('').page.len(10).ajax.reload();
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
        });
    </script>
@endpush
